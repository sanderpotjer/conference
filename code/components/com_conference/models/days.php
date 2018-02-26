<?php
/**
 * @package     Conference
 *
 * @author      Stichting Sympathy <info@stichtingsympathy.nl>
 * @copyright   Copyright (C) 2013 - [year] Stichting Sympathy. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://stichtingsympathy.nl
 */

use Joomla\CMS\MVC\Model\ListModel;

defined('_JEXEC') or die;

/**
 * Days model.
 *
 * @package     Conference
 *
 * @since       6.0
 */
class ConferenceModelDays extends ListModel
{
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery  The query to execute.
	 *
	 * @since   1.0.0
	 *
	 * @throws  RuntimeException
	 */
	protected function getListQuery()
	{
		$db = $this->getDbo();

		// Get the parent query
		$query = $db->getQuery(true)
			->from($db->quoteName('#__conference_days', 'days'))
			->select(
				$db->quoteName(
					array(
						'days.conference_day_id',
						'days.title',
						'days.slug',
						'days.date',
						'days.enabled',
						'days.ordering',
						'events.conference_event_id',
						'events.description',
					)
				)
			)
			->select($db->quoteName('events.title', 'event'))
			->join('LEFT OUTER', $db->quoteName('#__conference_events', 'events')
				. ' ON ' .
				$db->quoteName('events.conference_event_id') . ' = ' .
				$db->quoteName('days.conference_event_id')
			)
			->where($db->quoteName('days.enabled') . ' = 1');

		// Add the list ordering clause.
		$query->order(
			$db->quoteName(
				$db->escape(
					$this->getState('list.ordering', 'days.date')
				)
			)
			. ' ' . $db->escape($this->getState('list.direction', 'ASC'))
		);

		return $query;
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.0.0
	 *
	 * @throws  RuntimeException
	 */
	public function getItems()
	{
		$items = parent::getItems();

		if (!is_array($items))
		{
			return $items;
		}

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select(
				$db->quoteName(
					array(
						'conference_slot_id',
						'general',
						'start_time',
					)
				)
			)
			->from($db->quoteName('#__conference_slots'))
			->order($db->quoteName('start_time') . ' ASC');


		// Get the sessions for each item
		foreach ($items as $index => $item)
		{
			// Get the slots
			$query->clear('where')
				->where($db->quoteName('conference_day_id') . ' = ' . (int) $item->conference_day_id)
				->where($db->quoteName('enabled') . ' = 1');
			$db->setQuery($query);

			$item->slots   = $db->loadObjectList();
			$items[$index] = $item;
		}

		return $items;
	}

	/**
	 * Get a list of rooms.
	 *
	 * @return  array  List of rooms.
	 *
	 * @since   1.0
	 */
	public function getRooms()
	{
		$db = $this->getDbo();

		$query = $db->getQuery(true)
			->select(
				$db->quoteName(
					array(
						'conference_room_id',
						'title',
						'subtitle'
					)
				)
			)
			->from($db->quoteName('#__conference_rooms'))
			->where($db->quoteName('enabled') . ' = 1')
			->where($db->quoteName('type') . ' = 1');
		$db->setQuery($query);

		return $db->loadObjectList();

	}

	/**
	 * Get the ID of the general.
	 *
	 * @return  int  ID of the general room.
	 *
	 * @since   1.0
	 */
	public function getGeneralRoom()
	{
		$db = $this->getDbo();

		$query = $db->getQuery(true)
			->select($db->quoteName('conference_room_id'))
			->from($db->quoteName('#__conference_rooms'))
			->where($db->quoteName('enabled') . ' = 1')
			->where($db->quoteName('type') . ' = 0');
		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * Get the list of sessions.
	 *
	 * @return  array  List of ordered sessions.
	 *
	 * @since   1.0
	 */
	public function getSessions()
	{
		$db = $this->getDbo();

		$query = $db->getQuery(true)
			->select(
				$db->quoteName(
					array(
						'sessions.conference_session_id',
						'sessions.conference_slot_id',
						'sessions.conference_room_id',
						'sessions.conference_speaker_id',
						'sessions.listview',
						'sessions.title',
						'sessions.slides',
						'sessions.language',
					)
				)
			)
			->select(
				$db->quoteName(
					array(
						'levels.title',
						'levels.label'
					),
					array(
						'level',
						'level_label'
					)
				)
			)
			->from($db->quoteName('#__conference_sessions', 'sessions'))
			->leftJoin($db->quoteName('#__conference_levels', 'levels')
				. ' ON ' . $db->quoteName('levels.conference_level_id') . ' = ' . $db->quoteName('sessions.conference_level_id'))
			->where($db->quoteName('sessions.enabled') . ' = 1');

		$db->setQuery($query);

		$sessions = $db->loadObjectList();

		$ordered = array();
		$query->clear()
			->select($db->quoteName(array('conference_speaker_id', 'title', 'enabled')))
			->from($db->quoteName('#__conference_speakers', 'speakers'));

		foreach ($sessions as $session)
		{
			$session->speakers = new stdClass();

			if ($session->conference_speaker_id)
			{
				// Load the speakers
				$query->clear('where')
					->where($db->quoteName('conference_speaker_id') . ' IN (' . $session->conference_speaker_id . ')');
				$db->setQuery($query);

				$session->speakers = $db->loadObjectList();
			}

			$ordered[$session->conference_slot_id][$session->conference_room_id] = $session;
		}

		return $ordered;
	}
}