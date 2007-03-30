<?php
	$CI = &get_instance();
	$CI->load->view('directory/directory_sidebar');
?>

<div id="MainColumn">
	<div id="DirectoryMain" class="BlueBox">
		<div style="display: none" id="LetterJump">
		</div>
		<div id="NotFound" style="display: none;">
			<h3>No entries found</h3>
			<p>Try a simpler search, different keyowords, or include more filters.</p>
		</div>
<?php
$currentLetter = '';
foreach($organisations as $organisation) {
	$thisLetter = $organisation['name'][0];
	$thisLetter = strtoupper($thisLetter);
	if ($thisLetter < 'A' | $thisLetter > 'Z') {
		$thisLetter = 'Symbol';
	}

	if ($thisLetter != $currentLetter) {
		echo('		<div class="DirectoryList" id="DirectoryList'.$thisLetter.'">'."\n");
		/*echo('			<hr />'."\n");*/
		echo('		</div>'."\n");
		$currentLetter = $thisLetter;
	}

	echo('		<div id="'.htmlspecialchars($organisation['shortname']).'">'."\n");
	echo('			<h3>'."\n");
	echo('				<a href="/'.htmlspecialchars($organisation['link']).'">'.htmlspecialchars($organisation['name']).'</a>'."\n");
	/*echo('				<span>('.htmlspecialchars($organisation['type']).')</span>'."\n");*/
	echo('			</h3>'."\n");
	echo('			<div class="Date">'.htmlspecialchars($organisation['type']).'</div>'."\n");
	if($organisation['shortdescription'] != '') {
		echo('			<div>'."\n");
		echo('				'.htmlspecialchars($organisation['shortdescription'])."\n");
		echo('			</div>'."\n");
	}
	echo('		</div>'."\n");
}
?>
	</div>
</div>
