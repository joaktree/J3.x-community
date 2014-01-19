<?php
/**
 * Joomla! component Joaktree
 * file		front end community controller - communityy.php
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

class JoaktreeControllerCommunity extends JoaktreeController {
	function __construct() {
		// first check token
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
	
		// create an input object
		$this->input = JFactory::getApplication()->input;

		//Get View
		if($this->input->get('view') == '') {
			$this->input->set('view', 'joaktree');
		}

		parent::__construct();
	}
	
	
	function save() {
		
//		$model = $this->getModel('mygenealogy');
//		
//		$form   = $this->input->get('jform', null, 'array');		
//		$ids = $model->save($form);
//		$msg = ($ids) ? JText::_('JT_SAVED') : JText::_('JT_NOTSAVED');		
//
//		$link =  'index.php?option=com_joaktree';
//		if (($form['wizard'] == 1) && ($ids) && ($form['indStartType'] == 0)) {
//			// Add first person
//			$link .= '&view=personform'
//				 		.'&appId='.$ids['app']
//				 		.'&treeId='.$ids['tree'];
//				 		
//		} else if (($form['wizard'] == 1) && ($ids) && ($form['indStartType'] == 1)) {
//			// load GedCom
//			$model->initObject($ids['app']);
//			$msg 	= null;
//			$link .= '&view=mygenealogy'
//				 		.'&layout=wizard02';	
//			
//		} else if (($form['wizard'] == 1) && ($ids) && ($form['indStartType'] == 2)) {
//			// Add first person
//			$link .= '&view=joaktreestart'
//				 		.'&appId='.$ids['app']
//				 		.'&treeId='.$ids['tree'];
//		} else {
//			$link .= '&view=mygenealogy';		
//		}
												
		$this->setRedirect(Jroute::_($link), $msg);
	}
		
}
?>