	<div style="width: 220px; margin: 0; padding-left: 3px; float: right; ">
	<h4>Petition</h4>
	<p style="color: #FF9933; margin-bottom: 10px; font-size:x-large; text-align: center; margin-top: 10px;">239 Signatures</p>
	
	<h4>Sign</h4>
	<div class='Entry'>
		<p>I, <b>John Smith</b>, agree to the argument made on this page. Please register my opinion.<br><br>Please re-enter your password to sign: #TODO</p>
	</div>
	
	<h4><?php echo $sections['sidebar_more']['title']; ?></h4>
	<p style="margin-top: 0px; padding: 8px;">
	Email :<br><span style="color: #999; font-weight: bold;">campaigns@theyorker.co.uk</span><br><!--Could do with centering-->
	Or Write :<br>
	<span style="color: #999; font-weight: bold;">The Yorker<br><!--Change to "BoldText" or other thing insted of re-styleing-->
	University of York<br>
	YO10 5ND</span>
	</p>
	
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

	 
	
<div style="width: 420px; margin: 0px; padding-right: 3px; ">

	<div style="border: 1px solid #BBBBBB; padding: 6px; font-size: small; margin-bottom: 4px; ">
	<span style="font-size: x-large;  color: #BBBBBB; ">our campaign</span><br />
		Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.Lorem ipsum dolor sit amet, consectetuer adipiscing elit. 
	</div>

	<div style="border: 1px solid #2DC6D7; padding: 6px; font-size: small; margin-bottom: 4px; ">
	<span style="font-size: x-large;  color: #2DC6D7; ">facts</span><br />
		<ul style="padding-left: 15px; margin-top: 5px; margin-bottom: 0px;">
		<li style="padding-bottom: 15px;">Counting only the economic value of fisheries, tourism, and shoreline protection, the costs of destroying 1km of coral reef ranges between US$137,000-1,200,000 over a 25-year period.</li>
		<li style="padding-bottom: 15px;">Properly managed coral reefs can yield an average of 15 tonnes of fish and other seafood per square kilometre each year.</li>
		<li style="padding-bottom: 15px;">Cake is a made up drug.</li>
		</ul>
	</div>
	
	<div style="border: 1px solid #BBBBBB; padding: 6px; font-size: small; margin-bottom: 4px; ">
	<span style="font-size: x-large;  color: #BBBBBB; ">progress report</span><br />
		<br>
		<div style="float: right; ">
		<a href='/news/oarticle/2'><img src='/images/prototype/news/thumb3.jpg' alt='Some Spy' title='Some Spy' /></a>
		</div>
		<span style="font-size: large;  color: #BBBBBB; ">24/12/2006 3:15PM</span><br />
		Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.

		<br><br>
		<div style="float: right; ">
		<a href='/news/oarticle/2'><img src='/images/prototype/news/thumb3.jpg' alt='Some Spy' title='Some Spy' /></a>
		</div>
		<span style="font-size: large;  color: #BBBBBB; ">24/12/2006 3:15PM</span><br />
		Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.Lorem ipsum dolor sit amet, consectetuer adipiscing elit. 
	</div>

</div>