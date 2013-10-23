<?php
/**
 * Joomla! component Joaktree
 * file		table: joaktree_users.php
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

class TableJoaktree_users extends JTable
{
	var $user_id			= null; // PK
	var $app_id				= null;
	var $tree_id			= null;
	var $usergroup1_id		= null;
	//var $usergroup2_id		= null;
	var $params				= null;
	
	function __construct( &$db) {
		parent::__construct('#__joaktree_users', 'user_id', $db);
	}
	
	/**
	 * Overloaded bind function
	 *
	 * @param	array		$hash named array
	 * @return	null|string	null is operation was satisfactory, otherwise returns an error
	 * @see JTable:bind
	 * @since 1.5
	 */
	public function bind($array, $ignore = array())
	{	
		if (isset($array['params']) && is_array($array['params'])) {
			$registry = new JRegistry();
			$registry->loadArray($array['params']);

			$array['params'] = (string)$registry;
		}		
		
		return parent::bind($array, $ignore);
	}

}
?>