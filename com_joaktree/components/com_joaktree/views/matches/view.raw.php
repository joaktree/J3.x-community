<?php
/**
 * Joomla! component Joaktree
 * file		view joaktree matches - view.raw.php
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
class JoaktreeViewMatches extends JViewLegacy {
	function display($tpl = null) {
		
		// Get data from the model
		$this->personlist 			= $this->get('personlist');
		
		$this->lists['technology']	= $this->get('technology');
		$this->lists['tree_id']		= $this->get('treeId');
		
		parent::display($tpl);
	}
}
?>