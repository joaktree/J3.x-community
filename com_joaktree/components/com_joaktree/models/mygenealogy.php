<?php
/**
 * Joomla! component Joaktree
 * file		front end mygenealogy model - mygenealogy.php
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
JLoader::import('helper.person', JPATH_COMPONENT);
JLoader::import('components.com_joaktree.helpers.jt_relations', JPATH_ADMINISTRATOR);
JLoader::import('components.com_joaktree.helpers.jt_gedcomfile2', JPATH_ADMINISTRATOR);
JLoader::import('components.com_joaktree.helpers.jt_gedcomimport2', JPATH_ADMINISTRATOR);
//JLoader::import('components.com_joaktree.models.jt_import_gedcom', JPATH_ADMINISTRATOR);
JLoader::import('components.com_users.models.group', JPATH_ADMINISTRATOR);

if(!defined('JT_TMP_PATH'))	define('JT_TMP_PATH', 'tmp\joaktree');

class JoaktreeModelMygenealogy extends JModelForm {
	
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
		$form = $this->loadForm('com_joaktree.mygenealogy', 'mygenealogy', array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_joaktree.edit.mygenealogy.data', array());

		if (empty($data)) {
			$data = $this->getItem();		
		}

		return $data;
	}
		
	public function getItem() {
		$query = $this->_db->getQuery(true);
		
		// select from users
		$query->select(' jur.* ');
		$query->from(  ' #__joaktree_users jur ');
						
		// select from trees
		$query->select(' jte.* ');
		$query->innerJoin(' #__joaktree_trees  jte '
						 .' ON jte.id = jur.tree_id '
						 );
		
		// select from applications
		$query->select(' japp.* ');
		$query->innerJoin(' #__joaktree_applications  japp '
						 .' ON japp.id = jte.app_id '
						 );
						 
		// Get the WHERE, GROUP BY and ORDER BY clauses for the query
		$wheres      	= $this->_buildContentWhere();
		foreach ($wheres as $where) {
			$query->where(' '.$where.' ');
		}
							
		$this->_db->setQuery($query);			
		$item = $this->_db->loadObject();
		
		return $item;
	}

	private function _buildContentWhere() {		
		$where = array();
		$user	= JFactory::getUser();
				
		$where[] = ' jur.user_id = '.(int) $user->id.' ';
		
		return $where;
	}

	public function delete() {
		$msg	= '';
		$canDo	= JoaktreeHelper::getActions();
		
		if ($canDo->get('core.delete')) {
			$query 	= $this->_db->getQuery(true);
			
			// select from users
			$query->select(' jur.* ');
			$query->from(  ' #__joaktree_users jur ');
			$query->where('  jur.user_id = '.(int)JFactory::getUser()->id.' ');
			$this->_db->setQuery($query);			
			$item = $this->_db->loadObject();
		
			if (is_object($item)) {
				// delete tree
				$query->clear();
				$query->delete(' #__joaktree_trees ');
				$query->where('  id = '.(int)$item->tree_id.' ');
				$this->_db->setQuery($query);
					
				if ($this->_db->execute()) {					
					// delete application data
					$msg	= jt_gedcomfile2::deleteGedcomData((int)$item->app_id, true);
						
					// delete application 
					$query->clear();
					$query->delete(' #__joaktree_applications ');
					$query->where('  id = '.(int)$item->app_id.' ');
					$this->_db->setQuery($query);
					
					if (!$this->_db->execute()) {
						$msg	= JText::_('JERROR_LAYOUT_ERROR_HAS_OCCURRED_WHILE_PROCESSING_YOUR_REQUEST');
					}					
				} else {
					$msg	= JText::_('JERROR_LAYOUT_ERROR_HAS_OCCURRED_WHILE_PROCESSING_YOUR_REQUEST');
				}
			}
		} else {
			$msg	= JText::_('JT_NOTAUTHORISED');
		}
		
		return($msg);
	}
		
	public function save($form) {		
		$canDo	= JoaktreeHelper::getJoaktreeActions();	
		
		if ($canDo->get('core.create') || $canDo->get('core.edit')) {			
			switch ($form['wizard']) {
				case 1: $return = $this->save_wizard_1($form);
						break;
				case 2: $return = $this->save_wizard_2($form);
						break;
				default: // do nothing 
						break;
			}			
		} else {			
			$return = false;
		}
		
		return $return;
	}
	
	private function save_wizard_1($array) {		
		$base1	= 'TreeOwners';
		$base2	= 'Tree';
		$user	= array('user_id' => (int)JFactory::getUser()->id);
		$gedcom = array();
		$tree	= array();
		$ids    = array();
	
		// first look for a record in joaktree_users
		$userTab	= JTable::getInstance('joaktree_users', 'Table');
		$update		= ($userTab->load($user['user_id'])) ? true : false;
		
		// second create usergroup if it doesn't exist
		$userGroupTable = JTable::getInstance('Usergroup');	
		
		// find the parent of the usergroup
		$keys = array('title' => 'Joaktree');
		if ($userGroupTable->load($keys)) {
			$parentUG = $userGroupTable->id;
			
			// look whether a group for this user exists
			$keys = array('title' => $base1.'_'.sprintf('%08d ', $user['user_id']));
			if ($userGroupTable->load($keys)) {
				// it exists
				$user['usergroup1_id'] = $userGroupTable->id;
			} else {
				// it does not exist -> create it
				$data = array();
				$data['id'] 		= 0;
				$data['title']		= $base1.'_'.sprintf('%08d ', $user['user_id']);
				$data['parent_id']	= $parentUG;
					
				if ($userGroupTable->bind($data)) {;
					if ($userGroupTable->store()) {
						$user['usergroup1_id'] = $userGroupTable->id;
						
						// place the user in the group
						$query = $this->_db->getQuery(true);
						$query->values($user['user_id'] . ',' . $user['usergroup1_id']); 
						$query->insert($this->_db->quoteName('#__user_usergroup_map'))
							->columns(array($this->_db->quoteName('user_id'), $this->_db->quoteName('group_id')));
						
						$this->_db->setQuery($query);						
							
						try
						{
							$this->_db->execute();
						}
						catch (RuntimeException $e)
						{
							$this->setError($e->getMessage());
							return false;
						}
						
					} else {
						return false;
					}
				}				
			}
		} 
		
		if ($user['usergroup1_id']) {
			// secondly create new application with permission for new usergroup
			$gedcomTab	= JTable::getInstance('joaktree_applications', 'Table');
			
			// set params to default values
			$gedcom['params']['gedcomfile_path']	= JT_TMP_PATH;
			$gedcom['params']['gedcomfile_name']	= (empty($array['params']['upload']))
														? 'joaktree.ged'
														: $array['params']['upload'];
			$gedcom['params']['unicode2utf'] 		= $array['params']['unicode2utf'];
			$gedcom['params']['familyname'] 		= $array['params']['familyname'];
			$gedcom['params']['patronym'] 			= $array['params']['patronym'];
			$gedcom['params']['patronymSeparation'] = $array['params']['patronymSeparation'];
			$gedcom['params']['age_no_page'] 		= $array['params']['age_no_page'];
			$gedcom['params']['removeChar'] 		= $array['params']['removeChar'];
			$gedcom['params']['truncrelations'] 	= $array['params']['truncrelations'];
			$gedcom['params']['indDocuments'] 		= $array['params']['indDocuments'];
			$gedcom['params']['gedcomDocumentRoot'] = $array['params']['gedcomDocumentRoot'];
			$gedcom['params']['joomlaDocumentRoot'] = $array['params']['joomlaDocumentRoot'];
			$gedcom['params']['colon'] 				= $array['params']['colon'];
			$gedcom['params']['indLogging'] 		= $array['params']['indLogging'];
						
			// set rules to default values
			$gedcom['rules']['core.create']			= array($user['usergroup1_id'] => 1);
			$gedcom['rules']['core.delete']			= array($user['usergroup1_id'] => 1);
			$gedcom['rules']['core.edit']			= array($user['usergroup1_id'] => 1);
			$gedcom['rules']['core.edit.state']		= array($user['usergroup1_id'] => 1);
			
			// set values
			$gedcom['title'] 						= $array['name'];		
			$gedcom['description']					= $base2.'_'.sprintf('%08d ', $user['user_id']);			
			
			if ($gedcomTab->bind($gedcom)) {
				if ($gedcomTab->store()) {
					$user['app_id']					= $gedcomTab->id;
					$ids['app'] 					= $gedcomTab->id;
					
					// create the tree
					$treeTab	= JTable::getInstance('joaktree_trees', 'Table');
					//$tree['id'] = ;
					$tree['app_id'] 				= $ids['app'];
					//$tree['asset_id'] = ;
					//$tree['root_person_id'] = ;
					$tree['published'] 				= 1;
					$tree['name'] 					= $base2.'_'.sprintf('%08d ', $user['user_id']);;
					$tree['theme_id'] 				= $array['theme_id'];
					$tree['indGendex'] 				= 1;
					$tree['indPersonCount'] 		= 0;
					$tree['indMarriageCount'] 		= 0;
					$tree['access'] 				= 1;
					$tree['holds'] 					= 'all';
					$tree['robots'] 				= 0;
					//$tree['catid'] 					= ;
					
					if ($treeTab->bind($tree)) {
						if ($treeTab->store()) {
							$user['tree_id']		= $treeTab->id;
							$ids['tree']			= $treeTab->id;
							
							// Last link the user to the tree and the group
							//$user['params'] = array();
							if ($userTab->bind($user)) {
								if ($update) {
									$result = $this->_db->updateObject('#__joaktree_users', $userTab, 'user_id', false);
								} else {
									$result = $this->_db->insertObject('#__joaktree_users', $userTab, 'user_id');
								}
								
								return ($result) ? $ids : false;
							} else {
								return false;
							}	
							
						} else {
							return false;
						}
					} else {
						return false;
					}	
				} else {
					return false;
				}
			}
			
		}
		
		return false;
	}

	private function save_wizard_2($form) {
		return false;
	}
	
	public function initObject($cid) {
		jt_gedcomimport2::initObject(array($cid));		
	}
	
			
}
