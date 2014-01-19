<?php
/**
 * Joomla! component Joaktree
 * file		view my genealogy - view.html.php
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

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the Joaktree component
 */
class JoaktreeViewMygenealogy extends JViewLegacy {
	function display($tpl = null) {


		// Do we have a user? 
		$this->user = JFactory::getUser();
		$input 		= JFactory::getApplication()->input;
		
		if ($this->user->id) {
			$this->form		= $this->get('Form');		
			$this->item		= $this->get('Item');

			// get params
			if (is_object($this->item) && !empty($this->item->papp_id) && !empty($this->item->person_id)) {
				$input->set('personId', (int) $this->item->papp_id.'!'.$this->item->person_id);
				$this->params	= JoaktreeHelper::getJTParams(); 
			} else if (is_object($this->item) && !empty($this->item->app_id) && !empty($this->item->tree_id)) {
				$input->set('appId',  (int) $this->item->app_id); 
				$input->set('treeId', (int) $this->item->tree_id);
				$this->params	= JoaktreeHelper::getJTParams();
			} else {
				$this->params	= JFactory::getApplication('site')->getParams();
				$this->params->merge(JoaktreeHelper::getTheme(false, true));
			}
			
			// read some values from params
			$this->lists['community']	= $this->params->get('indCommunity', 0);			

			// get person
			if (is_object($this->item) && !empty($this->item->papp_id) && !empty($this->item->person_id)) {
				$this->person 	=  new Person(array( 'app_id' 	=> $this->item->papp_id
												   , 'tree_id' 	=> null
												   , 'person_id'	=> $this->item->person_id
												   ));
			} else {
				$this->person	= null; 				
			}
			
			// get can do - depending on community level 
			switch($this->lists['community']) {
				case 2: 	// continue
				case 1: 	if (is_object($this->item) && !empty($this->item->app_id) && !empty($this->item->tree_id)) {
								$this->canDo 	= JoaktreeHelper::getActions();
							}
							break;
					
				case 0: 	// continue
				default:	if (is_object($this->person) && !empty($this->person->tree_id)) {
								$input->set('treeId', (int) $this->person->tree_id);
								$this->canDo 	= JoaktreeHelper::getActions();
							}
			}
			if (!isset($this->canDo)) { $this->canDo = JoaktreeHelper::getActions('component'); }
			
			$this->lists['access']		= true;
			$this->lists['technology'] 	= $this->get( 'technology' );
		} else {
			$this->params			= JoaktreeHelper::getTheme(false, true);
			$this->canDo 			= null;
			$this->lists['access']	= false;
		}

		// set up style sheets and javascript files
		JHTML::stylesheet( JoaktreeHelper::joaktreecss() );
		JHTML::stylesheet( JoaktreeHelper::joaktreecss($this->params->get('theme')) );
		JHTML::stylesheet( 'components/com_joaktree/assets/css/uploadManager.css' );

		// copyright
		$this->lists[ 'CR' ]			= JoaktreeHelper::getJoaktreeCR();
				
		parent::display($tpl);
	}	
}
?>