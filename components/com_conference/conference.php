<?php
/**
 * @package     Conference
 *
 * @author      Stichting Sympathy <info@stichtingsympathy.nl>
 * @copyright   Copyright (C) 2013 - [year] Stichting Sympathy. All rights reserved.
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

JHtml::_('stylesheet', 'com_conference/frontend.css', array('version' => 'auto', 'relative' => true));

// Core Joomla views
$views = ['days', 'day'];

$jinput = JFactory::getApplication()->input;
$view = $jinput->get('view');

if (empty($view))
{
	$task = $jinput->get('task');

	if (strpos($task, '.'))
	{
		list($view, $task) = explode('.', $task);
	}
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

