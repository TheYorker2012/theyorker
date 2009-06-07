<?php

/**
 *	@file	views/comments/latest_box.php
 *	@brief	View for listing the most recent comments on the homepage
 *	@author	Chris Travis (cdt502 - ctravis@gmail.com)
 */

switch ($size) {
	case '1/2':
		$box_size = 'Box12';
		break;
	case '1/3':
		$box_size = 'Box13';
		break;
	case '2/3':
		$box_size = 'Box23';
		break;
	default:
		$box_size = '';
}


function print_comment ($comment, $comments_per_page) {
	$page = (floor(($comment['article_comment_count'] - 1) / $comments_per_page) * $comments_per_page) + 1;
	if ($comment['comment_anonymous']) {
		echo('			<li class="anonymous">'."\n");
		echo('				<i>Anonymous</i>'."\n");
	} else {
		echo('			<li>'."\n");
		echo('				<i>' . xml_escape($comment['user_firstname'] . ' ' . $comment['user_surname']) . '</i>'."\n");
	}
	echo('				on <a href="/comments/thread/' . $comment['comment_id'] . '">' . xml_escape($comment['heading']) . '</a>'."\n");
	echo('			</li>'."\n");
}

?>

<div class="FlexiBox<?php if (!empty($box_size)) { echo(' ' . $box_size); } if (!empty($last)) { echo(' FlexiBoxLast'); } ?>" style="float:right;">
	<div class="ArticleListTitle">
<?php if (!empty($title_link)) { ?>
		<a href="<?php echo(xml_escape($title_link)); ?>">
<?php } ?>
		<?php echo(xml_escape($title)); ?>
<?php if (!empty($title_link)) { ?>
		</a>
<?php } ?>
	</div>
	<ul class="comments" style="margin:0 5px;">
<?php foreach ($comments as $comment) print_comment($comment, $comments_per_page); ?>
	</ul>
</div>
