<?php
/**
 * @package     Conference
 *
 * @author      Stichting Sympathy <info@stichtingsympathy.nl>
 * @copyright   Copyright (C) 2013 - [year] Stichting Sympathy. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://joomladagen.nl
 */

use Joomla\CMS\MVC\Model\ListModel;

defined('_JEXEC') or die;

/**
 * Sessions model.
 *
 * @package     Conference
 *
 * @since       1.0.0
 */
class ConferenceModelSessions extends ListModel
{
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery  The query to execute.
	 *
	 * @since   1.0.0
	 *
	 * @throws  Exception
	 */
	protected function getListQuery()
	{
		// Get the event ID
		$params = JFactory::getApplication()->getPageParameters('com_conference');
		$eventId = $params->get('eventid', 0);

		$db = $this->getDbo();

		// Get the parent query
		$query = $db->getQuery(true)
			->from($db->quoteName('#__conference_sessions', 'sessions'))
			->select(
				$db->quoteName(
					array(
						'sessions.conference_session_id',
						'sessions.conference_speaker_id',
						'sessions.title',
						'sessions.description',
						'sessions.slug',
						'sessions.slides',
						'sessions.enabled',
						'sessions.ordering'
					)
				)
			)
			->select(
				$db->quoteName(
					array(
						'levels.title',
						'levels.label',
					),
					array(
						'level',
						'level_label',
					)
				)
			)
			->join('LEFT OUTER', $db->quoteName('#__conference_levels', 'levels')
				. ' ON ' .
				$db->quoteName('levels.conference_level_id') . ' = ' .
				$db->quoteName('sessions.conference_level_id')
			)
			->join('LEFT OUTER', $db->quoteName('#__conference_slots', 'slots')
				. ' ON ' .
				$db->quoteName('slots.conference_slot_id') . ' = ' .
				$db->quoteName('sessions.conference_slot_id')
			)
			->join('LEFT OUTER', $db->quoteName('#__conference_days', 'days')
				. ' ON ' .
				$db->quoteName('days.conference_day_id') . ' = ' .
				$db->quoteName('slots.conference_day_id')
			)
			->join('LEFT OUTER', $db->quoteName('#__conference_events', 'events')
				. ' ON ' .
				$db->quoteName('events.conference_event_id') . ' = ' .
				$db->quoteName('days.conference_event_id')
			)
			->where($db->quoteName('events.conference_event_id') . ' = ' . (int) $eventId)
			->where($db->quoteName('sessions.conference_speaker_id') . " != ''")
			->where($db->quoteName('sessions.enabled') . ' = 1');

		// Add the list ordering clause.
		$query->order(
			$db->quoteName(
				$db->escape(
					$this->getState('list.ordering', 'sessions.title')
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

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select(
				$db->quoteName(
					array(
						'conference_speaker_id',
						'image',
						'title',
					)
				)
			)
			->from($db->quoteName('#__conference_speakers'))
			->order($db->quoteName('title') . ' ASC');


		// Get the sessions for each item
		foreach ($items as $index => $item)
		{
			$speaker = new stdClass;
			$speaker->conference_speaker_id = 0;
			$speaker->image = null;
			$speaker->title = null;
			$item->speakers = array($speaker);

			if ($item->conference_speaker_id)
			{
				// Get the speakers
				$query->clear('where')
					->where($db->quoteName('conference_speaker_id') . ' IN (' . $item->conference_speaker_id . ')')
					->where($db->quoteName('enabled') . ' = 1');
				$db->setQuery($query);

				$item->speakers = $db->loadObjectList();
				$items[$index]  = $item;
			}
		}

		return $items;
	}
}
