	<br>
	<div class="wholepage2"> <!-- Remove This Hack Sometime -->
	<div class="title">Our Charity</div>
	<div class="Blurb"><img src="<?php echo $Picture; ?>"><br><br>
	
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
		Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text
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
		Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text, Descriptive Text
		<div class="ContainerHalf">
			<div class="CharityRelated">
				<div style="font-weight: bold; font-size: 11px;">Related News Stories</div>
				<div style="font-weight: bold; font-size: 9px;">>Boy Can Fly</div>
			</div>
			<div class="CharityRelated">
				<div style="font-weight: bold; font-size: 11px;">External Links</div>
				<div style="font-weight: bold; font-size: 9px;">>Boy Can Fly</div>
			</div>
		</div>
	</div>
	</div>