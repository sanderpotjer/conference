<?php
/*<?php
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
 * Sessions model.
 *
 * @package     Conference
 * @since       1.0
 */
class ConferenceModelSessions extends JModelList
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
				'ordering', 'sessions.ordering',
				'conference_session_id', 'sessions.conference_session_id',
				'enabled', 'sessions.enabled',
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
	protected function populateState($ordering = 'sessions.ordering', $direction = 'DESC')
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
			->from($db->quoteName('#__conference_sessions', 'sessions'))
			->select(
				$db->quoteName(
					array(
						'sessions.conference_session_id',
						'sessions.start_time',
						'sessions.end_time',
						'sessions.enabled',
						'sessions.general',
						'sessions.locked_by',
					)
				)
			)
			->select(
				$db->quoteName(
					array(
						'level.title',
						'level.label',
						'room.title',
						'day.title',
						'slots.start_time',
						'slots.end_time',
						'slots.conference_day_id',
						'speakers.image',
						'speakers.user_id',
						'events.title',
						'events.conference_event_id'
					)
					, array(
						'level',
						'level_label',
						'room',
						'day',
						'start_time',
						'end_time',
						'day_id',
						'speakerimage',
						'user_id',
						'event',
						'conference_event_id'
					)
				)
			)
			->join('LEFT OUTER', $db->quoteName('#__conference_speakers', 'speakers')
				. ' ON ' .
				$db->quoteName('speakers.conference_speaker_id') . ' = ' .
				$db->quoteName('sessions.conference_speaker_id')
			)
			->join('LEFT OUTER', $db->quoteName('#__conference_levels', 'levels')
				. ' ON ' .
				$db->quoteName('levels.conference_level_id') . ' = ' .
				$db->quoteName('sessions.conference_level_id')
			)
			->join('LEFT OUTER', $db->quoteName('#__conference_rooms', 'rooms')
				. ' ON ' .
				$db->quoteName('rooms.conference_room_id') . ' = ' .
				$db->quoteName('sessions.conference_room_id')
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
			);

		// Filter by search field
		$search = $this->getState('filter.search');

		if ($search)
		{
			$query->where($db->quoteName('sessions.title') . ' LIKE ' . $db->quote('%' . $search . '%'));
		}

		// Filter by enabled
		$enabled = $this->getState('filter.enabled');

		if ('' !== $enabled && null !== $enabled)
		{
			$query->where($db->quoteName('sessions.enabled') . ' = ' . (int) $enabled);
		}

		// Filter by status
		$status = $this->getState('filter.status');

		if ('' !== $status && null !== $status)
		{
			$query->where($db->quoteName('sessions.status') . ' = ' . (int) $status);
		}

		// Filter by speaker
		$speaker = $this->getState('filter.conference_speaker_id');

		if ('' !== $speaker && null !== $speaker)
		{
			$query->where($db->quoteName('sessions.conference_speaker_id') . ' = ' . (int) $speaker);
		}

		// Filter by level
		$level = $this->getState('filter.conference_level_id');

		if ('' !== $level && null !== $level)
		{
			$query->where($db->quoteName('sessions.conference_level_id') . ' = ' . (int) $level);
		}

		// Filter by room
		$room = $this->getState('filter.conference_room_id');

		if ('' !== $room && null !== $room)
		{
			$query->where($db->quoteName('sessions.conference_room_id') . ' = ' . (int) $room);
		}

		// Filter by day
		$day = $this->getState('filter.conference_day_id');

		if ('' !== $day && null !== $day)
		{
			$query->where($db->quoteName('slots.conference_day_id') . ' = ' . (int) $day);
		}

		// Filter by slot
		$slot = $this->getState('filter.conference_slot_id');

		if ('' !== $slot && null !== $slot)
		{
			$query->where($db->quoteName('sessions.conference_slot_id') . ' = ' . (int) $slot);
		}

		// Filter by event
		$event = $this->getState('filter.conference_event_id');

		if ('' !== $event && null !== $event)
		{
			$query->where($db->quoteName('events.conference_event_id') . ' = ' . (int) $event);
		}

		// Add the list ordering clause.
		$query->order(
			$db->quoteName(
				$db->escape(
					$this->getState('list.ordering', 'slots.ordering')
				)
			)
			. ' ' . $db->escape($this->getState('list.direction', 'DESC'))
		);

		return $query;
	}
	
	
	private function getFilterValues()
	{		
		$enabled = $this->getState('enabled','','cmd');
		
		return (object)array(
			'title'			=> $this->getState('title',null,'string'),
			'speaker'		=> $this->getState('speaker',null,'int'),
			'level'			=> $this->getState('level',null,'int'),
			'room'			=> $this->getState('room',null,'int'),
			'day'			=> $this->getState('day',null,'int'),
			'slot'			=> $this->getState('slot',null,'int'),
			'listview'		=> $this->getState('listview',null,'int'),
			'status'		=> $this->getState('status',null,'int'),
			'event'			=> $this->getState('event',null,'int'),
			'enabled'		=> $enabled,
		);
	}
	
	protected function _buildQueryColumns($query)
	{
		$db = $this->getDbo();
		$state = $this->getFilterValues();

		$query->select(array(
			$db->qn('tbl').'.*',
			$db->qn('l').'.'.$db->qn('title').' AS '.$db->qn('level'),
			$db->qn('l').'.'.$db->qn('label').' AS '.$db->qn('level_label'),
			$db->qn('r').'.'.$db->qn('title').' AS '.$db->qn('room'),
			$db->qn('d').'.'.$db->qn('title').' AS '.$db->qn('day'),
			$db->qn('t').'.'.$db->qn('start_time').' AS '.$db->qn('start_time'),
			$db->qn('t').'.'.$db->qn('end_time').' AS '.$db->qn('end_time'),
			$db->qn('t').'.'.$db->qn('conference_day_id').' AS '.$db->qn('day_id'),
			$db->qn('s').'.'.$db->qn('image').' AS '.$db->qn('speakerimage'),
			$db->qn('s').'.'.$db->qn('user_id').' AS '.$db->qn('user_id'),
			$db->qn('e').'.'.$db->qn('title').' AS '.$db->qn('event'),
			$db->qn('e').'.'.$db->qn('conference_event_id').' AS '.$db->qn('conference_event_id'),
		));
		
		$order = $this->getState('filter_order', 'conference_session_id', 'cmd');
		if(!in_array($order, array_keys($this->getTable()->getData()))) $order = 'conference_session_id';
		$dir = $this->getState('filter_order_Dir', 'DESC', 'cmd');
		$query->order($order.' '.$dir);

	}
	
	protected function _buildQueryJoins($query)
	{
		$db = $this->getDbo();

		$query
			->join('LEFT OUTER', $db->qn('#__conference_speakers').' AS '.$db->qn('s').' ON '.
					$db->qn('s').'.'.$db->qn('conference_speaker_id').' = '.
					$db->qn('tbl').'.'.$db->qn('conference_speaker_id'))
			->join('LEFT OUTER', $db->qn('#__conference_levels').' AS '.$db->qn('l').' ON '.
					$db->qn('l').'.'.$db->qn('conference_level_id').' = '.
					$db->qn('tbl').'.'.$db->qn('conference_level_id'))
			->join('LEFT OUTER', $db->qn('#__conference_rooms').' AS '.$db->qn('r').' ON '.
					$db->qn('r').'.'.$db->qn('conference_room_id').' = '.
					$db->qn('tbl').'.'.$db->qn('conference_room_id'))
			->join('LEFT OUTER', $db->qn('#__conference_slots').' AS '.$db->qn('t').' ON '.
					$db->qn('t').'.'.$db->qn('conference_slot_id').' = '.
					$db->qn('tbl').'.'.$db->qn('conference_slot_id'))
			->join('LEFT OUTER', $db->qn('#__conference_days').' AS '.$db->qn('d').' ON '.
					$db->qn('d').'.'.$db->qn('conference_day_id').' = '.
					$db->qn('t').'.'.$db->qn('conference_day_id'))
			->join('LEFT OUTER', $db->qn('#__conference_events').' AS '.$db->qn('e').' ON '.
					$db->qn('d').'.'.$db->qn('conference_event_id').' = '.
					$db->qn('e').'.'.$db->qn('conference_event_id'))
		;	
		
	}
	
	protected function _buildQueryWhere($query)
	{
		$db = $this->getDbo();
		$state = $this->getFilterValues();
	
		if(is_numeric($state->speaker) && ($state->speaker > 0)) {
			$query->where(
				$db->qn('tbl').'.'.$db->qn('conference_speaker_id').' = '.
					$db->q($state->speaker)
			);
		}
		
		if(is_numeric($state->level) && ($state->level > 0)) {
			$query->where(
				$db->qn('tbl').'.'.$db->qn('conference_level_id').' = '.
					$db->q($state->level)
			);
		}
		
		if(is_numeric($state->room) && ($state->room > 0)) {
			$query->where(
				$db->qn('tbl').'.'.$db->qn('conference_room_id').' = '.
					$db->q($state->room)
			);
		}
		
		if(is_numeric($state->day) && ($state->day > 0)) {
			$query->where(
				$db->qn('t').'.'.$db->qn('conference_day_id').' = '.
					$db->q($state->day)
			);
		}
		
		if(is_numeric($state->slot) && ($state->slot > 0)) {
			$query->where(
				$db->qn('tbl').'.'.$db->qn('conference_slot_id').' = '.
					$db->q($state->slot)
			);
		}
		
		if(is_numeric($state->event) && ($state->event > 0)) {
			$query->where(
				$db->qn('e').'.'.$db->qn('conference_event_id').' = '.
					$db->q($state->event)
			);
		}
		
		if(is_numeric($state->status) && ($state->status > 0)) {
			$query->where(
				$db->qn('tbl').'.'.$db->qn('status').' = '.
					$db->q($state->status)
			);
		}
		
		if(is_numeric($state->enabled)) {
			$query->where(
				$db->qn('tbl').'.'.$db->qn('enabled').' = '.
					$db->q($state->enabled)
			);
		}
		
		if(is_numeric($state->listview)) {
			$query->where(
				$db->qn('tbl').'.'.$db->qn('listview').' = 1'
			);
		}

		if($state->title) {
			$search = '%'.$state->title.'%';
			$query->where(
				$db->qn('tbl').'.'.$db->qn('title').' LIKE '.
					$db->q($search)
			);
		}	
		
	}
	
	public function buildQuery($overrideLimits = false) {
		$db = $this->getDbo();
		$query = FOFQueryAbstract::getNew($db)
			->from($db->quoteName('#__conference_sessions').' AS '.$db->qn('tbl'));
		
		$this->_buildQueryColumns($query);
		$this->_buildQueryJoins($query);
		$this->_buildQueryWhere($query);
		//$this->_buildQueryGroup($query);
		
		return $query;
	}
}