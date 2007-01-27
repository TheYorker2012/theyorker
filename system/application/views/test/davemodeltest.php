<!-- i fucking hate big text -->
<style>
body {
	font-size: 0.8em;
}
</style>

<?php



echo('<b>Showing '.count($league).' entries from '.$league[0]['league_name'].' ordered by default</b><hr />');
foreach($league as $row) {
	#foreach($row as $field) {
		# i know this doesnt need to be styled here but was getting on my nerves!
		echo('<div style="position: relative; top: 5px;">');
		echo($row['organisation_name'].'<br />');
		echo($row['organisation_url'].'<br />');
		echo($row['content_type_name'].'<br />');
		
		echo('</div>');
		echo('<span style="float: right; position: relative; bottom: 25px; right: 25px;">'.$row['league_entry_position']).'</span><br />';
		
		
	#}
	echo('<hr>');
}


?>