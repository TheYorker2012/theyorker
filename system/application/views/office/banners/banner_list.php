<div class='RightToolbar'>
	<h4 class="first">Information</h4>
	<div class="Entry">
		<?php echo($page_information); ?>
	</div>
	<h4>Actions</h4>
	<div class="Entry">
		<ul>
			<li><a href="/office/banners/upload">Upload new banner(s)</a></li>
		</ul>
	</div>
</div>
<div id="MainColumn">
	<div class='BlueBox'>
		<h2>Scheduled banners</h2>
		<?php if(!empty($scheduled_banners)){ ?>
		<table>
			<tr>
				<th>Banner</th>
				<th>Schedule</th>
				<th>Has Link</th>
				<th>Edit</th>
			</tr>
			<?php 
			foreach($scheduled_banners as $banner) { 
				echo('			<tr>'."\n");
				echo('				<td>');
				echo($this->image->getImage($banner['banner_id'], $banner['banner_type'], array('width' => '196', 'height' => '50')));
				echo('</td>'."\n");
				echo('				<td align="center">');
				//no need to check if there is a current_banner_id, as if there is at least one scheduled banner, there must be an assigned one.
				if ($banner['banner_id']==$current_banner_id) {
					echo '<span class="red">Live</span>';
				} else {
					echo date("d/m/y",$banner['banner_last_displayed_timestamp']);
				}
				echo('</td>'."\n");
				echo('				<td align="center">');
				if (!empty($banner['banner_link'])) {
					echo('<img src="/images/prototype/members/confirmed.png" alt="Yes" title="Yes">');
				} else {
					echo('<img src="/images/prototype/members/no9.png" alt="No" title="No">');
				}
				echo('</td>'."\n");
				echo('				<td>');
				echo('<a href="/office/banners/edit/'.$banner['banner_id'].'" title="Edit this banner">Edit</a>');
				echo('</td>'."\n");
				echo('			</tr>'."\n");
			} 
			?>
		</table>
		<?php 
		} else {
			echo($no_banner_wikitext);
		}
		?>
	</div>
	<?php if(!empty($pooled_banners)){ ?>
	<div class='BlueBox'>
		<h2>Pooled banners</h2>
		<table>
			<tr>
				<th>Banner</th>
				<th>Has Link</th>
				<th>Edit</th>
			</tr>
			<?php 
			foreach($pooled_banners as $banner) { 
				echo('			<tr>'."\n");
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
				echo('				<td>');
				echo('<a href="/office/banners/edit/'.$banner['banner_id'].'" title="Edit this banner">Edit</a>');
				echo('</td>'."\n");
				echo('			</tr>'."\n");
			} 
			?>
		</table>
	</div>
	<?php 
	}
	if(!empty($unused_banners)){ 
	?>
	<div class='BlueBox'>
		<h2>Unassigned banners</h2>
		<table>
		<tr>
			<th>Banner</th>
			<th>Edit</th>
		</tr>
			<?php 
			foreach($unused_banners as $banner) { 
				echo('			<tr>'."\n");
				echo('				<td>');
				echo($this->image->getImage($banner['banner_id'], $banner['banner_type'], array('width' => '196', 'height' => '50')));
				echo('</td>'."\n");
				echo('				<td>');
				echo('<a href="/office/banners/edit/'.$banner['banner_id'].'" title="Edit this banner">Edit</a>');
				echo('</td>'."\n");
				echo('			</tr>'."\n");
			} 
			?>
		</table>
	</div>
	<?php } ?>
	<a href="/office/banners/" title="Go Back">Back to the homepages list.</a>
</div>