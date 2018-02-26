<?php
/**
 * @package     Conference
 *
 * @author      Stichting Sympathy <info@stichtingsympathy.nl>
 * @copyright   Copyright (C) 2013 - [year] Stichting Sympathy. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://stichtingsympathy.nl
 */

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\FormModel;

defined('_JEXEC') or die;

/**
 * Session model.
 *
 * @package     Conference
 *
 * @since       1.0.0
 */
class ConferenceModelSession extends FormModel
{
	/**
	 * Get the form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success | False on failure.
	 *
	 * @since   1.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_conference.session', 'session', array('control' => 'jform', 'load_data' => $loadData));

		if (0 === count($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Load the form data.
	 *
	 * @return  object  The form data.
	 *
	 * @since   1.0
	 *
	 * @throws Exception
	 */
	protected function loadFormData()
	{
		$id    = $this->getState('session.id');

		if (empty($id))
		{
			return new stdClass;
		}

		$table = $this->getTable('Session');
		$table->load($id);
		$item = $table->getProperties();

		// Set the user ID
		$item['user_id'] = Factory::getUser()->get('id');


		if ((int) $item['conference_speaker_id'] === 0)
		{
			return (object) $item;
		}

		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select(
				$db->quoteName(
					array(
						'speakers.conference_speaker_id',
						'speakers.image',
						'speakers.title',
					)
				)
			)
			->from($db->quoteName('#__conference_speakers', 'speakers'))
			->order($db->quoteName('title') . ' ASC')
			->where($db->quoteName('conference_speaker_id') . ' IN (' . $item['conference_speaker_id'] . ')')
			->where($db->quoteName('enabled') . ' = 1');
		$db->setQuery($query);

		$item['speakers'] = $db->loadObjectList();

		$table = $this->getTable('Level');
		$table->load($item['conference_level_id']);
		$item['level'] = $table->getProperties();

		return (object) $item;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 *
	 * @throws  Exception
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication();

		$ids = $app->getUserState('com_conference.edit.session.id');

		if (count($ids) === 1)
		{
			$this->setState('session.id', $ids[0]);
		}
	}

	/**
	 * Load the item data.
	 *
	 * @return  object  The session data.
	 *
	 * @since   1.0
	 *
	 * @throws  Exception
	 */
	public function getItem()
	{
		$db    = $this->getDbo();
		$id    = $this->getState('session.id');
		$table = $this->getTable();
		$table->load($id);
		$item = (object) $table->getProperties();

		// Add the level information
		$query = $db->getQuery(true)
			->select($db->quoteName(array('label', 'title')))
			->from($db->quoteName('#__conference_levels'))
			->where($db->quoteName('conference_level_id') . ' = ' . (int) $item->conference_level_id);
		$db->setQuery($query);
		$item->level = $db->loadObject();

		// Add the speaker information
		$query->clear()
			->select(
				$db->quoteName(
					array
					(
						'conference_speaker_id',
						'title',
						'enabled',
						'image'
					)
				)
			)
			->from($db->quoteName('#__conference_speakers'))
			->where($db->quoteName('conference_speaker_id') . ' IN (' . $item->conference_speaker_id . ')');
		$db->setQuery($query);
		$item->speakers = $db->loadObjectList();

		// Get the room title
		$query->clear()
			->select($db->quoteName('title'))
			->from($db->quoteName('#__conference_rooms'))
			->where($db->quoteName('conference_room_id') . ' = ' . (int) $item->conference_room_id);
		$db->setQuery($query);
		$item->room = $db->loadResult();

		// Get the slot
		$query->clear()
			->select($db->quoteName(array('conference_day_id', 'start_time')))
			->from($db->quoteName('#__conference_slots'))
			->where($db->quoteName('conference_slot_id') . '  = ' . (int) $item->conference_slot_id);
		$db->setQuery($query);
		$slot = $db->loadObject();

		$query->clear()
			->select($db->quoteName('title'))
			->from($db->quoteName('#__conference_days'))
			->where($db->quoteName('conference_day_id') . ' = ' . (int) $slot->conference_day_id);
		$db->setQuery($query);
		$day = $db->loadObject();

		$item->slot = $day->title . ', ' . JHtml::_('date', $slot->start_time, 'H:i');

		// Get the language
		$languages = ComponentHelper::getParams('com_conference')->get('languages');
		$languages = explode("\n", $languages);

		foreach($languages as $language)
		{
			$list[] = explode("=", $language);
		}

		foreach ($list as $entry)
		{
			if ($item->language === $entry[0])
			{
				$item->language = $entry[1];
			}
		}

		return $item;
	}

	/**
	 * Save the session.
	 *
	 * @param   array  $data  The array of data to store
	 *
	 * @return  boolean  True if success saved | False if not saved.
	 *
	 * @since   1.0.0
	 *
	 * @throws  Exception
	 * @throws  RuntimeException
	 */
	public function save($data)
	{
		/** @var TableSession $table */
		$table = $this->getTable();

		$table->bind($data);

		if (!$table->check())
		{
			throw new InvalidArgumentException(Text::_('COM_CONFERENCE_CANNOT_SAVE_SESSION_DATA_CHECK_FAILED'));
		}

		return $table->store();
	}
}
