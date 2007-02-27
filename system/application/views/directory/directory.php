<div id="RightColumn">
	<h4 class="first">Search</h4>
	<div class="Entry">
		<form id="search_directory" action="" method="post">
			<fieldset>
				<label for="search">Enter a keyword below:</label>
				<input type="text" name="search" id="search" onkeyup="searchDirectory();" />
			</fieldset>
		</form>
	</div>

	<h4>Filters</h4>
	<div class="Entry">
<?php
	$idPostfix = 0;
	foreach ($organisation_types as $org_type) {
?>
		<label for="filterCheck<?php echo($idPostfix); ?>">
			<input id="filterCheck<?php echo($idPostfix); ?>" onclick="searchDirectory();" type="checkbox" name="<?php echo(htmlspecialchars($org_type['id'])); ?>" checked="checked" />
			<?php echo(htmlspecialchars($org_type['name']).' ('.$org_type['quantity'].')')?>
		</label>
<?php
		$idPostfix++;
	}
?>
	</div>

	<h4>What's this?</h4>
	<div class="Entry">
		<p><?php echo(htmlspecialchars($maintext)); ?></p>
		<p>The directory currently has <?php echo count($organisations); ?> entries.  </p>
	</div>
</div>

<div id="MainColumn">
	<div id="DirectoryMain" class="BlueBox">
		<div id="LetterJump">
		</div>
		<div id="NotFound" style="display: none;">
			<p><b>No entries found</b></p>
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
		echo('			<hr />'."\n");
		echo('		</div>'."\n");
		$currentLetter = $thisLetter;
	}

	echo('		<div id="'.htmlspecialchars($organisation['shortname']).'">'."\n");
	echo('			<h4>'."\n");
	echo('				<a href="/'.htmlspecialchars($organisation['link']).'">'.htmlspecialchars($organisation['name']).'</a>'."\n");
	echo('				<span>('.htmlspecialchars($organisation['type']).')</span>'."\n");
	echo('			</h4>'."\n");
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
