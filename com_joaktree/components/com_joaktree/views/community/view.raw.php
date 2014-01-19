<?php
/**
 * Joomla! component Joaktree
 * file		view joaktree community - view.raw.php
 *
 * @version	1.5.0
 * @author	Niels van Dantzig
 * @package	Joomla
 * @subpackage	Joaktree
 * @license	GNU/GPL
 *
 * Component for genealogy in Joomla!
 *
 * This component file was created using the Joomla Component Creator by Not Web Design
 * http://www.notwebdesign.com/joomla_component_creator/
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// import component and kunena libraries
jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Joaktree component
 */
class JoaktreeViewCommunity extends JViewLegacy {
	function display($tpl = null) {
	
		// Get data from the model
		$this->topic	= $this->get( 'topic' );
	
		$this->form		= $this->get( 'form' );
		$this->person	= $this->get( 'person' );  
		$this->user 	= KunenaFactory::getUser();
		$this->config 	= KunenaFactory::getConfig();
		$this->applicationMessages	= JFactory::getApplication()->getMessageQueue();
		
		if (is_object($this->topic) && $this->topic->exists()) { 
			$this->category = $this->topic->getCategory();
		} else {
			$this->category = KunenaForumCategoryHelper::get($this->person->kunenacatid);;
		}
			
		$this->lists	= array( 'technology' => $this->get('technology')
							   , 'canpost' => (is_object($this->topic)) 
							   					? $this->canPost($this->category, $this->topic)
							   					: false
							   , 'hasCaptcha' => KunenaSpamRecaptcha::getInstance()->enabled()
							   );
							   
		if (is_object($this->topic) && $this->topic->exists()) { 
			$this->messages = $this->showTopic($this->category, $this->topic);
		}
		parent::display($tpl);
	}
	
	protected function showTopic(KunenaForumCategory $category, KunenaForumTopic $topic) {
		$ordering = 1; //$this->params->get ( 'ordering', 1 ); // 0=ASC, 1=DESC
		$params = array(
			'catid' => $category->id,
			'id' => $topic->id,
			'limitstart' => (int)!$ordering,
			'limit' => 15, //$this->params->get ( 'limit', 25 ),
			'filter_order_Dir' => $ordering ? 'desc' : 'asc',
			'templatepath' => __DIR__ . '/tmpl/topic'
		);
		ob_start();
		KunenaForum::display('topic', 'default', null, $params);
		$str = ob_get_contents();
		ob_end_clean();
		
		return $str;
	}
	
	/**
	 * @param KunenaForumCategory $category
	 * @param KunenaForumTopic    $topic
	 *
	 * @return bool
	 */
	protected function canPost(KunenaForumCategory $category, $topic) {
		if (is_object($this->topic) && $this->topic->exists()) {
			return $topic->authorise('reply');
		} else {
			return $category->authorise('topic.reply');
		}
	}
		
	
}
?>