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
JLoader::import('components.com_joaktree.tables.JMFPKtable', JPATH_ADMINISTRATOR);
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

	public function getUserAccess() {
		return JoaktreeHelper::getUserAccess();
	}
	
	public function getTechnology() {
		return JoaktreeHelper::getTechnology();
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
		$query->leftJoin(' #__joaktree_trees  jte '
						.' ON jte.id = jur.tree_id '
						);
		
		// select from applications
		$query->select(' japp.* ');
		$query->leftJoin(' #__joaktree_applications  japp '
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
				$treeTab			= JTable::getInstance('joaktree_trees', 'Table');
				$treeTab->app_id	= (int) $item->app_id;
				$treeTab->id		= (int) $item->tree_id;
				
				if ($treeTab->delete()) {					
					// delete application data
					$msg	= jt_gedcomfile2::deleteGedcomData((int)$item->app_id, true);
						
					// delete application 
					$gedcomTab	= JTable::getInstance('joaktree_applications', 'Table');
					
					if (!$gedcomTab->delete((int)$item->app_id)) {
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
				        if ($return) {
				        	$return = $this->pull_from_community_tree($form, $return);
				        }
						break;
				case 2: $return = $this->save_wizard_2($form);
						break;
				default: // do nothing 
						break;
			}			
		} else  if ($form['wizard'] == 3) {
			// for linking user to a person, no specific extra permissions are needed			
			$return = $this->save_wizard_3($form);
		} else  {			
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
		if (!$userGroupTable->load($keys)) {
			// group does not exist ... create it
			if (!$userGroupTable->load(array('title' => 'Public'))) {
				return false;
			}
			$parentUG = $userGroupTable->id;
			if (!$userGroupTable->bind(array( 'id' => 0
											, 'parent_id' => $parentUG
											, 'lft' => null
											, 'rgt' => null
											, 'title' => 'Joaktree'
											))) {
				return false;
			}
			if (!$userGroupTable->store()) {
				return false;
			}
		}
		
		// group should exist ... load it again ...
		if ($userGroupTable->load($keys)) {
			$parentUG = $userGroupTable->id;
			
			// look whether a group for this user exists
			if ($userGroupTable->load(array('title' => $base1.'_'.sprintf('%08d ', $user['user_id'])))) {
				// it exists
				$user['usergroup1_id'] = $userGroupTable->id;
			} else {
				// it does not exist -> create it
				if ($userGroupTable->bind(array( 'id' => 0
											   , 'parent_id' => $parentUG
											   , 'lft' => null
											   , 'rgt' => null
											   , 'title' => $base1.'_'.sprintf('%08d ', $user['user_id'])
											   ))) {
					if ($userGroupTable->store()) {
						$user['usergroup1_id'] = $userGroupTable->id;
						
						// place the user in the group
						$query = $this->_db->getQuery(true);
						$query->values($user['user_id'] . ',' . $user['usergroup1_id']); 
						$query->insert($this->_db->quoteName('#__user_usergroup_map'))
							->columns(array($this->_db->quoteName('user_id'), $this->_db->quoteName('group_id')));
						
						$this->_db->setQuery($query);						
							
						try {
							$this->_db->execute();
						}
						catch (RuntimeException $e) {
							$this->setError($e->getMessage());
							return false;
						}
						
					} else {
						return false;
					}
				} else {
					return false;
				}				
			}
		} else {
			return false;
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
	
	private function save_wizard_3($form) {
		$user = JFactory::getUser();
		
		if ($user->id) {
			// first get info from form
			$tmp			= explode('!', $form['person_id']);
			$ids    		= array();
			$ids['papp_id']	= array_shift($tmp);
			$ids['person_id']= array_shift($tmp);
			
			// initialize the user info
			$jt_user		= array( 'user_id'		=> (int)$user->id
								   , 'papp_id'		=> (int)$ids['papp_id']
								   , 'person_id'	=> $ids['person_id']
								   );
	
			// look for a record in joaktree_users
			$userTab		= JTable::getInstance('joaktree_users', 'Table');
			$update			= ($userTab->load($jt_user['user_id'])) ? true : false;

			// store the info
			if ($userTab->bind($jt_user)) {
				if ($update) {
					$result = $this->_db->updateObject('#__joaktree_users', $userTab, 'user_id', false);
				} else {
					$result = $this->_db->insertObject('#__joaktree_users', $userTab, 'user_id');
				}
				
				return ($result) ? $ids : false;
			} else {
				return false;
			}
		}	
		
		return false;
	}
	
	private function pull_from_community_tree($array, $return) {
		if (($array['indStartType'] == 2) && (!empty($array['person_id']))) {
			// First, retrieve all persons which has to be copied
			
			// Initialize some valies
			$person_id 			= explode('!', $array['person_id']); 			
			$id					= array('app_id' => $person_id[0]);
			$thisGeneration		= array($person_id[1]); 
			$nextGeneration 	= array();
			$allPersons			= array();
			$generationNumber	= 1;
			$counter			= 1;
			$community_app_id	= 1;

			// start retrieving the persons
			$continue = true;		
			while ($continue == true) {
				
				foreach ($thisGeneration as $nextPerson) {
					$id[ 'person_id' ] 	= $nextPerson;
					$allPersons[$id['person_id']] = $counter++;
					
					$person	    		= new Person($id, 'basic');		
					$children			= $person->getChildren('basic');		
					$partners			= $person->getPartners('basic');
						
					// when there are no children , skip this routine
					if (count($children) > 0 ) {
						foreach ($children as $child) {
							$nextGeneration[] = $child->id;
						}
					}
						
					// when there are no partners , skip this routine
					if (count($partners) > 0 ) {
						foreach ($partners as $partner) {
							// add the partner to the array
							$allPersons[$partner->id] = $counter++; 
						}
					}

				} // end loop through this generation
				
				array_splice($thisGeneration, 0);
				$thisGeneration = $nextGeneration;
				array_splice($nextGeneration, 0);	
			
				$generationNumber++;
				if (count($thisGeneration) > 0) {
					if ($generationNumber <= 100) {
						$continue = true;
					} else {
						$continue = false;
					}
				} else {
					$continue = false;
				}
			} // end of while-continue-is-true loop
			
			// Second, start copying in chuncks
			if (count($allPersons) > 0) {
				$person_links	= JMFPKTable::getInstance('joaktree_person_links', 'Table');
			
				$chunks = array_chunk(array_flip($allPersons), 4);	
				foreach ($chunks as $chunk) {
					$where = " IN ('".implode("','", $chunk)."')";
					
					// joaktree_admin_persons
					$query = 'INSERT INTO #__joaktree_admin_persons '
							.'(app_id,id,default_tree_id,published,access,living,page,robots,map) '
							.'SELECT '.$return['app'].',id,'.$return['tree'].',published,access,living,page,robots,map '
							.'FROM #__joaktree_admin_persons '
							.'WHERE app_id = '.$community_app_id.' '
							.'AND id '.$where.' '
							.'AND published = true ';
					
					$this->_db->setQuery($query);						
					try { $this->_db->execute(); }
					catch (RuntimeException $e) {
						print_r('; fout bij joaktree_admin_persons ;'); 
						print_r($e->getMessage());
						stop_joaktree_admin_persons();
						$this->setError($e->getMessage()); return false; }
							
					// joaktree_citations
					$query = 'INSERT INTO #__joaktree_citations '
							.'(objectType,objectOrderNumber,app_id,person_id_1,person_id_2,source_id,orderNumber,dataQuality,page,quotation,note) '
							.'SELECT jcn.objectType,jcn.objectOrderNumber,'.$return['app'].',jcn.person_id_1,jcn.person_id_2,jcn.source_id,jcn.orderNumber,jcn.dataQuality,jcn.page,jcn.quotation,jcn.note '
							.'FROM #__joaktree_citations jcn '
							.'INNER JOIN #__joaktree_admin_persons jan1 '
							.'ON (jan1.app_id = jcn.app_id AND jan1.id = jcn.person_id_1 AND jan1.published = true AND jan1.living = false) '
							.'LEFT JOIN #__joaktree_admin_persons jan2 '
							.'ON (jan2.app_id = jcn.app_id AND jan2.id = jcn.person_id_2 AND jan2.published = true AND jan2.living = false) '
							.'WHERE jcn.app_id = '.$community_app_id.' '
							.'AND (jcn.person_id_1 '.$where.' OR (jcn.person_id_2 '.$where.' AND jcn.person_id_1 NOT '.$where.')) '
							.'AND NOT EXISTS '
							.'(SELECT 1 FROM #__joaktree_citations jcn2 '
							.' WHERE jcn2.objectType = jcn.objectType '
							.' AND jcn2.objectOrderNumber = jcn.objectOrderNumber '
							.' AND jcn2.app_id = '.$return['app'].''
							.' AND jcn2.person_id_1 = jcn.person_id_1 '
							.' AND jcn2.person_id_2 = jcn.person_id_2) ';
					
					$this->_db->setQuery($query);						
					try { $this->_db->execute(); }
					catch (RuntimeException $e) { 
						print_r('; fout bij joaktree_citations ;'); 
						print_r($e->getMessage());
						stop_joaktree_citations();
						$this->setError($e->getMessage()); return false; }
													
					// joaktree_persons
					$query = 'INSERT INTO #__joaktree_persons '
							.'(app_id,id,indexNam,firstName,patronym,namePreposition,familyName,prefix,suffix,sex,indNote,indCitation,indHasParent,indHasPartner,indHasChild,indIsWitness,lastUpdateTimeStamp) '
							.'SELECT '.$return['app'].',jpn.id,jpn.indexNam,jpn.firstName,jpn.patronym,jpn.namePreposition,jpn.familyName,jpn.prefix,jpn.suffix,jpn.sex,jpn.indNote,jpn.indCitation,jpn.indHasParent,jpn.indHasPartner,jpn.indHasChild,jpn.indIsWitness,jpn.lastUpdateTimeStamp '
							.'FROM #__joaktree_persons jpn '
							.'INNER JOIN #__joaktree_admin_persons jan '
							.'ON (jan.app_id = jpn.app_id AND jan.id = jpn.id AND jan.published = true) '
							.'WHERE jpn.app_id = '.$community_app_id.' '
							.'AND jpn.id '.$where.' ';
							
					$this->_db->setQuery($query);						
					try { $this->_db->execute(); }
					catch (RuntimeException $e) { 
						print_r('; fout bij joaktree_persons ;'); 
						print_r($e->getMessage());
						stop_joaktree_persons();
						$this->setError($e->getMessage()); return false; }
							
					// joaktree_person_events
					$query = 'INSERT INTO #__joaktree_person_events '
							.'(app_id,person_id,orderNumber,code,indNote,indCitation,type,eventDate,loc_id,location,value) '
							.'SELECT '.$return['app'].',jpe.person_id,jpe.orderNumber,jpe.code,jpe.indNote,jpe.indCitation,jpe.type,jpe.eventDate,jpe.loc_id,jpe.location,jpe.value '
							.'FROM #__joaktree_person_events jpe '
							.'INNER JOIN #__joaktree_admin_persons jan '
							.'ON (jan.app_id = jpe.app_id AND jan.id = jpe.person_id AND jan.published = true AND jan.living = false) '
							.'WHERE jpe.app_id = '.$community_app_id.' '
							.'AND jpe.person_id '.$where.' ';
					
					$this->_db->setQuery($query);						
					try { $this->_db->execute(); }
					catch (RuntimeException $e) { 
						print_r('; fout bij joaktree_person_events ;'); 
						print_r($e->getMessage());
						stop_joaktree_person_events();
						$this->setError($e->getMessage()); return false; }
					
					// joaktree_person_names
					$query = 'INSERT INTO #__joaktree_person_names '
							.'(app_id,person_id,orderNumber,code,indNote,indCitation,eventDate,value) '
							.'SELECT '.$return['app'].',jpa.person_id,jpa.orderNumber,jpa.code,jpa.indNote,jpa.indCitation,jpa.eventDate,jpa.value '
							.'FROM #__joaktree_person_names jpa '
							.'INNER JOIN #__joaktree_admin_persons jan '
							.'ON (jan.app_id = jpa.app_id AND jan.id = jpa.person_id AND jan.published = true AND jan.living = false) '
							.'WHERE jpa.app_id = '.$community_app_id.' '
							.'AND jpa.person_id '.$where.' ';
					
					$this->_db->setQuery($query);						
					try { $this->_db->execute(); }
					catch (RuntimeException $e) { 
						print_r('; fout bij joaktree_person_names ;'); 
						print_r($e->getMessage());
						stop_joaktree_person_names();
						$this->setError($e->getMessage()); return false; }
					
					// joaktree_person_notes
					$query = 'INSERT INTO #__joaktree_person_notes '
							.'(app_id,person_id,orderNumber,indCitation,nameOrderNumber,eventOrderNumber,note_id,value) '
							.'SELECT '.$return['app'].',jpo.person_id,jpo.orderNumber,jpo.indCitation,jpo.nameOrderNumber,jpo.eventOrderNumber,jpo.note_id,jpo.value '
							.'FROM #__joaktree_person_notes jpo '
							.'INNER JOIN #__joaktree_admin_persons jan '
							.'ON (jan.app_id = jpo.app_id AND jan.id = jpo.person_id AND jan.published = true AND jan.living = false) '
							.'WHERE jpo.app_id = '.$community_app_id.' '
							.'AND jpo.person_id '.$where.' ';
					
					$this->_db->setQuery($query);						
					try { $this->_db->execute(); }
					catch (RuntimeException $e) { 
						print_r('; fout bij joaktree_person_notes ;'); 
						print_r($e->getMessage());
						stop_joaktree_person_notes();
						$this->setError($e->getMessage()); return false; }
					
					// joaktree_relations
					$query = 'INSERT INTO #__joaktree_relations '
							.'(app_id,person_id_1,person_id_2,type,subtype,family_id,indNote,indCitation,orderNumber_1,orderNumber_2) '
							.'SELECT '.$return['app'].',jrn.person_id_1,jrn.person_id_2,jrn.type,jrn.subtype,jrn.family_id,jrn.indNote,jrn.indCitation,jrn.orderNumber_1,jrn.orderNumber_2 '
							.'FROM #__joaktree_relations jrn '
							.'INNER JOIN #__joaktree_admin_persons jan1 '
							.'ON (jan1.app_id = jrn.app_id AND jan1.id = jrn.person_id_1 AND jan1.published = true) '
							.'LEFT JOIN #__joaktree_admin_persons jan2 '
							.'ON (jan2.app_id = jrn.app_id AND jan2.id = jrn.person_id_2 AND jan2.published = true) '
							.'WHERE jrn.app_id = '.$community_app_id.' '
							.'AND (jrn.person_id_1 '.$where.' OR (jrn.person_id_2 '.$where.' AND jrn.person_id_1 NOT '.$where.')) '
							.'AND NOT EXISTS '
							.'(SELECT 1 '
							.' FROM #__joaktree_relations jrn2 '
							.' WHERE jrn2.app_id = '.$return['app'].' '
							.' AND jrn2.person_id_1 = jrn.person_id_1 '
							.' AND jrn2.person_id_2 = jrn.person_id_2) ';
							
					$this->_db->setQuery($query);						
					try { $this->_db->execute(); }
					catch (RuntimeException $e) { 
						print_r('; fout bij joaktree_relations ;'); 
						print_r($e->getMessage());
						stop_joaktree_relations();
						$this->setError($e->getMessage()); return false; }
					
					// joaktree_relation_events
					$query = 'INSERT INTO #__joaktree_relation_events '
							.'(app_id,person_id_1,person_id_2,orderNumber,code,indNote,indCitation,type,eventDate,loc_id,location,value) '
							.'SELECT '.$return['app'].',jre.person_id_1,jre.person_id_2,jre.orderNumber,jre.code,jre.indNote,jre.indCitation,jre.type,jre.eventDate,jre.loc_id,jre.location,jre.value '
							.'FROM #__joaktree_relation_events jre '
							.'INNER JOIN #__joaktree_admin_persons jan1 '
							.'ON (jan1.app_id = jre.app_id AND jan1.id = jre.person_id_1 AND jan1.published = true AND jan1.living = false) '
							.'LEFT JOIN #__joaktree_admin_persons jan2 '
							.'ON (jan2.app_id = jre.app_id AND jan2.id = jre.person_id_2 AND jan2.published = true AND jan2.living = false) '
							.'WHERE jre.app_id = '.$community_app_id.' '
							.'AND (jre.person_id_1 '.$where.' OR (jre.person_id_2 '.$where.' AND jre.person_id_1 NOT '.$where.')) '
							.'AND NOT EXISTS '
							.'(SELECT 1 FROM #__joaktree_relation_events jre2 '
							.' WHERE jre2.app_id = '.$return['app'].' '
							.' AND jre2.person_id_1 = jre.person_id_1 '
							.' AND jre2.person_id_2 = jre.person_id_2 '
							.' AND jre2.orderNumber = jre.orderNumber) ';
							
					$this->_db->setQuery($query);						
					try { $this->_db->execute(); }
					catch (RuntimeException $e) { 
						print_r('; fout bij joaktree_relation_events ;'); 
						print_r($e->getMessage());
						stop_joaktree_relation_events();
						$this->setError($e->getMessage()); return false; }
					
					// joaktree_relation_notes
					$query = 'INSERT INTO #__joaktree_relation_notes '
							.'(app_id,person_id_1,person_id_2,orderNumber,indCitation,eventOrderNumber,note_id,value) '
							.'SELECT '.$return['app'].',jro.person_id_1,jro.person_id_2,jro.orderNumber,jro.indCitation,jro.eventOrderNumber,jro.note_id,jro.value '
							.'FROM #__joaktree_relation_notes jro '
							.'INNER JOIN #__joaktree_admin_persons jan1 '
							.'ON (jan1.app_id = jro.app_id AND jan1.id = jro.person_id_1 AND jan1.published = true AND jan1.living = false) '
							.'LEFT JOIN #__joaktree_admin_persons jan2 '
							.'ON (jan2.app_id = jro.app_id AND jan2.id = jro.person_id_2 AND jan2.published = true AND jan2.living = false) '
							.'WHERE jro.app_id = '.$community_app_id.' '
							.'AND (jro.person_id_1 '.$where.' OR (jro.person_id_2 '.$where.' AND jro.person_id_1 NOT '.$where.')) '
							.'AND NOT EXISTS '
							.'(SELECT 1 FROM #__joaktree_relation_notes jro2 '
							.' WHERE jro2.app_id = '.$return['app'].' '
							.' AND jro2.person_id_1 = jro.person_id_1 '
							.' AND jro2.person_id_2 = jro.person_id_2 '
							.' AND jro2.orderNumber = jro.orderNumber) ';
							
					$this->_db->setQuery($query);						
					try { $this->_db->execute(); }
					catch (RuntimeException $e) { 
						print_r('; fout bij joaktree_relation_notes ;'); 
						print_r($e->getMessage());
						stop_joaktree_relation_notes();
						$this->setError($e->getMessage()); return false; }
					
					// joaktree_tree_persons
					$query = 'INSERT INTO #__joaktree_tree_persons '
							.'(id,app_id,tree_id,person_id,type,lineage) '
							.'SELECT CONCAT_WS("+",jtp.person_id,'.$return['app'].'),'.$return['app'].','.$return['tree'].',jtp.person_id,jtp.type,jtp.lineage '
							.'FROM #__joaktree_tree_persons jtp '
							.'INNER JOIN #__joaktree_admin_persons jan '
							.'ON (jan.app_id = jtp.app_id AND jan.id = jtp.person_id AND jan.published = true) '
							.'WHERE jtp.app_id = '.$community_app_id.' '
							.'AND jtp.person_id '.$where.' ';
					
					$this->_db->setQuery($query);						
					try { $this->_db->execute(); }
					catch (RuntimeException $e) { 
						print_r('; fout bij joaktree_tree_persons ;'); 
						print_r($e->getMessage());
						stop_joaktree_tree_persons();
						$this->setError($e->getMessage()); return false; }
						
					// joaktree_person_links
					foreach ($chunk as $person_id) {
						$person_links->app_id		= $return['app'];
						$person_links->person_id	= $person_id;
						$person_links->app_id_c		= $community_app_id;
						$person_links->person_id_c	= $person_id;
						
						if (!$person_links->store()) {
							print_r('; fout bij joaktree_person_links ;'); 
							stop_joaktree_person_links();
							return false;					
						}
					}	
					
				} // end loop through set of chunks
				
				// Third, start copying the tables without person_ids
				// joaktree_notes
				$query = 'INSERT INTO #__joaktree_notes '
						.'(app_id,id,value) '
						.'SELECT '.$return['app'].',id,value '
						.'FROM #__joaktree_notes  '
						.'WHERE app_id = '.$community_app_id.' ';
				
				$this->_db->setQuery($query);						
				try { $this->_db->execute(); }
				catch (RuntimeException $e) { 
					print_r('; fout bij joaktree_notes ;'); 
					print_r($e->getMessage());
					stop_joaktree_notes();
					$this->setError($e->getMessage()); return false; }													
					
				// joaktree_repositories
				$query = 'INSERT INTO #__joaktree_repositories '
						.'(app_id,id,name,website) '
						.'SELECT '.$return['app'].',id,name,website '
						.'FROM #__joaktree_repositories  '
						.'WHERE app_id = '.$community_app_id.' ';
				
				$this->_db->setQuery($query);						
				try { $this->_db->execute(); }
				catch (RuntimeException $e) { 
					print_r('; fout bij joaktree_repositories ;'); 
					print_r($e->getMessage());
					stop_joaktree_repositories();
					$this->setError($e->getMessage()); return false; }
				
				// joaktree_sources
				$query = 'INSERT INTO #__joaktree_sources '
						.'(app_id,id,title,author,publication,information,repo_id) '
						.'SELECT '.$return['app'].',id,title,author,publication,information,repo_id '
						.'FROM #__joaktree_sources  '
						.'WHERE app_id = '.$community_app_id.' ';
				
				$this->_db->setQuery($query);						
				try { $this->_db->execute(); }
				catch (RuntimeException $e) { 
					print_r('; fout bij joaktree_sources ;'); 
					print_r($e->getMessage());
					stop_joaktree_sources();
					$this->setError($e->getMessage()); return false; }				
			}
		}
		
		return $return;
	}
	
	public function initObject($cid) {
		jt_gedcomimport2::initObject(array($cid));		
	}
	
			
}
