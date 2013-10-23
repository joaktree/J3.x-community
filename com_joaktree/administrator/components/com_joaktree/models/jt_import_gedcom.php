<?php
/**
 * Joomla! component Joaktree
 * file		jt_import_gedcom model - jt_import_gedcom.php
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

// no direct access
defined('_JEXEC') or die('Restricted access');

JLoader::import('components.com_joaktree.tables.JMFPKtable', JPATH_ADMINISTRATOR);
JLoader::import('components.com_joaktree.helpers.jt_gedcomimport2', JPATH_ADMINISTRATOR);

JTable::addIncludePath(JPATH_COMPONENT.DS.'tables');
JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joaktree'.DS.'models');

// Import Joomla! libraries
jimport('joomla.application.component.modellist');

class JoaktreeModelJt_import_gedcom extends JModelLegacy {

	var $_data;
	var $_pagination 	= null;
	var $_total         = null;

	function __construct() {
		parent::__construct();	

		$this->jt_registry	= & JTable::getInstance('joaktree_registry_items', 'Table');
	}

	private function _buildQuery() {
		// Get the WHERE and ORDER BY clauses for the query
		$wheres      =  $this->_buildContentWhere();
		
		if (($wheres) &&(is_array($wheres))) {
			$query = $this->_db->getQuery(true);
			$query->select(' japp.* ');
			$query->from(  ' #__joaktree_applications  japp ');
			foreach ($wheres as $where) {
				$query->where(' '.$where.' ');
			}
			$query->order(' japp.id ');
				
		} else {
			// if there is no where statement, there are no applications selected.
			unset($query);
		}
				
		return $query;
	}

	private function _buildContentWhere() {
		$wheres = array();
		
		$procObject = jt_gedcomimport2::getProcessObject();
		$cids = $procObject->japp_ids;
		array_unshift($cids, $procObject->id);
				
		if (count($cids) == 0) {
			// no applications are selected
			return false;
			
		} else {
			// make sure the input consists of integers
			for($i=0;$i<count($cids);$i++) {
				$cids[$i] = (int) $cids[$i];
				
				if ($cids[$i] == 0) {
					die('wrong request');
				}
			}
			
			// create a string
			$japp_ids = '('.implode(",", $cids).')';
			
			// create where
			$wheres[] = 'japp.id IN '.$japp_ids;
			
		}
								
		return $wheres;
	}
	
	public function getData() {
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
                     $query = $this->_buildQuery();
                     $this->_data = $this->_getList( $query );
		}
		
		return $this->_data;
	}
	
	/* 
	** function for processing the gedcom file
	*/
	public function initialize() {
		$cids = JFactory::getApplication()->input->get( 'cid', null, 'array' );
				
		// make sure the input consists of integers
		for($i=0;$i<count($cids);$i++) {
			$cids[$i] = (int) $cids[$i];
			
			if ($cids[$i] == 0) {
				die('wrong request');
			}
		}
		
		// store first empty object
		jt_gedcomimport2::initObject ($cids);
	}
		
	public function getGedcom() {
		return jt_gedcomimport2::getGedcom();
	}
}
?>