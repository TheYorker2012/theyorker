<?php

/**
 *	@file	views/comments/latest_box.php
 *	@brief	View for listing the most recent comments on the homepage
 *	@author	Chris Travis (cdt502 - ctravis@gmail.com)
 */

function print_comment ($comment, $comments_per_page) {
	$page = (floor(($comment['article_comment_count'] - 1) / $comments_per_page) * $comments_per_page) + 1;
	if ($comment['comment_anonymous']) {
		echo('			<li class="anonymous">'."\n");
		echo('				<i>Anonymous</i>'."\n");
	} else {
		echo('			<li>'."\n");
		echo('				<i>' . xml_escape($comment['user_firstname'] . ' ' . $comment['user_surname']) . '</i>'."\n");
	}
	echo('				on <a href="/comments/thread/' . $comment['comment_id'] . '">' . xml_escape($comment['article_content_heading']) . '</a>'."\n");
	echo('			</li>'."\n");
}

?>

	<div class="BlueBox">
		<h2>latest comments</h2>
		<ul class="comments">
			<?php foreach ($comments as $comment) print_comment($comment, $comments_per_page); ?>
		</ul>
	</div>