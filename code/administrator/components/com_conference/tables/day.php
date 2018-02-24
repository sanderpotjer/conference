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

defined('_JEXEC') or die;

/**
 * Day table.
 *
 * @package     Conference
 * @since       1.0
 */
class TableDay extends JTable
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
		parent::__construct('#__conference_days', 'conference_day_id', $db);

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
		// Make sure we have a slug
		if (trim($this->get('slug')) == '')
		{
			$this->set('slug', $this->get('title'));
		}

		$this->set('slug', ApplicationHelper::stringURLSafe($this->get('slug')));

		// Add some basic data for new entries or updated entries
		$userId = Factory::getUser()->get('id');

		if ((int) $this->get('conference_day_id') === 0)
		{
			$this->set('created_by', $userId);
			$this->set('created_on', (new Date())->toSql());
		}
		else
		{
			$this->set('modified_by', $userId);
			$this->set('modified_on', (new Date())->toSql());
		}

		return parent::check();
	}
}
