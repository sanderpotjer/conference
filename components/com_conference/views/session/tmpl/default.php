<?php
/*
 * @package		Conference Schedule Manager
 * @copyright	Copyright (c) 2013 Sander Potjer / sanderpotjer.nl
 * @license		GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

$this->loadHelper('cparams');
$this->loadHelper('modules');
$this->loadHelper('format');

if($this->item->conference_speaker_id) {
	 $speakers = ConferenceHelperFormat::speakers($this->item->conference_speaker_id);
}
?>
<div class="conference">
	<div class="row-fluid">
		<h1><?php echo $this->escape($this->item->title)?></h1>
	</div>
	<div class="well well-small spreker">
	<?php //print_r($this->item);?>
		<div class="row-fluid">
			<div class="span4">
				<span class="thumbnail">
					<?php if($this->item->conference_speaker_id):?>
						<?php foreach($speakers as $speaker) :?>
						<img src="<?php echo $speaker->image?>">
						<?php endforeach;?>
					<?php else:?>
						<img src="http://placehold.it/200x200">
					<?php endif;?>
				</span>
				<div class="speakersocial">
					<?php if($this->item->twitter):?>
						<a class="btn btn-small btn-block" target="_blank" href="http://twitter.com/<?php echo $this->item->twitter?>"><span class="icon conference-twitter"></span> <?php echo $this->item->twitter?></a>
					<?php endif;?>
					<?php if($this->item->facebook):?>
						<a class="btn btn-small btn-block" target="_blank" href="http://facebook.com/<?php echo $this->item->facebook?>"><span class="icon conference-facebook"></span> <?php echo $this->item->facebook?></a>
					<?php endif;?>
					<?php if($this->item->googleplus):?>
						<a class="btn btn-small btn-block" target="_blank" href="http://plus.google.com/<?php echo $this->item->googleplus?>"><span class="icon conference-google-plus"></span> <?php echo $this->item->title?></a>
					<?php endif;?>
					<?php if($this->item->website):?>
						<a class="btn btn-small btn-block" target="_blank" href="http://<?php echo $this->item->website?>"><span class="icon conference-earth"></span> <?php echo $this->item->website?></a>
					<?php endif;?>
				</div>

			</div>
			<div class="span8">
				<?php echo ($this->item->description)?>
				<?php if($this->item->conference_speaker_id):?>
				<h4>Sprekers</h4>
				<table class="table table-striped">
				<tbody>
					<?php 
						$sessionspeakers = array();
						foreach($speakers as $speaker) :
							if($speaker->enabled) {
								echo('<tr><td><a href="index.php?option=com_conference&view=speaker&id='.$speaker->conference_speaker_id.'">'.trim($speaker->title).'</a></td></tr>');
							} else {
								echo('<tr><td>'.trim($speaker->title).'</td></tr>');
							}
						endforeach;
					?>
				</tbody>
				</table>
				<?php endif;?>
			</div>
		</div>
	</div>
</div>