<?php
/**
 * @package     Conference
 *
 * @author      Stichting Sympathy <info@stichtingsympathy.nl>
 * @copyright   Copyright (C) 2013 - [year] Stichting Sympathy. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://stichtingsympathy.nl
 */

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

$params = ComponentHelper::getParams('com_conference');
?>

<div class="conference levels">
	<div class="row-fluid">
		<h1><?php echo Text::_('COM_CONFERENCE_LEVELS_TITLE')?></h1>
	</div>
	
	<?php if (!empty($this->items)) foreach ($this->items as $item):?>
	<div class="well well-small">
		<div class="row-fluid">
			<div class="span12">
				<span class="label <?php echo $item->label ?>"><?php echo $this->escape($item->title)?></span>
				<?php echo($item->description);?>
				<?php if (!empty($item->sessions)) :?>
				<table class="table table-bordered table-striped">
					<thead>
						<tr>
							<th><?php echo JText::_('COM_CONFERENCE_FIELD_SESSION')?></th>
							<th width="30%"><?php echo Text::_('COM_CONFERENCE_FIELD_SPEAKER')?></th>
							<th width="25%"><?php echo Text::_('COM_CONFERENCE_FIELD_SLOT')?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($item->sessions as $session) : ?>
						<tr>
							<td>
								<?php if ($session->listview): ?>
									<a href="<?php echo Route::_('index.php?option=com_conference&view=session&id='.$session->conference_session_id)?>"><?php echo($session->title)?></a>
								<?php else:?>
									<?php echo($session->title)?>
								<?php endif;?>
								<?php if ($params->get('language',0)): ?>
									<?php if ($session->language == 'en'): ?>
										<img class="lang" src="media/mod_languages/images/<?php echo($session->language)?>.gif"/>
									<?php endif; ?>
								<?php endif; ?>
							</td>
							<td>
								<?php if (!empty($session->speakers)):?>
								<?php 
									$sessionspeakers = array();

									foreach ($session->speakers as $speaker)
									{
										if ($speaker->enabled)
										{
											$sessionspeakers[] = '<a href="index.php?option=com_conference&view=speaker&id=' . $speaker->conference_speaker_id . '">' . trim($speaker->title) . '</a>';
										}
										else
										{
											$sessionspeakers[] = trim($speaker->title);
										}
									}
									?>
									<div class="speaker">
										<?php echo implode(', ', $sessionspeakers); ?>
									</div>
								<?php endif;?>
							</td>
							<td>
								<?php echo HTMLHelper::_('date', $session->date, 'l j F'); ?>
								<br/>
								<span aria-hidden="true" class="icon-clock"></span> 
								<?php echo HTMLHelper::_('date', $session->start_time,'H:i')?> - <?php echo HTMLHelper::_('date', $session->end_time, 'H:i')?>
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