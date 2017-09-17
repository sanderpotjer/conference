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

$listOrder  = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));

$loggeduser = JFactory::getUser();
?>
<form action="<?php echo JRoute::_('index.php?option=com_conference&view=levels'); ?>" method="post" id="adminForm" name="adminForm" class="form-horizontal">
    <div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
    </div>
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
            <table class="table table-striped" id="levelsList">
                <thead>
                <tr>
                    <th width="20">
                        <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
                    </th>
                    <th width="1%" class="nowrap center">
						<?php echo JHtml::_('searchtools.sort', 'COM_CONFERENCE_FIELD_STATUS', 'levels.enabled', $listDirn, $listOrder); ?>
                    </th>
                    <th class="title">
						<?php echo JHtml::_('searchtools.sort', 'COM_CONFERENCE_FIELD_TITLE', 'levels.title', $listDirn, $listOrder); ?>
                    </th>
                    <th class="center" width="12%">
		                <?php echo JText::_('COM_CONFERENCE_FIELD_LABEL') ?>
                    </th>
                    <th class="title">
						<?php echo JText::_('COM_CONFERENCE_TITLE_SESSIONS'); ?>
                    </th>
                </tr>
                </thead>
                <tfoot>
                <tr>
                    <td colspan="20">
                        <div class="pull-left">
							<?php
							if ($this->pagination->total > 0)
							{
								echo $this->pagination->getListFooter();
							}
							?>
                        </div>
                        <div class="pull-right"><?php echo $this->pagination->getResultsCounter(); ?></div>
                    </td>
                </tr>
                </tfoot>
                <tbody>
				<?php
				$canEdit    = $this->canDo->get('core.edit');
				$canChange  = $loggeduser->authorise('core.edit.state', 'com_conference');

				foreach ($this->items as $i => $item) :
					$checkedOut = ($item->locked_by != 0);
					?>
                    <tr>
                        <td class="center">
							<?php if ($canEdit || $canChange) : ?>
								<?php echo JHtml::_('grid.id', $i, $item->conference_level_id, $checkedOut); ?>
							<?php endif; ?>
                        </td>
                        <td class="center">
							<?php echo JHtml::_('jgrid.published', $item->enabled, $i, 'events.', $canChange); ?>
                        </td>
                        <td>
                            <div class="name break-word">
								<?php if ($canEdit)
								{
									echo JHtml::_(
										'link',
										JRoute::_('index.php?option=com_conference&task=level.edit&conference_level_id=' . (int) $item->conference_level_id),
										$this->escape($item->title),
										'title="' . JText::sprintf('COM_CONFERENCE_EDIT_LEVEL', $this->escape($item->title)) . '"'
									);
								}
								else
								{
									echo $this->escape($item->title);
								}
								?>
                            </div>
                        </td>
                        <td class="center">
                            <span class="label <?php echo $item->label ?>"><?php echo $this->escape($item->title) ?></span>
                        </td>
                        <td>
							<?php echo $item->sessions; ?>
                        </td>
                    </tr>
				<?php endforeach; ?>
                </tbody>
            </table>
		<?php endif; ?>
    </div>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token'); ?>
</form>

