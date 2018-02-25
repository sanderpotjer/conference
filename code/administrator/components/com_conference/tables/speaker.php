<?php
/**
 * @package     Conference
 *
 * @author      Stichting Sympathy <info@stichtingsympathy.nl>
 * @copyright   Copyright (C) 2013 - [year] Stichting Sympathy. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://stichtingsympathy.nl
 */

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;

/**
 * Speaker table.
 *
 * @package     Conference
 * @since       1.0
 */
class TableSpeaker extends Table
{
	/**
	 * Constructor.
	 *
	 * @param   JDatabaseDriver  $db  A database connector object.
	 *
	 * @since   1.0.0
	 */
	public function __construct($db)
	{
		parent::__construct('#__conference_speakers', 'conference_speaker_id', $db);

		$this->setColumnAlias('published', 'enabled');
	}

	/**
	 * Method to perform sanity checks on the Table instance properties to ensure they are safe to store in the database.
	 *
	 * Child classes should override this method to make sure the data they are storing in the database is safe and as expected before storage.
	 *
	 * @return  boolean  True if the instance is sane and able to be stored in the database.
	 *
	 * @throws  Exception
	 *
	 * @since   1.0.0
	 */
	public function check()
	{
		$result = true;

		// Make sure assigned events really exists and normalize the list
		if (!empty($this->conference_event_id))
		{
			if (is_array($this->conference_event_id))
			{
				$eventIds = $this->conference_event_id;
			}
			else
			{
				$eventIds = explode(',', $this->conference_event_id);
			}

			if (empty($eventIds))
			{
				$this->conference_event_id = '';
			}
			else
			{
				$events = array();
				$db       = $this->getDbo();
				$query = $db->getQuery(true)
					->select($db->quoteName('conference_event_id'))
					->from($db->quoteName('#__conference_events'));

				foreach ($eventIds as $eventId)
				{
					$query->clear('where')
						->where($db->quoteName('conference_event_id') . ' = ' . (int) $eventId);

					$conferenceEventId = $db->loadResult();

					if ($conferenceEventId > 0)
					{
						$events[] = $eventId;
					}
				}

				$this->conference_event_id = implode(',', $events);
			}
		}

		// Make sure we have a slug
		if (trim($this->get('slug')) == '')
		{
			$this->set('slug', $this->get('title'));
		}

		$this->set('slug', ApplicationHelper::stringURLSafe($this->get('slug'), $this->get('language')));

		// Get the user
		$userId = Factory::getUser()->get('id');

		if ((int) $this->get('conference_speaker_id') === 0)
		{
			$this->set('created_by', $userId);
			$this->set('created_on', (new Date)->toSql());

			if (Factory::getApplication()->isClient('site'))
			{
				$this->set('user_id', $userId);
			}
		}
		else
		{
			$this->set('modified_by', $userId);
			$this->set('modified_on', (new Date)->toSql());
		}

		return $result;
	}
}