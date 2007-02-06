	<div class='RightToolbar'>
		<h4>Common Tasks</h4>
		<ul>
			<li><a href=''>Edit Request</a></li>
			<li><a href=''>Edit Article Content</a></li>
			<li><a href=''>Accept/Reject Request</a></li>
			<li><a href=''>Move to box...</a></li>
			<li><a href=''>Publish Article</a></li>
			<li><a href=''>Attach Article</a></li>
		</ul>
		<h4>Photos</h4>
		<br />
		<h4>Photo Requests</h4>
		<br />
		<h4>Revisions</h4>
		<br />
	</div>
	<div class='blue_box'>
		<h2><?php echo $request_heading; ?></h2>
		<b>More Porters Cut</b><br />
		Look into the recent reduction of porter availabilty and the impact its having on colleges. Maybe interview
		the head of Campus Services and JCRC chairs who are leading the campaign against the cuts to porter staff.<br />
		<b>Deadline: </b>Tuesday 25th December 2007<br />
		<b>Requested by: </b>Father Christmas<br />
		<b>Assigned to: </b><br />
		Prancer (accepted)<br />
		Rudolph (pending red nose)
	</div>
	<form name='article_edit' id='article_edit' action='/office/news/article/#ID#' method='post' class='form'>
		<div class='grey_box'>
			<fieldset>
				<label for='r_headline'>Headline:</label>
				<input type='text' name='r_headline' id='r_headline' value='' size='30' />
				<br />
				<label for='r_brief' class='full'>Article Content:</label>
				<textarea name='r_content' id='r_content' cols='52' rows='15'></textarea>
			    <br />
			</fieldset>
		</div>
	<div style='width: 422px;'>
	 	<input type='submit' name='submit' id='submit' value='Update Changes' class='button' />
	</div>
	</form>