<?php
if (0)
{
	/*
	 * @package		Conference Schedule Manager
	 * @copyright	Copyright (c) 2013-2014 Sander Potjer / sanderpotjer.nl
	 * @license		GNU General Public License version 3 or later
	 */

// No direct access.
	defined('_JEXEC') or die;

// Load the helpers
	$this->loadHelper('select');
	$this->loadHelper('format');

// Sorting filters
	$sortFields = array(
		'enabled' => JText::_('JPUBLISHED'),
		'title'   => JText::_('COM_CONFERENCE_FIELD_TITLE'),
	);
	?>

	<?php if (version_compare(JVERSION, '3.0', 'ge')): ?>
    <script type="text/javascript">
        Joomla.orderTable = function () {
            table = document.getElementById("sortTable");
            direction = document.getElementById("directionTable");
            order = table.options[table.selectedIndex].value;
            if (!order) {
                dirn = 'asc';
            }
            else {
                dirn = direction.options[direction.selectedIndex].value;
            }
            Joomla.tableOrdering(order, dirn);
        }
    </script>
<?php endif; ?>

    <div class="conference">
        <form name="adminForm" id="adminForm" action="index.php" method="post">
            <input type="hidden" name="option" id="option" value="com_conference"/>
            <input type="hidden" name="view" id="view" value="levels"/>
            <input type="hidden" name="task" id="task" value="browse"/>
            <input type="hidden" name="boxchecked" id="boxchecked" value="0"/>
            <input type="hidden" name="hidemainmenu" id="hidemainmenu" value="0"/>
            <input type="hidden" name="filter_order" id="filter_order" value="<?php echo $this->lists->order ?>"/>
            <input type="hidden" name="filter_order_Dir" id="filter_order_Dir"
                   value="<?php echo $this->lists->order_Dir ?>"/>
            <input type="hidden" name="<?php echo JFactory::getSession()->getFormToken(); ?>" value="1"/>

			<?php if (version_compare(JVERSION, '3.0', 'gt')): ?>
                <div id="filter-bar" class="btn-toolbar">
                    <div class="btn-group pull-right hidden-phone">
                        <label for="limit"
                               class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC') ?></label>
						<?php echo $this->getModel()->getPagination()->getLimitBox(); ?>
                    </div>
					<?php
					$asc_sel  = ($this->getLists()->order_Dir == 'asc') ? 'selected="selected"' : '';
					$desc_sel = ($this->getLists()->order_Dir == 'desc') ? 'selected="selected"' : '';
					?>
                    <div class="btn-group pull-right hidden-phone">
                        <label for="directionTable"
                               class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC') ?></label>
                        <select name="directionTable" id="directionTable" class="input-medium"
                                onchange="Joomla.orderTable()">
                            <option value=""><?php echo JText::_('JFIELD_ORDERING_DESC') ?></option>
                            <option value="asc" <?php echo $asc_sel ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING') ?></option>
                            <option value="desc" <?php echo $desc_sel ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING') ?></option>
                        </select>
                    </div>
                    <div class="btn-group pull-right">
                        <label for="sortTable"
                               class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY') ?></label>
                        <select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
                            <option value=""><?php echo JText::_('JGLOBAL_SORT_BY') ?></option>
							<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $this->getLists()->order) ?>
                        </select>
                    </div>
                </div>
                <div class="clearfix"></div>
			<?php endif; ?>

            <table class="adminlist table table-striped">
                <thead>
                <tr>
                    <th width="20">
                        <input type="checkbox" name="toggle" value=""
                               onclick="checkAll(<?php echo count($this->items) + 1; ?>);"/>
                    </th>
                    <th width="20">
						<?php echo JHTML::_('grid.sort', 'COM_CONFERENCE_FIELD_STATUS', 'enabled', $this->lists->order_Dir, $this->lists->order); ?>
                    </th>
                    <th>
						<?php echo JHTML::_('grid.sort', 'COM_CONFERENCE_FIELD_TITLE', 'title', $this->lists->order_Dir, $this->lists->order) ?>
                    </th>
                    <th class="center" width="12%">
						<?php echo JText::_('COM_CONFERENCE_FIELD_LABEL') ?>
                    </th>
                    <th class="center" width="12%">
						<?php echo JText::_('COM_CONFERENCE_TITLE_SESSIONS') ?>
                    </th>
                </tr>
                <tr>
                    <td></td>
                    <td class="center">
						<?php echo ConferenceHelperFormat::enabled($this->escape($this->getModel()->getState('enabled', ''))); ?>
                    </td>
                    <td>
						<?php echo ConferenceHelperFormat::search($this->escape($this->getModel()->getState('title', ''))); ?>
                    </td>
                    <td></td>
                    <td></td>

                </tr>
                </thead>

                <tfoot>
                <tr>
                    <td colspan="20">
						<?php if ($this->pagination->total > 0) echo $this->pagination->getListFooter() ?>
                    </td>
                </tr>
                </tfoot>

                <tbody>
				<?php if ($count = count($this->items)): ?>
					<?php $i = -1;
					$m       = 1; ?>
					<?php foreach ($this->items as $item) : ?>
						<?php
						$i++;
						$m               = 1 - $m;
						$checkedOut      = ($item->locked_by != 0);
						$ordering        = $this->lists->order == 'ordering';
						$item->published = $item->enabled;
						?>
                        <tr class="<?php echo 'row' . $m; ?>">
                            <td>
								<?php echo JHTML::_('grid.id', $i, $item->conference_level_id, $checkedOut); ?>
                            </td>
                            <td class="center">
								<?php echo JHTML::_('jgrid.published', $item->enabled, $i); ?>
                            </td>
                            <td align="left">
                                <a href="<?php echo JRoute::_('index.php?option=com_conference&view=level&id=' . $item->conference_level_id) ?>"
                                   class="conferenceitem">
                                    <strong><?php echo $this->escape($item->title) ?></strong>
                                </a>
                            </td>
                            <td class="center">
                                <span class="label <?php echo $item->label ?>"><?php echo $this->escape($item->title) ?></span>
                            </td>
                            <td class="center">
                                <a href="<?php echo JRoute::_('index.php?option=com_conference&view=sessions&speaker=&level=' . $item->conference_level_id . '&room=&time=&day=') ?>">
									<?php
									echo FOFModel::getTmpInstance('Sessions', 'ConferenceModel')
										->level($item->conference_level_id)
										->getTotal();
									?><?php echo JText::_('COM_CONFERENCE_TABLE_SESSIONS') ?>
                                </a>
                            </td>
                        </tr>
					<?php endforeach; ?>
				<?php else: ?>
                    <tr>
                        <td colspan="20">
							<?php echo JText::_('COM_CONFERENCE_NORECORDS') ?>
                        </td>
                    </tr>
				<?php endif; ?>
                </tbody>
            </table>
        </form>
    </div>
	<?php
}