<div id="Footer">
	<a id="ShowFeedback" href="#FeedbackForm" onclick="showFeedback();">Please give feedback about this page</a>
	<div id="FeedbackForm" style="display: none;">
		<form id="feedback_form" action="<?php echo(site_url('feedback/')); ?>" method="post" class="form">
			<fieldset>
				<h2>Feedback</h2>
				<!-- &lt;br /&gt; tags necessary for correct rendering in text based browsers -->
				<label for="a_authorname">Your Name: </label>
					<input type="text" name="a_authorname" id="a_authorname" size="40" value="" /><br />
				<label for="a_authoremail">Your E-mail: </label>
					<input type="text" name="a_authoremail" id="a_authoremail" size="40" value="" /><br />
				<label for="a_rating">Your Rating: </label>
					<select name="a_rating" id="a_rating" size="1">
						<option value="" selected="selected">&nbsp;</option>
						<option value="1">What's this for?</option>
						<option value="2">Good idea - but what does it do?</option>
						<option value="3">Useful.. I guess.</option>
						<option value="4">Great idea, and easy to use!</option>
						<option value="5">Amazing!!</option>
					</select><br />
				<?php if (isset($this->feedback_article_heading)) { ?>
				<label for="a_articleheading">Article: </label>
					<input type="text" name="a_articleheading" id="a_articleheading" size="40" value="<?php echo(xml_escape($this->feedback_article_heading)); ?>" /><br />
				<?php } ?>
				<label for="a_browser_info">Include Browser Information<br />(To help diagnose a fault): </label>
					<input type="checkbox" name="a_browser_info" id="a_browser_info" value="1" checked="checked" /><br />
				<?php /* spambot detection hidden field, should remain blank */ ?><div style="display:none">
					<input type="text" name="email" id="a_email" value="" />
				</div>
				<label for="a_feedbacktext">Your Comments: </label>
					<textarea name="a_feedbacktext" id="a_feedbacktext" rows="6" cols="40" ></textarea>
				<input type="hidden" name="a_pagetitle" id="a_pagetitle" value="<?php if(isset($head_title)) { echo(xml_escape($head_title)); } ?>" />
				<input type="hidden" name="r_redirecturl" id="r_redirecturl" value="<?php echo($_SERVER['REQUEST_URI']); ?>" />
			</fieldset>
			<fieldset>
				<input class="button" type="submit" name="r_submit" id="r_submit" value="Submit" />
				<input class="button" type="reset" name="r_cancel" id="r_cancel" value="Cancel" onclick="hideFeedback();"/>
			</fieldset>
		</form>
	</div>
	<small>
		Copyright 2007 The Yorker. Weather data provided by Yahoo. <a href='/policy/'>Privacy Policy</a>
	</small>
</div>
