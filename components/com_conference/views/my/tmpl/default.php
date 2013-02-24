<?php
/*
 * @package		Conference Schedule Manager
 * @copyright	Copyright (c) 2013 Sander Potjer / sanderpotjer.nl
 * @license		GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

// Load template helpers
$this->loadHelper('modules');

$speaker = $this->speaker;

if(empty($speaker->conference_speaker_id)) {
	$editprofileURL = 'index.php?option=com_conference&view=speaker&task=add';
} else {
	$editprofileURL = 'index.php?option=com_conference&view=speaker&task=edit&id='.$speaker->conference_speaker_id;
}
?>

<div class="conference my">

	
<div class="row-fluid">
<div class="span12">
	<h1 class="pull-left">
		<?php if($speaker->title) {
				echo($speaker->title);
			} else {
				echo JFactory::getUser()->name;
			}
		?>
	</h1>
	<a class="btn pull-right " href="<?php echo JRoute::_($editprofileURL)?>">
		<span class="icon-pencil"></span>  <?php echo JText::_('COM_CONFERENCE_MY_EDIT_PROFILE') ?>
	</a>
</div>
</div>
	
	<div class="well well-small spreker">
		<div class="row-fluid">
			<div class="span4">
				<span class="thumbnail">
					<?php if($speaker->image):?>
						<img src="<?php echo JURI::root().$speaker->image?>">
					<?php else:?>
						<img src="http://placehold.it/200x200">
					<?php endif;?>
				</span>
				<div class="speakersocial">
					<?php if($speaker->twitter):?>
						<a class="btn btn-small btn-block" href="http://twitter.com/<?php echo $speaker->twitter?>"><span class="icon conference-twitter"></span> <?php echo $speaker->twitter?></a>
					<?php endif;?>
					<?php if($speaker->facebook):?>
						<a class="btn btn-small btn-block" href="http://facebook.com/<?php echo $speaker->facebook?>"><span class="icon conference-facebook"></span> <?php echo $speaker->facebook?></a>
					<?php endif;?>
					<?php if($speaker->googleplus):?>
						<a class="btn btn-small btn-block" href="http://plus.google.com/<?php echo $speaker->googleplus?>"><span class="icon conference-google-plus"></span> <?php echo $speaker->title?></a>
					<?php endif;?>
					<?php if($speaker->website):?>
						<a class="btn btn-small btn-block" href="http://<?php echo $speaker->website?>"><span class="icon conference-earth"></span> <?php echo $speaker->website?></a>
					<?php endif;?>
				</div>
			</div>
			<div class="span8">
				<?php echo ($speaker->bio)?>
			</div>
		</div>
	</div>

<?php if(!empty($speaker->conference_speaker_id)): ?>
<div class="row-fluid sessions">
<div class="span12">
	<h2 class="pull-left"><?php echo JText::_('COM_CONFERENCE_TITLE_SESSIONS') ?></h2>
	<?php if(JFactory::getUser()->authorise('core.create', 'com_conference')) :?>
	<a class="btn pull-right btn-success" href="<?php echo JRoute::_('index.php?option=com_conference&view=session&task=add')?>">
		<span class="icon-plus"></span>  <?php echo JText::_('COM_CONFERENCE_MY_ADD_SESSION') ?>
	</a>
	<?php endif;?>
</div>
</div>
<div class="well well-small">
<table class="table table-striped">
	<thead>
		<tr>
			<th><?php echo JText::_('COM_CONFERENCE_FIELD_TITLE') ?></th>
			<th class="center"><?php echo JText::_('COM_CONFERENCE_FIELD_LEVEL') ?></th>
			<th class="center"><?php echo JText::_('COM_CONFERENCE_FIELD_DESCRIPTION') ?></th>
			<th class="center"><?php echo JText::_('COM_CONFERENCE_FIELD_SLIDES') ?></th>
			<th class="center"></th>
		</tr>
	</thead>
	<tbody>
<?php if(empty($this->sessions)): ?>
<tr>
	<td colspan="6" class="center">
	<?php echo JText::_('COM_CONFERENCE_NORECORDS') ?>
	</td>
</tr>

<?php endif; ?>
<?php foreach($this->sessions as $session):?>
	<tr>
		<td><?php echo($session->title) ?></td>
		<td class="center">
			<span class="label <?php echo $session->level_label ?>">
				<?php echo $session->level ?>
			</span>
		</td>
		<td class="center">
				<?php if($session->description): ?>
				<span class="badge badge-success"><span class="icon-checkmark"></span></span>
				<?php else:?>
				<span class="badge badge-important"><span class="icon-delete"></span></span>
				<?php endif;?>
			</td>
			<td class="center">
				<?php if($session->slides): ?>
				<span class="badge badge-success"><span class="icon-checkmark"></span></span>
				<?php else:?>
				<span class="badge badge-important"><span class="icon-delete"></span></span>
				<?php endif;?>
			</td>
			<td class="center">
				<a class="btn btn-small" href="<?php echo JRoute::_('index.php?option=com_conference&view=session&task=edit&id='.$session->conference_session_id)?>"><span class="icon-pencil"></span> <?php echo JText::_('COM_CONFERENCE_MY_EDIT') ?></a>
			</td>
	</tr>
<?php endforeach; ?>
	</tbody>
</table>

</div>
<?php endif; ?>

<h2>Algemene informatie</h2>
<div class="well well-small">
<h3>Nog een paar andere zaken rondom je presentatie:</h3>
<ul>
<li>Spreektijd is 40 minuten. Hierdoor is er tien minuten tijd om van zaal te wisselen.</li>
<li>Houdt rekening met vragen vanuit het publiek.</li>
<li>Er is een beamer aanwezig, maar zorg zelf voor een laptop.</li>
</ul>


<h3>Aanleveren slides presentatie</h3>
<p>Als organisatie willen we borg staan voor een kwalitatief gebeuren. Een goede, doordachte presentatie is de basis van een goed evenement.
Twee weken voor de Joomla!Dagen, <strong>uiterlijk zondag 7 april 2013</strong>, ontvangen we graag de slides van je presentatie per mail.</p>

<h3>Toegangskaarten en overnachting</h3>
<p>De Joomladagen zijn er voor en door de community, daarom zijn we ook zeer dankbaar voor je aanmelding. De prijzen van de toegangskaarten zijn laag, maar de kosten blijven hoog. Omdat je door het geven van de presentaties flink wat exposure krijgt op de dagen zelf, maar ook via de website, willen we je vragen om in overweging te nemen toch zelf een toegangskaart aan te schaffen.</p>
<p>Dit vragen we elke spreker. Het is geen verplichting, maar het helpt ons wel om de kosten te vergoeden. Wil je gebruik maken van een gratis toegangskaartje voor de dag dat je een presentatie geeft, dan vernemen we dat graag.</p>
<p>Hotelovernachting en diner op zaterdagavond zijn voor eigen rekening. Deze kunnen besteld worden via <a href="http://joomla.paydro.net">http://joomla.paydro.net</a></p>

<h3>Vragen?</h3>
<p>Het sprekersteam (Fonny Smets, Hans Kuijpers en Sander Potjer) is te bereiken op <a href="mailto:spreker@joomladagen.nl">spreker@joomladagen.nl</a></p>
<p>Tot op de Joomla!dagen!</p>
</div>

</div>