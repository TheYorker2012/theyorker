	<script type="text/javascript" src="/javascript/common.js"></script>

	<script type="text/javascript">
	//<![CDATA[
	// Major version of Flash required
	var requiredMajorVersion = 7;
	// Minor version of Flash required
	var requiredMinorVersion = 0;
	// Minor version of Flash required
	var requiredRevision = 0;
	//]]>
	</script>

	<?php
	include('maps.php');
	?>

	<!-- BEGIN 'head' tag items from controlling script -->
	<?php 
	if (isset($content['head'])) {
		foreach($content['head'] as $item) {
			$item->Load();
		}
	}
	?>
	<!-- END 'head' tag items from controlling script -->
