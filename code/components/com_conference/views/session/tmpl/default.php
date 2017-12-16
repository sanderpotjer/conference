<?php
/**
 * @package     Conference
 *
 * @author      Stichting Sympathy <info@stichtingsympathy.nl>
 * @copyright   Copyright (C) 2013 - [year] Stichting Sympathy. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://stichtingsympathy.nl
 */

defined('_JEXEC') or die;

$params = JComponentHelper::getParams('com_conference');
?>
<div class="conference">
	<div class="row-fluid">
		<h1>
			<?php echo $this->escape($this->item->title)?>
			<?php if ($this->item->conference_level_id):?>
			&nbsp;<span class="label <?php echo $this->item->level->label ?>">
				<?php echo $this->item->level->title ?>
			</span>
			<?php endif;?>
		</h1>
	</div>
	<div class="well well-small">
		<div class="row-fluid">
			<div class="span4">
				<span class="thumbnail">
					<?php if ($this->item->conference_speaker_id):?>
						<?php foreach($this->item->speakers as $speaker) :?>
						<img src="<?php echo $speaker->image?>">
						<?php endforeach;?>
					<?php else:?>
						<img src="http://placehold.it/200x200">
					<?php endif;?>
				</span>
				<div class="speakersocial">
					<?php 
						$sessionspeakers = array();
						foreach($this->item->speakers as $speaker) :
							if($speaker->enabled):?>
								
								<a class="btn btn-small btn-block" href="index.php?option=com_conference&view=speaker&id=<?php echo $speaker->conference_speaker_id?>"><span class="icon icon-user"></span> <?php echo(trim($speaker->title))?></a>
							<?php else:?>
								<span class="btn btn-small btn-block disabled"><span class="icon icon-user"></span> <?php echo(trim($speaker->title))?></span>
						<?php endif;
						endforeach;
					?>
					<?php if($this->item->conference_room_id):?>
						<span class="btn btn-small btn-block disabled">
							<span class="icon icon-home"></span> <?php echo $this->item->room; ?>
						</span>
					<?php endif;?>
					<?php if($this->item->conference_slot_id):?>
						<a class="btn btn-small btn-block" href="<?php echo JRoute::_('index.php?option=com_conference&view=days')?>">
							<span class="icon icon-clock"></span> <?php echo $this->item->slot; ?>
						</a>
					<?php endif;?>
					<?php if($this->item->conference_level_id):?>
						<a class="btn btn-small btn-block" href="<?php echo JRoute::_('index.php?option=com_conference&view=levels')?>">
							<span class="icon icon-equalizer"></span> <?php echo $this->item->level->title; ?>
						</a>
					<?php endif;?>
					<?php if (($params->get('language',0)) && ($this->item->language)):?>
						<span class="btn btn-small btn-block disabled">
							<span class="icon icon-comments-2"></span> <?php echo $this->item->language; ?>
						</span>
					<?php endif;?>
				</div>
			</div>
			<div class="span8">
				<?php echo ($this->item->description)?>
			</div>
			<?php if ($this->item->slides) : ?>
			<div class="span8">
				<?php echo $this->item->slides; ?>
			</div>
			<?php endif; ?>
			<?php if ($this->item->video) : ?>
				<div class="span8">
					<?php echo $this->item->video; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>