<div class='columnPhoto'>
	<a href='#'><img src='/images/prototype/directory/about/178327854723856.jpg' /></a>
</div>
<div class='columnText'>
	<p><?php echo $organisation['description']; ?></p>
</div>
<div>
	<ul>
		<?php if (!empty($organisation['website'])) {
			echo '<li><strong>Website: </strong><a href="'.
				$organisation['website'].'">'.$organisation['website'].'</a></li>';
		} ?>
		<?php if (!empty($organisation['location'])) {
			echo '<li><strong>Location: </strong>'.$organisation['location'].'</li>';
		} ?>
		<?php if (!empty($organisation['open_times'])) {
			echo '<li><strong>Opening Times: </strong>'.$organisation['open_times'].'</li>';
		} ?>
	</ul>
</div>