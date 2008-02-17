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
		<h2>Shop Categories</h2>
		<table border="0" width="97%">
			<tbody>
<?php
	foreach ($categories as $category)
	{
?>
				<tr>
					<td>
						<font size="+1"><strong><a href="/shop/view/<?php echo($category['id']); ?>/"><?php echo($category['name']); ?></a></strong></font>
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