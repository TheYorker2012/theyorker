	<div style="width: 220px; margin: 0; padding-left: 3px; float: right; ">
	<h4><?php echo $sections['sidebar_petition']['title']; ?></h4>
	<p style="color: #FF9933; margin-bottom: 10px; font-size:x-large; text-align: center; margin-top: 10px;">
		<?php echo str_replace("%%count%%", $campaign['signatures'], $sections['sidebar_petition']['text']); ?>
	</p>
	
	<h4><?php echo $sections['sidebar_sign']['title']; ?></h4>
	<div class='Entry'>
		<?php echo str_replace("%%name%%", "User's Name", $sections['sidebar_sign']['text']); ?> #TODO</p>
	</div>
	
	<h4><?php echo $sections['sidebar_more']['title']; ?></h4>
	<?php echo $sections['sidebar_more']['text']; ?>
	
	<h4><?php echo $sections['sidebar_related']['title']; ?></h4><!--Next 2 sections basically the same with different data and links-->
	<p style="margin-top: 0px; padding: 8px;">

	</p>

	<h4><?php echo $sections['sidebar_external']['title']; ?></h4>
	<p style="margin-top: 0px; padding: 8px;">

	</p>
	
    	<h4><?php echo $sections['sidebar_comments']['title']; ?></h4>
	<p style="margin-top: 0px; padding: 8px;">#TODO</p>
	
	</div>

	 
	
<div style="width: 420px; margin: 0px; padding-right: 3px; ">

	<div style="border: 1px solid #BBBBBB; padding: 6px; font-size: small; margin-bottom: 4px; ">
	<span style="font-size: x-large;  color: #BBBBBB; "><?php echo $sections['our_campaign']['title']; ?></span><br />
		<?php echo $sections['article']['text']; ?>
	</div>

	<div style="border: 1px solid #2DC6D7; padding: 6px; font-size: small; margin-bottom: 4px; ">
	<span style="font-size: x-large;  color: #2DC6D7; "><?php echo $sections['article']['fact_boxes']['title']; ?></span><br />
		<?php echo $sections['article']['fact_boxes']['wikitext']; ?>
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