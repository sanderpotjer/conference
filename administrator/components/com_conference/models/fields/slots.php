<?php
/**
 * @package     Conference
 *
 * @author      Stichting Sympathy <info@stichtingsympathy.nl>
 * @copyright   Copyright (C) 2013 - @year@ Stichting Sympathy. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://joomladagen.nl
 */

defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

/**
 * Slot field.
 *
 * @package     Conference
 * @since       1.0
 */
class ConferenceFormFieldSlots extends JFormFieldList
{
	/**
	 * The type of field
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $type = 'slots';

	/**
	 * Get the list of slots
	 *
	 * @return  array  An array of slots.
	 *
	 * @since   1.0
	 *
	 * @throws  RuntimeException
	 */
	protected function getOptions()
	{
		return array();
		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
			->select($db->quoteName('title', 'text'))
			->select($db->quoteName('conference_slot_id', 'value'))
			->from($db->quoteName('#__conference_slots'));
		$db->setQuery($query);
		$options = $db->loadObjectList();

		return array_merge(parent::getOptions(), $options);
	}
}
