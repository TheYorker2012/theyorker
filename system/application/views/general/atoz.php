<center>
<form>
Search: <input id="searchText" onKeyUp="searchPage('searchText','Letter');">
</form>

<script language="javascript">

insertJumpers('Jumper','Anchor');

function onLoad() {
	searchPage('searchText','Letter');
}

</script>

</center>

<div id="NotFound" style="display: none;">
<center><br>
<b> No match was found	 </b>
</center>
</div>

		<div >
		<table width="780" border="0" cellspacing="0" cellpadding="0">
		  <tr height="14">
			<td rowspan="2" class="AZTop">
			<div>

<?php 



$last_letter = "";

$current_letter_index = 0;

foreach ($organisation_array as $organisation) {

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
		<table width="780" border="0" cellspacing="0" cellpadding="0">
		  <tr height="14">
			<td width="20">&nbsp;</td>
			<td width="338" height="14" valign="top"><div class="AZLeft"><?php echo $current_letter ?></div></td>
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
			<table width="780" border="0" cellspacing="0" cellpadding="0">
			  <tr height="14">
				<td width="20">&nbsp;</td>
				<td width="338" height="14" valign="top"><div class="AZLeft">&nbsp;</div></td>
				<td rowspan="2" valign="top">
				<div class="AZTop">
		
		<?php
		}
		$last_letter = "0";
}
?>
<div id="Letter<?php echo $last_letter.$current_letter_index ?>" class="AZEntry" name="<?php echo $entry_name ?>"><?php echo $entry_name ?></div>
<?php


	}
?>
			</div>
			</td>
		  </tr>
		</table>

</body>
</html>