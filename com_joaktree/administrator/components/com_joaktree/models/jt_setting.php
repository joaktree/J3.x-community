<?php
/**
 * Joomla! component Joaktree
 * file		jt_setting modelAdmin - jt_setting.php
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

class JoaktreeModelJt_setting extends JModelAdmin
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
	public function getTable($type = 'joaktree_display_settings', $prefix = 'Table', $config = array())
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
		$form = $this->loadForm('com_joaktree.setting', 'jt_setting', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_joaktree.edit.setting.data', array());

		if (!empty($data) && is_array($data)) {
			$data = $this->getItem($data['id']);
		} else {		
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
			$query = $this->_db->getQuery(true);
		
			$query->select(' jds.* ');
			$query->from(  ' #__joaktree_display_settings jds ');
			$query->where( ' jds.id = '.(int) $pk.' ');
		
			$query->select(' vll1.title AS access_level ');
			$query->leftJoin(' #__viewlevels vll1 '
							.' ON (vll1.id = jds.access) '
							);
		
			$query->select(' IFNULL( vll2.title, '.$this->_db->quote('None').') AS access_level_living ');
			$query->leftJoin(' #__viewlevels vll2 '
							.' ON (vll2.id = jds.accessLiving) '
							);
							
			$query->select(' IFNULL( vll3.title, '.$this->_db->quote('None').') AS access_level_alttext ');
			$query->leftJoin(' #__viewlevels vll3 '
							.' ON (vll3.id = jds.altLiving) '
							);
			
			$this->_db->setQuery($query);
			$item = $this->_db->loadObject();
			$item->explanation = $this->showExplanation($item);
			$item->domainvalues = $this->getDomainValues($pk);     
			return $item;
		} else {
			return false;
		}
	}
	
	private function getDomainValues($pk = null) {
		$pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
		
		if ($pk > 0) { 
			$query = $this->_db->getQuery(true);
		
			$query->select(' jed.* ');
			$query->from(  ' #__joaktree_event_domains jed ');		
			$query->innerJoin( ' #__joaktree_display_settings jds '
							  .' ON (   jds.id    = '.(int) $pk.' '
							  .'    AND jds.code  = jed.code '
							  .'    AND jds.level = jed.level '
							  .'    ) '
							 );
			
			$this->_db->setQuery($query);
			return $this->_db->loadObjectList();
		} else {
			return array();
		}
	}

	private function showExplanation($item) {
		$html = '';
		$color 		= 'blue';
	
		$value 			= '<strong>'.strtoupper( JText::_( $item->code ) ).'</strong>';
		$txtPerson		= JText::_('JTSETTINGS_EXPTEXT_PERSON').'&nbsp;';
		$personNotLiv	= $txtPerson.'<em>'.JText::_('JTSETTINGS_EXPTEXT_NOT_LIVPERSON').'</em>:&nbsp;';
		$personLiving	= $txtPerson.'<em>'.JText::_('JTSETTINGS_EXPTEXT_LIVPERSON').'</em>:&nbsp;';
		
		if (!$item->published) {
			// nothing is published
			$html .= $value.'&nbsp;'.JText::_('JTSETTINGS_EXPTEXT_FULLYHIDDEN');
		} else {
			// something is published
			$html .= $personNotLiv.$value.'&nbsp;';
			$html .= JText::_('JTSETTINGS_EXPTEXT_ACCESSLEVELS').'&nbsp;';
			$html .= '<span style="color: '.$color.';">'.$item->access_level .'</span>.';
			
			if (  (($item->accessLiving != null) and ($item->accessLiving != 0)) 
			   or (($item->altLiving    != null) and ($item->altLiving    != 0)) 
			   ) {			
			
				if (($item->accessLiving != null) and ($item->accessLiving != 0)) {
					$html .= '<br/>'.$personLiving.$value.'&nbsp;';
					$html .= JText::_('JTSETTINGS_EXPTEXT_ACCESSLEVELS').'&nbsp;';
					$html .= '<span style="color: '.$color.';">'.$item->access_level_living.'</span>';
				}
					
				if (   ($item->altLiving != null) 
				   and ($item->altLiving != 0)
				   and ($item->altLiving != $item->accessLiving)
				   ) {
					$html .= '<br/>'.$personLiving;
					$html .= JText::_('JTSETTINGS_EXPTEXT_ALTTEXT').'&nbsp;';
					$html .= '<span style="color: '.$color.';">'.$item->access_level_alttext .'</span>';		
					
					if (($item->accessLiving != null) and ($item->accessLiving != 0)) {
						$html .= JText::_('JTSETTINGS_EXPTEXT_ALTTEXT2').'&nbsp;';
						$html .= '<span style="color: '.$color.';">'.$item->access_level_living.'</span>';
					}
				}
				
				$html .= '.';
							
			} else {
				$html .= '<br/>'.$personLiving.$value.'&nbsp;';
				$html .= JText::_('JTSETTINGS_EXPTEXT_FULLYHIDDEN');
			}
		}
		
		return $html;
	}
	
		
	public function save($form) {
		$canDo	= JoaktreeHelper::getActions();
		
		if ($canDo->get('core.create') || $canDo->get('core.edit')) {
			
			if (parent::save($form)) {
				switch ($form['level']) {
					case 'name'		: 	$layout = 'personname';
										break;
					case 'person'	: 	$layout = 'personevent';
										break;
					case 'relation'	: 	$layout = 'relationevent';
										break;
					default			: 	$layout = 'personevent';
										break;
				}
				return $layout;
			} else {
				return false;
			}
		} else {
			return false;
		}
		
		return false;
	}
	
	public function delete($cids) {
		$table = $this->getTable();
		
		foreach ($cids as $cid) {
			if ($table->load($cid)) {
				switch ($table->level) {
					case 'name'		: 	$layout = 'personname';
										break;
					case 'person'	: 	$layout = 'personevent';
										break;
					case 'relation'	: 	$layout = 'relationevent';
										break;
					default			: 	$layout = 'personevent';
										break;
				}
				
				if ($table->check()) {
					$table->delete($cid);
				}
			}
		}
		
		return (isset($layout)) ? $layout : 'personevent';
	}
}
