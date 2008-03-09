<div id="RightColumn">
	<h2 class="first">Page Information</h2>
	<div class="Entry">
		<?php echo $page_information; ?>
	</div>
	<h2>Registrations</h2>
	<div class="Entry">
		<img src="<?php echo xml_escape($signups_img_url); ?>" alt="Recent Signups Cumulative Graph" />
		<ul>
			<li><?php echo xml_escape($registrations['day']); ?> in the last day</li>
			<li><?php echo xml_escape($registrations['week']); ?> in the last week</li>
			<li><?php echo xml_escape($registrations['month']); ?> so far this month</li>
			<li><?php echo xml_escape($registrations['year']); ?> so far this year</li>
			<li><?php echo xml_escape($registrations['academic_year']); ?> so far this accademic year</li>
		</ul>
	</div>
	<?php
	//Access levels is being left out for the moment as the admin count isn't implimented and the other figures are a little misleading. This needs work.
	/*
	<h2>Access Levels</h2>
	<div class="Entry">
		<ul>
			<li><?php echo xml_escape($access['office']); ?> people have office access</li>
			<li><?php echo xml_escape($access['admin']); ?> people have admin access</li>
			<li><?php echo xml_escape($access['vip']); ?> people have vip access</li>
		</ul>
	</div>
	*/
	?>
	<h2>User Links</h2>
	<div class="Entry">
		<img src="<?php echo xml_escape($links_bar_chart_img); ?>" alt="User Links Bar Chart" /><br />
		<img src="<?php echo xml_escape($official_links_pie_img); ?>" alt="UnOfficial/Official Links Ratio Pie Chart" />
	</div>
