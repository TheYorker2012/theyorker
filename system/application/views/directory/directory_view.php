<div class='RightToolbar'>
	<div class='RightToolbarHeader'>
	Information
	</div>
	<p style='text-align:center;'>
	<img width='220' src='/images/prototype/directory/about/178327854723856.jpg' />
	</p>
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
		<li><a href=''>Static Data1</a></li>
		<li><a href=''>Static Data2</a></li>
		<li><a href=''>Static Data3</a></li>
		<li><a href=''>Static Data4</a></li>
	</ul>
	<div class='RightToolbarHeader'>
	Related Articles
	</div>
	<ul>
		<li><a href=''>Static Data1</a></li>
		<li><a href=''>Static Data2</a></li>
		<li><a href=''>Static Data3</a></li>
		<li><a href=''>Static Data4</a></li>
	</ul>
</div>
<h2>About Us</h2>
<p><?php echo $organisation['description']; ?></p>
<h2>Finding Us</h2>
<img width='400' src='/images/prototype/directory/about/gmapwhereamI.png' />