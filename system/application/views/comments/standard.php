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
?>
<h2 style="background-color: #999;color:#fff;padding:0.3em;font-size:12pt;">Comments</h2>
<?php
if (isset($CommentList)) {
	$CommentList->Load();
}
if (isset($CommentAdd)) {
	$CommentAdd->Load();
}

?>
