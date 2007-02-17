<div id="source" style="display:none">
	<p>Photo Title: <input type="text" name="title" size="30" /></p>
	<p>Photo File: <input type="file" name="userfile" size="30" /></p>
</div>
<?=form_open_multipart($this->uri->segment(1).'/gallery/do_upload'); ?>
<p>Photo's should be in jpg format. The upload size limit is 2mb(?).</p><br />
<div>
	<p>Photo Title: <input type="text" name="title1" size="30" /></p>
	<p>Photo File: <input type="file" name="userfile1" size="30" /></p>
</div>
<input type="hidden" name="destination" id="destination" value="1" />

<input type="button" onClick="AddClones()" value="Another"/>
<input type="submit" value="upload" />
</form>
