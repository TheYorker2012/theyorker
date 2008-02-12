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
			<?php echo($item['name']); ?>
		</h2>
		<table border="0" width="100%">
			<tbody>
				<tr>
					<td width="20%" valign="top">
						<a href="/shop/item/<?php echo($item['id']); ?>"><img src="http://ecx.images-amazon.com/images/I/31qa-xPHWNL._AA115_.jpg" height="115" width="115" title="GSBP" alt="GSBP"  /></a>
					</td>
					<td width="80%" valign="top">
						<table border="0" width="100%">
							<tbody>
<?php if($item['event_date'] > 0) { ?>
								<tr>
									<td>
										<div class="Entry">
											<?php echo($item['event_date_string']); ?>
										</div>
									</td>
								</tr>
<?php } ?>
								<tr>
									<td>
										<div class="Entry">
											<?php echo($item['description']); ?>
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class="Entry">
											<strong>Price: </strong> <?php echo($item['price_string']); ?><br /><strong>Availability: </strong> Limited<br />
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

<?php
/*
	echo('<div class="BlueBox"><pre>');
	print_r($data);
	echo('</pre></div>');
*/
?>

</div>