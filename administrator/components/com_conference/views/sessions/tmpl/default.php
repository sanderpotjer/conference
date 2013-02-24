<?php
/*
 * @package		Conference Schedule Manager
 * @copyright	Copyright (c) 2013 Sander Potjer / sanderpotjer.nl
 * @license		GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

// Load the helpers
$this->loadHelper('select');
$this->loadHelper('format');
?>
<div class="conference">
	<form name="adminForm" id="adminForm" action="index.php" method="post">
		<input type="hidden" name="option" id="option" value="com_conference" />
		<input type="hidden" name="view" id="view" value="sessions" />
		<input type="hidden" name="task" id="task" value="browse" />
		<input type="hidden" name="boxchecked" id="boxchecked" value="0" />
		<input type="hidden" name="hidemainmenu" id="hidemainmenu" value="0" />
		<input type="hidden" name="filter_order" id="filter_order" value="<?php echo $this->lists->order ?>" />
		<input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $this->lists->order_Dir ?>" />
		<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken();?>" value="1" />
	
		<table class="adminlist table table-striped">
			<thead>
				<tr>
					<th width="20">
						<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ) + 1; ?>);" />
					</th>
					<th width="20">
						<?php echo JHTML::_('grid.sort', 'COM_CONFERENCE_FIELD_STATUS', 'enabled', $this->lists->order_Dir, $this->lists->order); ?>
					</th>
					<th>
						<?php echo JHTML::_('grid.sort', 'COM_CONFERENCE_FIELD_TITLE', 'title', $this->lists->order_Dir, $this->lists->order) ?>
					</th>
					<th width="10%">
						<?php echo JHTML::_('grid.sort', 'COM_CONFERENCE_FIELD_SPEAKER', 'conference_speaker_id', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
					</th>
					<th width="10%">
						<?php echo JHTML::_('grid.sort', 'COM_CONFERENCE_FIELD_LEVEL', 'conference_level_id', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
					</th>
					<th width="10%">
						<?php echo JHTML::_('grid.sort', 'COM_CONFERENCE_FIELD_ROOM', 'conference_room_id', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
					</th>
					<th width="10%">
						<?php echo JHTML::_('grid.sort', 'COM_CONFERENCE_FIELD_SLOT', 'start_time', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
					</th>
					<th class="center" width="7%">
						<?php echo JText::_('COM_CONFERENCE_FIELD_DESCRIPTION') ?>
					</th>
					<th class="center" width="7%">
						<?php echo JText::_('COM_CONFERENCE_FIELD_SLIDES') ?>
					</th>
					<th class="center" width="7%">
						<?php echo JText::_('COM_CONFERENCE_FIELD_VIDEO') ?>
					</th>
					
				</tr>
				<tr>
					<td></td>
					<td class="center">
						<?php echo ConferenceHelperFormat::enabled($this->escape($this->getModel()->getState('enabled',''))); ?>
					</td>
					<td>
						<?php echo ConferenceHelperFormat::search($this->escape($this->getModel()->getState('title',''))); ?>
					</td>
					<td>
						<?php echo ConferenceHelperSelect::speakers($this->getModel()->getState('speaker',''), 'speaker', array('onchange'=>'this.form.submit();', 'class'=>'input-medium')) ?>
					</td>
					<td>
						<?php echo ConferenceHelperSelect::levels($this->getModel()->getState('level',''), 'level', array('onchange'=>'this.form.submit();', 'class'=>'input-small')) ?>
					</td>
					<td>
						<?php echo ConferenceHelperSelect::rooms($this->getModel()->getState('room',''), 'room', array('onchange'=>'this.form.submit();', 'class'=>'input-medium')) ?>
					</td>
					<td>
						<?php echo ConferenceHelperSelect::days($this->getModel()->getState('day',''), 'day', array('onchange'=>'this.form.submit();', 'class'=>'input-medium')) ?>
					</td>
					<td></td>
					<td></td>
					<td></td>
				</tr>
			</thead>
			
			<tfoot>
				<tr>
					<td colspan="20">
						<?php if($this->pagination->total > 0) echo $this->pagination->getListFooter() ?>	
					</td>
				</tr>
			</tfoot>
			
			<tbody>
			<?php if($count = count($this->items)): ?>
			<?php $i = -1; $m = 1; ?>
				<?php foreach ($this->items as $item) : ?>
				<?php
					$i++; $m = 1-$m;
					$checkedOut = ($item->locked_by != 0);
					$ordering = $this->lists->order == 'ordering';
					$item->published = $item->enabled;
				?>
				<tr class="<?php echo 'row'.$m; ?>">
					<td>
						<?php echo JHTML::_('grid.id', $i, $item->conference_session_id, $checkedOut); ?>
					</td>
					<td class="center">
						<?php echo JHTML::_('jgrid.published', $item->enabled, $i); ?>
					</td>
					<td align="left">					
						<a href="index.php?option=com_conference&view=session&id=<?php echo $item->conference_session_id ?>" class="conferenceitem">
							<strong><?php echo $this->escape($item->title) ?></strong>
						</a>
					</td>
					<td>
						<?php $speakers = ConferenceHelperFormat::speakers($item->conference_speaker_id); ?>
						<?php foreach($speakers as $speaker) :?>
							<a href="index.php?option=com_conference&view=speaker&id=<?php echo $speaker->conference_speaker_id ?>" class="subslevel"><?php echo(trim($speaker->title));?></a><br/>
						<?php endforeach;?>
					</td>
					<td class="center">
						<?php if($item->conference_level_id):?>
						<a href="index.php?option=com_conference&view=level&id=<?php echo $item->conference_level_id ?>" class="subslevel">
							<span class="label <?php echo $item->level_label ?>"><?php echo $this->escape($item->level) ?></span>
						</a>
						<?php endif;?>
					</td>
					<td>
						<a href="index.php?option=com_conference&view=room&id=<?php echo $item->conference_room_id ?>" class="subslevel">
							<?php echo $this->escape($item->room)?>
						</a>
					</td>
					<td>
						<?php if($item->day):?>
						<a href="index.php?option=com_conference&view=day&id=<?php echo $item->day_id ?>" class="subslevel">
							<?php echo $this->escape($item->day)?>
						</a><br/>
						<span aria-hidden="true" class="icon-clock"></span> 
						<a href="index.php?option=com_conference&view=time&id=<?php echo $item->conference_time_id ?>" class="subslevel">
							<?php echo JHtml::_('date', $item->start_time, JText::_('H:i'))?> - <?php echo JHtml::_('date', $item->end_time, JText::_('H:i'))?>
						</a>
						<?php endif;?>
					</td>
					<td class="center">
						<?php if($item->description): ?>
						<span class="badge badge-success"><span class="icon-checkmark"></span></span>
						<?php else:?>
						<span class="badge badge-important"><span class="icon-delete"></span></span>
						<?php endif;?>
					</td>
					<td class="center">
						<?php if($item->slides): ?>
						<span class="badge badge-success"><span class="icon-checkmark"></span></span>
						<?php else:?>
						<span class="badge badge-important"><span class="icon-delete"></span></span>
						<?php endif;?>
					</td>
					<td class="center">
						<?php if($item->video): ?>
						<span class="badge badge-success"><span class="icon-checkmark"></span></span>
						<?php else:?>
						<span class="badge badge-important"><span class="icon-delete"></span></span>
						<?php endif;?>
					</td>
				</tr>
				<?php endforeach; ?>
			<?php else: ?>
				<tr>
					<td colspan="20">
						<?php echo  JText::_('COM_CONFERENCE_NORECORDS') ?>
					</td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
	</form>
</div>