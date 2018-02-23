<?php
/**
 * @package     Conference
 *
 * @author      Stichting Sympathy <info@stichtingsympathy.nl>
 * @copyright   Copyright (C) 2013 - [year] Stichting Sympathy. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://stichtingsympathy.nl
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

$params = JComponentHelper::getParams('com_conference');
?>

<div class="conference schedule">
	<div class="row-fluid">
		<h1><?php echo Text::_('COM_CONFERENCE_DAYS_TITLE')?></h1>
	</div>
	
	<div class="row-fluid">
		<ul class="nav nav-tabs">
		<?php if (!empty($this->items)) foreach ($this->items as $i=>$item):?>
			<li class="<?php if ($i==0):?>active<?php endif;?>">
				<a href="#<?php echo $item->slug ?>" data-toggle="tab"><?php echo $item->title ?></a>
			</li>
		<?php endforeach;?>
		</ul>
		<div class="tab-content">
			<?php if (!empty($this->items)) foreach ($this->items as $i=>$item):?>
			<div class="tab-pane <?php if ($i==0):?>active<?php endif;?>" id="<?php echo $item->slug ?>">
				<table class="table table-bordered table-striped">
					<thead class="hidden-phone">
						<tr>
							<th width="10%"></th>
							<?php if (!empty($this->rooms)) foreach ($this->rooms as $room):?>
							<th width="<?php echo(90/count($this->rooms));?>%"><?php echo $room->title ?></th>
							<?php endforeach;?>
						</tr>
					</thead>
					
					<tbody>
						<?php if (!empty($item->slots)) foreach ($item->slots as $slot) : ?>
						<?php if ($slot->general):?>
						<tr class="info">
							<td class="hidden-phone"><?php echo HTMLHelper::_('date', $slot->start_time ,'H:i'); ?></td>
							<td colspan="<?php echo(count($this->rooms));?>">
								<span class="visible-phone">
									<?php echo HTMLHelper::_('date', $slot->start_time ,'H:i'); ?>: 
								</span>
								<?php if (isset($this->sessions[$slot->conference_slot_id][$this->generalRoom])) :?>
								<?php $session = $this->sessions[$slot->conference_slot_id][$this->generalRoom];?>
								<?php if ($session->listview): ?>
									<a href="<?php echo Route::_('index.php?option=com_conference&view=session&id=' . $session->conference_session_id); ?>"><?php echo $session->title; ?></a>
								<?php else:?>
									<?php echo $session->title ?>
								<?php endif;?>
								
								<?php endif;?>
							</td>
						</tr>
						<?php else:?>
						<tr>
							<td><?php echo HTMLHelper::_('date', $slot->start_time ,'H:i'); ?></td>
							<?php if (!empty($this->rooms)) foreach ($this->rooms as $room):?>
							<?php if (isset($this->sessions[$slot->conference_slot_id][$room->conference_room_id])) :?>
								<td>
								<?php $session = $this->sessions[$slot->conference_slot_id][$room->conference_room_id];?>
									<span class="visible-phone roomname">
										 <?php echo $room->title ?>
									</span>
									<?php if ($session->level):?>
									<a href="<?php echo Route::_('index.php?option=com_conference&view=levels')?>"><span class="label <?php echo $session->level_label ?>">
										<?php echo $session->level ?>
									</span></a><br/>
									<?php endif;?>
								<div class="session">
									<?php if ($session->listview): ?>
										<?php if ($session->slides): ?>
											<span class="icon-grid-view" rel="tooltip"  data-original-title="<?php echo Text::_('COM_CONFERENCE_SLIDES_AVAILABLE')?>"></span>
										<?php endif;?>
										<a href="<?php echo Route::_('index.php?option=com_conference&view=session&id=' . $session->conference_session_id)?>"><?php echo $session->title ?></a>
									<?php else:?>
										<?php echo $session->title ?>
									<?php endif;?>
									<?php if ($params->get('language', 0)): ?>
										<?php if ($session->language == 'en'): ?>
											<img class="lang" src="media/mod_languages/images/<?php echo($session->language)?>.gif"/>
										<?php endif; ?>
									<?php endif; ?>
								</div>
								<?php if ($session->speakers):?>
									<?php
										$sessionspeakers = array();

										foreach ($session->speakers as $speaker)
										{
											if ($speaker->enabled)
											{
												$sessionspeakers[] = '<span class="icon-user"></span> <a href="index.php?option=com_conference&view=speaker&id=' . $speaker->conference_speaker_id . '">' . trim($speaker->title) . '</a>';
											}
											else
											{
												$sessionspeakers[] = '<span class="icon-user"></span> ' . trim($speaker->title);
											}
										}
									?>
									<div class="speaker">
										<small><?php echo implode('<br/> ', $sessionspeakers); ?></small>
									</div>
								<?php endif;?>
							</td>
							<?php else:?>
							<td class="hidden-phone"></td>
							<?php endif;?>
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
</div>
