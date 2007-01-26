<div id="source" style="display:none">
	<p><input type="file" name="userfile" size="20" /></p>
</div>

<?=form_open_multipart('upload/do_upload'); ?>
Basic test script

<input type="hidden" id="destination" value="0" />

<input type="button" id="AddClone" value="Another"/>
<input type="submit" value="upload" />