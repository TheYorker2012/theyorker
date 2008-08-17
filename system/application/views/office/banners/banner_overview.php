<div id="RightColumn">
	<h2 class="first">Page Information</h2>
	<div class="Entry">
		<?php echo($page_information); ?>
	</div>
	<h2>Actions</h2>
	<div class="Entry">
		<ul>
			<li><a href="/office/banners/upload">Upload new banner(s)</a></li>
		</ul>
	</div>
</div>
<div id="MainColumn">
	<div class='BlueBox'>
		<h2>Homepages</h2>
		<table>
			<tr>
				<th>Homepage</th>
				<th>Current Banner</th>
				<th>Has Link</th>
			</tr>
			<?php 
			foreach($banners as $banner) { 
				echo('			<tr>'."\n");
				echo('				<td>');
				echo('<a href="/office/banners/section/'.$banner['banner_homepage_codename'].'" title="Edit this section">');
				echo(xml_escape($banner['banner_homepage']));
				echo('</a>');
				echo('</td>'."\n");
				echo('				<td>');
				echo($this->image->getImage($banner['banner_id'], $banner['banner_type'], array('width' => '196', 'height' => '50')));
				echo('</td>'."\n");
				echo('				<td align="center">');
				if (!empty($banner['banner_link'])) {
					echo('<img src="/images/prototype/members/confirmed.png" alt="Yes" title="Yes">');
				} else {
					echo('<img src="/images/prototype/members/no9.png" alt="No" title="No">');
				}
				echo('</td>'."\n");
				echo('			</tr>'."\n");
			} 
			?>
		</table>
	</div>
</div>