<div class="RightToolbar">
	<h4 class="first">Page Information</h4>
	<div class="Entry">
		<?php echo $page_information; ?>
	</div>
</div>
<div id="MainColumn">
	<div class="blue_box">
		<?php
		foreach($organisations as $organisation) {
			echo('			<h3>'."\n");
			echo('				<a href="/office/reviews/'.$organisation['shortname'].'/'.$content_type_codename.'/information">');
			echo(htmlspecialchars($organisation['name']));
			echo('</a>'."\n");
			echo('			</h3>'."\n");
			echo('			<div class="Date">'."\n");
			echo('				Information : ');
			echo('				<a href="/office/reviews/'.$organisation['shortname'].'/'.$content_type_codename.'/information">');
			if(htmlspecialchars($organisation['info_complete'])){echo('Complete');}else{echo('Not complete');}
			echo("\n".'</a><br />');
			echo('				Number of reviews : ');
			echo('				<a href="/office/reviews/'.$organisation['shortname'].'/'.$content_type_codename.'/review">'."\n");
			if(htmlspecialchars($organisation['review_count'])==0){
				echo 'No complete reviews!';
				echo("\n".'</a><br />');
			}else{
				echo htmlspecialchars($organisation['review_count']);
				echo("\n".'</a><br />');
				echo('					Date of last review: ');
				echo('				<a href="/office/reviews/'.$organisation['shortname'].'/'.$content_type_codename.'/review">'."\n");
				echo(htmlspecialchars($organisation['date_of_last_review'])."\n");
				echo('				</a><br />');
			}
			echo('				Assigned User : ');
			echo('				<a href="/office/reviews/'.$organisation['shortname'].'/'.$content_type_codename.'/information">'."\n");
			if(htmlspecialchars($organisation['assigned_user_name'])==''){echo 'No assigned user!';}else{echo htmlspecialchars($organisation['assigned_user_name']);}
			echo("\n".'</a><br />');
			echo('				Number of tags : ');
			echo('				<a href="/office/reviews/'.$organisation['shortname'].'/'.$content_type_codename.'/tags">'."\n");
			if(htmlspecialchars($organisation['number_of_tags'])==0){echo 'No tags!';}else{echo htmlspecialchars($organisation['number_of_tags']);}
			echo("\n".'</a><br />');
			echo('				Number of '.$content_type_codename.' leagues : ');
			echo('				<a href="/office/reviews/'.$organisation['shortname'].'/'.$content_type_codename.'/leagues">'."\n");
			if(htmlspecialchars($organisation['number_of_leagues'])==0){echo 'Not in any '.$content_type_codename.' leagues!';}else{echo htmlspecialchars($organisation['number_of_leagues']);}
			echo("\n".'</a><br />');
			echo('			</div>'."\n");
		}
		?>
	</div>
</div>
