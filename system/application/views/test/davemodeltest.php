<!-- i fucking hate big text -->
<style>
body {
	font-size: 0.8em;
}
</style>

<?php



echo('<b>Showing '.count($league).' entries from '.$league[0]['league_name'].' ordered by xxx</b><hr />');
foreach($league as $row) {
	#foreach($row as $field) {
		echo($row['organisation_name']).'<br />';
		echo($row['organisation_url']).'<br />';
		
	#}
	echo('<hr>');
}


?>