<!--
Weather data provided by Yahoo.
-->
<!--
<div id="Footer">
	<ul class="FooterLinks">
		<li class="first"><a href="/about">About Us</a></li>
		<li><a href="#">Join Us</a></li>
		<li><a href="#">Contact Us</a></li>
		<li><a href="/policy">Privacy Policy</a></li>
	</ul>
	<ul class="FooterLinks">
		<li class="first"><a href="/feeds">RSS Feeds</a></li>
		<li><a href="#">Advertising</a></li>
		<li><a href="#">FAQs</a></li>
	</ul>
	<div id="FooterCopyright">
		Copyright &copy; 2007-<?php echo(date('y')); ?> The Yorker. All Rights Reserved.
	</div>
	<div id="FooterFeedback">
		<a id="ShowFeedback" href="#FeedbackForm" onclick="showFeedback();">What do you think of this page?</a>
		<div id="FeedbackForm">
			<img src="/images/version2/frame/speech_arrow.gif" alt="Feedback Form" /><form id="feedback_form" action="<?php echo(site_url('feedback/')); ?>" method="post" class="form"><fieldset><h2>Feedback</h2>
					<!-- &lt;br /&gt; tags necessary for correct rendering in text based browsers -->
					<label for="a_authorname">Your Name: </label>
					<input type="text" name="a_authorname" id="a_authorname" size="40" value="" />
					<br />
					<label for="a_authoremail">Your E-mail: </label>
					<input type="text" name="a_authoremail" id="a_authoremail" size="40" value="" />
					<br />
					<label for="a_rating">Your Rating: </label>
					<select name="a_rating" id="a_rating" size="1">
						<option value="" selected="selected">&nbsp;</option>
						<option value="1">What's this for?</option>
						<option value="2">Good idea - but what does it do?</option>
						<option value="3">Useful.. I guess.</option>
						<option value="4">Great idea, and easy to use!</option>
						<option value="5">Amazing!!</option>
					</select>
					<br />
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
					<input class="button" type="reset" name="r_cancel" id="r_cancel" value="Cancel" onclick="showFeedback();"/>
				</fieldset>
			</form>
		</div>
	</div>
	<div class="clear"></div>
</div>
-->

<style type="text/css">
div.bottomlinks a {
	color: #000;
}
</style>

<div class="bottomlinks" style="text-align:center;width:984px;margin:0 auto; padding:0;color:#f06d26;">
	<div style="float:left;text-align:left;">
		<a href="">About Us</a> |
		<a href="">Join Us</a> |
		<a href="">Advertising</a> |
		<a href="">FAQs</a> |
		<a href="">RSS Feeds</a> |
		<a href="">Contact Us</a> |
		<a href="">Privacy Policy</a>
	</div>
	<div style="float:right;text-align:right;">
		<a href="">What do you think of this page?</a>
	</div>
	The Yorker
</div>

<?php if ($this->config->item('enable_analytics')) { ?>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
var pageTracker = _gat._getTracker("UA-864229-2");
pageTracker._initData();
pageTracker._trackPageview();
</script>
<?php } ?>
