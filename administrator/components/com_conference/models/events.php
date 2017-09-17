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

/**
 * Events model.
 *
 * @package     Conference
 * @since       1.0
 */
class ConferenceModelEvents extends JModelList
{
	/**
	 * Construct the class.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since   1.0.0
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'ordering', 'events.ordering',
				'conference_event_id', 'events.conference_event_id',
				'title', 'events.title',
				'enabled', 'events.enabled',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.0.0
	 */
	protected function populateState($ordering = 'events.ordering', $direction = 'DESC')
	{
		parent::populateState($ordering, $direction);
	}

	/**
	 * Method to get a store id based on the model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  An identifier string to generate the store id.
	 *
	 * @return  string  A store id.
	 *
	 * @since   1.0.0
	 */
	protected function getStoreId($id = '')
	{
		// Add the list state to the store id.
		$id .= ':' . $this->getState('list.start');
		$id .= ':' . $this->getState('list.limit');
		$id .= ':' . $this->getState('list.ordering');
		$id .= ':' . $this->getState('list.direction');
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.enabled');

		return md5($this->context . ':' . $id);
	}

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
			->from($db->quoteName('#__conference_events', 'events'))
			->select(
				$db->quoteName(
					array(
						'conference_event_id',
						'title',
						'enabled',
						'ordering',
					)
				)
			);

		// Filter by search field
		$search = $this->getState('filter.search');

		if ($search)
		{
			$query->where($db->quoteName('events.title') . ' LIKE ' . $db->quote('%' . $search . '%'));
		}

		// Filter by enabled
		$enabled = $this->getState('filter.enabled');

		if ('' !== $enabled && null !== $enabled)
		{
			$query->where($db->quoteName('events.enabled') . ' = ' . (int) $enabled);
		}

		// Add the list ordering clause.
		$query->order(
			$db->quoteName(
				$db->escape(
					$this->getState('list.ordering', 'events.ordering')
				)
			)
			. ' ' . $db->escape($this->getState('list.direction', 'DESC'))
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

		// Get the sessions for each item
		foreach ($items as $index => $item)
		{
			$item->sessions = $this->getSessionCount($item->conference_event_id);
			$items[$index] = $item;
		}

		return $items;
	}

	/**
	 * Get the number of sessions for each event
	 *
	 * @param   int  $conference_event_id  The conferene ID
	 *
	 * @return  int  The number of sessions
	 *
	 * @since   1.0.0
	 */
	public function getSessionCount($conference_event_id)
	{
		$db = $this->getDbo();

		$query = $db->getQuery(true)
			->select('COUNT(' . $db->quoteName('conference_session_id') . ')')
			->from($db->quoteName('#__conference_sessions', 'sessions'))
			->join('LEFT OUTER', $db->quoteName('#__conference_speakers').' AS '.$db->quoteName('speakers').' ON '.
				$db->quoteName('speakers').'.'.$db->quoteName('conference_speaker_id').' = '.
				$db->quoteName('sessions').'.'.$db->quoteName('conference_speaker_id'))
			->join('LEFT OUTER', $db->quoteName('#__conference_levels').' AS '.$db->quoteName('levels').' ON '.
				$db->quoteName('levels').'.'.$db->quoteName('conference_level_id').' = '.
				$db->quoteName('sessions').'.'.$db->quoteName('conference_level_id'))
			->join('LEFT OUTER', $db->quoteName('#__conference_rooms').' AS '.$db->quoteName('rooms').' ON '.
				$db->quoteName('rooms').'.'.$db->quoteName('conference_room_id').' = '.
				$db->quoteName('sessions').'.'.$db->quoteName('conference_room_id'))
			->join('LEFT OUTER', $db->quoteName('#__conference_slots').' AS '.$db->quoteName('slots').' ON '.
				$db->quoteName('slots').'.'.$db->quoteName('conference_slot_id').' = '.
				$db->quoteName('sessions').'.'.$db->quoteName('conference_slot_id'))
			->join('LEFT OUTER', $db->quoteName('#__conference_days').' AS '.$db->quoteName('days').' ON '.
				$db->quoteName('days').'.'.$db->quoteName('conference_day_id').' = '.
				$db->quoteName('slots').'.'.$db->quoteName('conference_day_id'))
			->join('LEFT OUTER', $db->quoteName('#__conference_events').' AS '.$db->quoteName('events').' ON '.
				$db->quoteName('days').'.'.$db->quoteName('conference_event_id').' = '.
				$db->quoteName('events').'.'.$db->quoteName('conference_event_id'))
			->where($db->quoteName('events.conference_event_id') . ' = ' . (int) $conference_event_id);
		$db->setQuery($query);

		return $db->loadResult();
	}
}
