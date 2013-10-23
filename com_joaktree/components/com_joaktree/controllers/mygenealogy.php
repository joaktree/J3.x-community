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
	
	function delete() {
		$model = $this->getModel('mygenealogy');
		
		$msg = $model->delete();
		
		$link =  'index.php?option=com_joaktree'
						.'&view=mygenealogy';
				
		$this->setRedirect(Jroute::_($link), $msg);
	}
	
//	function edit() {
//		$personId  = $this->input->get('personId', null, 'string');
//		$treeId    = $this->input->get('treeId', null, 'int');
//		$object    = $this->input->get('object', null, 'string');
//		
//		$link =  'index.php?option=com_joaktree'
//						.'&view=personform'
//						.'&tech=a'
//						.'&treeId='.$treeId;
//						
//		switch ($object) {
//			case "names":		$link .= '&layout=form_names'
//										.'&personId='.$personId;
//								break;
//			case "state":		$link .= '&layout=form_state'
//										.'&personId='.$personId;
//								break;
//			case "medialist":	$link .= '&layout=form_medialist'
//										.'&personId='.$personId;
//								break;
//			case "media":		$picture = $this->input->get('picture', null, 'string');
//								$link .= '&layout=form_media'
//										.'&personId='.$personId
//										.'&picture='.$picture;		
//								break;
//			case "notes":		$link .= '&layout=form_notes'
//										.'&personId='.$personId;
//								break;
//			case "references":	$link .= '&layout=form_references'
//										.'&personId='.$personId;
//								break;
//			case "pictures":	$link .= '&layout=form_pictures'
//										.'&personId='.$personId;
//								break;
//			case "parents":		$link .= '&layout=form_parents'
//										.'&personId='.$personId;
//								break;
//			case "partners":	$link .= '&layout=form_partners'
//										.'&personId='.$personId;
//								break;
//			case "partnerevents":	
//								$relationId = $this->input->get('relationId', null, 'string');
//								$link .= '&layout=form_partner_events'
//										.'&personId='.$personId
//										.'&relationId='.$relationId;
//								break;						
//			case "children":	$link .= '&layout=form_children'
//										.'&personId='.$personId;
//								break;
//			case "newparent":	$tmp = explode('!', $personId);
//								$personId = $tmp[0].'!';
//								$relationId = $tmp[1];
//								$link .= '&layout=default'
//										.'&personId='.$personId
//										.'&relationId='.$relationId
//										.'&action=addparent';
//								break;
//			case "newpartner":	$tmp = explode('!', $personId);
//								$personId = $tmp[0].'!';
//								$relationId = $tmp[1];
//								$link .= '&layout=default'
//										.'&personId='.$personId
//										.'&relationId='.$relationId
//										.'&action=addpartner';
//								break;
//			case "newchild":	$tmp = explode('!', $personId);
//								$personId = $tmp[0].'!';
//								$relationId = $tmp[1];
//								$link .= '&layout=default'
//										.'&personId='.$personId
//										.'&relationId='.$relationId
//										.'&action=addchild';
//								break;
//			case "pevents":		// continue
//			default:			$link .= '&layout=default'
//										.'&personId='.$personId;
//								break; 
//      		}
//		
//		
//		$this->setRedirect(Jroute::_($link), $msg);
//	}
	
	function save() {
		$model = $this->getModel('mygenealogy');
		
		$form   = $this->input->get('jform', null, 'array');
		
		$ids = $model->save($form);
		$msg = ($ids) ? JText::_('JT_SAVED') : JText::_('JT_NOTSAVED');		

		$link =  'index.php?option=com_joaktree';
		if (($form['wizard'] == 1) && ($ids) && ($form['indGedCom'] == 1)) {
			// load GedCom
			$model->initObject($ids['app']);
			$msg 	= null;
			$link .= '&view=mygenealogy'
				 		.'&layout=wizard02';	
			
		} else if (($form['wizard'] == 1) && ($ids) && ($form['indGedCom'] == 0)) {
			// Add first person
			$link .= '&view=personform'
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