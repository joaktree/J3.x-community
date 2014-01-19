<?php
/**
 * Joomla! component Joaktree
 * file		front end linkedpersons model - linkedpersons.php
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
jimport('joomla.application.component.model');

// import component libraries
JLoader::import('helper.tree', JPATH_COMPONENT);

class JoaktreeModelLinkedpersons extends JModelLegacy {

	function __construct() {
		parent::__construct();
		
		$app 		= JFactory::getApplication('site');
		
		$context			= 'com_joaktree.linkedpersons.list.';
		// Get the pagination request variables
		$limit		= $app->getUserStateFromRequest( 'global.list.limit', 'limit', $app->getCfg('list_limit'), 'int' );

		$limitstart	= $app->getUserStateFromRequest( $context.'limitstart',	'limitstart',	0, 'int' );
		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	public function getUserAccess() {
		return JoaktreeHelper::getUserAccess();
	}
	
	public function getApplicationId() {
		return JoaktreeHelper::getApplicationId();
	}

	public function getTreeId() {
		return JoaktreeHelper::getTreeId();
	}

	public function getTechnology() {
		return JoaktreeHelper::getTechnology();
	}

	private function _buildQuery()
	{
		$displayAccess	= JoaktreeHelper::getDisplayAccess();
		
		$query = $this->_db->getQuery(true);
		
		// select from persons
		$query->select(' jpn.id ');
		$query->select(' jpn.app_id ');
		$query->select(' '.JoaktreeHelper::getConcatenatedFullName().' AS name ');
		$query->select(' MIN( '.JoaktreeHelper::getSelectBirthYear().' ) AS birthDate ');
		$query->select(' MIN( '.JoaktreeHelper::getSelectDeathYear().' ) AS deathDate ');
		$query->from(  ' #__joaktree_persons jpn ');

		// select from linked persons
		$query->leftJoin( ' #__joaktree_person_links  jpl ' 
						 .' ON (   jpl.app_id    = jpn.app_id '
						 .'    AND jpl.person_id = jpn.id '
						 .'    ) '
						 );
		$query->select(' jpn2.id     AS id_c ');
		$query->select(' jpn2.app_id AS app_id_c ');				
		$query->select(' MIN( IF( (jan2.living = true AND '.$displayAccess['BIRTperson']->living.' = 1 ) '
					 .'         , '.$this->_db->Quote( JText::_('JT_ALTERNATIVE') ).' '
					 .'         , SUBSTR( RTRIM(birth2.eventDate), -4 ) '
					 .'         ) ) AS birthDate_c ');
		$query->select(' MIN( IF( (jan2.living = true AND '.$displayAccess['DEATperson']->living.' = 1 ) '
					 .'         , '.$this->_db->Quote( JText::_('JT_ALTERNATIVE') ).' '
					 .'         , SUBSTR( RTRIM(death2.eventDate), -4 ) '
					 .'         ) ) AS deathDate_c ');
		
		$query->select(' '.$query->concatenate(array( 'jpn2.firstName'
											  		, 'jpn2.namePreposition'
											  		, 'jpn2.familyName'
											  		)
											   , ' '
											   ).' AS name_c ');
		$query->leftJoin(' #__joaktree_persons   jpn2 ' 
						 .' ON (   jpn2.app_id   = jpl.app_id_c '
						 .'    AND jpn2.id       = jpl.person_id_c '
						 .'    ) '
						 );
						 
		// select from admin persons
		$query->innerJoin(JoaktreeHelper::getJoinAdminPersons(false));
		$query->leftJoin( ' #__joaktree_admin_persons jan2 '
						 .' ON (   jan2.app_id    = jpn2.app_id '
						 .'    AND jan2.id        = jpn2.id '
						 .'    AND jan2.published = true '
						 .'    AND (  (jan2.living = false AND '.$displayAccess['NAMEname']->notLiving.' = 2 ) '
						 .'        OR (jan2.living = true  AND '.$displayAccess['NAMEname']->living.'    = 2 ) '
						 .'        ) '
						 .'    ) ');
		
		// select from birth and death
		$query->leftJoin(JoaktreeHelper::getJoinBirth());
		$query->leftJoin(JoaktreeHelper::getJoinDeath());
		$query->leftJoin(' #__joaktree_person_events birth2 '
						.' ON (   birth2.app_id    = jpn2.app_id '
						.'    AND birth2.person_id = jpn2.id '
						.'    AND birth2.code      = '.$this->_db->Quote('BIRT').' '
						.'    AND (  (jan2.living  = false AND '.$displayAccess['BIRTperson']->notLiving.' > 0 ) '
						.'        OR (jan2.living  = true  AND '.$displayAccess['BIRTperson']->living.'    > 0 ) '
						.'        ) '
						.'    )');
		$query->leftJoin(' #__joaktree_person_events death2 '
						.' ON (   death2.app_id    = jpn2.app_id '
						.'    AND death2.person_id = jpn2.id '
						.'    AND death2.code      = '.$this->_db->Quote('DEAT').' '
						.'    AND (  (jan2.living  = false AND '.$displayAccess['DEATperson']->notLiving.' > 0 ) '
						.'        OR (jan2.living  = true  AND '.$displayAccess['DEATperson']->living.'    > 0 ) '
						.'        ) '
						.'    ) ');
		
		// Get the WHERE, GROUP BY and ORDER BY clauses for the query
		$wheres      	= $this->_buildContentWhere();
		foreach ($wheres as $where) {
			$query->where(' '.$where.' ');
		}
		$query->group(' jpn.id ');
		$query->group(' jpn.app_id ');
		$query->group(' jpn.firstName ');
		$query->group(' jpn.namePreposition ');
		$query->group(' jpn.familyName ');
		$query->group(' jpn2.id ');
		$query->group(' jpn2.app_id ');
		$query->group(' jpn2.firstName ');
		$query->group(' jpn2.namePreposition ');
		$query->group(' jpn2.familyName ');
		
		$query->order(' jpn.familyName ');
		$query->order(' jpn.firstName ');
		
			
		return $query;
	}

	private function _buildContentWhere()
	{
		$app 		= JFactory::getApplication('site');
		$appId    	= intval( $this->getApplicationId() );
		$context	= 'com_joaktree.linkedpersons.list.';

		$search1	= $app->getUserStateFromRequest( $context.'search1',	'search1',	'',	'string' );
		$search1	= $this->_db->escape( $search1, true );
		$search1	= JString::strtolower( $search1 );

		$where = array();

		if ($appId) {
			$where[] = 'jpn.app_id = ' . $appId;
		}
		
		if ($search1) {
			$where[] = '(  LOWER(jpn.firstName) LIKE '.$this->_db->Quote('%'.$search1.'%')
					  .'OR LOWER(jpn.familyName) LIKE '.$this->_db->Quote('%'.$search1.'%') 
					  .') ' ;
		}

		return $where;
	}

	public function getPersonlist() {
		// Lets load the content if it doesn't already exist
		if (empty($this->_personlist)) {
			$query = $this->_buildQuery();					
			$this->_personlist = $this->_getList( $query, $this->getState('limitstart'), $this->getState('limit'));
		}
		
		return $this->_personlist;
	}

//	public function getPatronymSetting() {
//		static $_patronymSetting;
//		
//		if (!isset($_patronymSetting)) {
//			$params = JoaktreeHelper::getJTParams();
//			$_patronymSetting	= (int) $params->get('patronym');
//		}
//		
//		return $_patronymSetting;
//	}
//
//	public function getFilter3() {
//		return $this->filter3;
//	}


	public function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}
		
		return $this->_total;
	}

	public function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}
		
		return $this->_pagination;
	}
		
	public function getLastUpdate() {
		return JoaktreeHelper::lastUpdateDateTime();	
	}
	
	public function save($cid1, $cid2) {
		$id1 = explode('!', $cid1);
		$id2 = explode('!', $cid2);
		
		$query = $this->_db->getQuery(true);
		$query->insert(' #__joaktree_person_links ');
		$query->set( ' app_id      = '.(int)$id2[0].' ');
		$query->set( ' person_id   = '.$this->_db->Quote($id2[1]).' ');
		$query->set( ' app_id_c    = '.(int)$id1[0].' ');
		$query->set( ' person_id_c = '.$this->_db->Quote($id1[1]).' ');

		$this->_db->setQuery($query);
		try { $this->_db->execute(); }
		catch (RuntimeException $e) {
			$msg = $e->getMessage();
			$this->setError($msg); 
			return $msg; 
		}
		
		return '';
	}
	
	public function delete($cid) {
		$id = explode('!', $cid);
		
		$query = $this->_db->getQuery(true);
		$query->delete(' #__joaktree_person_links ');
		$query->where( ' app_id    = '.(int)$id[0].' ');
		$query->where( ' person_id = '.$this->_db->Quote($id[1]).' ');
		
		$this->_db->setQuery($query);
		try { $this->_db->execute(); }
		catch (RuntimeException $e) {
			$msg = $e->getMessage();
			$this->setError($msg); 
			return $msg; 
		}
		
		return '';
	}
}
?>