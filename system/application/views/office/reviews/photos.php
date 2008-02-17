<div id="RightColumn">
<h2 class="first">Page Information</h2>
	<?php echo $page_information; ?>
<h2>Thumbnail Image</h2>
	<?php 
	echo $thumbnail_text;
	//Default image for thumbnail is the first image.
	if(count($images->result())>0)
	{
		if($has_thumbnail){
			//There is at least one image in slideshow, so the top image is the default image used with a thumbnail
			$default_image = $images->row_array(0);
			echo('<div align="center">');
			echo('<b>Current Thumbnail</b>');
			echo('<p>'.$this->image->getThumb($default_image['photo_id'], 'small').'</p>');
			echo '<a href="/office/gallery/show/'.$default_image['photo_id'].'">Re-Crop Thumbnail In Gallery</a>';
			echo('</div>');
		}else{
			//Error!! There are images and there is no thumbnail Image!! Warn user to add one
			$default_image = $images->row_array(0);
			echo('<div align="center">');
			echo('<span class="red"><b>No Thumbnail Exists!</b></span>');
			echo $thumbnail_text_none;
			echo '<a href="/office/gallery/show/'.$default_image['photo_id'].'">Crop Thumbnail In Gallery</a>';
			echo('</div>');
		}
	} else {
		//There are no images, print a message saying so
		echo $thumbnail_text_no_images;
	}
	?>
</div>
<div id="MainColumn">
	<div class="BlueBox">
	<h2>current slideshow images</h2>
		<?php 
		if(count($images->result())==0)
		{ 
			echo '<p>This venue has no images, please upload some.</p>';
		} 
		echo '<p>'."\n";
		foreach( $images->result() as $image ) { 
			echo($this->image->getThumb($image->photo_id, 'slideshow'));
			echo('<br />');
			echo('Options : <a href="/office/reviews/'.$organisation['shortname'].'/'.$ContextType.'/photos/move/'.$image->photo_id.'/up"><img src="/images/prototype/members/sortdesc.png" alt="Move Up" title="Move Up"></a> ');
			echo('<a href="/office/reviews/'.$organisation['shortname'].'/'.$ContextType.'/photos/move/'.$image->photo_id.'/down"><img src="/images/prototype/members/sortasc.png" alt="Move Down" title="Move Down"></a> ');
			echo('<a href="/office/reviews/'.$organisation['shortname']).'/'.xml_escape($ContextType).'/photos/delete/'.$image->photo_id.'" onClick="return confirm(\'Are you sure you want to delete this photo?\');"><img src="/images/prototype/members/no9.png" alt="Delete" title="Delete"></a> ';
			echo('<a href="/office/gallery/show/'.$image->photo_id.'">Crop</a>');
			echo('<br />');
		} 
		?>
		</p>
	</div>
	<?php
		$CI = &get_instance();
		$CI->load->view('uploader/upload_single_photo', array('action_url' => '/office/reviews/'.$organisation['shortname'].'/'.$ContextType.'/photos/upload') );
	?>
</div>