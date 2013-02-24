<?php
/*
 * @package		Conference Schedule Manager
 * @copyright	Copyright (c) 2013 Sander Potjer / sanderpotjer.nl
 * @license		GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

class ConferenceTableSession extends FOFTable
{
	public function check() {
	
		$result = true;
		
		// Make sure assigned subscriptions really do exist and normalize the list
		if(!empty($this->conference_speaker_id)) {
			if(is_array($this->conference_speaker_id)) {
				$subs = $this->conference_speaker_id;
			} else {
				$subs = explode(',', $this->conference_speaker_id);
			}
			if(empty($subs)) {
				$this->conference_speaker_id = '';
			} else {
				$subscriptions = array();
				foreach($subs as $id) {
					$subObject = FOFModel::getTmpInstance('Speakers','ConferenceModel')
						->setId($id)
						->getItem();
					$id = null;
					if(is_object($subObject)) {
						if($subObject->conference_speaker_id > 0) {
							$id = $subObject->conference_speaker_id;
						}
					}
					if(!is_null($id)) $subscriptions[] = $id;
				}
				$this->conference_speaker_id = implode(',', $subscriptions);
			}
		}
		
		return $result;
	}
}