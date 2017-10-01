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

/**
 * Session table.
 *
 * @package     Conference
 * @since       1.0
 */
class TableSession extends JTable
{
	/**
	 * Constructor.
	 *
	 * @param   JDatabaseDriver  $db  A database connector object.
	 *
	 * @since   1.0.0
	 */
	public function __construct($db)
	{
		parent::__construct('#__conference_sessions', 'conference_session_id', $db);

		$this->setColumnAlias('published', 'enabled');
	}

	public function checkX() {
	
		$result = true;
		
		// Make sure assigned speaker really exists and normalize the list
		if(!empty($this->conference_speaker_id)) {
			if(is_array($this->conference_speaker_id)) {
				$sprs = $this->conference_speaker_id;
			} else {
				$sprs = explode(',', $this->conference_speaker_id);
			}
			if(empty($sprs)) {
				$this->conference_speaker_id = '';
			} else {
				$speakers = array();
				foreach($sprs as $id) {
					$subObject = FOFModel::getTmpInstance('Speakers','ConferenceModel')
						->setId($id)
						->getItem();
					$id = null;
					if(is_object($subObject)) {
						if($subObject->conference_speaker_id > 0) {
							$id = $subObject->conference_speaker_id;
						}
					}
					if(!is_null($id)) $speakers[] = $id;
				}
				$this->conference_speaker_id = implode(',', $speakers);
			}
		}
		
		return $result;
	}
}