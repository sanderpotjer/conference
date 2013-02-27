<?php
/*
 * @package		Conference Schedule Manager
 * @copyright	Copyright (c) 2013 Sander Potjer / sanderpotjer.nl
 * @license		GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

require_once JPATH_SITE.'/components/com_conference/helpers/router.php';

/**
 * Which views does this router handle?
 */
global $atsHandleViews;
$atsHandleViews = array(
	'my', 'session', 'sessions', 'speaker', 'speakers', 'levels'
);

function ConferenceBuildRoute(&$query)
{
	global $atsHandleViews;
	$segments = array();
	
	// We need to find out if the menu item link has a view param
	$menuQuery = array();
	$menuView = 'categories';
	$Itemid = ConferenceHelperRouter::getAndPop($query, 'Itemid', 0);

	// Get the menu view, if an Item ID exists
	if($Itemid) {
		$menu = JFactory::getApplication()->getMenu()->getItem($Itemid);
		if(is_object($menu)) {
			parse_str(str_replace('index.php?',  '',$menu->link), $menuQuery); // remove "index.php?" and parse
			if(array_key_exists('view', $menuQuery)) {
				$menuView = $menuQuery['view'];
			}
		}
		
		$query['Itemid'] = $Itemid;
	}
	
	// Add the view
	$newView = array_key_exists('view', $query) ? $query['view'] : $menuView;
	
	// We can only handle specific views. Is it one of them?
	if(!in_array($newView, $atsHandleViews)) {
		if($Itemid) $query['Itemid'] = $Itemid;
		return array();
	}
	
	// Remove the option and view from the query
	ConferenceHelperRouter::getAndPop($query, 'view');
	
	// @todo Build the URL
	switch($newView)
	{
		case 'tickets':
			// Fetch the category ID
			$catID = ConferenceHelperRouter::getAndPop($query, 'category', 0);
			
			// Do I have to look for a new Item ID?
			$found = false;
			$menu = JFactory::getApplication()->getMenu()->getItem($Itemid);
			$qoptions = array(
				'option'	=> 'com_conference',
				'view'		=> 'tickets',
			);
			$params = array(
				'category'	=> $catID
			);
			$found = ConferenceHelperRouter::checkMenu($menu, $qoptions, $params);
			if(!$found) {
				$qoptions2 = array_merge($qoptions, array('category' => $catID));
				$found = ConferenceHelperRouter::checkMenu($menu, $qoptions2);
			}
			if(!$found) {
				// Try to find a menu item ID directly for this category
				$item = ConferenceHelperRouter::findMenu($qoptions, $params);
				
				// Or, try to find a manual link
				if(is_null($item)) {
					$item = ConferenceHelperRouter::findMenu($qoptions2);
				}
				
				if(!is_null($item)) {
					$Itemid = $item->id;
					$found = true;
				}
				
			}

			// Get and append the category alias path, if the category wasn't found
			if(!$found)
			{
				$db = JFactory::getDbo();
				$q = $db->getQuery(true)
					->select($db->qn('path'))
					->from($db->qn('#__categories'))
					->where($db->qn('id').' = '.$db->quote($catID))
					->where($db->qn('extension').' = '.$db->quote('com_ats'));
				$db->setQuery($q);
				$path = $db->loadResult();
				$pathParts = explode('/', $path);
				foreach($pathParts as $p) $segments[] = $p;
			}
			break;
			
		case 'speaker':
			$speakerID = ConferenceHelperRouter::getAndPop($query, 'id', 0);
			if($speakerID) {
				$speaker = FOFModel::getTmpInstance('Speakers', 'ConferenceModel')
					->setId($speakerID)
					->getItem();
				// Append the Speaker slug
				$segments[] = $speaker->slug;
			} else {
				$segments[] = 'new';
			}
			
			// Do I have to look for a new Item ID?
			$found = false;
			$menu = JFactory::getApplication()->getMenu()->getItem($Itemid);
			$qoptions = array(
				'option'	=> 'com_conference',
				'view'		=> 'speakers',
			);
			
			$found = ConferenceHelperRouter::checkMenu($menu, $qoptions);
			if(!$found) {
				// Try to find a menu item ID directly for this category
				$item = ConferenceHelperRouter::findMenu($qoptions);
				
				if(!is_null($item)) {
					$Itemid = $item->id;
					$found = true;
				}
			}
			
			break;
		case 'session':
			$sessionID = ConferenceHelperRouter::getAndPop($query, 'id', 0);
			if($sessionID) {
				$session = FOFModel::getTmpInstance('Sessions', 'ConferenceModel')
					->setId($sessionID)
					->getItem();
				// Append the Session slug
				$segments[] = $session->slug;
			} else {
				$segments[] = 'new';
			}
			
			// Do I have to look for a new Item ID?
			$found = false;
			$menu = JFactory::getApplication()->getMenu()->getItem($Itemid);
			$qoptions = array(
				'option'	=> 'com_conference',
				'view'		=> 'sessions',
			);
			
			$found = ConferenceHelperRouter::checkMenu($menu, $qoptions);
			if(!$found) {
				// Try to find a menu item ID directly for this category
				$item = ConferenceHelperRouter::findMenu($qoptions);
				
				if(!is_null($item)) {
					$Itemid = $item->id;
					$found = true;
				}
			}
			
			break;
		case 'my':
			// Do I have to look for a new Item ID?
			$found = false;
			$menu = JFactory::getApplication()->getMenu()->getItem($Itemid);
			$qoptions = array(
				'option'	=> 'com_conference',
				'view'		=> 'my',
			);
			
			$found = ConferenceHelperRouter::checkMenu($menu, $qoptions);
			if(!$found) {
				// Try to find a menu item ID directly for this category
				$item = ConferenceHelperRouter::findMenu($qoptions);
				
				if(!is_null($item)) {
					$Itemid = $item->id;
					$found = true;
				}
			}
			
			break;
			
		case 'levels':
			// Do I have to look for a new Item ID?
			$found = false;
			$menu = JFactory::getApplication()->getMenu()->getItem($Itemid);
			$qoptions = array(
				'option'	=> 'com_conference',
				'view'		=> 'levels',
			);
			
			$found = ConferenceHelperRouter::checkMenu($menu, $qoptions);
			if(!$found) {
				// Try to find a menu item ID directly for this category
				$item = ConferenceHelperRouter::findMenu($qoptions);
				
				if(!is_null($item)) {
					$Itemid = $item->id;
					$found = true;
				}
			}
			
			break;
	}
	
	// Process the Itemid
	$menuView = null;
	if($Itemid) {
		$menu = JFactory::getApplication()->getMenu()->getItem($Itemid);
		if(is_object($menu)) {
			parse_str(str_replace('index.php?',  '',$menu->link), $menuQuery); // remove "index.php?" and parse
			if(array_key_exists('view', $menuQuery)) {
				$menuView = $menuQuery['view'];
			}
		}
		
		$query['Itemid'] = $Itemid;
	}
	
	// If the menu's view is different to the new view, add the view name to the URL
	if(!empty($newView) && ($newView != $menuView)) {
		// Only append the view name if the $menuView is not categories, OR if the
		// $menuView is categories and the $newView IS NOT 'ticket' or 'tickets'
		if((($menuView != 'speakers') && ($menuView != 'sessions')) || empty($menuView) ) {
			array_unshift($segments, $newView);
		} elseif(!in_array($newView, array('speaker','speakers','session','sessions','levels'))) {
			array_unshift($segments, $newView);
		}
	}

	return $segments;
	//@ob_end_clean();var_dump($query);die();
}

