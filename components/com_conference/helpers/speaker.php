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
class ConferenceHelperSpeaker
{
	/**
	 * Uses the ATS plugins to fetch ana avatar for the user
	 * 
	 * @param JUser $user The user for which to fetch an avatar for
	 * @param int $size The size (in pixels), defaults to 64
	 * @return string The URL to the avatar image
	 */
	public static function speakerid($user_id)
	{		
		$speaker = FOFModel::getTmpInstance('Speakers', 'ConferenceModel')
			->user_id($user_id)
			->getFirstItem();
			
		$speakerID = $speaker->conference_speaker_id;
					
		return $speakerID;
	}
}