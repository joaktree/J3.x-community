<?php
/**
 * Joomla! component Joaktree
 * file		table: joaktree_display_settings.php
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

defined('_JEXEC') or die('Restricted access');
jimport('joomla.filter.input');

class TableJoaktree_display_settings extends JTable
{
	var $id 			= null;
	var $code			= null;
	var $level 			= null;
	var $ordering		= null;
	var $published		= null;
	var $access 		= null;
	var $accessLiving	= null;
	var $altLiving		= null;
	var $domain			= null;
	var $secondary		= null;
	
	public function __construct( &$db) {
		parent::__construct('#__joaktree_display_settings', 'id', $db);
	}
	
	public function check() {
		if (!empty($this->id)) {
			// we are updating an existing record - check whether it is used
			$query = $this->_db->getQuery(true);
			$query->select(' code ');
			$query->from(  ' #__joaktree_display_settings ');
	        $query->where( ' id = '.(int) $this->id);
	
	        $this->_db->setQuery($query);
	        $code = $this->_db->loadResult();
	        
	        if ($code == $this->code) {
	        	// nothing changed
        		return true;
	        } else {
				// check whether code was already used			
				switch ($this->level) {
					case 'name'		: 	$return = $this->check_personnames($code);
								  		break;
					case 'person' 	:	$return = $this->check_personevents($code);
								  		break;
					case 'person' 	:	$return = $this->check_relationevents($code);
								  		break;
					default			:	$return = false;
										break;							  		
				}
	        }
			
		} else {
			// we are adding a new record - check to seen that code is unique
			$return = $this->check_unique_keys();
		}
		
		return $return;
	}
	
	private function check_personnames($code) {
		$query = $this->_db->getQuery(true);
		$query->select(' COUNT(code) ');
		$query->from(  ' #__joaktree_person_names ');
        $query->where( ' code = '.$this->_db->quote($code).' ' );

        $this->_db->setQuery($query);
        $count = $this->_db->loadResult();
        
        return ((int)$count == 0) ? true : false;
	}
	
	private function check_personevents($code) {
		$query = $this->_db->getQuery(true);
		$query->select(' COUNT(code) ');
		$query->from(  ' #__joaktree_person_events ');
        $query->where( ' code = '.$this->_db->quote($code).' ' );

        $this->_db->setQuery($query);
        $count = $this->_db->loadResult();
        
        return ((int)$count == 0) ? true : false;
	}
	
	private function check_relationevents($code) {
		$query = $this->_db->getQuery(true);
		$query->select(' COUNT(code) ');
		$query->from(  ' #__joaktree_relation_events ');
        $query->where( ' code = '.$this->_db->quote($code).' ' );

        $this->_db->setQuery($query);
        $count = $this->_db->loadResult();
        
        return ((int)$count == 0) ? true : false;
	}
	
	private function check_unique_keys() {
		$query = $this->_db->getQuery(true);
		$query->select(' COUNT(code) ');
		$query->from(  ' #__joaktree_display_settings ');
        $query->where( ' code  = '.$this->_db->quote($this->code).' ' );
        $query->where( ' level = '.$this->_db->quote($this->level).' ' );
        
        $this->_db->setQuery($query);
        $count = $this->_db->loadResult();
        
        return ((int)$count == 0) ? true : false;
	}
}
?>