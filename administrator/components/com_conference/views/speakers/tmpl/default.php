<?php
/*
 * @package		Conference Schedule Manager
 * @copyright	Copyright (c) 2013 Sander Potjer / sanderpotjer.nl
 * @license		GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

// Load the helpers
$this->loadHelper('params');
$this->loadHelper('select');
$this->loadHelper('format');

//JHtml::_('bootstrap.tooltip');
JHtml::_('bootstrap.popover');
?>

<div class="conference">
	<form name="adminForm" id="adminForm" action="index.php" method="post">
		<input type="hidden" name="option" id="option" value="com_conference" />
		<input type="hidden" name="view" id="view" value="speakers" />
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
					<th colspan="2">
						<?php echo JHTML::_('grid.sort', 'COM_CONFERENCE_FIELD_NAME', 'title', $this->lists->order_Dir, $this->lists->order) ?>
					</th>
					<th class="center" width="">
					
					</th>
		
					<th class="center" width="">
					
					</th>
					<th class="center" width="7%">
						<?php echo JText::_('COM_CONFERENCE_FIELD_BIO') ?>
					</th>
					<th class="center" width="7%">
						<?php echo JText::_('COM_CONFERENCE_TITLE_SESSIONS') ?>
					</th>
					<th>
						<?php echo JText::_('COM_CONFERENCE_FIELD_EVENT') ?>
					</th>
					<th width="7%" class="nowrap">
						<?php echo JHtml::_('grid.sort', 'JGLOBAL_FIELD_MODIFIED_LABEL', 'modified_on', $this->lists->order_Dir, $this->lists->order, 'browse') ?>
					</th>
				</tr>
				<tr>
					<td></td>
					<td class="center">
						<?php echo ConferenceHelperFormat::enabled($this->escape($this->getModel()->getState('enabled',''))); ?>
					</td>
					<td colspan="2">
						<?php echo ConferenceHelperFormat::search($this->escape($this->getModel()->getState('title',''))); ?>
					</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td>
						<?php echo ConferenceHelperSelect::events($this->getModel()->getState('event',''), 'event', array('onchange'=>'this.form.submit();', 'class'=>'input-medium')) ?>
					</td>
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
						<?php echo JHTML::_('grid.id', $i, $item->conference_speaker_id, $checkedOut); ?>
					</td>
					<td class="center">
						<?php echo JHTML::_('jgrid.published', $item->enabled, $i); ?>
					</td>
					<td align="center" width="60">
						<div class="pull-left">
						<?php if($item->image): ?>
							<img width="50" class="thumbnail" src="<?php echo JURI::root().$item->image?>">
						<?php else:?>
							<img width="50" class="thumbnail" src="http://www.placehold.it/50x50/EFEFEF/AAAAAA&text=no+image" />
						<?php endif;?>
						</div>
					</td>	
					<td align="left">					
						<a href="<?php echo JRoute::_('index.php?option=com_conference&view=speaker&id='.$item->conference_speaker_id) ?>" class="conferenceitem">
							<strong><?php echo $this->escape($item->title) ?></strong>
						</a><br/>
						<?php echo JText::_('JGLOBAL_USERNAME') ?>: <a href="<?php echo JRoute::_('index.php?option=com_users&task=user.edit&id='.$item->user_id)?>"><?php echo JFactory::getUser($item->user_id)->username ?></a>
					</td>
					<td class="left">
						<?php if(ConferenceHelperParams::getParam('twitter',0) && $item->twitter): ?>
							<a class="btn" target="_blank" href="http://twitter.com/<?php echo $this->escape($item->twitter) ?>"><span class="icon conference-twitter"></span></a>
						<?php endif;?>
						<?php if(ConferenceHelperParams::getParam('facebook',0) && $item->facebook): ?>
							<a class="btn" target="_blank" href="http://facebook.com/<?php echo $this->escape($item->facebook) ?>"><span class="icon conference-facebook"></span></a>
						<?php endif;?>
						<?php if(ConferenceHelperParams::getParam('googleplus',0) && $item->googleplus): ?>
							<a class="btn" target="_blank" href="http://plus.google.com/<?php echo $this->escape($item->googleplus) ?>"><span class="icon conference-google-plus"></span></a>
						<?php endif;?>
						<?php if(ConferenceHelperParams::getParam('linkedin',0) && $item->linkedin): ?>
							<a class="btn" target="_blank" href="http://linkedin.com/<?php echo $this->escape($item->linkedin) ?>"><span class="icon conference-linkedin"></span></a>
						<?php endif;?>
						<?php if(ConferenceHelperParams::getParam('website',0) && $item->website): ?>
							<a class="btn" target="_blank" href="http://<?php echo $this->escape($item->website) ?>"><span class="icon conference-earth"></span></a>
						<?php endif;?>
					</td>
					<td>
						<?php if($item->speakernotes): ?>
							<a href="#" class="btn btn-info hasPopover" data-placement="top" data-content="<?php echo($item->speakernotes); ?>"><?php echo JText::_('COM_CONFERENCE_FIELD_NOTES_SPEAKER')?></a>
						<?php endif;?>	
					</td>
					<td class="center">
						<?php if($item->bio): ?>
						<span class="badge badge-success"><i class="icon-checkmark"></i></span>
						<?php else:?>
						<span class="badge badge-important"><i class="icon-delete"></i></span>
						<?php endif;?>
					</td>
					<td class="center">
						<a href="<?php echo JRoute::_('index.php?option=com_conference&view=sessions&speaker='.$item->conference_speaker_id.'&level=&room=&slot=&day=&event=')?>">
						<?php
							echo FOFModel::getTmpInstance('Sessions','ConferenceModel')
								->speaker($item->conference_speaker_id)
								->enabled(1)
								->getTotal();
						?> <?php echo  JText::_('COM_CONFERENCE_TABLE_SESSIONS') ?>
						</a>
					</td>
					<td align="left">
						<?php if($item->conference_event_id):?>						
						<?php $events = ConferenceHelperFormat::events($item->conference_event_id); ?>
						<?php $i=0;?>
						<?php foreach($events as $key=>$event) :?>
							<?php if($i > 0):?><br/><?php endif;?>
							<a href="<?php echo JRoute::_('index.php?option=com_conference&view=event&id='.$event->conference_event_id) ?>"><?php echo(trim($event->title));?></a>
							<?php $i++;?>
						<?php endforeach;?>
						<?php endif;?>
					</td>
					<td class="nowrap">
						<?php if($item->modified_on == '0000-00-00 00:00:00'): ?>
							&mdash;
						<?php else: ?>
							<?php echo JHtml::_('date',$item->modified_on, JText::_('DATE_FORMAT_LC4')); ?>
						<?php endif; ?>
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