<?php
/**
 * Joomla! component Joaktree
 * file		administrator gedcomimport view - view.raw.php
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

// Import Joomla! libraries
jimport( 'joomla.application.component.view');

JLoader::import('components.com_joaktree.helpers.jt_gedcomimport2', JPATH_ADMINISTRATOR);

class JoaktreeViewGedcomimport extends JViewLegacy {

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{	
		$this->item	= $this->get( 'item' );		
		//$tpl			= 'raw';
		parent::display($tpl);
	}

}
