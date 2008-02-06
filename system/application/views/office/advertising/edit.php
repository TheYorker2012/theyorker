<div class="RightToolbar">
	<h4 class="first">Quick Links</h4>
	<div class="Entry">
		<?php echo('<a href="/office/advertising/view/'.$advert['id'].'">View This Advert</a>'."\n"); ?>
	</div>
	<div class="Entry">
		<?php echo('<a href="/office/advertising/editimage/'.$advert['id'].'">Edit Advert Image</a>'."\n"); ?>
	</div>
	<div class="Entry">
		<?php echo('<a href="/office/advertising/">Back To Adverts List</a>'."\n"); ?>
	</div>
</div>

<div class="blue_box">
	<h2>edit advert</h2>
<?php
	echo('	<form class="form" action="/office/advertising/edit/'.$advert['id'].'" method="post">'."\n");
?>
		<fieldset>
<?php
	echo('			<input type="hidden" id="advert_id" name="advert_id" value="'.$advert['id'].'" />'."\n");
?>
		</fieldset>
		<fieldset>
			<label for="advert_name">Name: </label>
<?php
	echo('			<input type="text" id="advert_name" name="advert_name" value="'.$advert['name'].'" style="width: 250px" />'."\n");
?>
			<label for="advert_name">Target Web Address: </label>
<?php
	echo('			<input type="text" id="advert_url" name="advert_url" value="'.$advert['url'].'" style="width: 250px" />'."\n");
?>
			<label for="advert_alt">Image Hover Text: </label>
<?php
	echo('			<input type="text" id="advert_alt" name="advert_alt" value="'.$advert['alt'].'" style="width: 250px" />'."\n");
?>
			<label for="advert_image">Max Views: </label>
<?php
	echo('			<input type="text" id="advert_max_views" name="advert_max_views" value="'.$advert['max_views'].'" style="width: 250px" />'."\n");
?>
		</fieldset>
		<fieldset>
			<input type="submit" name="submit_save_advert" value="Save" style="float: right;" />
		</fieldset>
	</form>
</div>

<div class="blue_box">
	<h2>advert options</h2>
<?php
	if ($advert['is_live'] == true) {
?>
	<p>
		pull advert from the site so it is no longer in rotation
	</p>
<?php
	echo('	<form class="form" action="/office/advertising/edit/'.$advert['id'].'" method="post">'."\n");
?>
		<fieldset>
<?php
	echo('			<input type="hidden" id="advert_id" name="advert_id" value="'.$advert['id'].'" />'."\n");
?>
		</fieldset>
		<fieldset>
			<input type="submit" name="submit_pull_advert" value="Pull Advert" style="float: right;" />
		</fieldset>
	</form>
<?php
	}
	else {
?>
	<p>
		add the advert to the advert rotation system on the site
	</p>
<?php
	echo('	<form class="form" action="/office/advertising/edit/'.$advert['id'].'" method="post">'."\n");
?>
		<fieldset>
<?php
	echo('			<input type="hidden" id="advert_id" name="advert_id" value="'.$advert['id'].'" />'."\n");
?>
		</fieldset>
		<fieldset>
			<input type="submit" name="submit_make_advert_live" value="Make Advert Live" style="float: right;" />
		</fieldset>
	</form>
<?php
	}
?>
	<p>
		delete this advert
	</p>
<?php
	echo('	<form class="form" action="/office/advertising/edit/'.$advert['id'].'" method="post">'."\n");
?>
		<fieldset>
<?php
	echo('			<input type="hidden" id="advert_id" name="advert_id" value="'.$advert['id'].'" />'."\n");
?>
		</fieldset>
		<fieldset>
			<input type="submit" name="submit_delete_advert" value="Delete Advert" style="float: right;" />
		</fieldset>
	</form>
</div>

<?php
/*
echo('<div class="BlueBox"><pre>');
print_r($data);
echo('</pre></div>');
*/
?>