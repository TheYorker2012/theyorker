<?php
/**
 * @file views/crosswords/tips_list.php
 * @param $Tips array
 * @param $AddForm null,InputInterfaces
 * @param $SelfUri string Uri to post to.
 * @param $ShowCrosswordInfo bool
 * @param $ShowCategoryInfo bool
 * @param $Office bool
 */
$this->load->library('academic_calendar');
$tip_category_prefix = ($Office ? '/office/crosswords/tips/' : '/crosswords/tips/');
$crossword_prefix = ($Office ? '/office/crosswords/crossword/' : '/crosswords/');
$return_here_get = '?ret='.urlencode($SelfUri);
?>
<div>
<?php
	foreach ($Tips as &$tip) {
		?><div id="<?php echo(xml_escape('tip'.(int)$tip['id'])); ?>" class="crosswordTip"><?php
			?><h3><?php
				if ($ShowCategoryInfo) {
					?><a href="<?php echo(xml_escape(
						$tip_category_prefix.(int)$tip['category_id'].$return_here_get.'#tip'.(int)$tip['id']
						)); ?>"><?php
					echo(xml_escape($tip['category_name']));
					?></a><?php
					if ($ShowCrosswordInfo) {
						?> - <?php
					}
				}
				if ($ShowCrosswordInfo) {
					$pub = $tip['publication'];
					if (null !== $pub) {
						$pub = new Academic_time($pub);
						$pub = $pub->Format('D ').$pub->AcademicTermNameUnique().' week '.$pub->AcademicWeek();
					}
					else {
						$pub = "no publication scheduled";
					}
					?><a href="<?php echo(xml_escape(
						$crossword_prefix.(int)$tip['crossword_id'].$return_here_get.'#tip'.(int)$tip['id']
						)); ?>"><?php
					echo(xml_escape($pub));
					?></a><?php
				}
			?></h3><?php
			echo($tip['content_xml']);
		?></div><?php
	}
	if (null !== $AddForm) {
		?><h2>add tip</h2><?php
		?><form class="form" method="post" action="<?php echo(xml_escape($SelfUri)); ?>"><?php
			?><fieldset><?php
				$AddForm->Load();

				?><input class="button" type="submit" value="Add tip" /><?php
			?></fieldset><?php
		?></form><?php
	}
?>
</div>
