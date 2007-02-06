<div id="source" style="display:none">
	Photo Title: <input type="text" name="title" size="30" />
	Photo Gallery: <input type="checkbox" name="gallery" />
	Photo File: <input type="file" name="userfile" size="30" />
</div>

<?=form_open_multipart('upload/do_upload'); ?>
Basic test script
<div>
	Photo Title: <input type="text" name="title1" size="30" />
	Photo Gallery: <input type="checkbox" name="gallery1" />
	Photo File: <input type="file" name="userfile1" size="30" />
</div>
<input type="hidden" name="destination" id="destination" value="1" />

<input type="button" onClick="AddClones()" value="Another"/>
<input type="submit" value="upload" />
</form>
