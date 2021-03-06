<?php
/**
 * @version		$Id: toolbar.php 22155 2011-09-25 21:04:08Z dextercowley $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.html.toolbar');

/**
 * Utility class for the button bar.
 *
 * @package		Joaktree
 * @subpackage	
 */
abstract class JToolBarCustomHelper
{
	/**
	 * Writes a custom option and task button for the button bar.
	 *
	 * @param	string	$task		The task to perform (picked up by the switch($task) blocks.
	 * @param	string	$icon		The image to display.
	 * @param	string	$iconOver	The image to display when moused over.
	 * @param	string	$alt		The alt text for the icon image.
	 * @param	string  $msg		The warning/question message
	 * @param	bool	$listSelect	True if required to check that a standard list item is checked.
	 * @since	1.0
	 */
	public static function custom($task = '', $icon = '', $iconOver = '', $alt = '', $msg = '', $listSelect = true)
	{
		$bar = JToolBar::getInstance('toolbar');

		// Strip extension.
		$icon = preg_replace('#\.[^.]*$#', '', $icon);

		// Add a standard button.
		if ($msg) {
			$bar->appendButton('Confirm', $msg, $icon, $alt, $task, $listSelect);
		} else {
			$bar->appendButton('Standard', $icon, $alt, $task, $listSelect);
		}
	}

}

