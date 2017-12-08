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
 * Levels model.
 *
 * @package     Conference
 *
 * @since       1.0.0
 */
class ConferenceModelLevels extends ListModel
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
			->from($db->quoteName('#__conference_levels', 'levels'))
			->select(
				$db->quoteName(
					array(
						'levels.conference_level_id',
						'levels.label',
						'levels.title',
						'levels.description'
					)
				)
			)
			->where($db->quoteName('levels.enabled') . ' = 1');

		// Add the list ordering clause.
		$query->order(
			$db->quoteName(
				$db->escape(
					$this->getState('list.ordering', 'levels.title')
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
	 * @throws  Exception
	 */
	public function getItems()
	{
		$items = parent::getItems();

		if (!is_array($items))
		{
			return $items;
		}

		// Get the sessions for each item
		foreach ($items as $index => $item)
		{
			$item->sessions = $this->getSessions($item->conference_level_id);
			$items[$index]  = $item;
		}

		return $items;
	}

	/**
	 * Get the list of sessions.
	 *
	 * @param   int $conference_level_id The conference ID to filter on
	 *
	 * @return  array  List of ordered sessions.
	 *
	 * @since   1.0
	 *
	 * @throws  Exception
	 */
	private function getSessions($conference_level_id)
	{
		// Get the event ID
		$params = JFactory::getApplication()->getPageParameters('com_conference');
		$eventId = $params->get('eventid', 0);

		$db = $this->getDbo();

		$query = $db->getQuery(true)
			->select(
				$db->quoteName(
					array(
						'sessions.conference_session_id',
						'sessions.conference_speaker_id',
						'sessions.conference_slot_id',
						'sessions.listview',
						'sessions.title',
						'sessions.language',
						'slots.start_time',
						'slots.end_time',
						'days.date'
					)
				)
			)
			->from($db->quoteName('#__conference_sessions', 'sessions'))
			->join('inner', $db->quoteName('#__conference_slots', 'slots')
				. ' ON ' . $db->quoteName('slots.conference_slot_id') . ' = ' . $db->quoteName('sessions.conference_slot_id'))
			->join('inner', $db->quoteName('#__conference_days', 'days')
				. ' ON ' . $db->quoteName('days.conference_day_id') . ' = ' . $db->quoteName('slots.conference_day_id'))
			->where($db->quoteName('sessions.conference_level_id') . ' = ' . (int) $conference_level_id)
			->where($db->quoteName('days.conference_event_id') . ' = ' . (int) $eventId)
			->where($db->quoteName('sessions.enabled') . ' = 1')
			->order($db->quoteName('sessions.title') . ' ASC');

		$db->setQuery($query);

		$sessions = $db->loadObjectList();

		$query->clear()
			->select($db->quoteName(array('conference_speaker_id', 'title', 'enabled')))
			->from($db->quoteName('#__conference_speakers', 'speakers'));

		foreach ($sessions as $index => $session)
		{
			// Load the speakers
			$query->clear('where')
				->where($db->quoteName('conference_speaker_id') . ' = ' . (int) $session->conference_speaker_id);
			$db->setQuery($query);
			$session->speakers = $db->loadObjectList();
			$sessions[$index] = $session;
		}

		return $sessions;
	}
}
