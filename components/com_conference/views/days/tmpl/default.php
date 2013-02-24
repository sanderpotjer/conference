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
$this->loadHelper('schedule');

?>

<div class="conference schedule">
	<div class="row-fluid">
		<h1 class="pull-left">Programma overzicht</h1>
	</div>

	<ul class="nav nav-tabs">
		<?php if(!empty($this->items)) foreach($this->items as $i=>$item):?>
		<li class="<?php if($i==0):?>active<?php endif;?>"><a href="#<?php echo $item->slug ?>" data-toggle="tab"><?php echo $item->title ?></a></li>
		<?php endforeach;?>
	</ul>

	<div class="tab-content">
		<?php if(!empty($this->items)) foreach($this->items as $i=>$item):?>
		<div class="tab-pane <?php if($i==0):?>active<?php endif;?>" id="<?php echo $item->slug ?>">
			<?php 
				$slots = ConferenceHelperSchedule::slots($item->conference_day_id); 
				$rooms = ConferenceHelperSchedule::rooms(); 
			?>
			<table class="table table-bordered table-striped">
				<thead>
					<tr>
						<th width="10%"></th>
						<?php if(!empty($rooms)) foreach($rooms as $room):?>
						<th width="<?php echo(90/count($rooms));?>%"><?php echo $room->title ?></th>
						<?php endforeach;?>
					</tr>
				</thead>
				
				<tbody>
					<?php if(!empty($slots)) foreach($slots as $slot):?>
					<?php if($slot->general):?>
					<tr class="info">
						<td><?php echo JHtml::_('date', $slot->start_time , JText::_('H:i')); ?></td>
						<td colspan="<?php echo(count($rooms));?>">
							<?php if(isset($this->sessions[$slot->conference_slot_id][ConferenceHelperSchedule::generalroom()])) :?>
							<?php $session = $this->sessions[$slot->conference_slot_id][ConferenceHelperSchedule::generalroom()];?>
							<?php echo $session->title ?>
							<?php endif;?>
						</td>
					</tr>
					<?php else:?>
					<tr>
						<td><?php echo JHtml::_('date', $slot->start_time , JText::_('H:i')); ?></td>
						<?php if(!empty($rooms)) foreach($rooms as $room):?>
						<td>
							<?php if(isset($this->sessions[$slot->conference_slot_id][$room->conference_room_id])) :?>
							<?php $session = $this->sessions[$slot->conference_slot_id][$room->conference_room_id];?>
								<?php if($session->level):?>
								<a href="<?php echo JRoute::_('index.php?option=com_conference&view=levels')?>"><span class="label <?php echo $session->level_label ?>">
									<?php echo $session->level ?>
								</span></a><br/>
								<?php endif;?>
							<div class="session">
								<?php if($session->listview): ?>
									<a href="<?php echo JRoute::_('index.php?option=com_conference&view=session&id='.$session->conference_session_id)?>"><?php echo $session->title ?></a>
								<?php else:?>
									<?php echo $session->title ?>
								<?php endif;?>
								<?php if($session->language == 'en'): ?><img class="lang" src="media/mod_languages/images/en.gif"/><?php endif; ?>
							</div>
							<?php if($session->conference_speaker_id):?>								
								<?php $speakers = ConferenceHelperFormat::speakers($session->conference_speaker_id); ?>
								<?php if(!empty($speakers)):?>
								<?php 
									$sessionspeakers = array();
									foreach($speakers as $speaker) :
										if($speaker->enabled) {
											$sessionspeakers[] = '<span class="icon-user"></span> <a href="index.php?option=com_conference&view=speaker&id='.$speaker->conference_speaker_id.'">'.trim($speaker->title).'</a>';
										} else {
											$sessionspeakers[] = '<span class="icon-user"></span> '.trim($speaker->title);
										}
									endforeach;
								?>
								<div class="speaker">
									<?php echo implode('<br/> ', $sessionspeakers); ?>
								</div>
								<?php endif;?>
							<?php endif;?>
							<?php endif;?>
						</td>
						<?php endforeach;?>
					</tr>
					<?php endif;?>
					<?php endforeach;?>
				</tbody>
			</table>
		</div>
		<?php endforeach;?>
	</div>
</div>
