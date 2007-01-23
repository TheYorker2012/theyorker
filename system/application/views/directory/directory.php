<p><?php echo $maintext; ?></p>
<div id='minibox' style="float:right;margin-right:5px">
    <div id='title'>Filters</div>
    <?php
    	$idPostfix = 1;
		foreach ($organisation_types as $org_type) {
			echo '<input id="filterCheck'.$idPostfix++.'" onChange="searchPage(\'searchText\',\'Letter\',\'filterCheck\');" type="checkbox" name="'.$org_type['id'].'" value="checked" checked><span style="font-size:small" />'.$org_type['name'].'</span><br />';
		}
    ?>
</div>
<div  style="padding:0px 150px 0px 0px">
	<form name='search_directory' action='/directory/' method='POST' class='form'>
		<fieldset>
			<legend>Search</legend>
			<input id="searchText" name="search" onKeyUp="searchPage('searchText','Letter','filterCheck');">
			<input type='submit' name='Submit' value='Search'>
		</fieldset>
	</form>
	<div align='center'>
		<script language="javascript">
		insertJumpers('Jumper','Anchor');

		function onLoad() {
			searchPage('searchText','Letter','filterCheck');
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
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr height="14">
	<td rowspan="2" class="AZTop">
	<div>
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
		<a name="Anchor<?php echo $current_letter ?>"></a>
		<div id="Letter<?php echo $current_letter ?>">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		  <tr height="14">
			<td width="20">&nbsp;</td>
			<td width="40" height="14" valign="top"><div class="AZLeft"><?php echo $current_letter ?></div></td>
			<td rowspan="2" valign="top">
			<div class="AZTop">
			<a href='#top' style="font-size:12px;">Back to top.</a>
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
	/*
	 * $organisation['description'] is the description of the organisation
	 * $organisation['shortdescription'] is cut to a finite number of words
	 */
	?>

<div id="Letter<?php echo $last_letter.$current_letter_index ?>" class="AZEntry" name="<?php echo $organisation['type']; ?>">

	<?php echo '<a href=\'/directory/' . $organisation['shortname'] . '\' style="display: inline;"><span style="color:#08c0ef; font-weight: bold;">' . $organisation['name']; ?></span></a>
	<span style='font-size: 12px'>(<?php echo $organisation['type']; ?>)</span><br />
	<span style='font-size: 12px'><?php echo $organisation['description']; ?></span>
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