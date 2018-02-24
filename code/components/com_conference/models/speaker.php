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
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Image\Image;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\FormModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Cms\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\User;
use Joomla\CMS\User\UserHelper;

defined('_JEXEC') or die;

/**
 * Speaker model.
 *
 * @package     Conference
 *
 * @since       1.0.0
 */
class ConferenceModelSpeaker extends FormModel
{
	/**
	 * Get the form.
	 *
	 * @param   array   $data     Data for the form.
	 * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success | False on failure.
	 *
	 * @since   1.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_conference.speaker', 'speaker', array('control' => 'jform', 'load_data' => $loadData));

		if (0 === count($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Load the form data.
	 *
	 * @return  array  The form data.
	 *
	 * @since   1.0
	 *
	 * @throws Exception
	 */
	protected function loadFormData()
	{
		$id    = $this->getState('speaker.id');
		$table = $this->getTable('Speaker');
		$table->load($id);

		return $table->getProperties();
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
		$app = Factory::getApplication();

		$this->setState('speaker.id', $app->input->getInt('id'));
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
		$id    = $this->getState('speaker.id');
		$table = $this->getTable();
		$table->load($id);
		$item = (object) $table->getProperties();

		// Get the speaker sessions
		$query = $db->getQuery(true)
			->select(
				$db->quoteName(
					array(
						'conference_session_id',
						'listview',
						'title'
					)
				)
			)
			->from($db->quoteName('#__conference_sessions'))
			->where(
				'(' . $db->quoteName('conference_speaker_id') . ' LIKE ' . $db->quote($id)
				. ' OR ' . $db->quoteName('conference_speaker_id') . ' LIKE ' . $db->quote($id . ',%')
				. ' OR ' . $db->quoteName('conference_speaker_id') . ' LIKE ' . $db->quote('%,' . $id . ',%')
				. ' OR ' . $db->quoteName('conference_speaker_id') . ' LIKE ' . $db->quote('%,' . $id) . ')'
			)
			->where($db->quoteName('enabled') . ' = 1')
			->order($db->quoteName('ordering'));
		$db->setQuery($query);

		$item->sessions = $db->loadObjectList();

		return $item;
	}

	/**
	 * Save the edit form.
	 *
	 * @param   array  $data  The validated data to store
	 *
	 * @return  boolean  True on success | False on failure
	 *
	 * @since   1.0.0
	 *
	 * @throws  Exception
	 */
	public function save($data)
	{
		// Check if we need to create a user
		$userId = Factory::getUser()->get('id');

		if (!$userId || ((int) $userId === 0))
		{
			$this->createUser($data);
		}

		// Create the user image
		$data['image'] = $this->createUserImage($data, $userId);

		// Save the user
		/** @var TableSpeaker $table */
		$table = $this->getTable();
		$table->save($data);

		return true;
	}

	/**
	 * Create the user image.
	 *
	 * @param   array  $data    The user validated data
	 * @param   int    $userId  The ID of the user changing the profile
	 *
	 * @return  string  The path to the user image
	 *
	 * @since   1.0.0
	 *
	 * @throws  Exception
	 */
	private function createUserImage($data, $userId)
	{
		$input = Factory::getApplication()->input;
		$file  = $input->files->get('jform')['image'];

		if ($file['error'] && $file['name'] === '')
		{
			// Check if the user already has an image
			$db = $this->getDbo();
			$query = $db->getQuery(true)
				->select($db->quoteName('image'))
				->from($db->quoteName('#__conference_speakers'))
				->where($db->quoteName('user_id') . ' = ' . (int) $userId);
			$db->setQuery($query);

			return $db->loadResult();
		}
		elseif ($file['error'])
		{
			throw new InvalidArgumentException(Text::sprintf('COM_CONFERENCE_ERROR_' . $file['error'] . '_UPLOADING_IMAGE'));
		}

		// Speaker
		$speaker = $data['title'];
		$speaker = strtolower($speaker);
		$speaker = str_replace(' ', '_', $speaker);

		// Filename
		$filename = ($file['name']);
		$filename = strstr($filename, '.');
		$filename = $speaker . $filename;

		// Path
		$filepath = JPath::clean(JPATH_SITE . '/images/speakers/orig_' . $filename);

		// Do the upload
		jimport('joomla.filesystem.file');

		if (!JFile::upload($file['tmp_name'], $filepath))
		{
			throw new InvalidArgumentException(Text::_('COM_CONFERENCE_IMAGE_CANNOT_BE_UPLOADED'));
		}

		// Instantiate our JImage object
		$image = new Image($filepath);

		// Get the file's properties
		$properties = $image->getImageFileProperties($filepath);

		// Resize the file as a new object
		$resizedImage =
			$image->resize(250, 250, false, Image::SCALE_OUTSIDE)
			->crop(250, 250);

		// Determine the MIME of the original file to get the proper type for output
		$mime = $properties->mime;

		switch ($mime)
		{
			case 'image/png':
				$type = IMAGETYPE_PNG;
				break;
			case 'image/gif':
				$type = IMAGETYPE_GIF;
				break;
			default:
			case 'image/jpeg':
				$type = IMAGETYPE_JPEG;
				break;
		}

		// Store the resized image to a new file
		$croppath = JPath::clean(JPATH_SITE . '/images/speakers/' . $filename);
		$resizedImage->toFile($croppath, $type);

		return 'images/speakers/' . $filename;
	}

