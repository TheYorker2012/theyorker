<div class='RightToolbar'>
	<h4><?php echo $sections['sidebar_vote']['title']; ?></h4>
	<?php $sidebar_vote_text = str_replace("%%name%%", "User's Name", $sections['sidebar_vote']['text']); ?>
	<?php echo $sidebar_vote_text; ?>#TODO<br /><br /><!--ad padding, and the submit button-->
	
	<h4><?php echo $sections['sidebar_other_campaigns']['title']; ?></h4>
	<p style="margin-top: 0px; padding: 8px;">
	<?php
        foreach ($campaign_list as $key => $campaigns)
	{
		if ($key != $selected_campaign)
			echo '<b><a href="'.site_url('campaign/details/').'/'.$key.'">'.$campaigns['name'].'</a></b><br />';
	};
	?>
	</p>
	
	<h4><?php echo $sections['sidebar_more']['title']; ?></h4>
	<?php echo $sections['sidebar_more']['text']; ?>
	
	<h4><?php echo $sections['sidebar_related']['title']; ?></h4><!--Next 2 sections basically the same with different data and links-->
	<p style="margin-top: 0px; padding: 8px;">
	<?php
        foreach ($campaign_list as $key => $campaigns)
	{
		if ($key != $selected_campaign)
			echo '<b><a href="'.site_url('campaign/details/').'/'.$key.'">'.$campaigns['name'].'</a></b><br />';
	};
	?>
	</p>
	
	<h4><?php echo $sections['sidebar_external']['title']; ?></h4>
	<p style="margin-top: 0px; padding: 8px;">
	<?php
        foreach ($campaign_list as $key => $campaigns)
	{
		if ($key != $selected_campaign)
			echo '<b><a href="'.site_url('campaign/details/').'/'.$key.'">'.$campaigns['name'].'</a></b><br />';
	};
	?>
	</p>
	
    	<h4><?php echo $sections['sidebar_comments']['title']; ?></h4>
	<p style="margin-top: 0px; padding: 8px;">#TODO</p>
</div>

<div class='grey_box'>
	<h2>why ban cake?</h2><!--Replace with "why #Title#?"-->
	Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.Lorem ipsum dolor sit amet, consectetuer adipiscing elit.<!--Replace with campaign description-->
</div>

<div class='blue_box'>
	<h2>facts</h2>
	<ul style="padding-left: 15px; margin-top: 5px; margin-bottom: 0px;"><!--Got me an array of facts sir?-->
		<li style="padding-bottom: 15px;">Counting only the economic value of fisheries, tourism, and shoreline protection, the costs of destroying 1km of coral reef ranges between US$137,000-1,200,000 over a 25-year period.</li>
		<li style="padding-bottom: 15px;">Properly managed coral reefs can yield an average of 15 tonnes of fish and other seafood per square kilometre each year.</li>
		<li style="padding-bottom: 15px;">Cake is a made up drug.</li>
	</ul>
</div>