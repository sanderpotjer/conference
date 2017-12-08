<?php
/**
 * @package     Conference
 *
 * @author      Stichting Sympathy <info@stichtingsympathy.nl>
 * @copyright   Copyright (C) 2013 - [year] Stichting Sympathy. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://joomladagen.nl
 */

use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\MVC\View\HtmlView;

defined('_JEXEC') or die;

/**
 * Profile view.
 *
 * @package  Conference
 * @since    1.0
 */
class ConferenceViewProfile extends HtmlView
{
	/**
	 * Object with actions users is allowed to do
	 *
	 * @var    JObject
	 * @since  1.0.0
	 */
	protected $canDo;

	/**
	 * The speaker to display.
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	protected $profile;

	/**
	 * A list of sessions
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	protected $sessions = array();

	/**
	 * Executes before rendering the page for the Browse task.
	 *
	 * @param   string $tpl Subtemplate to use
	 *
	 * @return  boolean  Return true to allow rendering of the page
	 *
	 * @since   6.0
	 */
	public function display($tpl = null)
	{
		/** @var ConferenceModelProfile $model */
		$model          = $this->getModel();
		$this->canDo    = ContentHelper::getActions('com_conference');
		$this->profile  = $model->getProfile();

		if ($this->profile->conference_speaker_id > 0)
		{
			$this->sessions = $model->getSessions($this->profile->conference_speaker_id);
		}

		// Display it all
		return parent::display($tpl);
	}
}
