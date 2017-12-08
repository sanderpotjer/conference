<?php
/**
 * @package     Conference
 *
 * @author      Stichting Sympathy <info@stichtingsympathy.nl>
 * @copyright   Copyright (C) 2013 - [year] Stichting Sympathy. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://joomladagen.nl
 */

defined('_JEXEC') or die;

$params = JComponentHelper::getParams('com_conference');
?>

<div class="conference">
	<div class="row-fluid">
		<h1><?php echo JText::_('COM_CONFERENCE_TITLE_SESSIONS')?></h1>
	</div>
	<?php if(!empty($this->items)) foreach($this->items as $item):?>
	<?php $speaker = isset($item->speakers[0]) ? $item->speakers[0] : new stdClass; ?>
	<div class="well well-small spreker">
		<div class="row-fluid">
			<div class="span4">
				<a href="<?php echo JRoute::_('index.php?option=com_conference&view=session&id=' . $item->conference_session_id)?>" class="thumbnail">
					<?php if (isset($speaker->image) && $speaker->image):?>
						<img src="<?php echo $speaker->image?>">
					<?php else:?>
						<img src="http://placehold.it/200x200">
					<?php endif;?>
				</a>
			</div>
			<div class="span8">
				<h3>
					<a href="<?php echo JRoute::_('index.php?option=com_conference&view=session&id=' . $item->conference_session_id)?>"><?php echo $this->escape($item->title)?></a>
					<?php if($item->level):?>
					&nbsp;<span class="label <?php echo $item->level_label ?>">
						<?php echo $item->level ?>
					</span>
					<?php endif;?>
				</h3>
				<h4><span class="icon-user"></span>
					<?php if(!empty($item->speakers))
					{
						$sessionspeakers = array();

						foreach ($item->speakers as $speaker)
						{
							$sessionspeakers[] = trim($speaker->title);
						}

						echo implode(', ', $sessionspeakers);
					}
					?>
				</h4>
				<?php echo(substr($item->description,0, strpos($item->description, "</p>")+4));?>
				<a class="btn btn-small pull-right" href="<?php echo JRoute::_('index.php?option=com_conference&view=session&id=' . $item->conference_session_id)?>">
					<?php echo JText::_('COM_CONFERENCE_READ_MORE') ?> <?php echo $this->escape($item->title)?>
				</a>
			</div>
		</div>
	</div>
	<?php endforeach;?>

	<div class="row-fluid">
		<form id="conference-pagination" name="conference-pagination" action="<?php echo JRoute::_('index.php?option=com_conference&view=sessions'); ?>" method="post">
			<input type="hidden" name="option" value="com_conference" />
			<input type="hidden" name="view" value="speakers" />
			<?php if ($this->pageparams->get('show_pagination',1)) : ?>
				<?php if($this->pagination->get('pages.total') > 1): ?>
				<div class="pagination">
					<?php if ($this->pageparams->get('show_pagination_results',1)) : ?>
					<p class="counter">
						<?php echo $this->pagination->getPagesCounter(); ?>
					</p>
					<?php endif; ?>
					<?php echo $this->pagination->getPagesLinks(); ?>
				</div>
				<?php endif; ?>
			<?php endif; ?>
		</form>
	</div>
</div>
