<div id="RightColumn">
	<h2 class="first"><?php echo($whats_this_heading); ?></h2>
	<?php echo($whats_this_text); ?>
</div>

<div id="MainColumn">
	<div class="BlueBox">
		<h2>edit article slot</h2>

		<form action="/office/ticker/edit/<?php echo($slot_info['facebook_article_id']); ?>/" method="post">
			<fieldset>
				<label for="type">Latest From:</label>
				<select id="type" name="type" size="1">
					<option value="NULL"></option>
<?php foreach ($content_types as $type) { ?>
					<option value="<?php echo($type['content_type_id']); ?>"<?php if ($form_content_type_id == $type['content_type_id']) echo(' selected="selected"'); ?>><?php echo($type['content_type_name']); ?></option>
<?php } ?>
				</select>
				<br />
				<input type="submit" name="cancel" id="cancel" value="Cancel" class="button" />
				<input type="submit" name="submit" id="submit" value="Edit Article Slot" class="button" />
			</fieldset>
		</form>
	</div>
</div>