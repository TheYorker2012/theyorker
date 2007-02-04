<div class="RightToolbar">
	<h4>Search by...</h4>
	<div class="Entry">
		<form id="form">
			<input type="radio" name="searchcriteria" />Title<br />
			<input type="radio" name="searchcriteria" />Tag<br />
			<input type="radio" name="searchcriteria" />Photographer<br /><br />
			Search Criteria:<input type="text" />
			<input type="submit" class="buttom" value="Search" />
		</form>
	</div>
	<h4>Advanced</h4>
	<div class="Entry">
		<form id="form">
			Order by:<br />
			<input type="radio" name="order" />Title<br />
			<input type="radio" name="order" />Date<br />
			<input type="radio" name="order" />Photographer<br /><br />
			Show only tags:<br />
			<select name="tag">
				<option value="pope">Pope</option>
				<option value="pope">Students</option>
				<option value="pope">Bob</option>
				<option value="pope">Gary</option>
			</select><br /><br />
			Show only photographers:<br />
			<select name="tag">
				<option value="pope">Pope</option>
				<option value="pope">Students</option>
				<option selected value="pope">Bob</option>
				<option value="pope">Gary</option>
			</select><br /><br />
			<input type="submit" class="buttom" value="Display" />
		</form>
	</div>
</div>
<div class="blue_box">
	<?php
		// Load a subview.
		$content[0]->Load();
	?>
</div>
