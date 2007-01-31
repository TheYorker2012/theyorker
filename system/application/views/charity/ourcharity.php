<div class='RightToolbar'>
	<h4>Our Goal</h4>
	<div class='Entry'>
		<h5>What Are We Aiming For?</h5>
		<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</p>
	</div>
	
	<h4>Funding</h4>
	<div class='Entry'>
		<h5>Current Money<br />£3,982</h5>
		<h5>Amount Needed<br />£15,034</h5>
		<h5>Donate Now!!!</h5>
	</div>
	
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
</div>





<div class='grey_box'>
	<h2>Why We Chose Cancer Research</h2>
	<img src="/images/prototype/campaign/field.jpg">
	Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc. Phasellus semper. Sed lobortis porta purus. Morbi egestas elit vitae magna. Morbi mollis consequat diam. Phasellus mauris. Pellentesque non tortor. Morbi sit amet lorem eu nisl sollicitudin fringilla. Sed sapien magna, vestibulum a, pellentesque id, tempor et, eros. Proin ante nibh, convallis non, rutrum vel, pretium vel, lectus. Aliquam congue malesuada augue. Duis tellus. Integer arcu odio, scelerisque a, mattis a, interdum porta, ante. Nulla diam. Fusce nisl sapien, mattis quis, sagittis in, auctor id, sem. Etiam congue dolor vitae neque. Praesent libero metus, aliquet vel, lobortis eget, porta et, justo.Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc. Phasellus semper. Sed lobortis porta purus. Morbi egestas elit vitae magna. Morbi mollis consequat diam. Phasellus mauris. Pellentesque non tortor. Morbi sit amet lorem eu nisl sollicitudin fringilla. Sed sapien magna, vestibulum a, pellentesque id, tempor et, eros. Proin ante nibh, convallis non, rutrum vel, pretium vel, lectus. Aliquam congue malesuada augue. Duis tellus. Integer arcu odio, scelerisque a, mattis a, interdum porta, ante. Nulla diam. Fusce nisl sapien, mattis quis, sagittis in, auctor id, sem. Etiam congue dolor vitae neque. Praesent libero metus, aliquet vel, lobortis eget, porta et, justo.
</div>

<div class='blue_box'>
	<h2>Progress Report</h2><!--Needs to be fixed later-->
		<?php
			foreach ($ProgressItems as $ProgressItem) {
				echo '<div class="ProgressItem">';
				if ($ProgressItem['good'] == 'y'){
					echo '<img style="float: left" src="/images/prototype/campaign/tick.jpg">';
				} else {
					echo '<img style="float: left" src="/images/prototype/campaign/cross.jpg">';
				}
				echo '<div class="ProgressItemText">' . $ProgressItem['details'] . '</div></div><div class="breaker"></div>';
			}
		?> 
</div>

<div class='grey_box'>
	<h2>What You Can Do To Help</h2>
	Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc. Phasellus semper. Sed lobortis porta purus. Morbi egestas elit vitae magna. Morbi mollis consequat diam. Phasellus mauris. Pellentesque non tortor. Morbi sit amet lorem eu nisl sollicitudin fringilla. Sed sapien magna, vestibulum a, pellentesque id, tempor et, eros. Proin ante nibh, convallis non, rutrum vel, pretium vel, lectus. Aliquam congue malesuada augue. Duis tellus. Integer arcu odio, scelerisque a, mattis a, interdum porta, ante. Nulla diam. Fusce nisl sapien, mattis quis, sagittis in, auctor id, sem. Etiam congue dolor vitae neque. Praesent libero metus, aliquet vel, lobortis eget, porta et, justo.Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. 
</div>