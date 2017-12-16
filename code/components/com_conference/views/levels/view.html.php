<?php
/**
 * @package     Conference
 *
 * @author      Stichting Sympathy <info@stichtingsympathy.nl>
 * @copyright   Copyright (C) 2013 - [year] Stichting Sympathy. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://stichtingsympathy.nl
 */

use Joomla\CMS\MVC\View\HtmlView;

defined('_JEXEC') or die;

/**
 * Levels view.
 *
 * @package  Conference
 * @since    1.0
 */
class ConferenceViewLevels extends HtmlView
{
	/**
	 * The items to display.
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	protected $items;

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
		$this->items = $this->get('Items');

		// Display it all
		return parent::display($tpl);
	}
}
