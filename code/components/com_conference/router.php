<?php
/**
 * @package     Conference
 *
 * @author      Stichting Sympathy <info@stichtingsympathy.nl>
 * @copyright   Copyright (C) 2013 - [year] Stichting Sympathy. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://stichtingsympathy.nl
 */

use Joomla\CMS\Component\Router\RouterBase;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ItemModel;

defined('_JEXEC') or die;

/**
 * Routing class from com_banners
 *
 * @since  1.0.0
 */
class ConferenceRouter extends RouterBase
{
	/**
	 * Build the route for the com_conference component
	 *
	 * @param   array  $query  An array of URL arguments
	 *
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 *
	 * @since   1.0.0
	 *
	 * @throws  Exception
	 */
	public function build(&$query)
	{
		$segments = array();
		$layout   = isset($query['layout']) ? $query['layout'] : '';

		if ($layout)
		{
			unset($query['layout']);
		}

		// Check for view
		if (!isset($query['view']) && !isset($query['Itemid']))
		{
			// No view and no Itemid is gonna be hard ;)
			return $segments;
		}

		if (!isset($query['view']))
		{
			// Get the view and layout from the Itemid.
			$link   = Factory::getApplication()->getMenu()->getItem($query['Itemid'])->link;
			$uri    = \Joomla\CMS\Uri\Uri::getInstance($link);
			$view   = $uri->getVar('view');
			$layout = $uri->getVar('layout');
		}
		else
		{
			// Easy mode
			$view = $query['view'];
			unset($query['view']);
		}

		// Get the ID of the item being displayed
		$id = 0;

		if (isset($query['id']))
		{
			$id = $query['id'];
			unset($query['id']);
		}

		switch ($view)
		{
			// Singular views
			case 'session':
				if (!isset($query['Itemid']))
				{
					$segments[] = 'session';

					if ($layout === 'edit')
					{
						$segments[] = 'edit';
					}
				}
				// This handles the Joomla redirect from the task session.edit to the view=session&layout=edit
				elseif (!isset($query['task']))
				{
					if ($layout)
					{
						unset($query['Itemid']);
						$segments[] = 'session';
						$segments[] = $layout;

						if (array_key_exists('conference_session_id', $query))
						{
							$segments[] = $query['conference_session_id'];
							unset($query['conference_session_id']);
						}
					}
					else
					{
						if (array_key_exists('conference_session_id', $query))
						{
							$db    = Factory::getDbo();
							$speakerQuery = $db->getQuery(true)
								->select($db->quoteName('slug'))
								->from($db->quoteName('#__conference_sessions'))
								->where($db->quoteName('conference_session_id') . ' = ' . (int) $query['conference_session_id']);
							$db->setQuery($speakerQuery);
							$segments[] = $db->loadResult();
							unset($query['conference_session_id']);
						}
					}
				}
				break;
			case 'speaker':
				if (!isset($query['Itemid']))
				{
					$segments[] = 'speaker';

					if ($layout === 'edit')
					{
						$segments[] = 'edit';
					}
				}
				// This handles the Joomla redirect from the task speaker.edit to the view=speaker&layout=edit
				elseif (!isset($query['task']))
				{
					if ($layout)
					{
						unset($query['Itemid']);
						$segments[] = 'speaker';
						$segments[] = $layout;

						if (array_key_exists('conference_speaker_id', $query))
						{
							$segments[] = $query['conference_speaker_id'];
							unset($query['conference_speaker_id']);
						}
					}
					else
					{
						if (array_key_exists('conference_speaker_id', $query))
						{
							$db    = Factory::getDbo();
							$speakerQuery = $db->getQuery(true)
								->select($db->quoteName('slug'))
								->from($db->quoteName('#__conference_speakers'))
								->where($db->quoteName('conference_speaker_id') . ' = ' . (int) $query['conference_speaker_id']);
							$db->setQuery($speakerQuery);
							$segments[] = $db->loadResult();
							unset($query['conference_speaker_id']);
						}
					}
				}
				break;

			case 'profile':
				if (isset($query['task']))
				{
					list ($view, $task) = explode('.', $query['task']);
					$segments[] = $view;
					$segments[] = $task;
					unset($query['task']);
					unset($query['layout']);

					if (array_key_exists('conference_speaker_id', $query))
					{
						$segments[] = $query['conference_speaker_id'];
						unset($query['conference_speaker_id']);
					}

					if (array_key_exists('conference_session_id', $query))
					{
						$segments[] = $query['conference_session_id'];
						unset($query['conference_session_id']);
					}
				}

				if ($layout)
				{
					$segments[] = $layout;
				}

				// This handles the cancel URL when the user clicks cancel on the edit profile page
				if (count($segments) === 0)
				{
					$query['Itemid'] = $this->getItemid($view);
				}
				break;

			case 'sessions':
				// Get the Itemid
				$query['Itemid'] = $this->getItemid('sessions');

				if ($id)
				{
					/** @var ConferenceModelSession $sessionModel */
					$sessionModel = ItemModel::getInstance('Session', 'ConferenceModel', array('ignore_request' => true));
					$sessionModel->setState('session.id', $id);
					$session    = $sessionModel->getItem();
					$segments[] = $session->slug;
				}
				break;

			case 'speakers':
				// Get the Itemid
				$query['Itemid'] = $this->getItemid('speakers');

				if ($id)
				{
					/** @var ConferenceModelSpeaker $speakerModel */
					$speakerModel = ItemModel::getInstance('Speaker', 'ConferenceModel', array('ignore_request' => true));
					$speakerModel->setState('speaker.id', $id);
					$speaker    = $speakerModel->getItem();
					$segments[] = $speaker->slug;
				}
				break;

			default:
				$query['Itemid'] = $this->getItemid($view);
				break;
		}

		return $segments;
	}

