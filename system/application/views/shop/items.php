<div id="RightColumn">
	<h2 class="first">
		Options
	</h2>
	<div class="Entry">
		<a href="/shop/checkout/">Checkout</a>
	</div>
</div>
<div id="MainColumn">
	<div id="HomeBanner">
		<?php 
		//$this->homepage_boxes->print_homepage_banner($banner);
		?>
	</div>
	<div class="BlueBox">
		<h2>
			<?php echo($category['name']); ?>
		</h2>
		<table border="0" width="97%">
			<tbody>
<?php
	foreach ($items as $item)
	{
?>
				<tr>
					<td>
						<table border="0" width="100%">
							<tbody>
								<tr>
									<td valign="top">
										<font size="+1"><strong><a href="/shop/item/<?php echo($item['id']); ?>"><?php echo($item['name']); ?></a></strong></font>
<?php if($item['event_date'] > 0) { ?>
										<div class="Date"><?php echo($item['event_date_string']); ?></div>
<?php } ?>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td>
						<table border="0" width="100%">
							<tbody>
								<tr>
									<td width="20%" valign="top">
										<a href="/shop/item/<?php echo($item['id']); ?>"><img src="http://ecx.images-amazon.com/images/I/31qa-xPHWNL._AA115_.jpg" height="115" width="115" title="GSBP" alt="GSBP"  /></a>
									</td>
									<td width="80%" valign="top">
										<table border="0" width="100%">
											<tbody>
												<tr>
													<td>
														<div style="font-size: 0.9em;">
															<?php echo($item['blurb']); ?>
														</div>
													</td>
												</tr>
												<tr>
													<td>
														<div style="font-size: 0.9em;">
															<strong>Price: </strong> <?php echo($item['price_string']); ?><br /><strong>Availability: </strong> Limited<br /><a href="/shop/item/1">[more info]</a><br />
														</div>
													</td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
<?php
	}
?>
			</tbody>
		</table>
	</div>
</div>

<?php
/*
	echo('<div class="BlueBox"><pre>');
	print_r($data);
	echo('</pre></div>');
*/
?>