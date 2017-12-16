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
 * Sessions view.
 *
 * @package  Conference
 * @since    1.0
 */
class ConferenceViewSessions extends HtmlView
{
	/**
	 * The items to display.
	 *
	 * @var    array
	 * @since  1.0.0
	 */
	protected $items;

	/**
	 * The pagination object
	 *
	 * @var    JPagination
	 * @since  1.0.0
	 */
	protected $pagination;

	/**
	 * Page parameters
	 *
	 * @var    \Joomla\Registry\Registry
	 * @since  1.0.0
	 */
	protected $pageparams;

	/**
	 * Executes before rendering the page for the Browse task.
	 *
	 * @param   string $tpl Subtemplate to use
	 *
	 * @return  boolean  Return true to allow rendering of the page
	 *
	 * @since   6.0
	 *
	 * @throws  Exception
	 */
	public function display($tpl = null)
	{
		// Load the data
		$this->items      = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->pageparams = JFactory::getApplication()->getPageParameters('com_conference');

		// Display it all
		return parent::display($tpl);
	}
}
