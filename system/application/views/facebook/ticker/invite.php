		<fb:request-form type="The Yorker" action="http://apps.facebook.com/theyorker/invite/"
		method="post" invite="true" content="The Yorker provides online independent student
		news. Why not check out the website and find out what's hot in York?
		<fb:req-choice url='http://www.facebook.com/add.php?api_key=<?php echo($this->facebook_ticker->fb_config['ticker']['api_key']); ?>' label='Read the latest news!' />">
			<fb:multi-friend-selector actiontext="Your following friends haven't added the The Yorker application, why not invite them?" bypass="cancel" exclude_ids="<?php echo(implode(',', $app_users)); ?>" />
		</fb:request-form>