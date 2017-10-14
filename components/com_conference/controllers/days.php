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

/**
 * Days controller.
 *
 * @package  Conference
 * @since    1.0
 */
class ConferenceControllerDays extends JControllerLegacy
{
	public function onBeforeRead() {
		$params = JFactory::getApplication()->getPageParameters('com_conference');
		$this->getThisView()->assign('pageparams',		$params);
					
		$speakers = FOFModel::getTmpInstance('Speakers', 'ConferenceModel')
			->limit(0)
			->limitstart(0)
			->enabled(1)
			->speaker($this->getThisModel()->getItem()->conference_session_id)
			->getList();

		$this->getThisView()->assign('speakers', $speakers);	
		
		return true;
	}

	/**
	 * This runs before the browse() method. Return false to prevent executing
	 * the method.
	 * 
	 * @return bool
	 */
	public function onBeforeBrowse() {
		$result = parent::onBeforeBrowse();
		if($result) {
			// Get the current order by column
			$orderby = $this->getThisModel()->getState('filter_order','');
			// If it's not one of the allowed columns, force it to be the "ordering" column
			if(!in_array($orderby, array('conference_session_id','ordering','title','due'))) {
				$orderby = 'ordering';
			}
			
			// Get the event ID
			$params = JFactory::getApplication()->getPageParameters('com_conference');
			$eventid = $params->get('eventid', 0);
			
			// Apply ordering and filter only the enabled items
			$this->getThisModel()
				->filter_order($orderby)
				->enabled(1)
				->event($eventid)
				->filter_order('date')
				->filter_order_Dir('ASC');
			
			$sessions = FOFModel::getTmpInstance('Sessions', 'ConferenceModel')
				->limit(0)
				->limitstart(0)
				->enabled(1)
				->getList();
				
			$ordered = array();
			foreach($sessions as $session) {
				$ordered[$session->conference_slot_id][$session->conference_room_id] = $session;
			}
				
			$this->getThisView()->assign('sessions', $ordered);	
			
			// Fetch page parameters
			$params = JFactory::getApplication()->getPageParameters('com_conference');
			
			// Push page parameters
			$this->getThisView()->assign('pageparams', $params);
		}
		return $result;
	}
}