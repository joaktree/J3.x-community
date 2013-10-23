<?php
/**
 * Joomla! component Joaktree
 * file		front end gedcomimport model - gedcomimport.php
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
jimport('joomla.application.component.modelform');

// import component libraries
JLoader::import('components.com_joaktree.tables.JMFPKtable', JPATH_ADMINISTRATOR);
JLoader::import('components.com_joaktree.helpers.jt_gedcomimport2', JPATH_ADMINISTRATOR);

class JoaktreeModelGedcomimport extends JModelForm {
	
	function __construct() {
		//load the administrator language file
		$lang 	= JFactory::getLanguage();
		$lang->load('com_joaktree', JPATH_ADMINISTRATOR);
		parent::__construct();            		
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
		$form = $this->loadForm('com_joaktree.jt_import_gedcom', 'jt_import_gedcom', array('control' => 'jform', 'load_data' => $loadData));
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
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_joaktree.edit.jt_import_gedcom.data', array());

		if (empty($data)) {
			$data = $this->getItem();		
		}

		return $data;
	}
		
	public function getItem() {
		return jt_gedcomimport2::getGedcom();
	}
}
