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
 * Days model.
 *
 * @package     Conference
 * @since       1.0
 */
class ConferenceModelSlots extends JModelList
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
				'ordering', 'slots.ordering',
				'conference_slot_id', 'slots.conference_slot_id',
				'start_time', 'slots.start_time',
				'end_time', 'slots.end_time',
				'enabled', 'slots.enabled',
				'conference_event_id', 'events.conference_event_id',
				'days.title',
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
	protected function populateState($ordering = 'slots.ordering', $direction = 'DESC')
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
		$id .= ':' . $this->getState('filter.conference_day_id');
		$id .= ':' . $this->getState('filter.conference_event_id');

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
			->from($db->quoteName('#__conference_slots', 'slots'))
			->select(
				$db->quoteName(
					array(
						'slots.conference_slot_id',
						'slots.start_time',
						'slots.end_time',
						'slots.enabled',
						'slots.general',
						'slots.locked_by',
						'days.conference_day_id',
						'events.conference_event_id'
					)
				)
			)
			->select($db->quoteName('days.title', 'day'))
			->select($db->quoteName('days.date', 'date'))
			->select($db->quoteName('events.title', 'event'))
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
			$query->where($db->quoteName('days.title') . ' LIKE ' . $db->quote('%' . $search . '%'));
		}

		// Filter by enabled
		$enabled = $this->getState('filter.enabled');

		if ('' !== $enabled && null !== $enabled)
		{
			$query->where($db->quoteName('slots.enabled') . ' = ' . (int) $enabled);
		}

		// Filter by day
		$day = $this->getState('filter.conference_day_id');

		if ('' !== $day && null !== $day)
		{
			$query->where($db->quoteName('days.conference_day_id') . ' = ' . (int) $day);
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

		/** @var ConferenceModelEvents $model */
		$model = JModelLegacy::getInstance('Events', 'ConferenceModel');

		// Get the sessions for each item
		foreach ($items as $index => $item)
		{
			$item->sessions = $model->getSessionCount($item->conference_event_id);
			$items[$index] = $item;
		}

		return $items;
	}
}
?>
<?php
/*
 * @package		Conference Schedule Manager
 * @copyright	Copyright (c) 2013-2014 Sander Potjer / sanderpotjer.nl
 * @license		GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

class ConferenceModelSlots_ extends FOFModel
{			
	private function getFilterValues()
	{		
		$enabled = $this->getState('enabled','','cmd');
		
		return (object)array(
			'day'			=> $this->getState('day',null,'int'),
			'event'			=> $this->getState('event',null,'int'),
			'id'			=> $this->getState('id',null,'int'),
			'enabled'		=> $enabled,
		);
	}
	
	protected function _buildQueryColumns($query)
	{
		$db = $this->getDbo();
		$state = $this->getFilterValues();

		$query->select(array(
			$db->quoteName('tbl').'.*',
			$db->quoteName('d').'.'.$db->quoteName('title').' AS '.$db->quoteName('day'),
			$db->quoteName('d').'.'.$db->quoteName('date').' AS '.$db->quoteName('date'),
			$db->quoteName('e').'.'.$db->quoteName('title').' AS '.$db->quoteName('event'),
			$db->quoteName('e').'.'.$db->quoteName('conference_event_id').' AS '.$db->quoteName('conference_event_id'),
		));
		
		$order = $this->getState('filter_order', 'conference_slot_id', 'cmd');
		if(!in_array($order, array_keys($this->getTable()->getData()))) $order = 'conference_slot_id';
		$dir = $this->getState('filter_order_Dir', 'DESC', 'cmd');
		$query->order($order.' '.$dir);

	}
	
	protected function _buildQueryJoins($query)
	{
		$db = $this->getDbo();

		$query
			->join('LEFT OUTER', $db->quoteName('#__conference_days').' AS '.$db->quoteName('d').' ON '.
					$db->quoteName('d').'.'.$db->quoteName('conference_day_id').' = '.
					$db->quoteName('tbl').'.'.$db->quoteName('conference_day_id'))
			->join('LEFT OUTER', $db->quoteName('#__conference_events').' AS '.$db->quoteName('e').' ON '.
					$db->quoteName('e').'.'.$db->quoteName('conference_event_id').' = '.
					$db->quoteName('d').'.'.$db->quoteName('conference_event_id'))
		;	
		
	}
	
	protected function _buildQueryWhere($query)
	{
		$db = $this->getDbo();
		$state = $this->getFilterValues();
		
		if(is_numeric($state->enabled)) {
			$query->where(
				$db->quoteName('tbl').'.'.$db->quoteName('enabled').' = '.
					$db->q($state->enabled)
			);
		}
		
		if(is_numeric($state->day) && ($state->day > 0)) {
			$query->where(
				$db->quoteName('tbl').'.'.$db->quoteName('conference_day_id').' = '.
					$db->q($state->day)
			);
		}
		
		if(is_numeric($state->event) && ($state->event > 0)) {
			$query->where(
				$db->quoteName('e').'.'.$db->quoteName('conference_event_id').' = '.
					$db->q($state->event)
			);
		}
		
		if(is_numeric($state->id) && ($state->id > 0)) {
			$query->where(
				$db->quoteName('tbl').'.'.$db->quoteName('conference_slot_id').' = '.
					$db->q($state->id)
			);
		}
		
	}
	
	public function buildQuery($overrideLimits = false) {
		$db = $this->getDbo();
		$query = FOFQueryAbstract::getNew($db)
			->from($db->quoteName('#__conference_slots').' AS '.$db->quoteName('tbl'));
		
		$this->_buildQueryColumns($query);
		$this->_buildQueryJoins($query);
		$this->_buildQueryWhere($query);
		//$this->_buildQueryGroup($query);

		return $query;
	}
}