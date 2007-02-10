<h2>search results</h2>
<?php foreach ($photos as $thumbnail) { ?>
	<div class="thumbnail_small">
		<a href="/office/gallery/show/<?=$thumbnail->photo_id?>"><img src="<?=site_url(imageLocation($thumbnail->photo_id, 1))?>" alt="<?=$thumbnail->photo_title?>" width="66" height="49"/></a>
		<div>
			<span class="orange">Image Title: </span><?=$thumbnail->photo_title?><br />
			<span class="orange">Date: </span><?=$thumbnail->photo_timestamp?><br />
			<span class="orange">Photographer: </span><?=fullname($thumbnail->photo_author_user_entity_id)?><br />
		</div>
	</div>
<?php } ?>