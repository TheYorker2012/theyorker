

<p>The Directory contains many different organisations. Every page has useful information about each organisation such as its contact details, reviews, events and members</p>
<div id='minibox' style="float:right;margin-right:5px">
    <div id='title'>Filters</div>
    <input type='checkbox' name='venues' value="checked" checked><span style="font-size:small">Venues</span><br />
    <input type='checkbox' name='societies' value="checked" checked><span style="font-size:small">Societies</span><br />
    <input type='checkbox' name='athletics_union' value="checked" checked><span style="font-size:small">Athletics Union</span><br />
    <input type='checkbox' name='organisation' value="checked" checked><span style="font-size:small">Organisations</span><br />
    <input type='checkbox' name='college_campus' value="checked" checked><span style="font-size:small">College &#038; Campus</span>
</div>
<div  style="padding:0px 150px 0px 0px">
	<form name='search_directory' action='/directory/' method='POST' class='form'>
		<fieldset>
			<legend>Search</legend>
			<input id="searchText" name="search" onKeyUp="searchPage('searchText','Letter');">
			<input type='submit' name='Submit' value='Search'>
		</fieldset>
	</form>
	<div align='center'>
		<script language="javascript">
		insertJumpers('Jumper','Anchor');

		function onLoad() {
			searchPage('searchText','Letter');
		}
		</script>
		<br />
		Browsing <?php echo count($organisations); ?> results.
	</div>
</div>
<div class="clear">&nbsp;</div>
<!-- Start showing results -->
<div id='searchresults' style="padding:0px 0px 0px 0px">
<div id="NotFound" style="display: none;">
<center>
<b>No entries found</b><br />
<div style="text-size:small">Try a simpler search, different keywords or include more filters.</div>
</center>
</div>

<div>
<table width="780" border="0" cellspacing="0" cellpadding="0">
  <tr height="14">
	<td rowspan="2" class="AZTop">
	<?php
	$last_letter = "";
	$current_letter_index = 0;

	foreach ($organisations as $organisation) {

		$current_letter_index ++;

		$entry_name = $organisation['name'];

		$current_letter = strtoupper($entry_name{0});

		if($this->character_lib->isalpha($current_letter)) {
			if ($current_letter!=$last_letter) {
				$current_letter_index = 1;
			?>
			</div>
			</td>
		  </tr>
		  <tr>
			<td colspan="2">&nbsp;</td>
		  </tr>
		</table>
		</div>
		<a name="Anchor<?php echo $current_letter ?>">
		<div id="Letter<?php echo $current_letter ?>">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		  <tr height="14">
			<td width="20">&nbsp;</td>
			<td width="40" height="14" valign="top"><div class="AZLeft"><?php echo $current_letter ?></div></td>
			<td rowspan="2" valign="top">
			<div class="AZTop">
		<?php
		}
		$last_letter = $current_letter;
	} else {
		if ($last_letter != "0") {
			$last_letter = "0";
		?>
			</div>
			</td>
		  </tr>
		  <tr>
			<td colspan="2">&nbsp;</td>
		  </tr>
		</table>
		<div id="Letter0">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			  <tr height="14">
				<td width="20">&nbsp;</td>
				<td width="80" height="14" valign="top"><div class="AZLeft">&nbsp;</div></td>
				<td rowspan="2" valign="top">
				<div class="AZTop">
		<?php
		}
		$last_letter = "0";
	}
	?>
<div id="Letter<?php echo $last_letter.$current_letter_index ?>" class="AZEntry" name="<?php echo $entry_name ?>">

	<?php echo '<a href=\'/directory/' . $organisation['shortname'] . '\' style="display: inline;">' . $organisation['name']; ?></a>
	<span style='font-size: 12px'>(<?php echo $organisation['type']; ?>)</span><br />
	<?php echo $organisation['description']; ?>

</div>
<?php
}
?>
	</div>
	</td>
  </tr>
</table>
</div>
</div>