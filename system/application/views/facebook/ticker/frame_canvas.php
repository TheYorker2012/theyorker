<fb:fbml version="1.1">
	<fb:dashboard>
		<?php if ($selected_tab == 'myarticles') { ?><fb:create-button href="http://apps.facebook.com/theyorker/myarticles/bylines/">My Byline Settings</fb:create-button><?php } ?>
	</fb:dashboard>
	<fb:tabs>
		<fb:tab-item href="http://apps.facebook.com/theyorker/" title="Latest News"<?php echo(($selected_tab == 'latest') ? ' selected="true"' : ''); ?> />
		<fb:tab-item href="http://apps.facebook.com/theyorker/myarticles/" title="My Articles"<?php echo(($selected_tab == 'myarticles') ? ' selected="true"' : ''); ?> />
		<fb:tab-item href="http://apps.facebook.com/theyorker/invite/" title="Invite Friends"<?php echo(($selected_tab == 'invite') ? ' selected="true"' : ''); ?> />
	</fb:tabs>
	<div style="width:90%; margin: 10px auto;">
<?php if ((isset($_SESSION['fbticker_messages'])) && (count($_SESSION['fbticker_messages']) > 0)) {
	// Message types $msg[0] must have a valid from error, success and explaination
	foreach ($_SESSION['fbticker_messages'] as $msg)
		echo('		<fb:' . $msg[0] . ' message="' . $msg[1] . '" />');
	unset($_SESSION['fbticker_messages']);
} ?>
		<?php $this->load->view($view, $data); ?>
	</div>
</fb:fbml>