<div id="RightColumn">
	<h2 class="first">My Links</h2>
	<div class="Entry">
<?php 	if ($link->num_rows() > 0)
	{
		/// @todo FIXME data from database should be processed in the model
		foreach($link->result() as $picture){
			echo('	<a href="'.xml_escape($picture->link_url).'" target="_blank">'.
				$this->image->getImage( // getImage does the escaping
					$picture->link_image_id, 'link',
					array(
						'title'	=> $picture->link_name,
						'alt'	=> $picture->link_name,
					)
				).
				'</a>'."\n"
			);
		}
	} else {
// 		<a href="/account/links">You have no links</a>
	}
?>
		<a class="RightColumnAction"  href="/account/links">Customise</a>
	</div>
	
<?php
	if (null !== $poll_vote_box)
	{
		$poll_vote_box->Load();
	}
?>

	<h2>Search the Web</h2>
	<div class="Entry">
		<form method="get" action="http://www.google.co.uk/search" target="_blank">
			<fieldset class="inline">
				<input type="hidden" name="ie" value="UTF-8" />
				<input type="hidden" name="oe" value="UTF-8" />
				<a href="http://www.google.co.uk/" target="_blank">
					<img src="http://www.google.co.uk/logos/Logo_40wht.gif" alt="Google" />
				</a>
				<input type="text" name="q" value="" />
				<input type="submit" class="button" name="btnG" value="Search" target="_blank" />
			</fieldset>
		</form>
	</div>

	<h2>My Webmail </h2>
	<div class="Entry">
		<a class="MailLogo" href="https://webmail.york.ac.uk/" target="_blank">
			<img src="/images/prototype/news/test/webmail_large.jpg" alt="Webmail Logo" />
		</a>
		<p class="MailText">
			<a href="https://webmail.york.ac.uk/" target="_blank">E-mail</a>
		</p>
	</div>

	<h2>Upcoming Events</h2>
	<div class="Entry">
		<?php $events->Load(); ?>
	</div>
	<?php /*
	<h2>To Do</h2>
	<div class="Entry">
		<?php $todo->Load(); ?>
	</div>
	*/ ?>

<?php
if ($weather_forecast != null) {
?>
	<h2>York Weather</h2>
	<div class="Entry">
		<?php echo($weather_forecast);?>
	</div>

	<h2>Quote of the Day</h2>
	<div class="Entry">
		"<?php echo(xml_escape($quote->quote_text));?>" - <b><?php echo(xml_escape($quote->quote_author));?></b>
	</div>
<?php
}
?>
</div>

<div id="MainColumn">
	<div id="HomeBanner">
		<?php 
		$this->homepage_boxes->print_homepage_banner($banner);
		?>
	</div>

	<div class="BlueBox">
		<div style="margin:0;position:relative;">
			<img src="/images/prototype/homepage/roses08.jpg" alt="Roses 2008" />
			<table cellpadding="0" cellspacing="0" border="0" style="width:100%;position:absolute;bottom:0;text-align:center;font-size:large;background:url('/images/prototype/homepage/roses_bg.png');">
				<tr>
					<td style="width:155px;text-align:right;"><img src="images/prototype/homepage/roses_york.png" alt="York" /></td>
					<td><b>200</b></td>
					<td>&nbsp;vs&nbsp;</td>
					<td><b>200</b></td>
					<td style="width:155px;text-align:left;"><img src="images/prototype/homepage/roses_lancaster.png" alt="Lancaster" /></td>
				</tr>
			</table>
		</div>

		<h2 style="margin:0 10px">the latest</h2>

		<div style="margin:0 10px 5px 10px">
			<ul style="margin:0">
				<li><b>11:24 </b>Unfortunately Lancaster have won all the Netball matches, giving them a huge point boost :(</li>
				<li><b>10:00 </b>York mens badminton 1st have taken York into the lead with a 2pt win!</li>
				<li><b>09:31 </b>Remember, York is already 12pts behind Lancaster due to the Rowing races taking place last Sunday!</li>
				<li><b>09:30 </b>Roses has begun!</li>
			</ul>
		</div>

		<h2 style="margin:0 10px">roses articles</h2>

		<div style="margin:0 0 5px 0">
			<div class="LeftNewsBox NewsBox">
				<a class="NewsImgSmall" href="/news/football/473">
					<img src="/photos/small/1066" height="49" width="66" title="bikes" alt="bikes" class="left"  />
				</a>
				<p class="More">
					<a href="/news/netball/471">Lancaster row into the lead</a>
				</p>
			</div>
	
			<div class="RightNewsBox NewsBox">
				<a class="NewsImgSmall" href="/news/generalsport/456">
					<img src="/photos/small/1068" height="49" width="66" title="Bridge" alt="Bridge" class="left"  />
				</a>
				<p class="More">
					<a href="/news/netball/471">Roses opening ceremony</a>
				</p>
			</div>
	
			<div class="LeftNewsBox NewsBox">
				<a class="NewsImgSmall" href="/news/football/473">
					<img src="/photos/small/1069" height="49" width="66" title="bikes" alt="bikes" class="left"  />
				</a>
				<p class="More">
					<a href="/news/netball/471">Getting excited : Roses Preview</a>
				</p>
			</div>
	
			<div class="RightNewsBox NewsBox">
				<a class="NewsImgSmall" href="/news/generalsport/456">
					<img src="/photos/small/1067" height="49" width="66" title="Bridge" alt="Bridge" class="left"  />
				</a>
				<p class="More">
					<a href="/news/netball/471">Roses Fixture List &amp; Results</a>
				</p>
			</div>
		</div>

		<h2 style="margin:0 10px;clear:both">roses photos</h2>

		<div style="margin:0 10px 5px 10px">
			<?php $p_count = 0; foreach ($roses_slideshow as $p) { $p_count++; ?><a href="/gallery/<?php echo($p['id']); ?>"<?php if ($p_count % 5) { ?> style="margin-right:10px"<?php } ?>><?php echo($p['xhtml']); ?></a><?php } ?>
		</div>
	</div>

	<?php
		$this->homepage_boxes->print_box_with_picture_list($articles['uninews'],'latest news','news');
		if($special['lifestyle']['show']) { $this->homepage_boxes->print_specials_box($special['lifestyle']['title'],$special['lifestyle']['data']); }
		$this->homepage_boxes->print_box_with_picture_list($articles['arts'],'latest arts','news');
		$this->homepage_boxes->print_box_with_picture_list($articles['sport'],'latest sport','news');
		if($special['blogs']['show']) { $this->homepage_boxes->print_specials_box($special['blogs']['title'],$special['blogs']['data']); }
		$this->homepage_boxes->print_box_with_picture_list($articles['features'],'latest features','news');
		$this->homepage_boxes->print_box_with_picture_list($articles['videocasts'],'latest videocasts','news');
		$latest_comments->Load();
	?>
</div>
