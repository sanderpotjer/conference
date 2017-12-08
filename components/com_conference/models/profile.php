<?php
/**
 * @package     Conference
 *
 * @author      Stichting Sympathy <info@stichtingsympathy.nl>
 * @copyright   Copyright (C) 2013 - [year] Stichting Sympathy. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://joomladagen.nl
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

defined('_JEXEC') or die;

/**
 * Profile model.
 *
 * @package     Conference
 *
 * @since       1.0.0
 */
class ConferenceModelProfile extends BaseDatabaseModel
{
	/**
	 * Load the profile data.
	 *
	 * @return  object  The profile data.
	 *
	 * @since   1.0
	 *
	 * @throws Exception
	 */
	public function getProfile()
	{
		$db     = $this->getDbo();
		$table  = $this->getTable('Speaker');
		$user   = Factory::getUser();
		$userId = $user->get('id');

		if ((int) $userId === 0)
		{
			throw new InvalidArgumentException(Text::_('COM_CONFERENCE_YOU_NEED_TO_BE_LOGGED_IN'));
		}

		$query = $db->getQuery(true)
			->select($db->quoteName('conference_speaker_id'))
			->from($db->quoteName('#__conference_speakers'))
			->where($db->quoteName('user_id') . ' = ' . (int) $userId);
		$db->setQuery($query);
		$id = $db->loadResult();

		$table->load($id);
		$speaker = (object) $table->getProperties();

		return $speaker;
	}

	/**
	 * Get the sessions for the speaker.
	 *
	 * @param   int  $conference_speaker_id  The speaker ID
	 *
	 * @return  array  List of sessions.
	 *
	 * @since   1.0
	 */
	public function getSessions($conference_speaker_id)
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true)
			->select(
				$db->quoteName(
					array
					(
						'sessions.conference_session_id',
						'sessions.title',
						'sessions.description',
						'sessions.slides',
						'days.date',
						'slots.start_time',
						'slots.end_time',
					)
				)
			)
			->select(
				$db->quoteName(
					array(
						'sessions.enabled',
						'levels.label',
						'levels.title',
						'events.title',
					)
					, array(
						'status',
						'level_label',
						'level',
						'event',
					)
				)
			)
			->from($db->quoteName('#__conference_sessions', 'sessions'))
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
			->join('LEFT OUTER', $db->quoteName('#__conference_levels', 'levels')
				. ' ON ' .
				$db->quoteName('levels.conference_level_id') . ' = ' .
				$db->quoteName('sessions.conference_level_id')
			)
			->where($db->quoteName('conference_speaker_id') . ' = ' . (int) $conference_speaker_id)
			->order($db->quoteName('events.conference_event_id') . ' DESC, ' . $db->quoteName('days.date') . ' ASC, ' . $db->quoteName('slots.start_time') . ' ASC');
		$db->setQuery($query);

		return $db->loadObjectList();
	}
}
