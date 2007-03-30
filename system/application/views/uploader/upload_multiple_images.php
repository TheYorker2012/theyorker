<div id="source" style="display:none">
	Image Filename: <input type="file" name="userfile" size="30" />
</div>
<div class="blue_box">
<form action="<?=site_url($this->uri->uri_string())?>" method="post" enctype="multipart/form-data">
<fieldset>
Generic helping text should go here, each file is limited to 2Mb.
<div>
	Image Filename: <input type="file" name="userfile1" size="30" />
</div>
<input type="hidden" name="destination" id="destination" value="1" />

<input type="button" onClick="AddClones()" value="Another"/>
<input type="submit" value="upload" />
</fieldset>
</form>
</div>