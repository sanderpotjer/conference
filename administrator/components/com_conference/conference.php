<?php
/**
 * @package     Conference
 *
 * @author      Stichting Sympathy <info@stichtingsympathy.nl>
 * @copyright   Copyright (C) 2013 - @year@ Stichting Sympathy. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://joomladagen.nl
 */

defined('_JEXEC') or die;

// Load FOF
include_once JPATH_LIBRARIES.'/fof/include.php';
if(!defined('FOF_INCLUDED')) {
	JError::raiseError ('500', 'FOF is not installed');

	return;
}

// Load the helper class for the submenu
require_once JPATH_ADMINISTRATOR . '/components/com_conference/helpers/conference.php';

JForm::addFieldPath(JPATH_ADMINISTRATOR . '/components/com_conference/models/fields');

// Core Joomla views
$views = ['events', 'event', 'days', 'day', 'slots', 'slot', 'levels', 'level'];

$jinput = JFactory::getApplication()->input;
$view = $jinput->get('view');

if (empty($view))
{
	list($view, $task) = explode('.', $jinput->get('task'));
}

try
{
	if (in_array($view, $views))
	{
		$controller = JControllerLegacy::getInstance('conference');
		$controller->execute($jinput->get('task'));
		$controller->redirect();
	}
	else
	{
		// Dispatch
		FOFDispatcher::getAnInstance('com_conference')->dispatch();
	}
}
catch (Exception $e)
{
	$oldUrl = JUri::getInstance($_SERVER['HTTP_REFERER']);
	//JFactory::getApplication()->redirect('index.php?option=com_conference&view=' . $oldUrl->getVar('view', ''), $e->getMessage(), 'error');
	echo $e->getMessage();
}