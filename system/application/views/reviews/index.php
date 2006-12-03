<div id='pageheader'>
	<h1><img alt="Reviews" src="<?php echo $title_image ?>" /></h1>
	<div id='pagelinks'>
		<ul>
			<li><a href='/review/food'>Food</a></li>
			<li><a href='/review/drink'>Drink</a></li>
			<li><a href='/review/culture'>Culture</a></li>
		</ul>
	</div>
</div>
<table class="ReviewSetBlock">
	<tr>
	<td>
		<table class="ReviewSet">
		<tr><td>
		<div class="ReviewSetHeader"><h2><a href="/review/food">FOOD</a></h2></div>
		<img alt="Food" src="<?php echo $food_image ?>" />
		<?php echo $food_text ?>
		<div class="WhyNotTry">
		<h3>Why Not Try?</h3>
		<ul>
			<li><a href="review/foodreview"><?php echo $food_try1 ?></a>
			<li><?php echo $food_try2 ?>
			<li><?php echo $food_try3 ?>
			<li><?php echo $food_try4 ?>
			<li><?php echo $food_try5 ?>
		</ul>
		</div>
		</td></tr>
		</table>
	</td>
	<td>
		<table class="ReviewSet">
		<tr><td>
		<div class="ReviewSetHeader"><h2><a href="/review/drink">DRINK</a></h2></div>
		<img alt="Drink" src="<?php echo $drink_image ?>" />
		<?php echo $drink_text ?>
		<div class="WhyNotTry">
		<h3>Why Not Try?</h3>
		<ul>
			<li><a href="review/foodreview"><?php echo $drink_try1 ?></a>
			<li><?php echo $drink_try2 ?>
			<li><?php echo $drink_try3 ?>
			<li><?php echo $drink_try4 ?>
			<li><?php echo $drink_try5 ?>
		</ul>
		</div>
		</td></tr>
		</table>
	</td>
	<td>
		<table class="ReviewSet">
		<tr><td>
		<div class="ReviewSetHeader"><h2><a href="/review/culture">CULTURE</a></h2></div>
		<img alt="Culture" src="<?php echo $culture_image ?>" />
		<?php echo $culture_text ?>
		<div class="WhyNotTry">
		<h3>Why Not Try?</h3>
		<ul>
			<li><a href="review/culturereview"><?php echo $culture_try1 ?></a>
			<li><?php echo $culture_try2 ?>
			<li><?php echo $culture_try3 ?>
			<li><?php echo $culture_try4 ?>
			<li><?php echo $culture_try5 ?>
		</ul>
		</div>
		</td></tr>
		</table>
	</td>
	</tr>
</table>

