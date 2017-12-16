<?php
/**
 * @package     Conference
 *
 * @author      Stichting Sympathy <info@stichtingsympathy.nl>
 * @copyright   Copyright (C) 2013 - [year] Stichting Sympathy. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://stichtingsympathy.nl
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\View\HtmlView;

defined('_JEXEC') or die;

/**
 * Speaker view.
 *
 * @package  Conference
 * @since    1.0
 */
class ConferenceViewSpeaker extends HtmlView
{
	/**
	 * The form to display.
	 *
	 * @var    Form
	 * @since  1.0.0
	 */
	protected $form;

	/**
	 * The item to display
	 *
	 * @var    object
	 * @since  1.0.0
	 */
	protected $item;

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
		/** @var ConferenceModelSpeaker $model */
		$model = $this->getModel();

		// Get the actions
		$canDo = JHelperContent::getActions('com_conference');

		if ($this->getLayout() === 'edit' && $canDo->get('core.edit.own'))
		{
			$this->form = $model->getForm();
		}
		else
		{
			$this->item = $model->getItem();
		}

		// Display it all
		return parent::display($tpl);
	}
}
