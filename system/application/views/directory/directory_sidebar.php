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
			<input id="filterCheck<?php echo($idPostfix); ?>" onclick="searchDirectory();" type="checkbox" name="<?php echo($org_type['id']); ?>" checked="checked" class="checkbox"/>
			<?php echo(xml_escape($org_type['name']).' ('.$org_type['quantity'].')')?>
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
