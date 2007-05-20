<div class="blue_box">
<h2>search results</h2>
<?php foreach ($photos as $thumbnail) { ?>
	<div class="thumbnail_small">
		<a href="/office/gallery/show/<?=$thumbnail->photo_id?>"><?=$this->image->getThumb($thumbnail->photo_id, 'small')?>"</a>
		<div>
			<span class="orange">Image Title: </span><?=$thumbnail->photo_title?><br />
			<span class="orange">Date: </span><?=$thumbnail->photo_timestamp?><br />
			<span class="orange">Photographer: </span><?=fullname($thumbnail->photo_author_user_entity_id)?><br />
		</div>
	</div>
<?php } ?>
</div>