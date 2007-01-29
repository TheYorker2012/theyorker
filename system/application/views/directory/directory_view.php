<div class='RightToolbar'>
	<h4>Information</h4>
	<div style='text-align:center; padding: 10px 5px 10px 5px;'>
	<img width='200' src='/images/prototype/directory/about/178327854723856.jpg' />
	</div>
	<p>
		<?php if (!empty($organisation['website'])) {
			echo '<img alt="Website" name="Website" src="/images/prototype/directory/link.gif" /> <a href="'.
				$organisation['website'].'">'.$organisation['website'].'</a><br />';
		} ?>
		<?php if (!empty($organisation['email_address'])) {
			echo '<img alt="Email" name="Email" src="/images/prototype/directory/email.gif" /> '.$organisation['email_address'].'<br />';
		} ?>
		<?php if (!empty($organisation['phone_external']) or !empty($organisation['phone_internal']) or !empty($organisation['fax_number'])) {
			echo '<img alt="Phone Number" name="Phone Number" src="/images/prototype/directory/phone.gif" /> ';
			if (!empty($organisation['phone_external'])) {
				echo $organisation['phone_external'].', ';
			}
			if (!empty($organisation['phone_internal'])) {
				echo $organisation['phone_internal'].', ';
			}
			if (!empty($organisation['fax_number'])) {
				echo $organisation['fax_number'].', ';
			}
			echo "<br />";
		}
		?>
		<?php if (!empty($organisation['location']) && !empty($organisation['postcode'])) {
			echo '<img alt="Location" name="Location" src="/images/prototype/directory/flag.gif" /> '.$organisation['location'].','.$organisation['postcode'].'<br />';
		} ?>
		<?php if (!empty($organisation['open_times'])) {
			echo '<img alt="Opening Times" name="Opening Times" src="/images/prototype/directory/clock.gif" /> '.$organisation['open_times'].'<br />';
		} ?>
		<?php if (!empty($organisation['postal_address'])) {
					echo '<img alt="Address" name="Address" src="/images/prototype/directory/address.gif" /> '.$organisation['postal_address'].'<br />';
		} ?>
		<?php if (NULL === $organisation['yorkipedia']){}else{
		echo '<img alt="Yorkipedia Entry" name="Yorkipedia Entry" src="/images/prototype/directory/yorkipedia.gif" /> <a href="'.$organisation['yorkipedia']['url'].'">'.$organisation['yorkipedia']['title'].'</a>';
		}
		?>
	</p>
	<h4>Reviews</h4>
	<div style='padding: 10px 5px 10px 5px;'>
		<p>
			<a href='#'>Food review by Nick Evans</a><br />
			<a href='#'>Culture review by Nick Evans</a><br />
			<a href='#'>Drink review by Nick Evans</a><br />
		</p>
	</div>
	<h4>Related articles</h4>
	<div style='padding: 10px 5px 10px 5px;'>
		<p>
			<a href='#'>Article title 1</a><br />
			<a href='#'>Article title 2</a><br />
			<a href='#'>Article title 3</a>
		</p>
	</div>
</div><div  style="padding:0px 0px 0px 0px; width: 420px; margin: 0px;">
<div style="padding:0px 3px 0px 0px; width: 420px; margin: 0px;">
	<div style="border: 1px solid #2DC6D7; padding: 5px; font-size: small; margin-bottom: 4px; ">
		<span style="font-size: x-large;  color: rgb(32, 193, 240); ">about us</span>
		<p><?php echo $organisation['description']; ?></p>
	</div>
	<div style="border: 1px solid #999; padding: 5px; font-size: small; margin-bottom: 4px; ">
		<span style="font-size: x-large;  color: #999; ">finding us</span>
		<p>
		<img width='390' src='/images/prototype/directory/about/gmapwhereamI.png' />
		</p>
	</div>
</div>