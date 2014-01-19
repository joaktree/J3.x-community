<?php
/**
 * Joomla! component Joaktree
 * file		front end linkedpersons controller - linkedpersons.php
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

class JoaktreeControllerLinkedpersons extends JoaktreeController {
	function __construct() {
		// first check token
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		
		// create an input object
		$this->input = JFactory::getApplication()->input;

		//Get View
		if($this->input->get('view') == '') {
			$this->input->set('view', 'linkedpersons');
		}
		
		parent::__construct();
	}
	
	function delete() {
		$model	= $this->getModel('linkedpersons');
		$cid	= $this->input->get('cid', null, 'string');
		$treeId	= $this->input->get('treeId', null, 'int');
		$appId	= $this->input->get('appId', null, 'int');
		$msg 	= $model->delete($cid);
		
		$link =  'index.php?option=com_joaktree'
						.'&view=linkedpersons'
						.'&treeId='.(int)$treeId
						.'&appId='.(int)$appId;
				
		$this->setRedirect(Jroute::_($link), $msg);
	}
				
	function save() {
		$model	= $this->getModel('linkedpersons');
		$cid1	= $this->input->get('cid', null, 'string');
		$cid2	= $this->input->get('mygencid', null, 'string');
		$treeId	= $this->input->get('treeId', null, 'int');
		$appId	= $this->input->get('appId', null, 'int');
		$msg 	= $model->save($cid1, $cid2);
		
		$link =  'index.php?option=com_joaktree'
						.'&view=linkedpersons'
						.'&treeId='.(int)$treeId
						.'&appId='.(int)$appId;
				
		$this->setRedirect(Jroute::_($link), $msg);
	}
	
	function cancel() {
		$treeId	= $this->input->get('treeId', null, 'int');
		$appId	= $this->input->get('appId', null, 'int');
		$link =  'index.php?option=com_joaktree'
						.'&view=linkedpersons'
						.'&treeId='.(int)$treeId
						.'&appId='.(int)$appId;
												
		$this->setRedirect(Jroute::_($link), $msg);
	}
}
?>