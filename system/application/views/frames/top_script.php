	
	<script src="/javascript/AC_OETags.js" type="text/javascript"></script>
	<script type="text/javascript">
	<!--
	// -----------------------------------------------------------------------------
	// Globals
	// Major version of Flash required
	var requiredMajorVersion = 7;
	// Minor version of Flash required
	var requiredMinorVersion = 0;
	// Minor version of Flash required
	var requiredRevision = 0;
	// -----------------------------------------------------------------------------
	// -->
	</script>

	<!-- BEGIN Multiple event handlers code -->
	<script type="text/javascript">
	//<![CDATA[

	// An array containing functors for all function to be run on page load
	var onLoadFunctions = new Array();

	// An array containing functors for all function to be run on page unload
	var onUnloadFunctions = new Array();

	// The function which is run on page load ensuring all functors are run
	function onLoadHandler() {
		for (i = 0; i < onLoadFunctions.length; i++) {
			onLoadFunctions[i]();
		}
	}
	// The function which is run on page unload ensuring all functors are run
	function onUnloadHandler() {
		for (i = 0; i < onUnloadFunctions.length; i++) {
			onUnloadFunctions[i]();
		}
	}

	//]]>
	</script>
	<!-- END Multiple event handlers code -->

	<?php
	include('maps.php');
	?>

	<!-- BEGIN search box code -->
	<script type="text/javascript">
	//<![CDATA[

	function inputFocus(element) {
		if (element.value == element.defaultValue) {
			element.value = '';
		}
	}

	function inputBlur(element) {
		if (element.value =='') {
			element.value = element.defaultValue;
		}
	}

	//]]>
	</script>
	<!-- END search box code -->

	<!-- BEGIN feedback form code -->
	<script type="text/javascript">
	//<![CDATA[

	function showFeedback() {
		var showFeedbackObj = document.getElementById('ShowFeedback');
		var feedbackObj = document.getElementById('FeedbackForm');
		showFeedbackObj.style.display = 'none';
		feedbackObj.style.display = 'block';

		return false;
	}

	function hideFeedback() {
		var showFeedbackObj = document.getElementById('ShowFeedback');
		var feedbackObj = document.getElementById('FeedbackForm');
		showFeedbackObj.style.display = 'block';
		feedbackObj.style.display = 'none';

		return false;
	}

	onLoadFunctions.push(hideFeedback);

	//]]>
	</script>
	<!-- END feedback form code -->

	<!-- BEGIN 'head' tag items from controlling script -->
	<?php if (isset($extra_head)) { echo($extra_head."\n"); }; ?>
	<!-- END 'head' tag items from controlling script -->
