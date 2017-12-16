<?php
/**
 * @package     Conference
 *
 * @author      Stichting Sympathy <info@stichtingsympathy.nl>
 * @copyright   Copyright (C) 2013 - [year] Stichting Sympathy. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://stichtingsympathy.nl
 */

use Joomla\CMS\MVC\Controller\BaseController;

defined('_JEXEC') or die;

/**
 * Speakers controller.
 *
 * @package  Conference
 * @since    1.0
 */
class ConferenceControllerSpeakers extends BaseController
{
	/**
	 * Save the incoming data and then return to the Browse task
	 *
	 * @since   1.0
	 */
	public function onAfterSave()
	{
		// Redirect
		$this->setRedirect(JRoute::_('index.php?option=com_conference&view=profile'), JText::_('COM_CONFERENCE_LBL_SPEAKER_SAVED'),'info');
		
		return true;
	}
}
