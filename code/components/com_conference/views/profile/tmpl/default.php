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
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

$params         = ComponentHelper::getParams('com_conference');
$profile        = $this->profile;
$editprofileURL = 'index.php?option=com_conference&conference_speaker_id=' . $profile->conference_speaker_id;
$task           = '&task=speaker.edit';

// @todo Check if we need to entertain the add task
if (!$profile->conference_speaker_id)
{
	$task = '&task=speaker.add';
}

$editprofileURL .= $task;
?>

<div class="conference my">
	<div class="row-fluid">
		<div class="span12">
			<h1 class="pull-left">
			<?php echo $profile->title ? $profile->title : Factory::getUser()->name; ?>
			</h1>
			<a class="btn pull-right " href="<?php echo Route::_($editprofileURL)?>">
				<span class="icon-pencil"></span>  <?php echo Text::_('COM_CONFERENCE_MY_EDIT_PROFILE') ?>
			</a>
		</div>
	</div>
	<div class="well well-small">
		<div class="row-fluid">
			<div class="span4">
				<span class="thumbnail">
					<?php if ($profile->image):?>
						<img src="<?php echo Uri::root() . $profile->image?>">
					<?php else:?>
						<img src="http://placehold.it/200x200">
					<?php endif;?>
				</span>
				<div class="speakersocial">
					<?php if (($profile->twitter) && ($params->get('twitter'))):?>
						<a class="btn btn-small btn-block" target="_blank" href="https://twitter.com/<?php echo $profile->twitter?>"><span class="icon conference-twitter"></span> <?php echo $profile->twitter?></a>
					<?php endif;?>
					<?php if (($profile->facebook) && ($params->get('facebook'))):?>
						<a class="btn btn-small btn-block" target="_blank" href="https://facebook.com/<?php echo $profile->facebook?>"><span class="icon conference-facebook"></span> <?php echo $profile->facebook?></a>
					<?php endif;?>
					<?php if (($profile->googleplus) && ($params->get('googleplus'))):?>
						<a class="btn btn-small btn-block" target="_blank" href="https://plus.google.com/<?php echo $profile->googleplus?>"><span class="icon conference-google-plus"></span> <?php echo $profile->title?></a>
					<?php endif;?>
					<?php if (($profile->linkedin) && ($params->get('linkedin'))):?>
						<a class="btn btn-small btn-block" target="_blank" href="https://www.linkedin.com/in/<?php echo $profile->linkedin?>"><span class="icon conference-linkedin"></span> <?php echo $profile->linkedin?></a>
					<?php endif;?>
					<?php if (($profile->website) && ($params->get('website'))):?>
						<a class="btn btn-small btn-block" target="_blank" href="https://<?php echo $profile->website?>"><span class="icon conference-earth"></span> <?php echo $profile->website?></a>
					<?php endif;?>
				</div>
			</div>
			<div class="span8">
				<?php echo ($profile->bio)?>
			</div>
		</div>
	</div>

	<?php if ($profile->conference_speaker_id) : ?>
	<div class="row-fluid">
		<div class="span12">
			<h2 class="pull-left"><?php echo Text::_('COM_CONFERENCE_TITLE_SESSIONS') ?></h2>
			<?php if ($this->canDo->get('core.create')) :?>
			<a class="btn pull-right btn-success" href="<?php echo Route::_('index.php?option=com_conference&task=session.add&layout=edit')?>">
				<span class="icon-plus"></span>  <?php echo Text::_('COM_CONFERENCE_MY_ADD_SESSION') ?>
			</a>
			<?php endif;?>
		</div>
	</div>
	<div class="well well-small">
		<table class="table table-striped">
			<thead>
				<tr>
					<th><?php echo Text::_('COM_CONFERENCE_FIELD_EVENT') ?></th>
					<th width="25%"><?php echo Text::_('COM_CONFERENCE_FIELD_SLOT')?></th>
					<th><?php echo Text::_('COM_CONFERENCE_FIELD_TITLE') ?></th>
					<th width="10%" class="center"><?php echo Text::_('COM_CONFERENCE_FIELD_LEVEL') ?></th>
					<th width="10%" class="center"><?php echo Text::_('COM_CONFERENCE_FIELD_DESCRIPTION') ?></th>
					<th width="10%" class="center"><?php echo Text::_('COM_CONFERENCE_FIELD_SLIDES') ?></th>
				</tr>
			</thead>
			<tbody>
			<?php if (empty($this->sessions)): ?>
			<tr>
				<td colspan="7" class="center">
				<?php echo Text::_('COM_CONFERENCE_NORECORDS') ?>
				</td>
			</tr>
			<?php endif; ?>
			<?php foreach ($this->sessions as $session):?>
				<tr>
					<td>
						<?php echo $session->event ?>
					</td>
					<td>
						<?php echo HTMLHelper::_('date', $session->date, 'l j F'); ?>
						<br/>
						<span aria-hidden="true" class="icon-clock"></span>
						<?php echo HTMLHelper::_('date', $session->start_time,'H:i')?> - <?php echo HTMLHelper::_('date', $session->end_time, 'H:i')?>
					</td>
					<td>
						<?php if ($this->canDo->get('core.edit.own')) :?>
							<a href="<?php echo Route::_('index.php?option=com_conference&task=session.edit&layout=edit&conference_session_id='.$session->conference_session_id)?>"><?php echo $session->title ?></a>
						<?php else:?>
							<?php echo $session->title ?>
						<?php endif;?>
					</td>
					<td class="center">
						<span class="label <?php echo $session->level_label ?>">
							<?php echo $session->level ?>
						</span>
					</td>
					<td class="center">
						<?php if ($session->description): ?>
							<span class="badge badge-success"><span class="icon-checkmark"></span></span>
						<?php else:?>
							<span class="badge badge-important"><span class="icon-delete"></span></span>
						<?php endif;?>
					</td>
					<td class="center">
						<?php if ($session->slides): ?>
							<span class="badge badge-success"><span class="icon-checkmark"></span></span>
						<?php else:?>
							<span class="badge badge-important"><span class="icon-delete"></span></span>
						<?php endif;?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php endif; ?>
</div>