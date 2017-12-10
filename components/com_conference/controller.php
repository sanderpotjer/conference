<?php
/**
 * @package     Conference
 *
 * @author      Stichting Sympathy <info@stichtingsympathy.nl>
 * @copyright   Copyright (C) 2013 - [year] Stichting Sympathy. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://joomladagen.nl
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

/**
 * Base controller.
 *
 * @package     Conference
 *
 * @since       1.0
 */
class ConferenceController extends BaseController
{
	/**
	 * The default view.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $default_view = 'days';

	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  BaseController  This object to support chaining.
	 *
	 * @since   1.5
	 */
	public function display($cachable = false, $urlparams = array())
	{
		$view   = $this->input->get('view', 'speaker');
		$layout = $this->input->get('layout', 'default');
		$id     = $this->input->getInt('id');
		$user   = Factory::getUser();
		$model  = $this->getModel('Speaker', 'ConferenceModel');
		$item   = $model->getItem();
		$canDo  = JHelperContent::getActions('com_conference');

		// Check for access to edit form.
		if ($view == 'speaker' && $layout == 'edit' && (((int) $user->id !== (int) $item->user_id) && ($user->id === 0 && $canDo->get('core.create'))))
		{
			// Somehow the person just went to the form - we don't allow that.
			$this->setMessage(Text::_('COM_CONFERENCE_EDIT_NOT_ALLOWED'), 'error');
			$this->setRedirect(Route::_('index.php?option=com_conference&view=speaker&id=' . $id, false));

			$this->redirect();
		}

		return parent::display();
	}
}