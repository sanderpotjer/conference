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

defined('_JEXEC') or die;

JHtml::_('stylesheet', 'com_conference/frontend.css', array('version' => 'auto', 'relative' => true));

// Load the admin language file
$language = Factory::getLanguage();
$language->load('com_conference', JPATH_ADMINISTRATOR . '/components/com_conference');

try
{
	$controller = JControllerLegacy::getInstance('conference');
	$controller->execute(Factory::getApplication()->input->get('task'));
	$controller->redirect();
}
catch (Exception $e)
{
	//$oldUrl = JUri::getInstance($_SERVER['HTTP_REFERER']);
	//JFactory::getApplication()->redirect('index.php?option=com_conference&view=' . $oldUrl->getVar('view', ''), $e->getMessage(), 'error');
	echo $e->getMessage();
}
