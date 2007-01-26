<div class="clear">&nbsp;</div>
<div class='RightToolbar'>
	<div class='RightToolbarHeader'>
	Information
	</div>
	<div style='text-align:center; padding: 10px 5px 10px 5px;'>
	<img width='200' src='/images/prototype/directory/about/178327854723856.jpg' />
	</div>
	<p>
		<?php if (!empty($organisation['website'])) {
			echo '<img alt="Website" name="Website" src="/images/prototype/directory/link.gif" /> <a href="'.
				$organisation['website'].'">'.$organisation['website'].'</a><br />';
		} ?>
		<?php if (!empty($organisation['location'])) {
			echo '<img alt="Location" name="Location" src="/images/prototype/directory/flag.gif" /> '.$organisation['location'].'<br />';
		} ?>
		<?php if (!empty($organisation['open_times'])) {
			echo '<img alt="Opening Times" name="Opening Times" src="/images/prototype/directory/clock.gif" /> '.$organisation['open_times'].'<br />';
		} ?>
		<?php if (NULL === $organisation['yorkipedia']){}else{
		echo '<img alt="Yorkipedia Entry" name="Yorkipedia Entry" src="/images/prototype/directory/yorkipedia.gif" /> <a href="'.$organisation['yorkipedia']['url'].'">'.$organisation['yorkipedia']['title'].'</a>';
		}
		?>
	</p>
	<div class='RightToolbarHeader'>
	Reviews
	</div>
	<ul>
		<li><a href='#'>Static Data1</a></li>
		<li><a href='#'>Static Data2</a></li>
		<li><a href='#'>Static Data3</a></li>
		<li><a href='#'>Static Data4</a></li>
	</ul>
	<div class='RightToolbarHeader'>
	Related Articles
	</div>
	<ul>
		<li><a href='#'>Static Data1</a></li>
		<li><a href='#'>Static Data2</a></li>
		<li><a href='#'>Static Data3</a></li>
		<li><a href='#'>Static Data4</a></li>
	</ul>
</div>
<div style="width: 400px; margin: 0px; padding-right: 3px; ">
	<div style="border: 1px solid #2DC6D7; padding: 5px; font-size: small; margin-bottom: 4px; ">
		<span style="font-size: x-large;  color: #2DC6D7; ">About Us</span>
		<p><?php echo $organisation['description']; ?></p>
	</div>
	<div style="border: 1px solid #BBBBBB; padding: 5px; font-size: small; margin-bottom: 4px; ">
		<span style="font-size: x-large;  color: #BBBBBB; ">Finding Us</span>
		<p>
		<img width='390' src='/images/prototype/directory/about/gmapwhereamI.png' />
		</p>
	</div>
</div>