	/**
	 * Parse the segments of a URL.
	 *
	 * @param   array &$segments The segments of the URL to parse.
	 *
	 * @return  array  The URL attributes to be used by the application.
	 *
	 * @since   1.0.0
	 */
	public function parse(&$segments)
	{
		$vars = array();

		// Get the view from the active menu
		$app        = Factory::getApplication();
		$activeMenu = $app->getMenu()->getActive();

		// The Edit profile page may not have an Itemid, so we take the first segment element
		if ($activeMenu)
		{
			$view = $activeMenu->query['view'];
		}
		else
		{
			$view = $segments[0];
		}

		$db         = Factory::getDbo();

		switch ($view)
		{
			case 'days':
				break;
			case 'levels':
				$vars['view'] = 'level';
				break;
			case 'profile':
				switch ($segments[0])
				{
					case 'speaker':
						$vars['task']   = 'speaker.edit';
						$vars['layout'] = 'edit';

						if (array_key_exists(2, $segments))
						{
							$vars['conference_speaker_id'] = $segments[2];
						}
						break;
					case 'session':
						$vars['task'] = 'session.' . $segments[1];
						$vars['layout'] = 'edit';

						if (array_key_exists(2, $segments))
						{
							$vars['conference_session_id'] = $segments[2];
						}
						break;
				}
				break;
			case 'speaker':
				// This handles the view=speaker&layout=edit URL
				$vars['view'] = 'speaker';
				$vars['layout'] = 'edit';

				if (array_key_exists(2, $segments))
				{
					$vars['conference_speaker_id'] = $segments[2];
				}
				break;
			case 'session':
				// This handles the view=session&layout=edit URL
				$vars['view'] = 'session';
				$vars['layout'] = 'edit';

				if (array_key_exists(2, $segments))
				{
					$vars['conference_session_id'] = $segments[2];
				}
				break;
			case 'sessions':
				$vars['view'] = 'session';
				$query        = $db->getQuery(true)
					->select($db->quoteName('conference_session_id'))
					->from($db->quoteName('#__conference_sessions'))
					->where($db->quoteName('slug') . ' = ' . $db->quote($segments[0]));
				$db->setQuery($query);
				$vars['conference_session_id'] = $db->loadResult();
				break;
			case 'speakers':
				$vars['view'] = 'speaker';
				$query        = $db->getQuery(true)
					->select($db->quoteName('conference_speaker_id'))
					->from($db->quoteName('#__conference_speakers'))
					->where($db->quoteName('slug') . ' = ' . $db->quote($segments[0]));
				$db->setQuery($query);
				$vars['conference_speaker_id'] = $db->loadResult();
				break;
		}

		return $vars;
	}

	/**
	 * Find the item ID for a given view.
	 *
	 * @param   string $view The name of the view to find the item ID for
	 * @param   int    $id   The id of an item
	 *
	 * @return  mixed  The item ID or null if not found.
	 *
	 * @since   4.3.1
	 */
	private function getItemid($view, $id = null)
	{
		// Get all relevant menu items.
		$items = $this->menu->getItems('component', 'com_conference');

		// ItemId
		$itemId = null;

		if ($id)
		{
			foreach ($items as $item)
			{
				if (
					(isset($item->query['view']) && $item->query['view'] == $view) &&
					(isset($item->query['id']) && $item->query['id'] == $id))
				{
					$itemId = $item->id;
					break;
				}
			}
		}

		if (!$itemId)
		{
			foreach ($items as $item)
			{
				if (isset($item->query['view']) && $item->query['view'] == $view)
				{
					$itemId = $query['Itemid'] = $item->id;
					break;
				}
			}
		}

		return $itemId;
	}
}

function conferenceBuildRoute(&$query)
{
	$router = new ConferenceRouter;

	return $router->build($query);
}

function ConferenceParseRoute(&$segments)
{
	$router = new ConferenceRouter;

	return $router->parse($segments);
}
