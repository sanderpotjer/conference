<?php
/*
 * @package		Conference Schedule Manager
 * @copyright	Copyright (c) 2013 Sander Potjer / sanderpotjer.nl
 * @license		GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

$this->loadHelper('cparams');
$this->loadHelper('modules');
$this->loadHelper('format');
$this->loadHelper('message');
$this->loadHelper('levels');

// Get the Itemid
$itemId = FOFInput::getInt('Itemid',0,$this->input);
if($itemId != 0) {
	$actionURL = 'index.php?Itemid='.$itemId;
} else {
	$actionURL = 'index.php';
}

$filter_status = $this->getModel()->getState('status','');

?>

<div class="conference levels">
	<div class="row-fluid">
		<h1>Niveaus</h1>
	</div>
	<?php if(!empty($this->items)) foreach($this->items as $item):?>
	<div class="well well-small">
		<div class="row-fluid">
			<div class="span12">
				<span class="label <?php echo $item->label ?>"><?php echo $this->escape($item->title)?></span>
				<?php echo($item->description);?>
				 <?php $sessions = ConferenceHelperLevels::sessions($item->conference_level_id);?>
				<?php if(!empty($sessions)) :?>
				<table class="table table-bordered table-striped">
	              <thead>
	                <tr>
	                  <th>Presentatie</th>
	             
	                  <th width="30%">Spreker</th>
	                  <th width="25%">Tijd</th>
	                </tr>
	              </thead>
	              <tbody>
	              <?php foreach($sessions as $session):?>
	                <tr>
	                  <td>
		                <?php if($session->listview): ?>
		                  	<a href="<?php echo JRoute::_('index.php?option=com_conference&view=session&id='.$session->conference_session_id)?>">
		                  		<?php echo($session->title)?>
		                  	</a>
		                  <?php else:?>
		                  	<?php echo($session->title)?>
		                  <?php endif;?>
		                  <?php if($session->language == 'en'): ?><img class="lang" src="media/mod_languages/images/en.gif"/><?php endif; ?>
	                  	</td>
	                  	<td>
	                  	<?php $speakers = ConferenceHelperFormat::speakers($session->conference_speaker_id); ?>
						<?php if(!empty($speakers)):?>
						<?php 
							$sessionspeakers = array();
							foreach($speakers as $speaker) :
								if($speaker->enabled) {
									$sessionspeakers[] = '<a href="index.php?option=com_conference&view=speaker&id='.$speaker->conference_speaker_id.'">'.trim($speaker->title).'</a>';
								} else {
									$sessionspeakers[] = trim($speaker->title);
								}
							endforeach;
						?>
						<div class="speaker">
							<?php echo implode(', ', $sessionspeakers); ?>
						</div>
						<?php endif;?>
	                  </td>
	                  	<td>
							<?php echo($session->day)?>
							<br/>
							<span aria-hidden="true" class="icon-clock"></span> 
								<?php echo JHtml::_('date', $session->start_time, JText::_('H:i'))?> - <?php echo JHtml::_('date', $session->end_time, JText::_('H:i'))?>
	                  	</td>
	                  
	                </tr>
	               <?php endforeach;?>
	              </tbody>
	            </table>
	            <?php endif;?>
			</div>
		</div>
	</div>
	<?php endforeach;?>
</div>
