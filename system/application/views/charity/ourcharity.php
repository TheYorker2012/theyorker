	<div class="Blurb"><img src="/images/prototype/campaign/field.jpg" width='600'>
	<div class="Container">
	<div class="HalfBox">
		<div class="BlackBox">
			<div class="BlackBoxTitle">Our Goal</div>
			<div class="WhiteFullBox">This Is Our Goal. This Is Our Goal. This Is Our Goal. This Is Our Goal. This Is Our Goal. This Is Our Goal. This Is Our Goal. This Is Our Goal.</div>
			<div class="BlackBoxSubTitle">Amount Needed:<br>£15,034</div>
		</div>
	</div>
	<div class="HalfBox">
		<div class="BlackBox">
				<div class="BlackBoxTitle">Current Money:</div>
				<div class="WhiteBox">£3,982</div>
				<div class="BlackBoxTitle">Donate Now!</div>
		</div>
	</div>
	<div class="breaker"></div> <!-- Remove This Hack Sometime -->
	</div>
		<div class="CharitySubText">Why We Chose Cancer Research</div>
		Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc. Phasellus semper. Sed lobortis porta purus. Morbi egestas elit vitae magna. Morbi mollis consequat diam. Phasellus mauris. Pellentesque non tortor. Morbi sit amet lorem eu nisl sollicitudin fringilla. Sed sapien magna, vestibulum a, pellentesque id, tempor et, eros. Proin ante nibh, convallis non, rutrum vel, pretium vel, lectus. Aliquam congue malesuada augue. Duis tellus. Integer arcu odio, scelerisque a, mattis a, interdum porta, ante. Nulla diam. Fusce nisl sapien, mattis quis, sagittis in, auctor id, sem. Etiam congue dolor vitae neque. Praesent libero metus, aliquet vel, lobortis eget, porta et, justo.Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc. Phasellus semper. Sed lobortis porta purus. Morbi egestas elit vitae magna. Morbi mollis consequat diam. Phasellus mauris. Pellentesque non tortor. Morbi sit amet lorem eu nisl sollicitudin fringilla. Sed sapien magna, vestibulum a, pellentesque id, tempor et, eros. Proin ante nibh, convallis non, rutrum vel, pretium vel, lectus. Aliquam congue malesuada augue. Duis tellus. Integer arcu odio, scelerisque a, mattis a, interdum porta, ante. Nulla diam. Fusce nisl sapien, mattis quis, sagittis in, auctor id, sem. Etiam congue dolor vitae neque. Praesent libero metus, aliquet vel, lobortis eget, porta et, justo.
		<div class="FloatRightHalf"><div class="subheader">Progress Report</div>
		<?php
			foreach ($ProgressItems as $ProgressItem) {
				echo '<div class="ProgressItem">';
				if ($ProgressItem['good'] == 'y'){
					echo '<img style="float: left" src="http://localhost/images/prototype/campaign/tick.jpg">'; //change from localhost
				} else {
					echo '<img style="float: left" src="http://localhost/images/prototype/campaign/cross.jpg">'; //change from localhost
				}
				echo '<div class="ProgressItemText">' . $ProgressItem['details'] . '</div></div><div class="breaker"></div>';
			}
		?> 
		</div>	
		<div class="CharitySubText">What You Can Do To Help</div>
		Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc. Phasellus semper. Sed lobortis porta purus. Morbi egestas elit vitae magna. Morbi mollis consequat diam. Phasellus mauris. Pellentesque non tortor. Morbi sit amet lorem eu nisl sollicitudin fringilla. Sed sapien magna, vestibulum a, pellentesque id, tempor et, eros. Proin ante nibh, convallis non, rutrum vel, pretium vel, lectus. Aliquam congue malesuada augue. Duis tellus. Integer arcu odio, scelerisque a, mattis a, interdum porta, ante. Nulla diam. Fusce nisl sapien, mattis quis, sagittis in, auctor id, sem. Etiam congue dolor vitae neque. Praesent libero metus, aliquet vel, lobortis eget, porta et, justo.
		<div class="Container">
			<div class="CharityRelated">
				<div style="font-weight: bold; font-size: 20px;">Related News Stories</div>
				<div style="font-weight: bold; font-size: 13px;">> Boy Can Fly</div>
			</div>
			<div class="CharityRelated">
				<div style="font-weight: bold; font-size: 20px;">External Links</div>
				<div style="font-weight: bold; font-size: 13px;">> Boy Can Fly</div>
			</div>
		</div>
	</div>
<div align='center' id='related_pages'>
Related pages : <a href='/about/'>About Us</a> | <a href='/contact/'>Contact Us</a> | <a href='/policy/'>Our Policy</a> | <a href='/charity/'>Sponsored Charity</a>
</div>
