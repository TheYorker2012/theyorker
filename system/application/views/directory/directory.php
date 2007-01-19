<p>The Directory contains many different organisations. Every page has useful information about each organisation such as its contact details, reviews, events and members</p>
<div>
<div id='searchresults' style="padding:0px 0px 0px 0px">
    <ul>
	<?php
	foreach ($organisations as $organisation) { ?>
		<li>
			<?php echo '<a href=\'/directory/' . $organisation['shortname'] . '\' style="display: inline;">' . $organisation['name']; ?></a>
			<span style='font-size: 12px'>(<?php echo $organisation['type']; ?>)</span><br />
			<?php echo $organisation['description']; ?>
		</li>
	<?php } ?>
	</ul>
</div>
<div align='center'>
	<h5>Showing <?php echo count($organisations); ?> results.</h5>
</div>
<form name='search_directory' action='/directory/' method='POST' class='form'>
	<fieldset>
		<legend>Search</legend>
		<ul>
			<li>
				Venues <input type='checkbox' name='venues' value="checked" >
			</li>
			<li>
				Societies <input type='checkbox' name='societies' value="checked">
			</li>
			<li>
				Athletics Union <input type='checkbox' name='athletics_union' value="checked">
			</li>
			<li>
				Organisation <input type='checkbox' name='organisation' value="checked">
			</li>
			<li>
			College &#038; Campus <input type='checkbox' name='college_campus' value="checked">
			</li>
		</ul>
		<input type='text' name='search'>
		<input type='submit' name='Submit' value='Search'>
	</fieldset>
	<fieldset>
	</fieldset>
</form>