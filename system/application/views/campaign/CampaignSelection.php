<div style="width: 220px; margin: 0; padding-left: 3px; float: right; ">
	<div style="padding: 5px; background-color: #999; color: #fff; font-size: small; font-weight: bold; ">
	About Campaigns
	</div>
	<p>
		<div style="float: right; ">
		<a href='/news/oarticle/2'><img src='/images/prototype/news/thumb3.jpg' alt='Some Spy' title='Some Spy' /></a>
		</div>
		<p style="color: #FF9933; font-weight: bold; margin-bottom: 0px;">What are these about?</p>
		<p style="margin-top: 0px; font-size: x-small; ">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</p>
	</p>
	<div style="padding: 5px; background-color: #999; color: #fff; font-size: small; font-weight: bold; ">
	What now?
	</div>
	<p>
		<div style="float: right; ">
		<a href='/news/oarticle/2'><img src='/images/prototype/news/thumb2.jpg' alt='Some Spy' title='Some Spy' /></a>
		</div>
		<p style="color: #FF9933; font-weight: bold; margin-bottom: 0px;">What do I do now?</p>
		<p style="margin-top: 0px; font-size: x-small; ">Well my friend, you wait and wait then see the results. This page will change to something else because you smell.</p>
	</p>
</div>

<div style="width: 400px; margin: 0px; padding-right: 3px; ">
	<div style="border: 1px solid #2DC6D7; padding: 6px; font-size: small; margin-bottom: 4px; ">
	<span style="font-size: x-large;  color: #2DC6D7; ">current campaigns</span><br />
		This is a list of our current campaigns. Click through the links to check more information on a particular campaign. A bigger blurb could be written here maybe with some snazzy image of a form of students voting and having a really fun time doing it. Like everyone does<br /><br />
		<table width="100%">
			<?php
			$total_votes = 0;
                        foreach ($data['vars']['Campaign_List'] as $campaigns)
			{
				$total_votes += $campaigns['votes'];
			}
                        foreach ($data['vars']['Campaign_List'] as $campaigns)
			{
				$percentage = $campaigns['votes']/$total_votes*100;
				echo '<tr>
					<td>
					<b><a href="'.site_url('campaign/details/').'/'.$campaigns['id'].'">'.$campaigns['name'].'</a></b>
					</td><td style="width:40%; border: thin solid teal;">
					<div style="float: left; width: '.$percentage.'%; background-color: teal;">&nbsp</div>
					<div stlye="float: right;">&nbsp;'.round($percentage).'%</div>
					</td>
					</tr>';
			}
			?>
		</table>
		<br />
		The deadline for the voting is the 23th of May. The winner will be officially announced on the 90th of June.
	
	</div>

	<div style="border: 1px solid #BBBBBB; padding: 6px; font-size: small; margin-bottom: 4px; ">
	<span style="font-size: x-large;  color: #BBBBBB; ">vote now</span><br />
		If you think you have decided on which campaign you wish to support, you can place your vote now. Remember, you will be able to change your vote later (but only before the deadline)
		<form id='form1' name='form1' action='#' method='POST'>
		</form>
		<form id='form1' name='form1' action='#' method='POST' class='form'>
		<table width="80%">
		<?php
                foreach ($data['vars']['Campaign_List'] as $campaigns)
                {
			echo '<tr><td style="text-align: right;">'.$campaigns['name'];
			echo '</td><td><fieldset style="display: inline;"><input type="submit" name="addorgform_addbutton" value="Add" class="button" /></fieldset><br /></td></tr>';
		}
		?>
		</table>
		</form>
	</div>
</div>
