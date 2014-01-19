<?php
/**
 * Joomla! component Joaktree
 * file		table: joaktree_kunena.php
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

class TableJoaktree_kunena extends JMFPKTable
{
	var $app_id			= null; // PK
	var $person_id		= null; // PK
	var $thread_id		= null;

	function __construct( &$db) {
		$pk = array('app_id', 'person_id');
		parent::__construct('#__joaktree_kunena', $pk, $db);
	}
}
?>