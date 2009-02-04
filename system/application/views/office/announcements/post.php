<?php if (isset($preview)) { ?>
	<div class="BlueBox">
		<h2>announcement preview</h2>

		<div class="item">
			<div class="header unread">
				<img src="/images/version2/office/smallicon_announcements_unread.png" alt="Unread" />
				<div class="date">
					<?php echo(date('d/m/y H:i')); ?>
				</div>
				<?php echo(xml_escape($_POST['subject'])); ?>
			</div>
			<div class="content" style="display:block">
				<div class="author">
					<img src="<?php if ($preview['byline']->image !== NULL) { ?>/photos/userimage/<?php echo($preview['byline']->image); ?><?php } else { ?>/images/prototype/directory/members/anon.png<?php } ?>" alt="<?php echo(xml_escape($preview['byline']->name)); ?>" title="<?php echo(xml_escape($preview['byline']->name)); ?>" />
					<div><?php echo(xml_escape($preview['byline']->name)); ?></div>
					<div><?php echo(xml_escape($preview['byline']->title)); ?></div>
				</div>
				<?php echo($preview['content']); ?>
				<div class="clear_right"></div>
			</div>
		</div>
	</div>
<?php } ?>

<div class="BlueBox">
	<h2>new announcement</h2>

	<form action="" method="post">
		<fieldset>
			<label for="sendto">Recipients</label>
			<select name="sendto" id="sendto">
<?php foreach ($roles as $role) { ?>
				<option value="<?php echo($role->role); ?>"<?php if (isset($_POST['sendto']) && $role->role == $_POST['sendto']) echo(' selected="selected"'); ?>><?php echo($role->role); ?></option>
<?php } ?>
			</select>
			<label for="subject">Subject:</label>
			<input type="text" name="subject" id="subject" value="<?php if (isset($_POST['subject'])) echo($_POST['subject']); ?>" />
			<label for="content">Content:</label>
			<textarea name="content" id="content" rows="10" cols="50"><?php if (isset($_POST['content'])) echo($_POST['content']); ?></textarea>
			<label for="sender">Sender Byline:</label>
			<select name="sender" id="sender">
<?php foreach ($bylines as $byline) { ?>
				<option value="<?php echo($byline->id); ?>"<?php if (isset($_POST['sender']) && $byline->id == $_POST['sender']) echo(' selected="selected"'); ?>><?php echo($byline->name . ' - ' . $byline->title); ?></option>
<?php } ?>
			</select>
		</fieldset>
		<fieldset>
<?php if (isset($preview)) { ?>
			<input type="submit" name="post" id="post" value="Post" class="button" />
<?php } ?>
			<input type="submit" name="preview" id="preview" value="Preview" class="button" />
		</fieldset>
	</form>
</div>