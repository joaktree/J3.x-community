<?php
/**
 * Joomla! component Joaktree
 * file		front end community model - community.php
 *
 * @version	1.5.0
 * @author	Niels van Dantzig
 * @package	Joomla
 * @subpackage	Joaktree
 * @license	GNU/GPL
 *
 * Component for genealogy in Joomla!
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');

// import component and kunena libraries
JLoader::import('helper.person', JPATH_COMPONENT);


class JoaktreeModelCommunity extends JModelForm {

	function __construct() {
		parent::__construct();
	}
	
	public function getUserAccess() {
		return JoaktreeHelper::getUserAccess();
	}
	
	public function getPersonId() {
		return JoaktreeHelper::getPersonId();
	}
	
	public function getApplicationId() {
		return JoaktreeHelper::getApplicationId();
	}

	public function getTechnology() {
		return JoaktreeHelper::getTechnology();
	}
	
	public function getPerson() {
		static $person;
		
		if (!isset($person)) {
			$id[ 'app_id' ] 	= JoaktreeHelper::getApplicationId();
			$id[ 'person_id' ] 	= JoaktreeHelper::getPersonId(); 
			$person	  =  new Person($id, 'basic');
		}
		
		return $person;
	}
	
	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_joaktree.joaktree', 'community', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {		
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		$data 			= new stdClass;
		$kunenaUser 	= KunenaFactory::getUser();

		//$data->open 	= false; 
		$data->name 	= $kunenaUser->getName(); 
		$data->email	= $kunenaUser->get('email');
		$data->message	= null;

		return $data;
	}

	public function getTopic() {
		// Kunena detection and version check
		$minKunenaVersion = '3.0';
		if (!class_exists('KunenaForum') || !KunenaForum::isCompatible($minKunenaVersion)) {
			JFactory::getApplication()->enqueueMessage(JText::sprintf('JT_KUNENADISCUSS_DEPENDENCY_FAIL', $minKunenaVersion), 'warning');
			return;
		} 
		
		// Kunena online check
		if (!KunenaForum::enabled()) {
			JFactory::getApplication()->enqueueMessage(JText::_('JT_KUNENADISCUSS_DISABLED'), 'warning');
			return;
		}
		
		// Person check
		$person 	= $this->getPerson();
		if ($person->living || !$person->published || !$person->kunenacatid) {
			// no discussion for living or unpublished persons or unknown forum category
			JFactory::getApplication()->enqueueMessage(JText::_('JT_NODISCUSSION_ALLOWED'), 'notice');
			return;
		}
		
		$kunenaUser 	= KunenaFactory::getUser();
		$kunenaConfig 	= KunenaFactory::getConfig();
		KunenaForum::setup();
		KunenaFactory::loadLanguage();
		$query = $this->_db->getQuery(true);
		
		// Find cross reference and the real topic
		$query->clear();
		$query->select( ' thread_id ' );
		$query->from(   ' #__joaktree_kunena ');
		$query->where(  ' app_id    = '.$person->app_id.' ' );
		$query->where(  ' person_id = '.$this->_db->quote($person->id).' ' );
		$this->_db->setQuery( $query );
		$thread_id	= $this->_db->loadResult();
		KunenaError::checkDatabaseError();
		
		// in case no thread_id found, we set it to 0 using int
		$thread_id = (int) $thread_id;
		$topic = KunenaForumTopicHelper::get($thread_id);
		
		// If topic has been moved, find the real topic
		while ($topic->moved_id) {
			// Topic has moved -> find the new topic.
			$topic = KunenaForumTopicHelper::get($topic->moved_id);
		}

		if ($thread_id) {
			if (!$topic->exists()) {
				// Topic doesn't exist, so we remove the link from our table
				$query->clear();
				$query->delete(' #__joaktree_kunena ');
				$query->where( ' app_id    = '.$person->app_id.' ' );
				$query->where( ' person_id = '.$this->_db->quote($person->id).' ' );
				$this->_db->setQuery( $query );
				$this->_db->query();
				KunenaError::checkDatabaseError ();
				
			} elseif ($topic->id != $thread_id) {
				// Topic has been moved. We update the table to the new thread
				$query->clear();
				$query->update(' #__joaktree_kunena ');
				$query->set(   ' thread_id = '.(int)$topic->id.' ');
				$query->where( ' app_id    = '.$person->app_id.' ' );
				$query->where( ' person_id = '.$this->_db->quote($person->id).' ' );
				$this->_db->setQuery( $query );
				$this->_db->query();
				KunenaError::checkDatabaseError ();
				
			}
		} elseif ($topic->id && $topic->exists()) {
			// It is a new topic. We create a new link in our table
			$this->createReference($person->app_id, $person->id, $topic->id);
		}

		// Initialise some variables
		$subject = $person->fullName;
		
		if ( $topic->exists() ) {
			// If current user doesn't have authorisation to read existing topic, we are done		
			if ($thread_id && !$topic->authorise('read')) {
				JFactory::getApplication()->enqueueMessage($topic->getError(), 'error');
				return $topic;
			}

			$category = $topic->getCategory();

		} else {
			// If current user doesn't have authorisation to read category, we are done
			$category = KunenaForumCategoryHelper::get($person->kunenacatid);
			if (!$category->authorise('read')) {
				JFactory::getApplication()->enqueueMessage($category->getError(), 'error');
				return $topic;
			}
		}
		
		// check whether we have save an reply
		if (JoaktreeHelper::getAction() == 'save') {
			$input = JFactory::getApplication()->input;
			$token = $input->get('tkn', null, 'string');
			
			if ($token != JSession::getFormToken()) {
				JFactory::getApplication()->enqueueMessage (JText::_ ('JINVALID_TOKEN'), 'error');
				return $topic;
			}
		
			if ($this->hasCaptcha() && !$this->verifyCaptcha()) {
				return $topic; //$this->showForm ($category, $topic, $user);
			}
			
			if (!$category->exists()) {
				return $topic;
			}
			
			$params = array (
				'name' => $input->get( 'name', $kunenaUser->getName(), 'string' ),
				'email' => $input->get( 'email', null, 'string' ),
				'subject' => $this->getPerson()->fullName,
				'message' => JFactory::getApplication()->input->get('comment', null, 'string')
			);

			if ( !$topic->exists() && ($category->authorise('topic.create')) ) {				
				$safefields = array('category_id' => intval($category->id));
				// 165 = user id van topic owner
				list ($topic, $message) = $category->newTopic($params, 165, $safefields);
				
				// Create a reference after message save
				$indNew = true;
			} else if ($topic->authorise('reply')) {
				$message = $topic->newReply($params);
				$indNew = false;
			} else {
				JFactory::getApplication()->enqueueMessage (JText::_('JT_KUNENADISCUSS_NOREPLY_ALLOWED'), 'warning' );
				return $topic;
			}
			
			if ($topic->authorise('reply')) {
				// save the message
				$message->time = JFactory::getDate()->toUnix();
				if (!$message->save()) {
					JFactory::getApplication()->enqueueMessage ( $message->getError(), 'error' );
					return $topic;
				}
				
				if ($indNew && $topic->id) {
					$this->createReference ($person->app_id, $person->id, $topic->id );
				}
				
				$message->sendNotification();
				JFactory::getApplication()->enqueueMessage (JText::_(($message->hold) 
															? 'JT_KUNENADISCUSS_PENDING_MODERATOR_APPROVAL' 
															: 'JT_KUNENADISCUSS_MESSAGE_POSTED'), 'message' );
			}
		}
		
		return $topic;
	}
	
	protected function createReference($app_id, $person_id, $topic_id) {
		$query = $this->_db->getQuery(true);
		$query->insert( ' #__joaktree_kunena ');
		$query->columns(' app_id, person_id, thread_id ');
		$query->values( (int)$app_id.', '.$this->_db->quote($person_id).', '.(int)$topic_id );
		$this->_db->setQuery( $query );
		$this->_db->query();
		KunenaError::checkDatabaseError ();		
	}
	
	protected function hasCaptcha() {
		$captcha = KunenaSpamRecaptcha::getInstance();
		$result = $captcha->enabled();
		return $result;
	}

	protected function verifyCaptcha() {
		$captcha = KunenaSpamRecaptcha::getInstance();
		$result = $captcha->verify();
		if (!$result) JFactory::getApplication()->enqueueMessage( $captcha->getError() );
		return $result;
	}
	
}
?>	