<?php
/**
 * @package     Conference
 *
 * @author      Stichting Sympathy <info@stichtingsympathy.nl>
 * @copyright   Copyright (C) 2013 - [year] Stichting Sympathy. All rights reserved.
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link        https://joomladagen.nl
 */

use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

extract($displayData);
?>
<div class="row-fluid">
			<h1 class="pull-left"><?php echo Text::_($title); ?></h1>
<div class="btn-toolbar pull-right">
	<div id="toolbar-cancel" class="btn-group">
		<button type="button" class="btn" onclick="Joomla.submitbutton('<?php echo $view; ?>.cancel')">
			<span class="icon-cancel"></span><?php echo JText::_('JCANCEL') ?>
		</button>
	</div>
	<div id="toolbar-apply" class="btn-group">
		<button type="button" class="btn btn-primary" onclick="Joomla.submitbutton('<?php echo $view; ?>.save')">
			<span class="icon-ok"></span><?php echo Text::_('JSAVE') ?>
		</button>
	</div>
</div>
</div>