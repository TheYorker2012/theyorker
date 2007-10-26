		<fb:request-form type="The Yorker" action="http://apps.facebook.com/theyorker/invite/"
		method="post" invite="true" content="The Yorker provides online independent student
		news. Why not check out the website and find out what\'s hot in York?
		<fb:req-choice url=\'http://www.facebook.com/add.php?api_key=' . $this->fb_config['ticker']['api_key'] . '\' label=\'Read the latest news!\' />">
			<fb:multi-friend-selector actiontext="Select the friends you wish to invite to add The Yorker application." bypass="cancel" />
		</fb:request-form>