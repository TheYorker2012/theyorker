<div id="Footer">
	<a id="ShowFeedback" href="#FeedbackForm" onclick="showFeedback();">Please give feedback about this page</a>
	<div id="FeedbackForm" style="display: none;">
		<form id="feedback_form" action="<?php echo site_url('feedback/'); ?>" method="post" class="form">
			<fieldset>
				<h2>Feedback</h2>
				<!-- <br /> tags necessary for correct rendering in text based browsers -->
				<label for="a_authorname">Your Name: </label>
					<input type="text" name="a_authorname" id="a_authorname" value="" /><br />
				<label for="a_authoremail">Your E-mail: </label>
					<input type="text" name="a_authoremail" id="a_authoremail" value="" /><br />
				<label for="a_rating">Your Rating: </label>
					<select name="a_rating" id="a_rating" size="1">
						<option value="" selected="selected">&nbsp;</option>
						<option value="1">What's this for?</option>
						<option value="2">Good idea - but what does it do?</option>
						<option value="3">Useful.. I guess.</option>
						<option value="4">Great idea, and easy to use!</option>
						<option value="5">Amazing!!</option>
					</select><br />
				<label for="a_browser_info">Include Browser Info: </label>
					<input type="checkbox" name="a_browser_info" id="a_browser_info" value="1" /> (Please tick this if you are experiencing a problem with the site, as it will help us diagnose the fault.)<br />
				<label for="a_feedbacktext">Your Comments: </label>
					<textarea name="a_feedbacktext" id="a_feedbacktext" rows="6" cols="40" ></textarea>
				<input type="hidden" name="a_pagetitle" id="a_pagetitle" value="<?php if(isset($head_title)) { echo str_replace("'", "", $head_title); } ?>" />
				<input type="hidden" name="r_redirecturl" id="r_redirecturl" value='<?php echo $_SERVER['REQUEST_URI']; ?>' />
			</fieldset>
			<fieldset>
				<input class="button" type="submit" name="r_submit" id="r_submit" value="Submit" />
				<input class="button" type="reset" name="r_cancel" id="r_cancel" value="Cancel" onclick="hideFeedback();"/>
			</fieldset>
		</form>
	</div>
	<small>
		Copyright 2007 The Yorker. Weather data provided by Yahoo. Page rendered in {elapsed_time} seconds. <a href='/policy/#privacy_policy'>Privacy Policy</a>
	</small>
</div>