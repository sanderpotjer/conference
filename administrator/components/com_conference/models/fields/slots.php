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
		$db = JFactory::getDbo();

		// Get the days
		$query = $db->getQuery(true)
			->select(
				$db->quoteName(
					array(
						'conference_day_id',
						'title',
						'date',
					)
				)
			)
			->from($db->quoteName('#__conference_days'))
			->where($db->quoteName('enabled') . ' = 1');
		$db->setQuery($query);
		$days = $db->loadObjectList();

		$options = array();
		$query->clear()
			->select($db->quoteName(array('conference_slot_id', 'start_time', 'end_time')))
			->from($db->quoteName('#__conference_slots'))
			->order($db->quoteName('start_time') . ' ASC');

		foreach ($days as $day)
		{
			$options[] = JHtml::_('select.optgroup', JHtml::_('date', $day->date, JText::_('l, j F Y')));

			// Get the slots
			$query->clear('where')
				->where($db->quoteName('enabled') . ' = 1')
				->where($db->quoteName('conference_day_id') . ' = ' . (int) $day->conference_day_id);
			$db->setQuery($query);
			$items = $db->loadObjectList();

			if (count($items)) foreach($items as $item)
			{
				$options[] = JHtml::_('select.option', $item->conference_slot_id, $day->title . ': ' . JHtml::_('date', $item->start_time, 'H:i') . ' - ' . JHtml::_('date', $item->end_time, 'H:i'));
			}

			$options[] = JHtml::_('select.optgroup', $day->title);
		}

		return array_merge(parent::getOptions(), $options);
	}
}