function ConferenceParseRoute(&$segments)
{
	$query = array();
	
	global $atsHandleViews;
	
	// Fetch the default query from the active menu item
	$mObject = JFactory::getApplication()->getMenu()->getActive();
	$query = is_object($mObject) ? $mObject->query : array();
	
	if(!array_key_exists('option', $query)) $query['option'] = 'com_conference';
	if(!array_key_exists('view', $query)) $query['view'] = 'sessions';
	$view = $query['view'];
	
	// Replace : with - in segments
	$segments = ConferenceHelperRouter::preconditionSegments($segments);
	
	// Do not process an empty segment list (just in case...)
	if(empty($segments)) return $query;
	
	// Do not process a view I know jack shit about
	if(!in_array($view, $atsHandleViews)) return $query;
	
	// Initialise
	$ticketID = null;
	$catID = null;
	
	// If we have segments and we're in a no-parameters view, we have to deal
	// with a different view than the one listed in the menu.
	if(in_array($view, array('my'))) {
		$view = array_shift($segments);
	}
	// Otherwise, do I have a new ticket view?
	else {
		$lastSegment = array_pop($segments);
		if($lastSegment == 'new') {
		//	$view = 'speaker';
		} else {
			$segments[] = $lastSegment;
		}
	}
	
	// If this is a categories or tickets view, we have to check the last
	// segment to figure out if it's really a "ticket" view.
	if(in_array($view, array('sessions','session','speakers','speaker','levels'))) {
		
		switch($view)
		{
			case 'speakers':
				// Speaker view
				$query['view'] = 'speaker';
				
				$db = JFactory::getDBO();
				$dbquery = $db->getQuery(true)
					->select('conference_speaker_id')
					->from($db->qn('#__conference_speakers'))
					->where($db->qn('slug').' = '.$db->q($segments[0]));
				$db->setQuery($dbquery);
				$id = $db->loadResult();
				$query['id'] = $id;
				break;
		

			
			case 'sessions':
				// Speaker view
				$query['view'] = 'session';
				
				$db = JFactory::getDBO();
				$dbquery = $db->getQuery(true)
					->select('conference_session_id')
					->from($db->qn('#__conference_sessions'))
					->where($db->qn('slug').' = '.$db->q($segments[0]))
					->where($db->qn('enabled').' = 1');
				$db->setQuery($dbquery);
				$id = $db->loadResult();
				$query['id'] = $id;
				break;
		}

	}

	return $query;
}