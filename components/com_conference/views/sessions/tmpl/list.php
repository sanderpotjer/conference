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

<div class="conference levels">
	<div class="row-fluid">
		<h1><?php echo JText::_('COM_CONFERENCE_TITLE_SESSIONS')?></h1>
	</div>

	<div class="row-fluid">
		<div class="span12">				
			<?php if(!empty($this->items)):?>
			<table class="table table-bordered table-striped">
				<thead>
					<tr>
						<th><?php echo JText::_('COM_CONFERENCE_FIELD_SESSION')?></th>
						<th width="30%"><?php echo JText::_('COM_CONFERENCE_FIELD_SPEAKER')?></th>
						<th width="25%"><?php echo JText::_('COM_CONFERENCE_FIELD_SLOT')?></th>
					</tr>
				</thead>
				<tbody>
				<?php foreach($this->items as $item):?>
					<tr>
						<td>
							<?php echo($item->title)?>
						</td>
						<td>
							<?php if(!empty($item->speakers)):?>
							<?php 
								$sessionspeakers = array();

								foreach($item->speakers as $speaker)
								{
									$sessionspeakers[] = trim($speaker->title);
								}
								?>
								<div class="speaker">
									<?php echo implode(', ', $sessionspeakers); ?>
								</div>
							<?php endif;?>
						</td>
						<td>
							<?php if($item->slides):?>
							<a href="#slides<?php echo($item->conference_session_id)?>" role="button" class="btn" data-toggle="modal">Slides</a>
							
							<!-- Start Modal -->
							<div id="slides<?php echo($item->conference_session_id)?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
							  <div class="modal-header">
							    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
							    <h3 id="myModalLabel"><?php echo($item->title)?></h3>
							  </div>
							  <div class="modal-body">
							    <?php echo($item->slides)?>
							  </div>
							  <div class="modal-footer">
							    <button class="btn btn-primary" data-dismiss="modal" aria-hidden="true">Close</button>
							  </div>
							</div>
							<!-- End Modal -->
							<?php endif;?>
						</td>
					</tr>
				<?php endforeach;?>
				</tbody>
			</table>
			<?php endif;?>
		</div>
	</div>

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