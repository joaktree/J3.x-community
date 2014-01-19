<?php
/**
 * Joomla! component Joaktree
 * file		jt_domainvalue modelAdmin - jt_domainvalue.php
 *
 * @version	1.5.0
 * @author	Niels van Dantzig
 * @package	Joomla
 * @subpackage	Joaktree
 * @license	GNU/GPL
 *
 * Component for genealogy in Joomla!
 *
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

class JoaktreeModelJt_domainvalue extends JModelAdmin
{	
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'joaktree_event_domains', $prefix = 'Table', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
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
		$form = $this->loadForm('com_joaktree.domain', 'jt_domainvalue', array('control' => 'jform', 'load_data' => $loadData));
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
	protected function loadFormData() {		
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_joaktree.edit.domain.data', array());

		if (empty($data)) {
			$data = $this->getItem();			
		}

		return $data;
	}
	
	public function getItem($pk = null)	{
		static $item;
		
		if (!empty($item)) {
			return $item;
		}
		
		$pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
		
		if ($pk > 0) {
			$item = parent::getItem($pk);
		} else {
			$display_id = JFactory::getApplication()->input->get('display', null, 'int');
			
			if ((int) $display_id > 0){ 
				$query = $this->_db->getQuery(true);
			
				$query->select(' jds.id   AS display_id ');
				$query->select(' jds.code ');
				$query->select(' jds.level ');
				$query->from(  ' #__joaktree_display_settings jds ');
				$query->where( ' jds.id = '.(int) $display_id.' ');
		
				$this->_db->setQuery($query);
				$item = $this->_db->loadObject();
				$item->id		= null;
				$item->value	= null;
			}     
		} 
		
		return (is_object($item)) ? $item : false;
	}
	
	public function save($form) {
		$canDo	= JoaktreeHelper::getActions();
		
		if ($canDo->get('core.create') || $canDo->get('core.edit')) {
			
			if (!parent::save($form)) {
				return false;
			} 
		} else {
			return false;
		}
		
		return true;
	}
	
	public function delete($cids) {
		$table = $this->getTable();
		
		foreach ($cids as $cid) {
			if (!$table->load($cid)) {
				return false;
			}
				
			if (!$table->check()) {
				return false;
			}
			
			if (!$table->delete($cid)) {
				return false;						
			}
		}
		
		return true;
	}
}
