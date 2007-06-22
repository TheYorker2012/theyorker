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
<a id="comments">&nbsp;</a>
<?php
if (isset($CommentThread)) {
	$CommentThread->Load();
}
if (isset($CommentAdd)) {
	$CommentAdd->Load();
}
if (isset($CommentList)) {
	$CommentList->Load();
}

?>