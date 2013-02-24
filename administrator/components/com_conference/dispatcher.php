<?php
/*
 * @package		Conference Schedule Manager
 * @copyright	Copyright (c) 2013 Sander Potjer / sanderpotjer.nl
 * @license		GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

class ConferenceDispatcher extends FOFDispatcher
{
	public $defaultView = 'sessions';
	
	public function onBeforeDispatch() {
		$result = parent::onBeforeDispatch();
		
		if($result) {
			// Load js & css
			$doc = JFactory::getDocument();
			$doc->addStyleSheet(JURI::root(true).'/media/com_conference/css/backend.css');

			JHTML::_('formbehavior.chosen', 'select');
		}
		
		return $result;
	}
}