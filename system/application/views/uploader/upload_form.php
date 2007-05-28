<div id="source" style="display:none">
	Photo Title: <input type="text" name="title" size="30" />
	Photo File: <input type="file" name="userfile" size="30" />
</div>

<?=form_open_multipart('upload/do_upload'); ?>
Basic test script
<div>
	<label for="title1">Photo Title: </label>
	<input type="text" name="File" size="30" />
	<label for="userfile1">Photo Title: </label>
	<input type="file" name="userfile1" size="30" />
</div>
<input type="hidden" name="destination" id="destination" value="1" />

<input type="button" onClick="AddClones()" value="Another"/>
<input type="submit" value="upload" />
</form>
