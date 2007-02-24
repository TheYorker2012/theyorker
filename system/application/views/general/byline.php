	<div class='Byline'>
		<?php foreach ($reporters as $reporter) { ?>
			<img src='<?php echo $reporter['photo']; ?>' alt='<?php echo $reporter['name']; ?>' title='<?php echo $reporter['name']; ?>' />
		<?php }
		foreach ($reporters as $reporter) { ?>
			<span class='reporter'><?php echo $reporter['name']; ?></span><br />
		<?php }
		echo $article_date; ?><br />
		<?php foreach ($reporters as $id => $reporter) { ?>
			<a href='/archive/reporter/<?php echo $id; ?>'><span class='orange'>Read more article by <?php echo $reporter['name']; ?></span></a>
		<?php } ?>
	</div>
