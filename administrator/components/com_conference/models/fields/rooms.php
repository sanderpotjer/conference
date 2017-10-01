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
 * Room field.
 *
 * @package     Conference
 * @since       1.0
 */
class ConferenceFormFieldRooms extends JFormFieldList
{
	/**
	 * The type of field
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $type = 'rooms';

	/**
	 * Get the list of rooms
	 *
	 * @return  array  An array of rooms.
	 *
	 * @since   1.0
	 *
	 * @throws  RuntimeException
	 */
	protected function getOptions()
	{
		$db = JFactory::getDbo();

		$query = $db->getQuery(true)
			->select($db->quoteName('title', 'text'))
			->select($db->quoteName('conference_room_id', 'value'))
			->from($db->quoteName('#__conference_rooms'));
		$db->setQuery($query);
		$options = $db->loadObjectList();

		return array_merge(parent::getOptions(), $options);
	}
}
