<?php
/**
 * Joomla! component Joaktree
 * file		front end matches model - matches.php
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

class JoaktreeModelMatches extends JModelLegacy {

	function __construct() {
		parent::__construct();
	}

	public function getUserAccess() {
		return JoaktreeHelper::getUserAccess();
	}
	
	public function getPersonId() {
		return JoaktreeHelper::getPersonId();
	}
	
	public function getApplicationId() {
		return JoaktreeHelper::getApplicationId();
	}

	public function getTreeId() {
		return 1;
		//return JoaktreeHelper::getTreeId();
	}

	public function getTechnology() {
		return JoaktreeHelper::getTechnology();
	}

	private function _buildQuery()
	{		
		$query 			= $this->_db->getQuery(true);
		$MyGenAppId 	= (int) $this->getApplicationId();
		$MyGenPersonId	= $this->getPersonId();
		
		// select from persons
		$query->select(' jpn.id ');
		$query->select(' jpn.app_id ');
		$query->select(' '.JoaktreeHelper::getConcatenatedFullName().' AS name ');
		$query->select(' MIN( '.JoaktreeHelper::getSelectBirthYear().' ) AS birthDate ');
		$query->select(' MIN( '.JoaktreeHelper::getSelectDeathYear().' ) AS deathDate ');
		$query->from(  ' #__joaktree_persons jpn ');

		// select from admin persons
		$query->innerJoin(JoaktreeHelper::getJoinAdminPersons(false));
								 
		// select from birth and death
		$query->leftJoin(JoaktreeHelper::getJoinBirth());
		$query->leftJoin(JoaktreeHelper::getJoinDeath());
		
		// select the person to compare with
		$query->select( $this->_db->Quote($MyGenAppId.'!'.$MyGenPersonId).' AS MyGenPersonId ' );
		$query->innerJoin(' #__joaktree_persons jpn2 '
						 .' ON (   jpn2.app_id = '.$MyGenAppId.' '
						 .'    AND jpn2.id     = '.$this->_db->Quote($MyGenPersonId).' '
						 .'    AND SOUNDEX(jpn2.familyName) = SOUNDEX(jpn.familyName) '
						 .'    AND SOUNDEX(jpn2.firstName)  = SOUNDEX(jpn.firstName) '
						 .'    ) '
						 );
						 
		// check whether the person from MyGenealogy is already linked
		$query->select(' jpl2.app_id AS indLinked ');
		$query->leftJoin( ' #__joaktree_person_links  jpl2 '
						 .' ON (   jpl2.app_id    = jpn2.app_id '
						 .'    AND jpl2.person_id = jpn2.id '
						 .'    ) '
						);
		
		// check whether the person from Community is already linked to any person of My Genealogy
		$query->select(' jpl1.app_id AS indUsed ');
		$query->leftJoin( ' #__joaktree_person_links  jpl1 '
						 .' ON (   jpl1.app_id_c    = jpn.app_id '
						 .'    AND jpl1.person_id_c = jpn.id '
						 .'    AND jpl1.app_id      = jpn2.app_id '
						 .'    ) '
						);
						 
		// Get the WHERE, GROUP BY and ORDER BY clauses for the query
		$query->where(' jpn.app_id = 1 ');
		
		$query->group(' jpn.id ');
		$query->group(' jpn.app_id ');
		$query->group(' jpn.firstName ');
		$query->group(' jpn.namePreposition ');
		$query->group(' jpn.familyName ');
		
		$query->order(' jpn.familyName ');
		$query->order(' jpn.firstName ');
		
			
		return $query;
	}

	public function getPersonlist() {
		// Lets load the content if it doesn't already exist
		if (empty($this->_personlist)) {
			$query = $this->_buildQuery();					
			$this->_personlist = $this->_getList( $query, $this->getState('limitstart'), $this->getState('limit'));
		}
		
		return $this->_personlist;
	}
}
?>