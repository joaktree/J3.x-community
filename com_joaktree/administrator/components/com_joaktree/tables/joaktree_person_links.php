<?php
/**
 * Joomla! component Joaktree
 * file		table: joaktree_person_links.php
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

require_once JPATH_ADMINISTRATOR.DS.'components'.DS.'com_joaktree'.DS.'tables'.DS.'JMFPKtable.php';

class TableJoaktree_person_links extends JMFPKTable
{
	var $app_id			= null; // PK
	var $person_id		= null; // PK
	var $app_id_c		= null; // PK
	var $person_id_c	= null; // PK

	function __construct( &$db) {
		$pk = array('app_id', 'person_id', 'app_id_c', 'person_id_c');
		parent::__construct('#__joaktree_person_links', $pk, $db);
	}
	
	public function check() {
		// mandatory fields
		if (empty($this->app_id)) {
			return false;
		}
		if (empty($this->person_id)) {
			return false;
		}
		if (empty($this->app_id_c)) {
			return false;
		}
		if (empty($this->person_id_c)) {
			return false;
		}
		
		return true;
	}
	
}
?>