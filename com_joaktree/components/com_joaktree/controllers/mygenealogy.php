<?php
/**
 * Joomla! component Joaktree
 * file		front end mygenealogy controller - mygenealogy.php
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

class JoaktreeControllerMygenealogy extends JoaktreeController {
	function __construct() {
		// first check token
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		// create an input object
		$this->input = JFactory::getApplication()->input;

		//Get View
		if($this->input->get('view') == '') {
			$this->input->set('view', 'mygenealogy');
		}
		
		parent::__construct();
	}
	
	function navigate() {
		$form   = $this->input->get('jform', null, 'array');
		
		if ($form['wizard'] == 12) {
			$link =  'index.php?option=com_joaktree'
							.'&view=joaktree'
							.'&tech='.$form['tech']
							.'&Itemid='.JoaktreeHelper::getMenuId($form['tree_id'], 'joaktree')
							.'&treeId='.$form['tree_id']
							.'&personId='.$form['person_id'];
									
			$this->setRedirect(Jroute::_($link));		
		}
		
	}
	
	function edit_me() {
		$form   = $this->input->get('jform', null, 'array');
		
		if (($form['wizard'] == 11) || ($form['wizard'] == 12)) {
			$link =  'index.php?option=com_joaktree'
							.'&view=mygenealogy'
							.'&layout=wizard03';
									
			$this->setRedirect(Jroute::_($link));		
		}
	}
	
	function edit_tree() {
		$form   = $this->input->get('jform', null, 'array');
		
		if (($form['wizard'] == 11) || ($form['wizard'] == 15)) {
			$link =  'index.php?option=com_joaktree'
							.'&view=mygenealogy'
							.'&layout=wizard01';
									
			$this->setRedirect(Jroute::_($link));		
		}
	}
	
	function delete() {
		$model = $this->getModel('mygenealogy');
		
		$msg = $model->delete();
		
		$link =  'index.php?option=com_joaktree'
						.'&view=mygenealogy';
				
		$this->setRedirect(Jroute::_($link), $msg);
	}
	
	function save() {
		$model = $this->getModel('mygenealogy');
		
		$form   = $this->input->get('jform', null, 'array');		
		$ids = $model->save($form);
		$msg = ($ids) ? JText::_('JT_SAVED') : JText::_('JT_NOTSAVED');		

		$link =  'index.php?option=com_joaktree';
		if (($form['wizard'] == 1) && ($ids) && ($form['indStartType'] == 0)) {
			// Add first person
			$link .= '&view=personform'
				 		.'&appId='.$ids['app']
				 		.'&treeId='.$ids['tree'];
				 		
		} else if (($form['wizard'] == 1) && ($ids) && ($form['indStartType'] == 1)) {
			// load GedCom
			$model->initObject($ids['app']);
			$msg 	= null;
			$link .= '&view=mygenealogy'
				 		.'&layout=wizard02';	
			
		} else if (($form['wizard'] == 1) && ($ids) && ($form['indStartType'] == 2)) {
			// Add first person
			$link .= '&view=joaktreestart'
				 		.'&appId='.$ids['app']
				 		.'&treeId='.$ids['tree'];
		} else {
			$link .= '&view=mygenealogy';		
		}
												
		$this->setRedirect(Jroute::_($link), $msg);
	}
		
	function cancel() {
		$link =  'index.php?option=com_joaktree'
						.'&view=mygenealogy';
												
		$this->setRedirect(Jroute::_($link), $msg);
	}
}
?>