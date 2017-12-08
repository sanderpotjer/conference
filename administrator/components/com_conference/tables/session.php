<?php
/**
 * @package     Conference
 *
 * @author      Stichting Sympathy <info@stichtingsympathy.nl>
 * @copyright   Copyright (C) 2013 - [year] Stichting Sympathy. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://joomladagen.nl
 */

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;

defined('_JEXEC') or die;

/**
 * Session table.
 *
 * @package     Conference
 * @since       1.0
 */
class TableSession extends Table
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
		parent::__construct('#__conference_sessions', 'conference_session_id', $db);

		$this->setColumnAlias('published', 'enabled');
	}

	/**
	 * Method to perform sanity checks on the Table instance properties to ensure they are safe to store in the database.
	 *
	 * Child classes should override this method to make sure the data they are storing in the database is safe and as expected before storage.
	 *
	 * @return  boolean  True if the instance is sane and able to be stored in the database.
	 *
	 * @since   1.0.0
	 */
	public function check()
	{
		$result = true;
		
		// Make sure assigned speaker really exists and normalize the list
		if (!empty($this->conference_speaker_id))
		{
			$speakerId = $this->conference_speaker_id;

			if (!is_array($this->conference_speaker_id))
			{
				$speakerId = explode(',', $this->conference_speaker_id);
			}

			if (empty($speakerId))
			{
				$this->conference_speaker_id = '';
			}
			else
			{
				$speakers = array();
				$db       = $this->getDbo();
				$query = $db->getQuery(true)
					->select($db->quoteName('conference_speaker_id'))
					->from($db->quoteName('#__conference_speakers'));

				foreach ($speakerId as $id)
				{
					$query->clear('where')
						->where($db->quoteName('conference_speaker_id') . ' = ' . (int) $id);

					$conferenceSpeakerId = $db->loadResult();

					if ($conferenceSpeakerId > 0)
					{
						$speakers[] = $id;
					}
				}

				$this->conference_speaker_id = implode(',', $speakers);
			}
		}

		// Make sure we have a slug
		if (trim($this->get('slug')) == '')
		{
			$this->set('slug', $this->get('title'));
		}

		$this->set('slug', ApplicationHelper::stringURLSafe($this->get('slug'), $this->get('language')));

		// Add some basic data for new entries or updated entries
		$userId = Factory::getUser()->get('id');

		if ((int) $this->get('conference_session_id') === 0)
		{
			$this->set('created_by', $userId);
			$this->set('created_on', (new Date())->toSql());

			// Get the speaker ID for the logged in user
			$db = $this->getDbo();
			$query = $db->getQuery(true)
				->select($db->quoteName('conference_speaker_id'))
				->from($db->quoteName('#__conference_speakers'))
				->where($db->quoteName('user_id') . ' = ' . (int) $userId);
			$db->setQuery($query);
			$this->set('conference_speaker_id', $db->loadResult());
		}
		else
		{
			$this->set('modified_by', $userId);
			$this->set('modified_on', (new Date())->toSql());
		}

		return $result;
	}
}
