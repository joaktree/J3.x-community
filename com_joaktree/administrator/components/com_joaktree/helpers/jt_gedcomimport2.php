<?php
/**
 * Joomla! component Joaktree
 * file		jt_gedcomfile model - jt_gedcomfile.php
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

class processObject {
	var $id			= null;
	var $start		= null;
	var $current	= null;
	var $end		= null;
	var $cursor		= 0;
	var $persons	= 0;
	var $families	= 0;
	var $sources	= 0;
	var $repos		= 0;
	var $notes		= 0;
	var $docs		= 0;
	var $unknown	= 0;
	var $japp_ids	= null;
	var $status		= 'new';
	var $msg		= null;			
}

class jt_gedcomimport2 {
	
	public function getProcessObject() {		
		// retrieve registry item
		$jt_registry = JTable::getInstance('joaktree_registry_items', 'Table');
		$jt_registry->loadUK('PROCESS_OBJECT');	
		$procObject = json_decode($jt_registry->value);
		unset($procObject->msg);			
		
		return $procObject;
	}
	
	public function setProcessObject($procObject) {
		// create a registry item	
		$jt_registry	= JTable::getInstance('joaktree_registry_items', 'Table');
		if (isset($procObject->msg)) {
			$procObject->msg 		= substr($procObject->msg, 0, 1500);
		} 
		$jt_registry->regkey 	= 'PROCESS_OBJECT';
		$jt_registry->value  	= json_encode($procObject);
		$jt_registry->storeUK();		
	}
	
	public function initObject ($cids) {		
		// store first empty object
		$newObject 				= new processObject();
		$newObject->id 			= array_shift($cids);
		$newObject->japp_ids 	= $cids;

		if (!$newObject->id) {
			$newObject->status = 'stop';
		}
		
		self::setProcessObject($newObject);
	}
		
	private function setLastUpdateDateTime() {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query->update(' #__joaktree_registry_items ');
		$query->set(   ' value  = NOW() ');
		$query->where( ' regkey = '.$db->quote( 'LAST_UPDATE_DATETIME' ).' ');
		
		$db->setQuery( $query );
		$db->execute();		
	}
	
	private function setInitialChar() {
		// update register with 0, meaning NO "initial character" present 	
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$query->update(' #__joaktree_registry_items ');
		$query->set(   ' value  = '.$db->quote('0').' ');
		$query->where( ' regkey = '.$db->quote( 'INITIAL_CHAR' ).' ');
		
		$db->setQuery( $query );
		$db->execute();			
	}
	// ===================================
	
//	function __construct($procObject) {
//		$this->procObject = $procObject;
//	}
	
	/* 
	** function for processing the gedcom file
	** status: new			- New process. Nothing has happened yet.
	**         progress		- Reading through the GedCom file
	**         endload		- Finished loading GedCom file
	**         endpat		- Finished setting patronyms
	**         endrel		- Finished setting relation indicators
	**         start		- Start assigning family trees
	**         starttree	- Start assigning one tree
	**         progtree		- Processing family trees (setting up link between persons and trees)
	**         endtree		- Finished assigning family trees
	**         treedef_1 	- Finished setting up default trees 1 (1 tree per person)
	**         treedef_2 	- Finished setting up default trees 2 (1 tree per person)
	**         treedef_3 	- Finished setting up default trees 3 (1 father tree per person)
	**         treedef_4 	- Finished setting up default trees 4 (1 mother tree per person)
	**         treedef_5 	- Finished setting up default trees 5 (1 partner tree per person)
	**         treedef_6 	- Finished setting up default trees 6 (lowest tree)
	**         endtreedef 	- Finished setting up default trees 7 (lowest tree)
	**         end			- Finished full process
	**         error		- An error has occured
	*/
	public function getGedcom() {
		$canDo	= JoaktreeHelper::getActions();
		$procObject = self::getProcessObject();
		
		if (($canDo->get('core.create')) && ($canDo->get('core.edit'))) {	

			JLoader::import('components.com_joaktree.helpers.jt_gedcomfile2', JPATH_ADMINISTRATOR);
			JLoader::import('components.com_joaktree.helpers.jt_gedcompersons2', JPATH_ADMINISTRATOR);
			JLoader::import('components.com_joaktree.helpers.jt_gedcomsources2', JPATH_ADMINISTRATOR);
			JLoader::import('components.com_joaktree.helpers.jt_gedcomrepos2', JPATH_ADMINISTRATOR);
			JLoader::import('components.com_joaktree.helpers.jt_gedcomnotes2', JPATH_ADMINISTRATOR);
			JLoader::import('components.com_joaktree.helpers.jt_gedcomdocuments2', JPATH_ADMINISTRATOR);

			JLoader::import('components.com_joaktree.helpers.jt_names', JPATH_ADMINISTRATOR);
			JLoader::import('components.com_joaktree.helpers.jt_trees', JPATH_ADMINISTRATOR);
			JLoader::import('components.com_joaktree.helpers.jt_relations', JPATH_ADMINISTRATOR);
			
			switch ($procObject->status) {
				case 'new':
					$procObject->start = strftime('%H:%M:%S');
					$procObject->msg = JText::sprintf('JTPROCESS_START_MSG', $procObject->id);					
				case 'progress':	// continue
				case 'endload':		// continue
				case 'endpat':		// continue
					$gedcomfile = new jt_gedcomfile2($procObject);
					$resObject 	= $gedcomfile->process('all');
		
					if ($resObject->status == 'endrel') {
						$msg = $gedcomfile->clear_gedcom();
						if ($msg) {
							$resObject->msg .= $msg.'<br />';
						}
					}			
							
					$resObject->current = strftime('%H:%M:%S');
					self::setProcessObject($resObject);
					$return = json_encode($resObject);
					break;
				case 'endrel':
					// Start loop throuth the assign FT
					$procObject->status = 'start';
				// Addition for processing tree-persons
				case 'start':		// continue
				case 'starttree':	// continue
				case 'progtree':	// continue
				case 'endtree':		// continue
				case 'treedef_1':	// continue
				case 'treedef_2':	// continue
				case 'treedef_3':	// continue
				case 'treedef_4':	// continue
				case 'treedef_5':	// continue
				case 'treedef_6':	// continue
					$familyTree = new jt_trees($procObject);
					$resObject 	= $familyTree->assignFamilyTree();
					
					$resObject->current = strftime('%H:%M:%S');
					self::setProcessObject($resObject);
					$return = json_encode($resObject);
					break;
				case 'endtreedef':
					// we are done
					$procObject->status  = 'end';
					$procObject->current = strftime('%H:%M:%S');
					$procObject->end 	 = $procObject->current;
					self::setLastUpdateDateTime();
					self::setInitialChar();
					
					self::setProcessObject($procObject);
					$return = json_encode($procObject);
					break;
				// End: Addition for processing tree-persons
				case 'end':
					// store first empty object
					$appId = $procObject->id;
					self::initObject($procObject->japp_ids);		
					$newObject = self::getProcessObject();
					$newObject->msg = JText::sprintf('JTPROCESS_END_MSG', $appId);

					$return = json_encode($newObject);
					break;
				case 'error':	// continue
				default:		// continue
					break;
			}			
		} else {
			
			$procObject->status = 'error';
			$procObject->msg    = JText::_('JT_NOTAUTHORISED');
			
			$return = json_encode($procObject);
		}
		
		return $return;
	}	
	
		
}
?>