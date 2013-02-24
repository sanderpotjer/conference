<?php
/*
 * @package		Conference Schedule Manager
 * @copyright	Copyright (c) 2013 Sander Potjer / sanderpotjer.nl
 * @license		GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Avatar display helper class
 */
class ConferenceHelperLevels
{
	/**
	 * Uses the ATS plugins to fetch ana avatar for the user
	 * 
	 * @param JUser $user The user for which to fetch an avatar for
	 * @param int $size The size (in pixels), defaults to 64
	 * @return string The URL to the avatar image
	 */
	public static function sessions($conference_level_id)
	{		
		$sessions = FOFModel::getTmpInstance('Sessions', 'ConferenceModel')
			->limit(0)
			->limitstart(0)
			->enabled(1)
			->level($conference_level_id)
			->filter_order('title')
			->filter_order_Dir('ASC')
			->getList();
					
		return $sessions;
	}
}