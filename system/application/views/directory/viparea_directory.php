<div id="RightColumn">
	<h2 class="first">Search</h2>
	<div class="Entry">
		<form id="search_directory" action="" method="post">
			<fieldset>
				<label for="search">Enter a keyword below:</label>
				<input type="text" name="search" id="search" onkeyup="searchDirectory();" />
			</fieldset>
		</form>
	</div>

	<h2>Filters</h2>
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

	<h2>Suggestions</h2>
	<div class="Entry">
		<ul>
		<li><a href="/wizard/organisation">Suggest an entry</a></li>
		</ul>
	</div>
</div>

<div id="MainColumn">
	<div id="DirectoryMain" class="BlueBox">
		<div style="display: none" id="LetterJump">
		</div>
		<div id="NotFound" style="<?php if (sizeof($organisations)>0) echo 'display: none;'?>">
			<h3>No entries found</h3>
			<?php if (sizeof($organisations)>0) { ?>
			<p>Try a simpler search, different keywords, or include more filters.</p>
			<?php } else { ?>
			<p>There are no entries with this status in the directory.</p>
			<?php } ?>
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