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
		<input type="hidden" name="view" id="view" value="slots" />
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
						<?php echo JHTML::_('grid.sort', 'COM_CONFERENCE_FIELD_DAY', 'day', $this->lists->order_Dir, $this->lists->order) ?>
					</th>
					<th>
						<?php echo JHTML::_('grid.sort', 'JDATE', 'day', $this->lists->order_Dir, $this->lists->order) ?>
					</th>
					<th class="center" width="12%">
						<?php echo JHTML::_('grid.sort', 'COM_CONFERENCE_FIELD_TIME_START', 'start_time', $this->lists->order_Dir, $this->lists->order) ?>
					</th>
					<th class="center" width="12%">
						<?php echo JHTML::_('grid.sort', 'COM_CONFERENCE_FIELD_TIME_END', 'end_time', $this->lists->order_Dir, $this->lists->order) ?>
					</th>
					<th class="center" width="12%">
						<?php echo JText::_('COM_CONFERENCE_FIELD_GENERAL') ?>
					</th>
					<th class="center" width="12%">
						<?php echo JText::_('COM_CONFERENCE_TITLE_SESSIONS') ?>
					</th>
				</tr>
				<tr>
					<td></td>
					<td class="center">
						<?php echo ConferenceHelperFormat::enabled($this->escape($this->getModel()->getState('enabled',''))); ?>
					</td>
					<td>
						<?php echo ConferenceHelperSelect::days($this->getModel()->getState('day',''), 'day', array('onchange'=>'this.form.submit();', 'class'=>'input-medium')) ?>
					</td>
					<td></td>
					<td></td>
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
						<?php echo JHTML::_('grid.id', $i, $item->conference_slot_id, $checkedOut); ?>
					</td>
					<td class="center">
						<?php echo JHTML::_('jgrid.published', $item->enabled, $i); ?>
					</td>
					<td align="left">						
						<a href="index.php?option=com_conference&view=slot&id=<?php echo $item->conference_slot_id ?>" class="conferenceitem">
							<strong><?php echo $this->escape($item->day) ?></strong>
						</a>
					</td>
					<td>
						<?php echo JHtml::_('date', $item->date, JText::_('DATE_FORMAT_LC')); ?>
					</td>
					<td class="center">
						<?php echo JHtml::_('date', $item->start_time, 'H:i'); ?>
					</td>
					<td class="center">
						<?php echo JHtml::_('date', $item->end_time, 'H:i'); ?>
					</td>
					<td class="center">
						<?php if($item->general): ?>General<?php endif;?>
					</td>
					<td class="center">
						<a href="index.php?option=com_conference&view=sessions&speaker=&level=&room=&slot=<?php echo $item->conference_slot_id ?>&day=">
						<?php
							echo FOFModel::getTmpInstance('Sessions','ConferenceModel')
								->slot($item->conference_slot_id)
								->getTotal();
						?> <?php echo  JText::_('COM_CONFERENCE_TABLE_SESSIONS') ?>
						</a>
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