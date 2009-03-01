<?php
/**
 * @file views/comments/standard.php
 * @brief Standard comment layout.
 *
 * @param $CommentThread ViewsView.
 * @param $CommentAdd    ViewsView.
 * @param $CommentList   ViewsView.
 */
?>

<div class="CommentsTitle">
	<div>
		<a href="#comments" id="comments">comments</a>
	</div>
</div>

<?php
if (isset($CommentThread)) {
	$CommentThread->Load();
}
?>
<?php
if (isset($CommentList)) {
	$CommentList->Load();
}
if (isset($CommentAdd)) {
	$CommentAdd->Load();
}
?>