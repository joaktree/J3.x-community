<?php
/**
 * Joomla! component Joaktree
 * file		view jt_domainvalue - view.html.php
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
class JoaktreeViewJt_domainvalue extends JViewLegacy {

	protected $form;
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{	
		JHTML::stylesheet( JoaktreeHelper::joaktreecss() );
		JHTML::script( JoaktreeHelper::jsfile() );		
		
		// Initialiase variables.
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');		
		$this->state	= $this->get('State');		
		$this->level 	= JFactory::getApplication()->input->get( 'level', 'person');
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));		
			return false;
		}

		$this->canDo	= JoaktreeHelper::getActions();
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
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$isNew		= (!is_object($this->item) || ((is_object($this->item)) && (!$this->item->id)) );
		$code		= (is_object($this->item)) ? $this->item->code : '';

		JToolBarHelper::title($isNew ? JText::sprintf('JTSETTINGS_DOMAIN_CREATE', JText::_($code)) 
									 : JText::sprintf('JTSETTINGS_DOMAIN_EDIT', JText::_($code))
							 , 'location'
							 );

		// If not checked out, can save the item.
		if ($this->canDo->get('core.edit')) {
			JToolBarHelper::save('domain.save', 'JTOOLBAR_SAVE');
		}

		JToolBarHelper::cancel('domain.cancel','JTOOLBAR_CANCEL');
	}
	
}