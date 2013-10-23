<?php
/**
 * Joomla! component Joaktree
 * file		table: joaktree_event_domains.php
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

class TableJoaktree_event_domains extends JTable
{
	var $id 			= null;
	var $code			= null;
	var $level 			= null;
	var $value			= null;
	
	function __construct( &$db) {
		parent::__construct('#__joaktree_event_domains', 'id', $db);
	}
}
?>