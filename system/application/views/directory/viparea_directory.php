<div style="width: 220px; margin: 0; padding-left: 3px; float: right; ">

	<div class='RightToolbar'>
		<h4>Search</h4>
		<div style='padding: 10px 5px 10px 5px; font-size:small;'>
			<form name='search_directory' action='' method='POST' class='form'>
				Enter a keyword below:<br />
				<div id="loadingdiv"style="padding: 5px; display: none; float:right;"> <image src="/images/prototype/prefs/loading.gif" width="16" height="16" /></div>
				<div style='padding: 5px;'><input id="searchText" width='300' name="search" onKeyUp="searchPage('searchText','Letter','filterCheck');"> </div>
				e.g. football

			</form>
		</div>
	</div>

	<div class='RightToolbar'>
		<h4>Filters</h4>
		<div style='padding: 10px 5px 10px 5px; font-size:small;'>
			<?php
				$idPostfix = 1;
				foreach ($organisation_types as $org_type) {
					echo '<input id="filterCheck'.$idPostfix++.'" onChange="searchPage(\'searchText\',\'Letter\',\'filterCheck\');" type="checkbox" name="'.$org_type['id'].'" value="checked" checked><span style="font-size:small" />'.$org_type['name'].'</span> ('.$org_type['quantity'].')<br />';
				}
			?>
		</div>
	</div>

	<div class='RightToolbar'>
		<h4>What's this? </h4>
		<div style='padding: 10px 5px 10px 5px; font-size:small;'>
			<p><?php echo $maintext; ?></p>
			<p>The directory currently has <?php echo count($organisations); ?> entries.</p>
		</div>
	</div>
	
	<div class='RightToolbar'>
		<h4>New Organisation</h4>
		<div style='padding: 10px 5px 10px 5px; font-size:small;'>
			<p><a href="/wizard/organisation">Suggest an entry</a></p>
		</div>
	</div>

</div>

<div  style="padding:0px; width: 420px; margin: 0px;">
<div style="border: 1px solid #999; padding: 5px; font-size: small; margin-bottom: 4px;">
<p></p>

<div align="center">
    <script language="javascript">
		insertJumpers('Jumper','Anchor');

		function onLoad() {
			searchPage('searchText','Letter','filterCheck');
		}

		onLoadFunctions.push(onLoad);
	</script>
</div>
<div id="NotFound" style="display: none;">
    <center>
    	<br />
    	<br />
        <b>No entries found</b><br />
        <div style="text-size:small">Try a simpler search, different keywords, or include more filters.</div>
    </center>
</div>
<div>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">

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
			</table>
			</div>
			<script language="javascript"> showJumpAsLoad('<?php echo $current_letter ?>'); </script>
			<a name="Anchor<?php echo $current_letter ?>"></a>
			<div>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<?php
			}
			$last_letter = $current_letter;
		} else {
			if ($last_letter != "0") {
				$last_letter = "0";
			?>
					<?php
		}
		$last_letter = "0";
	}
	/*
	 * $organisation['description'] is the description of the organisation
	 * $organisation['shortdescription'] is cut to a finite number of words
	 */
	?>
					<tr height="14">
						<td rowspan="1" valign="top">
                        <div id="Letter<?php echo $last_letter.$current_letter_index ?>" class="AZEntry" name="<?php echo $organisation['type']; ?>"> <?php echo '<a href=\'/' . $organisation['link'] . '\' style="display: inline;"><span style="color:#08c0ef; font-weight: bold;">' . $organisation['name']; ?></span></a><br /></div>
        				</td>
						<td rowspan="1" valign="top">
                        <span style='font-size: 12px'>(<?php echo $organisation['type']; ?>)</span>
        				</td>
					</tr>
                
						
						<?php
}
?>
        </table>
    </div>
</div>
