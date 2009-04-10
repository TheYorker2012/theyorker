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
	if (empty($Tips)) {
		?><div><?php
		if ($ShowCrosswordInfo) {
			?>There are no tips in this category. <?php
		}
		else if ($ShowCategoryInfo) {
			?>There are no tips attached to this crossword. <?php
		}
		else {
			?>No tips were found. <?php
		}
		if ($Office && null === $AddForm) {
			?>Tips can be created from crossword edit pages. <?php
		}
		?></div><?php
	}
	else {
		?><form class="form" method="post" action="<?php echo(xml_escape($SelfUri)); ?>"><?php
		?><fieldset><?php
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
				$editable = isset($tip['edit_form']);
				$viewable = !$editable || !$tip['edit_form']->Changed();
				if ($viewable) {
					if ($editable) {
						?><div	id="<?php echo(xml_escape('tip_'.$tip['id'].'_view')); ?>"<?php
							?>	style="display:none; cursor:pointer;"<?php
							?>	onclick="<? echo(xml_escape(
									'document.getElementById('.js_literalise('tip_'.$tip['id'].'_view').').style.display="none";'.
									'document.getElementById('.js_literalise('tip_'.$tip['id'].'_edit').').style.display="";'
								)); ?>"<?php
							?>><?php
					}
					echo($tip['content_xhtml']);
					if ($editable) {
						?></div><?php
					}
				}
				if ($editable) {
					?><div id="<?php echo(xml_escape('tip_'.$tip['id'].'_edit')); ?>"><?php
						$tip['edit_form']->Load();
						?><input class="button" type="submit" value="Save all tips" /><?php
						?><div style="clear:both"></div><?php
					?></div><?php
					if ($viewable) {
						echo(js_block(
							'document.getElementById('.js_literalise('tip_'.$tip['id'].'_view').').style.display="";'.
							'document.getElementById('.js_literalise('tip_'.$tip['id'].'_edit').').style.display="none";'
						));
					}
				}
			?></div><?php
		}
		?></fieldset><?php
		?></form><?php
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
