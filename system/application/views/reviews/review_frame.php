<!--Writer Header -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
  <head>
    <title><?php echo $title?></title>
    <link rel="stylesheet" href="/stylesheets/stylesheet.css" type="text/css" />
  </head>
  <body>
<!-- Page start -->
	<div id="TitleBar"><h1><?php echo $title ?></h1></div>
	<div id="ReviewSetBlock">
		<div class="ReviewSet">
			<h2><?php echo $food ?></h2>
			<img alt="<?php echo $food ?>" src="/images/prototype/reviews/reviews_07.jpg" />
			<br />
			<?php echo $foodtext ?>
			<div class="WhyNotTry">
			<h3>Why Not Try?</h3>
			<ul>
				<li>Chicken
				<li>Beef
				<li>Duck
				<li>Tuna
				<li>Pork
			</ul>
			</div>
		</div>
		<div class="ReviewSet">
			<h2><?php echo $drink ?></h2>
			<img alt="<?php echo $drink ?>" src="/images/prototype/reviews/reviews_07.jpg" />
			<br />
			<?php echo $drinktext ?>
			<div class="WhyNotTry">
			<h3>Why Not Try?</h3>
			<ul>
				<li>Coke
				<li>Hob Goblin Ale
				<li>Liquified Cake
				<li>Anal Rape
				<li>Warm Milk
			</ul>
			</div>
		</div>
		<div class="ReviewSet">
			<h2><?php echo $culture ?></h2>
			<img alt="<?php echo $culture ?>" src="/images/prototype/reviews/reviews_07.jpg" />
			<br />
			<?php echo $culturetext ?>
			<div class="WhyNotTry">
			<h3>Why Not Try?</h3>
			<ul>
				<li>Budgie-Jumping
				<li>Parrot-Shooting
				<li>Hen-Gliding
			</ul>
			</div>
		</div>
		<div class="clear">&nbsp;</div>
	</div>
<!-- Writer Footer -->
  </body>
</html>

