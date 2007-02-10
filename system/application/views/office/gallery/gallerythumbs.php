	<h2>search results</h2>
<!--	<div class="thumbnail_medium">
		<a href="/office/gallery/show"><img src="/images/prototype/news/thumb1.jpg" alt="CompSci.jpg" /></a>
		<div>
		<span class="orange">Image Title</span><br />
			BBC News Icon<br />
		<span class="orange">Date</span><br />
			03/02/2007<br />
		<span class="orange">Photographer</span><br />
			Ian Cook<br />
		<span class="orange">Dimensions</span><br />
			1024 x 769<br />
		</div>
	</div>
-->
<?php foreach ($photos as $thumbnail) { ?>
	<div class="thumbnail_small">
		<a href="/office/gallery/show/<?=$thumbnail->photo_id?>"><img src="<?=imageLocation($thumbnail->photo_id, 2)?>" alt="<?=$thumbnail->photo_title?>" /></a>
		<div>
		<span class="orange">Image Title: </span><?=$thumbnail->photo_title?><br />
		<span class="orange">Date: </span><?=$thumbnail->photo_timestamp?><br />
		<span class="orange">Photographer: </span><?=$thumbnail->photo_author_user_entity_id?><br />
		</div>
	</div>
<?php } ?>