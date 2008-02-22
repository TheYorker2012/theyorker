<div class='RightToolbar'>
<h4 class="first" >Page Information</h4>
	<div class="Entry">
		<?php echo($page_information); ?>
	</div>
</div>
<div id="MainColumn">
	<div class='blue_box'>
		<h2>entries for this organisation</h2>

		<table cellspacing="0" cellpadding="0">
			<?php
			foreach ($contexts as $id => $context) {
				echo('<tr>');
				// name
				echo('<td style="width:83"><p align="center">'.xml_escape($context['name']).'</p></td>');
				// add/edit
				echo('<td style="width:78"><p align="center">');
				if ($context['exists']) {
					if ($context['editable']) {
						echo('<b><a href="'.$context['edit'].'">Edit</a></b>');
					}
				} else {
					if ($context['creatable']) {
						echo('<form method="post" action="'.$context['create'].'">');
						echo('<input type="hidden" name="create_context" value="'.$id.'" />');
						echo('<input type="hidden" name="create_confirm" value="1" />');
						echo('<input type="submit" value="Add '.xml_escape($context['name']).' Section" />');
						echo('</form>');
					}
				}
				echo('</p></td>');
				// updated
				echo('<td style="width:168"><p align="center">');
				if (empty($context['updated'])) {
					echo('&nbsp;');
				} else {
					echo($context['updated']);
				}
				echo('</p></td>');
				// delete
				echo('<td style="width:32">');
				if ($context['deletable']) {
					echo('<p align="center">');
					echo('<form method="post" action="'.$context['delete'].'" onSubmit="return confirm(\'Are you sure you want to remove this section?\');">');
					echo('<input type="hidden" name="remove_context" value="'.$id.'" />');
					echo('<input type="hidden" name="remove_confirm" value="1" />');
					echo('<input type="image" src="/images/icons/delete.png" alt="Delete" />');
					echo('</form>');
					echo('</p>');
				} else {
					echo('&nbsp;');
				}
				echo('</td>');
				echo('</tr>');
			}
			?>
		</table>
	</div>
</div>