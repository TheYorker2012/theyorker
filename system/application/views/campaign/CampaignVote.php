	<br>
	<div class="wholepage2"> <!-- Remove This Hack Sometime -->
	<div class="title">CAMPAIGN</div>
	<div class="Blurb"><img src="/images/prototype/campaign/field.jpg"><br><br>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc. </div>
	<div class="subtitle">PETITION</div>
	<div class="SubText">NUMBER OF SIGNATURES: <?php echo $NumOfSignatures; ?></div>
	<div class="SignBox">
		<div class="signit">SIGN PETITION</div>
		<div class="SignVoteForm">&nbsp;&nbsp;&nbsp;NAME: &nbsp;&nbsp;&nbsp;&nbsp;<input type="text" style="font-size:17px; height: 29px; border-style:solid; border-width: 2px;" value="<?php echo $Username; ?>"><BR><div class="sendbox">SEND</div></div>
	</div>
	<div class="Container">
		<div class="HalfBox">
			<div class="subheader">PROGRESS REPORT</div>
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
		<div class="HalfBox">
			<div class="subheader">COMMENTS</div>
			<div style="padding-top:10px;">
				<div style="float: left;font-weight: bold;">John Doe</div><div style="float: right;font-weight: bold;">Mon Nov 6:05</div><div class="breaker"></div>
				<div style="float: left;font-weight: bold;color: FF6A00;">I want a dog.</div><br>
				<div style="float: left;font-size: 12px;">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc. </div>
			</div>
			<div style="padding-top:10px;">
				<div style="float: left;font-weight: bold;">John Doe</div><div style="float: right;font-weight: bold;">Mon Nov 6:05</div><div class="breaker"></div>
				<div style="float: left;font-weight: bold;color: FF6A00;">This campaign rules!</div><br>
				<div style="float: left;font-size: 12px;">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc. </div>
			</div>
			<div style="padding-top:10px;">
				<div style="float: left;font-weight: bold;">John Doe</div><div style="float: right;font-weight: bold;">Mon Nov 6:05</div><div class="breaker"></div>
				<div style="float: left;font-weight: bold;color: FF6A00;">This campaign is bad.</div><br>
				<div style="float: left;font-size: 12px;">Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc. </div>
			</div>
		</div>
		<div class="breaker"></div> <!-- Remove This Hack Sometime -->
	</div>
	</div>