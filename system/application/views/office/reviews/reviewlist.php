<div class="RightToolbar">
	<h4 class="first">Page Information</h4>
	<div class="Entry">
		<?php echo $page_information; ?>
	</div>
</div>
<div id="MainColumn">
	<div id="DirectoryMain" class="blue_box">
		<div style="display: none" id="LetterJump">
		</div>
		<div id="NotFound" style="display: none;">
			<h3>No entries found</h3>
			<p>Try a simpler search, different keywords, or include more filters.</p>
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
			echo('			<div class="Date">');
			
				echo('			<div>'."\n");
				echo('				Info Complete: '.htmlspecialchars($organisation['info_complete'])."\n<br />");
				echo('				Date: '.htmlspecialchars($organisation['date_of_last_review'])."\n<br />");
				echo('				Review Count: '.htmlspecialchars($organisation['review_count'])."\n<br />");
				echo('				Assigned User: '.htmlspecialchars($organisation['assigned_user_name'])."\n<br />");
				echo('			</div>'."\n");
			
			echo('		</div>'."\n");
		}
		?>
	</div>
</div>
