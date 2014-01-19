<?php
/**
 * Joomla! component Joaktree
 * file		view joaktree jt_domainvalues - view.html.php
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
jimport( 'joomla.html.html.select' );

/**
 * HTML View class for the Joaktree component
 */
class JoaktreeViewJt_domainvalues extends JViewLegacy {
	function display($tpl = null) {
		// Get data from the model
		$this->items		= $this->get( 'Data' );
		
		if (is_array($this->items)) {
			$this->firstItem 	= array_shift($this->items);
			$this->display_id	= (is_object($this->firstItem)) ? $this->firstItem->display_id : 0;
			$this->code   		= (is_object($this->firstItem)) ? $this->firstItem->display_code : '' ;
			$this->level   		= (is_object($this->firstItem)) ? $this->firstItem->display_level : '' ;
			$this->indDomain	= (is_object($this->firstItem)) ? $this->firstItem->indDomain : false;
			array_unshift($this->items, $this->firstItem);
		}
		
		$this->addToolbar();
		parent::display($tpl);			
	}
	
	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		$canDo	= JoaktreeHelper::getActions();
		JToolBarHelper::title(JText::sprintf('JTSETTINGS_DOMAINVALUES', JText::_($this->code) ), 'location' );

		if ($this->indDomain) {
			if ($canDo->get('core.create')) {
				JToolBarHelper::addNew('domain.add');
			}
			
			if ($canDo->get('core.edit')) {
				JToolBarHelper::editList('domain.edit');
			}
	
			if ($canDo->get('core.delete')) {
				JToolBarHelper::deleteList('JT_CONFIRMDELETE', 'domain.delete');
			}
		}
				
		JToolBarHelper::cancel('domain.close', 'JTOOLBAR_CLOSE');
	}	
}
?>