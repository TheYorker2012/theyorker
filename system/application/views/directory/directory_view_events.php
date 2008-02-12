<!-- TODO: design, recreate and test this page -->
<div class="BlueBox">
	<table>
		<tr>
			<td align="center" width="150" valign="top">
				<?php $content['week_select']->Load(); ?>
			</td>
			<td align="center" valign="top">
				<P><SMALL><EM>Displaying <?php echo(xml_escape($date_range_description)); ?></EM></SMALL></P>
				<?php $content['events_list']->Load(); ?>
			</td>
		</tr>
	</table>
</div>
