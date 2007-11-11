<div class='RightToolbar'>
	<h4>Areas for Attention</h4>
	<div class="Entry">
		<div class="information_box">
			<b>Reviews</b> from the following sections are waiting to be published:
			<ul>
				<li><a href='#'>Food 02/02/2007</a></li>
				<li><a href='#'>Drink 02/02/2007</a></li>
			</ul>
		</div>
		<div class="information_box">
			<b>Information</b> in the following sections have been updated and is waiting to be published:
			<ul>
				<li><a href='#'>Food 02/02/2007</a></li>
				<li><a href='#'>Drink 02/02/2007</a></li>
			</ul>
		</div>
	</div>

<h4>What's this?</h4>
	<div class="Entry">
		<?php echo 'whats_this'; ?>
	</div>
</div>

<div class='blue_box'>
	<h2>entries for this organisation</h2>

	<table cellspacing="0" cellpadding="0">
		<?php
		foreach ($contexts as $id => $context) {
			echo('<tr>');
			// name
			echo('<td width="83"><p align="center">'.$context['name'].'</p></td>');
			// add/edit
			echo('<td width="78"><p align="center">');
			if ($context['exists']) {
				if ($context['editable']) {
					echo('<b><a href="'.$context['edit'].'">Edit</a></b>');
				}
			} else {
				if ($context['creatable']) {
					echo('<form method="post" action="'.$context['create'].'">');
					echo('<input type="hidden" name="create_context" value="'.$id.'" />');
					echo('<input type="hidden" name="create_confirm" value="1" />');
					echo('<input type="submit" value="Add '.$context['name'].' Section" />');
					echo('</form>');
				}
			}
			echo('</p></td>');
			// updated
			echo('<td width="168"><p align="center">');
			if (empty($context['updated'])) {
				echo('&nbsp;');
			} else {
				echo($context['updated']);
			}
			echo('</p></td>');
			// delete
			echo('<td width="32">');
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

