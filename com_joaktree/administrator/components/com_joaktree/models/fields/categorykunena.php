<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_categories
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_categories
 * @since       1.6
 */
class JFormFieldCategoryKunena extends JFormFieldList
{
	/**
	 * A flexible category list that respects access controls
	 *
	 * @var        string
	 * @since   1.6
	 */
	public $type = 'CategoryKunena';

	/**
	 * Method to get a list of categories that respects access controls and can be used for
	 * either category assignment or parent category assignment in edit screens.
	 * Use the parent element to indicate that the field will be used for assigning parent categories.
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   1.6
	 */
	protected function getOptions()
	{
		$options = array();
		$name = (string) $this->element['name'];
		
		$categories = KunenaForumCategoryHelper::getCategories();
		foreach ($categories as $categorie) {
			// create a dummy object
			$object = new stdClass;
			$object->value = $categorie->id;
			
			// Translate ROOT
			if ($categorie->published == 1) {
				$object->text  = str_repeat('- ', $categorie->level).$categorie->name;
			} else {
				$object->text  = str_repeat('- ', $categorie->level).'['.$categorie->name.']';
			}
			$options[] = $object;
			unset($object);
		}
		
		// create a dummy object
		$object = new stdClass;
		$object->value = 0;
		$object->text = JText::_('JTTREE_LABEL_NODISCUSSION');
		array_unshift($options, $object);
		
		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);	
		return $options;
	}
}
