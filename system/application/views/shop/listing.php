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
	<?php
	//$this->homepage_boxes->print_box_with_picture_list($main_articles,$latest_heading,'news');
	//if($show_featured_puffer) $this->homepage_boxes->print_specials_box($featured_puffer_title,$featured_puffer);
	//if(!empty($lists_of_more_articles)) $this->homepage_boxes->print_box_of_category_lists($more_heading,$more_article_types,$lists_of_more_articles);
	?>
	<div class="BlueBox">
		<table border="0" width="97%">
			<tbody>
				<tr>
					<td>
						Showing <b>all entries</b> in the tickets category.
					</td>
				</tr>
				<tr>
					<td>
						<hr style="height: 1px; border: 0; color: #20c1f0; background-color: #20c1f0;"/>
					</td>
				</tr>
				<tr>
					<td>
						<table border="0" width="100%">
							<tbody>
								<tr>
									<td valign="top">
										<font size="+1"><strong><a href="/shop/view/tickets/moreinfo/1">LAPD</a></strong></font>
										<div class="Date">Saturday, 2nd February 2008</div>
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
										<a href="/shop/view/tickets/moreinfo/1"><img src="http://profile.ak.facebook.com/object2/1387/87/n6481734985_6626.jpg" height="150" width="100" title="LAPD" alt="LAPD" /></a>
									</td>
									<td width="80%" valign="top">
										<table border="0" width="100%">
											<tbody>
												<tr>
													<td>
														<div style="font-size: 0.9em;">
															Derwent brings you LAPD. A chance for students to be on the other side of the law.
														</div>
													</td>
												</tr>
												<tr>
													<td>
														<div style="font-size: 0.9em;">
															<strong>Type: </strong> Ticket<br /><strong>Price: </strong> &pound;4.00<br /><strong>Availability: </strong> Limited<br /><a href="/shop/view/tickets/moreinfo/1">[more info]</a><br />
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
				<tr>
					<td>
						<hr style="height: 1px; border: 0; color: #20c1f0; background-color: #20c1f0;"/>
					</td>
				</tr>
				<tr>
					<td>
						<table border="0" width="100%">
							<tbody>
								<tr>
									<td valign="top">
										<font size="+1"><strong>GoodShack Beach Party</strong></font>
										<div class="Date">Friday, 6th June 2008</div>
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
										<img src="http://ecx.images-amazon.com/images/I/31qa-xPHWNL._AA115_.jpg" height="115" width="115" title="GSBP" alt="GSBP"  />
									</td>
									<td width="80%" valign="top">
										Goodricke presents THE Beach Party of the summer. Including performances from DanceSoc & Juggle Soc from 10pm and featuring Gallery/Toffs DJ Sarah Forster. <a href="/shop/view/tickets/moreinfo/2">[more info]</a>
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<div style="font-size: 0.9em;">
											<strong>Type: </strong> Ticket<br /><strong>Price: </strong> &pound;3.50<br /><strong>Availability: </strong> Good<br />
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
</div>

<?php

	echo('<div class="BlueBox"><pre>');
	print_r($data);
	echo('</pre></div>');
	
?>