<div id="RightColumn">
	<h2 class="first">Extra Operations</h2>
	<ul>
		<li>
			<a href="/office/ticker/add/">
				Add New Article Slot
			</a>
		</li>
		<li>
			<a href="/office/ticker/update/">
				Manually Update Facebook
			</a>
		</li>
	</ul>

	<h2><?php echo($whats_this_heading); ?></h2>
	<?php echo($whats_this_text); ?>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<h2>facebook articles</h2>

		<table style="width:80%">
			<tr>
				<th style="text-align:center">Slot #</th>
				<th>Article Type</th>
				<th>Ops</th>
			</tr>
<?php
$slot_id = 1;
foreach ($settings as $setting) { ?>
			<tr>
				<td style="text-align:center">
					<?php echo($slot_id++); ?>
				</td>
				<td><?php
if ($setting['facebook_article_content_type_id'] !== NULL) {
	echo('Latest article from <i>\'' . $setting['content_type_name'] . '\'</i>');
} elseif ($setting['facebook_article_article_id'] !== NULL) {
	echo('Show <i>\'' . $setting['facebook_article_heading'] . '\'</i> article');
} else {
	echo('<span style="color:red;font-weight:red">NO ARTICLE SET</span>');
} ?>
				</td>
				<td>
					<a href="/office/ticker/edit/<?php echo($setting['facebook_article_id']); ?>/">
						<img src="/images/prototype/news/edit.png" alt="Edit" title="Edit" />
					</a>
					<a href="/office/ticker/delete/<?php echo($setting['facebook_article_id']); ?>/">
						<img src="/images/prototype/news/delete.png" alt="Delete" title="Delete" />
					</a>
				</td>
			</tr>
<?php } ?>
		</table>
	</div>

	<div class="BlueBox">
		<h2>preview</h2>

		<div>
			<a href="http://www.theyorker.co.uk">
				<img src="http://www.theyorker.co.uk/images/prototype/homepage/facebook_yorker_wide.jpg" alt="The Yorker" />
			</a>
<?php echo($preview); ?>
		</div>
	</div>
</div>