	/**
	 * Create a new Joomla! user.
	 *
	 * @param   array  $data  The user submitted data
	 *
	 * @return  object  The user object.
	 *
	 * @since   1.0.0
	 *
	 * @throws  Exception
	 */
	private function createUser($data)
	{
		// Create a new user
		$params = array(
			'name'     => $data['title'],
			'username' => $data['email'],
			'email'    => $data['email']
		);

		$user = clone(Factory::getUser());

		$usersConfig = ComponentHelper::getParams('com_users');
		$newUsertype = $usersConfig->get('new_usertype');


		if (empty($newUsertype))
		{
			$newUsertype = 2;
		}

		$params['groups']    = array($newUsertype);
		$params['sendEmail'] = 1;

		// Set the user's default language to whatever the site's current language is
		$params['params'] = array(
			'language' => JFactory::getConfig()->get('language')
		);

		$params['block']      = 1;
		$params['activation'] = Factory::getApplication()->getHash(UserHelper::genRandomPassword());

		$user->bind($params);
		$userIsSaved = $user->save();

		if ($userIsSaved)
		{
			$this->sendActivationEmail($user);
		}

		return $user;
	}

	/**
	 * Send the activation email.
	 *
	 * @param   User  $user  The Joomla user
	 *
	 * @return  boolean  True on success | False on failure.
	 *
	 * @since   1.0.0
	 *
	 * @throws  Exception
	 */
	private function sendActivationEmail($user)
	{
		$config         = Factory::getConfig();
		$userParams     = ComponentHelper::getParams('com_users');
		$db             = Factory::getDbo();
		$useractivation = $userParams->get('useractivation');

		// Load the users plugin group.
		PluginHelper::importPlugin('user');

		if (($useractivation == 1) || ($useractivation == 2))
		{
			$params               = array();
			$params['activation'] = ApplicationHelper::getHash(UserHelper::genRandomPassword());
			$user->bind($params);
			$user->save();
		}

		// Set up data
		$data             = $user->getProperties();
		$data['fromname'] = $config->get('fromname');
		$data['mailfrom'] = $config->get('mailfrom');
		$data['sitename'] = $config->get('sitename');
		$data['siteurl']  = Uri::root();

		// Load com_users translation files
		$jlang = Factory::getLanguage();
		// Load English (British)
		$jlang->load('com_users', JPATH_SITE, 'en-GB', true);
		// Load the site's default language
		$jlang->load('com_users', JPATH_SITE, $jlang->getDefault(), true);
		// Load the currently selected language
		$jlang->load('com_users', JPATH_SITE, null, true);

		// Handle account activation/confirmation emails.
		if ($useractivation == 2)
		{
			// Set the link to confirm the user email.
			$uri              = Uri::getInstance();
			$base             = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
			$data['activate'] = $base . Route::_('index.php?option=com_users&task=registration.activate&token=' . $data['activation'], false);

			$emailSubject = Text::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);

			$emailBody = Text::sprintf(
				'COM_USERS_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY',
				$data['name'],
				$data['sitename'],
				$data['siteurl'] . 'index.php?option=com_users&task=registration.activate&token=' . $data['activation'],
				$data['siteurl'],
				$data['username'],
				$data['password_clear']
			);
		}
		elseif ($useractivation == 1)
		{
			// Set the link to activate the user account.
			$uri              = Uri::getInstance();
			$base             = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
			$data['activate'] = $base . Route::_('index.php?option=com_users&task=registration.activate&token=' . $data['activation'], false);

			$emailSubject = Text::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);

			$emailBody = Text::sprintf(
				'COM_USERS_EMAIL_REGISTERED_WITH_ACTIVATION_BODY',
				$data['name'],
				$data['sitename'],
				$data['siteurl'] . 'index.php?option=com_users&task=registration.activate&token=' . $data['activation'],
				$data['siteurl'],
				$data['username'],
				$data['password_clear']
			);
		}
		else
		{

			$emailSubject = Text::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);

			$emailBody = Text::sprintf(
				'COM_USERS_EMAIL_REGISTERED_BODY',
				$data['name'],
				$data['sitename'],
				$data['siteurl']
			);
		}

		// Send the registration email.
		$return = Factory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $data['email'], $emailSubject, $emailBody);

		//Send Notification mail to administrators
		if (($userParams->get('useractivation') < 2) && ($userParams->get('mail_to_admin') == 1))
		{
			$emailSubject = Text::sprintf(
				'COM_USERS_EMAIL_ACCOUNT_DETAILS',
				$data['name'],
				$data['sitename']
			);

			$emailBodyAdmin = Text::sprintf(
				'COM_USERS_EMAIL_REGISTERED_NOTIFICATION_TO_ADMIN_BODY',
				$data['name'],
				$data['username'],
				$data['siteurl']
			);

			// Get all admin users
			$query = $db->getQuery(true)
				->select(
					$db->quoteName(
						array(
							'name',
							'email',
							'sendEmail'
						)
					)
				)
				->from($db->quoteName('#__users'))
				->where($db->quoteName('sendEmail') . ' = 1');
			$db->setQuery($query);
			$rows = $db->loadObjectList();

			// Send mail to all Super Users
			foreach ($rows as $row)
			{
				$return = Factory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $row->email, $emailSubject, $emailBodyAdmin);
			}
		}

		return $return;
	}
}
