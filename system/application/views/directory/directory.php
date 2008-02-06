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
			<p>Try a simpler search, different keywords, or include more filters.</p>
		</div>
<?php
$currentLetter = '';
$while_child_pos = 0;
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

	echo('		<div id="'.$organisation['shortname'].'">'."\n");
	echo('			<h3>'."\n");
	echo('				<a href="/'.xml_escape($organisation['link']).'">'.xml_escape($organisation['name']).'</a>'."\n");
	/*echo('				<span>('.$organisation['type'].')</span>'."\n");*/
	echo('			</h3>'."\n");
	echo('			<div class="Date">'.xml_escape($organisation['type']).'</div>'."\n");
	if($organisation['shortdescription'] != '') {
		echo('			<div>'."\n");
		echo('				'.xml_escape($organisation['shortdescription'])."\n");
		echo('			</div>'."\n");
	}
	//make sure it stays within the array then if parent id matches current org id
	while (($while_child_pos < count($children)) && ($children[$while_child_pos]['parent_id'] == $organisation['id']))
	{
		echo('			<div>'."\n");
		echo('				&gt; <a href="/'.xml_escape($children[$while_child_pos]['link']).'">'.xml_escape($children[$while_child_pos]['name']).'</a>'."\n");
		echo('			</div>'."\n");
		$while_child_pos++;
	}
	echo('		</div>'."\n");
}
?>
	</div>
</div>

<?php
/*
echo('<div class="BlueBox"><pre>');
print_r($data);
echo('</pre></div>');
*/
?>
