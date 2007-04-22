<div class='RightToolbar'>
<h4>What's this?</h4>
	<p>
		<?php echo $main_text; ?>
	</p>
<h4>Other tasks</h4>
<ul>
	<li><a href='#'>Do not show a map</a></li>
</ul>
</div>

<form id='orgdetails' name='orgdetails' action='/viparea/directory/<?php echo $organisation['shortname']; ?>/updatemap' method='POST' class='form'>
<div class='blue_box'>
	<h2>location</h2>
		<div id='googlemaps' style='height: 300px'>
		</div>
</div>
<a href='/viparea/'>Back to the vip area.</a>
