<?php
/*
 * @package		Conference Schedule Manager
 * @copyright	Copyright (c) 2013 Sander Potjer / sanderpotjer.nl
 * @license		GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

class ConferenceHelperLevels
{
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