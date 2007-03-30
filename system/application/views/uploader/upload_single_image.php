<div class="blue_box">

<form action="<?=site_url($this->uri->uri_string())?>" method="post" enctype="multipart/form-data">
Generic helping text should go here, image size is limited to 2Mb.
<div>
	Image Title: <input type="text" name="title1" size="30" />
	Image Filename: <input type="file" name="userfile1" size="30" />
</div>
<input type="hidden" name="destination" id="destination" value="1" />

<input type="submit" value="upload" />
</form>
</div>