<?php
/**
 * Joomla! component Joaktree
 * file		jt_domainvalues modelList - jt_domainvalues.php
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

//JTable::addIncludePath(JPATH_COMPONENT.DS.'tables');
//JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joaktree'.DS.'models');

// Import Joomla! libraries
//jimport('legacy.model.list');

class JoaktreeModelJt_domainvalues extends JModelList {

	var $_data;

	private function _buildQuery() {
		$query = $this->_db->getQuery(true);
		
		$query->select(' jds.code    AS display_code ');
		$query->select(' jds.level   AS display_level ');
		$query->select(' jds.id      AS display_id ');
		$query->select(' jds.domain  AS indDomain ');
		$query->from(  ' #__joaktree_display_settings  jds ');
		$query->select(' jed.id ');
		$query->select(' jed.code ');
		$query->select(' jed.level ');
		$query->select(' jed.value ');
		$query->leftJoin( ' #__joaktree_event_domains  jed '
						. ' ON ( jed.display_id = jds.id ) '
						);
				
		// WHERE, GROUP BY and ORDER BY clauses for the query
		$wheres     =  $this->_buildContentWhere();
		foreach ($wheres as $where) {
			$query->where(' '.$where.' ');
		}

		$query->order(' '.$this->_buildContentOrderBy().' ');
		
		return $query;			
	}

	private function _buildContentWhere() {
		$where 	= array();

		$id 	= JFactory::getApplication()->input->get('display', array(), 'int');
		$where[] = 'jds.id = '.(int)$id.' ';
		
		return $where;
	}

	private function _buildContentOrderBy() {
		$orderby 	= ' jed.value ';
		return $orderby;
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
}
?>