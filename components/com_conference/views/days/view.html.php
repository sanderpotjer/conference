<?php
/**
 * @package     Conference
 *
 * @author      Stichting Sympathy <info@stichtingsympathy.nl>
 * @copyright   Copyright (C) 2013 - [year] Stichting Sympathy. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://joomladagen.nl
 */

use Joomla\CMS\MVC\View\HtmlView;

defined('_JEXEC') or die;

/**
 * Days view.
 *
 * @package  Conference
 * @since    1.0
 */
class ConferenceViewDays extends HtmlView
{
	/**
	 * The items to display.
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	protected $items;

	/**
	 * List of rooms
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	protected $rooms = array();

	/**
	 * List of general rooms
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	protected $generalRoom = array();

	/**
	 * List of sessions
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
		// Load the data
		$this->items       = $this->get('Items');
		$this->rooms       = $this->get('Rooms');
		$this->generalRoom = $this->get('GeneralRoom');
		$this->sessions    = $this->get('Sessions');

		// Display it all
		return parent::display($tpl);
	}
}
