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

JHtml::_('formbehavior.chosen');

// Sorting filters
$sortFields = array(
	'enabled' 				=> JText::_('JPUBLISHED'),
	'title'					=> JText::_('COM_CONFERENCE_FIELD_TITLE'),
	'conference_speaker_id'	=> JText::_('COM_CONFERENCE_FIELD_SPEAKER'),
	'conference_status_id'	=> JText::_('COM_CONFERENCE_FIELD_STATUS'),
	'conference_level_id'	=> JText::_('COM_CONFERENCE_FIELD_LEVEL'),
	'conference_room_id'	=> JText::_('COM_CONFERENCE_FIELD_ROOM'),
	'conference_room_id'	=> JText::_('COM_CONFERENCE_FIELD_SLOT'),
	'modified_on'			=> JText::_('JGLOBAL_FIELD_MODIFIED_LABEL'),
);

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>

<form action="<?php echo JRoute::_('index.php?option=com_conference&view=sessions'); ?>" method="post" id="adminForm" name="adminForm" class="form-horizontal">
    <div id="j-main-container" class="span10">
		<?php
		// Search tools bar
		echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
		?>
		<?php if (empty($this->items)) : ?>
            <div class="alert alert-no-items">
				<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
            </div>
		<?php else : ?>
            <table class="adminlist table table-striped">
            <thead>
            <tr>
                <th width="20">
                    <input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->items ) + 1; ?>);" />
                </th>
                <th width="20">
					<?php echo JHtml::_('searchtools.sort', 'COM_CONFERENCE_FIELD_STATUS', 'enabled', $listOrder, $listDirn); ?>
                </th>
                <th width="20">
					<?php echo JHtml::_('searchtools.sort', 'COM_CONFERENCE_FIELD_SHOW_LIST', 'listview', $listOrder, $listDirn); ?>
                </th>
                <th>
					<?php echo JHtml::_('searchtools.sort', 'COM_CONFERENCE_FIELD_TITLE', 'title', $listOrder, $listDirn) ?>
                    /
					<?php echo JHtml::_('searchtools.sort', 'COM_CONFERENCE_FIELD_SPEAKER', 'conference_speaker_id', $listOrder, $listDirn) ?>
                </th>

				<?php if(ConferenceHelperParams::getParam('status',0)): ?>
                    <th width="8%">
						<?php echo JHtml::_('searchtools.sort', 'COM_CONFERENCE_FIELD_STATUS', 'conference_status_id', $listOrder, $listDirn) ?>
                    </th>
				<?php endif;?>
                <th width="8%">
					<?php echo JHtml::_('searchtools.sort', 'COM_CONFERENCE_FIELD_LEVEL', 'conference_level_id', $listOrder, $listDirn) ?>
                </th>
                <th width="10%">
					<?php echo JHtml::_('searchtools.sort', 'COM_CONFERENCE_FIELD_ROOM', 'conference_room_id', $listOrder, $listDirn) ?>
                </th>
                <th width="10%">
					<?php echo JHtml::_('searchtools.sort', 'COM_CONFERENCE_FIELD_SLOT', 'start_time', $listOrder, $listDirn) ?>
                </th>
                <th>
					<?php echo JHtml::_('searchtools.sort', 'COM_CONFERENCE_FIELD_EVENT', 'event', $listOrder, $listDirn) ?>
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
                <th width="7%" class="nowrap">
					<?php echo JHtml::_('searchtools.sort', 'JGLOBAL_FIELD_MODIFIED_LABEL', 'modified_on', $listOrder, $listDirn) ?>
                </th>
            </tr>
            <tr>
                <td></td>
                <td class="center">
					<?php echo ConferenceHelperFormat::enabled($this->escape($this->getModel()->getState('enabled',''))); ?>
                </td>
                <td></td>
                <td>
					<?php echo ConferenceHelperFormat::search($this->escape($this->getModel()->getState('title',''))); ?>
					<?php echo ConferenceHelperSelect::speakers($this->getModel()->getState('speaker',''), 'speaker', array('onchange'=>'this.form.submit();', 'class'=>'input-medium'),true) ?>
                </td>

				<?php if(ConferenceHelperParams::getParam('status',0)): ?>
                    <td>
						<?php echo ConferenceHelperSelect::status($this->getModel()->getState('status',''), 'status', array('onchange'=>'this.form.submit();', 'class'=>'input-small')) ?>
                    </td>
				<?php endif;?>
                <td>
					<?php echo ConferenceHelperSelect::levels($this->getModel()->getState('level',''), 'level', array('onchange'=>'this.form.submit();', 'class'=>'input-small')) ?>
                </td>
                <td>
					<?php echo ConferenceHelperSelect::rooms($this->getModel()->getState('room',''), 'room', array('onchange'=>'this.form.submit();', 'class'=>'input-small')) ?>
                </td>
                <td>
					<?php echo ConferenceHelperSelect::days($this->getModel()->getState('day',''), 'day', array('onchange'=>'this.form.submit();', 'class'=>'input-small')) ?>
                </td>
                <td>
					<?php echo ConferenceHelperSelect::events($this->getModel()->getState('event',''), 'event', array('onchange'=>'this.form.submit();', 'class'=>'input-small')) ?>
                </td>
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
					$ordering = $listDirn == 'ordering';
					$item->published = $item->enabled;
					?>
                    <tr class="<?php echo 'row'.$m; ?>">
                        <td>
							<?php echo JHtml::_('grid.id', $i, $item->conference_session_id, $checkedOut); ?>
                        </td>
                        <td class="center">
							<?php echo JHtml::_('jgrid.published', $item->enabled, $i); ?>
                        </td>
                        <td class="center">
							<?php if($item->listview): ?>
                                <span class="badge badge-success"><span class="icon-checkmark"></span></span>
							<?php else:?>
                                <span class="badge badge-important"><span class="icon-delete"></span></span>
							<?php endif;?>
                        </td>
                        <td align="left">
                            <a href="<?php echo JRoute::_('index.php?option=com_conference&view=session&id='.$item->conference_session_id) ?>" class="conferenceitem">
                                <strong><?php echo $this->escape($item->title) ?></strong>
                            </a>
							<?php if($item->conference_speaker_id):?>
                                <br/><?php echo JText::_('COM_CONFERENCE_FIELD_SPEAKER'); ?>:
								<?php $speakers = ConferenceHelperFormat::speakers($item->conference_speaker_id); ?>
								<?php $i=0;?>
								<?php foreach($speakers as $key=>$speaker) :?>
									<?php if($i > 0):?>,<?php endif;?>
                                    <a href="<?php echo JRoute::_('index.php?option=com_conference&view=speaker&id='.$speaker->conference_speaker_id) ?>"><?php echo(trim($speaker->title));?></a>
									<?php $i++;?>
								<?php endforeach;?>
							<?php endif;?>
                        </td>
						<?php if(ConferenceHelperParams::getParam('status',0)): ?>
                            <td class="center">
								<?php $status = ConferenceHelperFormat::status($item->status); ?>
                                <span class="label <?php echo $status[2] ?>"><?php echo $status[1] ?></span>
                            </td>
						<?php endif;?>
                        <td class="center">
							<?php if($item->conference_level_id):?>
                                <a href="<?php echo JRoute::_('index.php?option=com_conference&view=level&id='.$item->conference_level_id) ?>">
                                    <span class="label <?php echo $item->level_label ?>"><?php echo $this->escape($item->level) ?></span>
                                </a>
							<?php endif;?>
                        </td>
                        <td>
                            <a href="<?php echo JRoute::_('index.php?option=com_conference&view=room&id='.$item->conference_room_id) ?>">
								<?php echo $this->escape($item->room)?>
                            </a>
                        </td>
                        <td>
							<?php if($item->day):?>
                                <a href="<?php echo JRoute::_('index.php?option=com_conference&view=day&id='.$item->day_id) ?>">
									<?php echo $this->escape($item->day)?>
                                </a><br/>
                                <span aria-hidden="true" class="icon-clock"></span>
                                <a href="<?php echo JRoute::_('index.php?option=com_conference&view=slot&id='.$item->conference_slot_id) ?>">
									<?php echo JHtml::_('date', $item->start_time,'H:i')?> - <?php echo JHtml::_('date', $item->end_time, 'H:i')?>
                                </a>
							<?php endif;?>
                        </td>
                        <td align="left">
                            <a href="<?php echo JRoute::_('index.php?option=com_conference&view=event&id='.$item->conference_event_id) ?>" class="conferenceitem">
                                <strong><?php echo $this->escape($item->event) ?></strong>
                            </a>
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
                        <td class="nowrap">
							<?php if($item->modified_on == '0000-00-00 00:00:00'): ?>
                                &mdash;
							<?php else: ?>
								<?php echo JHtml::_('date',$item->modified_on, JText::_('DATE_FORMAT_LC4')); ?>
							<?php endif; ?>
                        </td>
                    </tr>
				<?php endforeach; ?>
			<?php endif; ?>
                </tbody>
                </table>

		<?php endif; ?>

        <input type="hidden" name="task" id="task" value=""/>
        <input type="hidden" name="boxchecked" id="boxchecked" value="0"/>
        <input type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1"/>
</form>
<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
    </div>