</div>
<div id="MainColumn">
	<div class="BlueBox">
		<h2>Users</h2>
		<ul>
			<li><?php echo xml_escape($members['number_of_members']); ?> users have signed up</li>
			<li><?php echo xml_escape(round(($members['confirmed_members']/ $members['number_of_members'])*100)); ?>% have confirmed by email (<?php echo xml_escape($members['confirmed_members']); ?>)</li>
			<li><?php echo xml_escape(round(($members['members_with_stats']/ $members['number_of_members'])*100)); ?>% have entered their details (<?php echo xml_escape($members['members_with_stats']); ?>)</li>
		</ul>
		<h3>Gender Ratio</h3>
		<?php
			//Print percentage bars for male and female ratios
			$percentage = round(($member_genders['male']/ ($member_genders['male'] + $member_genders['female']))*100);
			echo('		<div class="CampaignBox">'."\n");
			echo('			<div class="ProgressBar">'."\n");
			echo('				<div class="ProgressInner" style="width: '.$percentage.'%">');
			if($percentage <= 50){
				echo('&nbsp;</div>&nbsp;'.$percentage.'%'."\n");
			}else{
				echo('&nbsp;'.$percentage.'%</div>'."\n");
			}
			echo('			</div>'."\n");
			echo('			Male'."\n");
			echo('		</div>'."\n");
			
			$percentage = round(($member_genders['female']/ ($member_genders['male'] + $member_genders['female']))*100);
			echo('		<div class="CampaignBox">'."\n");
			echo('			<div class="ProgressBar">'."\n");
			echo('				<div class="ProgressInner" style="width: '.$percentage.'%">');
			if($percentage <= 50){
				echo('&nbsp;</div>&nbsp;'.$percentage.'%'."\n");
			}else{
				echo('&nbsp;'.$percentage.'%</div>'."\n");
			}
			echo('			</div>'."\n");
			echo('			Female'."\n");
			echo('		</div>'."\n");
		?>
		<h3>Colleges</h3>
		<?php
		$total=0;//Work out total from all colleges to get percentages
		foreach ($member_colleges as $college){
			$total+=$college['member_count'];
		}
		foreach ($member_colleges as $college) {
			$percentage = round(($college['member_count']/$total)*100);
			echo('		<div class="CampaignBox">'."\n");
			echo('			<div class="ProgressBar">'."\n");
			echo('				<div class="ProgressInner" style="width: '.$percentage.'%">');
			if($percentage <= 50){
				echo('&nbsp;</div>&nbsp;'.$percentage.'%'."\n");
			}else{
				echo('&nbsp;'.$percentage.'%</div>'."\n");
			}
			echo('			</div>'."\n");
			echo('			'.xml_escape($college['college_name'])."\n");
			echo('		</div>'."\n");
		}
		?>
		<h3>Enrolement Years</h3>
		<?php
		$total=0;//Work out total from all colleges to get percentages
		foreach ($member_enrollments as $year){
			$total+=$year['member_count'];
		}
		foreach ($member_enrollments as $year) {
			$percentage = round(($year['member_count']/$total)*100);
			echo('		<div class="CampaignBox">'."\n");
			echo('			<div class="ProgressBar">'."\n");
			echo('				<div class="ProgressInner" style="width: '.$percentage.'%">');
			if($percentage <= 50){
				echo('&nbsp;</div>&nbsp;'.$percentage.'%'."\n");
			}else{
				echo('&nbsp;'.$percentage.'%</div>'."\n");
			}
			echo('			</div>'."\n");
			echo('			'.xml_escape($year['enrollment_year'])."\n");
			echo('		</div>'."\n");
		}
		?>
		<h3>Time Format</h3>
		<?php
			//Print percentage bars for male and female ratios
			$percentage = round(($member_times['12_hour']/ ($member_times['12_hour'] + $member_times['24_hour']))*100);
			echo('		<div class="CampaignBox">'."\n");
			echo('			<div class="ProgressBar">'."\n");
			echo('				<div class="ProgressInner" style="width: '.$percentage.'%">');
			if($percentage <= 50){
				echo('&nbsp;</div>&nbsp;'.$percentage.'%'."\n");
			}else{
				echo('&nbsp;'.$percentage.'%</div>'."\n");
			}
			echo('			</div>'."\n");
			echo('			12 Hour'."\n");
			echo('		</div>'."\n");
			
			$percentage = round(($member_times['24_hour']/ ($member_times['12_hour'] + $member_times['24_hour']))*100);
			echo('		<div class="CampaignBox">'."\n");
			echo('			<div class="ProgressBar">'."\n");
			echo('				<div class="ProgressInner" style="width: '.$percentage.'%">');
			if($percentage <= 50){
				echo('&nbsp;</div>&nbsp;'.$percentage.'%'."\n");
			}else{
				echo('&nbsp;'.$percentage.'%</div>'."\n");
			}
			echo('			</div>'."\n");
			echo('			24 Hour'."\n");
			echo('		</div>'."\n");
		?>
	</div>
	<div class="BlueBox">
		<h2>Comments</h2>
		<div style="text-align: center;">
			<img src="<?php echo xml_escape($comments_img_url); ?>" alt="Total Comments Posted Graph" />
		</div>
		<h3>Top commenters</h3>
		<div class="ArticleBox">
			<table>
				<thead>
					<tr>
						<th>&nbsp;</th>
						<th>Name</th>
						<th><img alt="Anonymous" src="/images/prototype/homepage/grey_user_small.gif"></th>
						<th><img alt="As Self" src="/images/prototype/homepage/blue_user_small.gif"></th>
						<th><img alt="Deleted" src="/images/prototype/members/no9.png"></th>
						<th>Total</th>
					</tr>
				</thead>
			<?php
			$count=0;
			foreach ($comment_top_users as $commenter)
			{
				$count++;
				echo ('				<tr class="tr'.(($count%2)+1).'">');
				echo ('<td>'.$count.')</td>');
				echo ('<td>');
				if(($commenter['firstname']=="" || $commenter['firstname']==" ")&&($commenter['surname']=="" || $commenter['surname']==" "))
				{
					echo xml_escape($commenter['email']);
				}else{
					echo xml_escape($commenter['firstname'].' '.$commenter['surname']);
				}
				echo ('</td>');
				echo ('<td>');
				echo xml_escape($commenter['anonymous_post_count']);
				echo ('</td>');
				echo ('<td>');
				echo xml_escape($commenter['total_post_count'] - $commenter['anonymous_post_count']);
				echo ('</td>');
				echo ('<td>');
				echo xml_escape($commenter['deleted_post_count']);
				echo ('</td>');
				echo ('<td>');
				echo xml_escape($commenter['total_post_count']);
				echo ('</td>');
				echo ('</tr>'."\n");
			}
			?>
			</table>
		</div>
		<h3>Worst commenters</h3>
		<div class="ArticleBox">
			<table>
				<thead>
					<tr>
						<th>&nbsp;</th>
						<th>Name</th>
						<th>
						<img alt="Anonymous" src="/images/prototype/homepage/grey_user_small.gif">
						<img alt="Deleted" src="/images/prototype/members/no9.png">
						</th>
						<th>
						<img alt="As Self" src="/images/prototype/homepage/blue_user_small.gif">
						<img alt="Deleted" src="/images/prototype/members/no9.png">
						</th>
						<th>
						<img alt="Deleted" src="/images/prototype/members/no9.png">
						</th>
						<th>Total</th>
					</tr>
				</thead>
			<?php
			$count=0;
			foreach ($comment_deleted_users as $commenter)
			{
				$count++;
				echo ('				<tr class="tr'.(($count%2)+1).'">');
				echo ('<td>'.$count.')</td>');
				echo ('<td>');
				if(($commenter['firstname']=="" || $commenter['firstname']==" ")&&($commenter['surname']=="" || $commenter['surname']==" "))
				{
					echo xml_escape($commenter['email']);
				}else{
					echo xml_escape($commenter['firstname'].' '.$commenter['surname']);
				}
				echo ('</td>');
				echo ('<td>');
				echo xml_escape($commenter['anonymous_deleted_post_count']);
				echo ('</td>');
				echo ('<td>');
				echo xml_escape($commenter['deleted_post_count'] - $commenter['anonymous_deleted_post_count']);
				echo ('</td>');
				echo ('<td>');
				echo xml_escape($commenter['deleted_post_count']);
				echo ('</td>');
				echo ('<td>');
				echo xml_escape($commenter['total_post_count']);
				echo ('</td>');
				echo ('</tr>'."\n");
			}
			?>
			</table>
		</div>
		<h3>Most popular articles</h3>
		<div class="ArticleBox">
			<table>
				<thead>
					<tr>
						<th>&nbsp;</th>
						<th width="40%">Title</th>
						<th>Section</th>
						<th><img alt="Deleted" src="/images/prototype/members/no9.png"></th>
						<th>Total</th>
					</tr>
				</thead>
			<?php
			$count=0;
			foreach ($comment_top_articles as $article)
			{
				$count++;
				echo ('				<tr class="tr'.(($count%2)+1).'">');
				echo ('<td>'.$count.')</td>');
				echo ('<td><a href="/news/'.$article['section_codename'].'/'.$article['article_id'].'">');
				echo xml_escape($article['article_title']);
				echo ('</a></td>');
				echo ('<td>');
				echo xml_escape($article['section_name']);
				echo ('</td>');
				echo ('<td>');
				echo xml_escape($article['deleted_post_count']);
				echo ('</td>');
				echo ('<td>');
				echo xml_escape($article['total_post_count']);
				echo ('</td>');
				echo ('</tr>'."\n");
			}
			?>
			</table>
		</div>
	</div>
	<div class="BlueBox">
		<h2>Subscriptions</h2>
		<p>Users have <?php echo xml_escape(round($subscription_average)); ?> subscriptions on average</p>
		<h3>Most subscribed</h3>
		<?php
		echo('		<ol>'."\n");
		foreach ($most_subscribed_orgs as $organisation)
		{
			echo ('			<li>');
			echo xml_escape($organisation['organisation_name'].' - '.$organisation['subscription_count']);
			echo ('</li>'."\n");
		}
		echo('		</ol>'."\n");
		?>
		<h3>Recently subscribed</h3>
		<?php
		echo('		<ol>'."\n");
		foreach ($recent_subscribed_orgs as $organisation)
		{
			echo ('			<li>');
			echo xml_escape($organisation['organisation_name'].' - '.date("d/m/y",$organisation['last_joined']).'');
			echo ('</li>'."\n");
		}
		echo('		</ol>'."\n");
		?>
	</div>
</div>
