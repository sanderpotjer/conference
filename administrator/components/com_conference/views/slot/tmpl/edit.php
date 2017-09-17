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

JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen');
?>
<div class="conference">
    <form action="index.php?option=com_conference&view=slot" method="post" name="adminForm" id="adminForm" class="form form-horizontal">
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="conference_slot_id" value="<?php echo $this->item->conference_slot_id ?>" />
        <input type="hidden" name="<?php echo JFactory::getSession()->getFormToken();?>" value="1" />

        <!-- Start row -->
        <div class="row-fluid">
            <!-- Start left -->
            <div class="span7">
				<?php echo $this->form->renderField('conference_day_id'); ?>
				<?php echo $this->form->renderField('start_time'); ?>
				<?php echo $this->form->renderField('end_time'); ?>
				<?php echo $this->form->renderField('general'); ?>
            </div>
            <!-- End left -->

            <!-- Start right -->
            <div class="span5">
                <div class="well iteminfo">
					<?php echo $this->form->renderField('enabled'); ?>
					<?php echo $this->form->renderField('created_by'); ?>
					<?php echo $this->form->renderField('created_on'); ?>
					<?php echo $this->form->renderField('modified_by'); ?>
					<?php echo $this->form->renderField('modified_on'); ?>
                </div>
            </div>
            <!-- End right -->
        </div>
        <!-- End row -->
    </form>
</div>
<?php
if (0) :
?>

<div class="conference">
	<form action="index.php" method="post" name="adminForm" id="adminForm" class="form form-horizontal">
		<input type="hidden" name="option" value="com_conference" />
		<input type="hidden" name="view" value="slot" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="conference_slot_id" value="<?php echo $this->item->conference_slot_id ?>" />
		<input type="hidden" name="<?php echo JFactory::getSession()->getFormToken();?>" value="1" />

		<!-- Start row -->
		<div class="row-fluid">
			<!-- Start left -->
			<div class="span7">
				<div class="control-group">
					<label for="conference_day_id" class="control-label">
						<?php echo JText::_('COM_CONFERENCE_FIELD_DAY'); ?>
					</label>
					<div class="controls">
						<?php echo ConferenceHelperSelect::days($this->item->conference_day_id); ?>
					</div>
				</div>
				<div class="control-group">
					<label for="start_time" class="control-label">
						<?php echo JText::_('COM_CONFERENCE_FIELD_TIME_START'); ?>
					</label>
					<div class="controls">
						<input type="text" name="start_time" id="start_time" value="<?php echo JHtml::_('date', $this->item->start_time, 'H:i'); ?>"/>
					</div>
				</div>
				<div class="control-group">
					<label for="end_time" class="control-label">
						<?php echo JText::_('COM_CONFERENCE_FIELD_TIME_END'); ?>
					</label>
					<div class="controls">
						<input type="text" name="end_time" id="end_time" value="<?php echo JHtml::_('date', $this->item->end_time, 'H:i'); ?>"/>
					</div>
				</div>
				<div class="control-group">
					<label for="general" class="control-label">
						<?php echo JText::_('COM_CONFERENCE_FIELD_GENERAL'); ?>
					</label>
					<div class="controls">
						<?php echo ConferenceHelperSelect::booleanlist('general','',$this->item->general); ?>
					</div>
				</div>
			</div>
			<!-- End left -->


		</div>
		<!-- End row -->
	</form>
</div>
<?php
endif;