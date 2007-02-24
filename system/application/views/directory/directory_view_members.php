<div class='RightToolbar'>
	<h4>Groups</h4>
	<div style='padding: 10px 5px 10px 5px;'>
		<p>
			<?php
			foreach ($organisation['groups'] as $group) {
			?>

			<a href='<?php echo $group['href'] ?>'><?php echo $group['name'] ?></a><br />

			<?php
			}
			?>
		</p>
	</div>
	<h4>Facts</h4>
	<div style='padding: 10px 5px 10px 5px;'>
		<p>	Number of members : 1337 </p>
		<p>	Member last joined : 5 hours ago</p>
		<p>	Male to female ratio: 2:1</p>
	</div>
</div>
<div style="width: 420px; margin: 0px; padding-right: 3px; ">
	<?php
	if(empty($organisation['cards'])) {
	?>
	<div align="center">
		<b>This organisation has not listed any members in this team.</b>
	</div>
	<?php
	} else {
		foreach ($organisation['cards'] as $member) {
			$this->load->view('directory/business_card',array(
				'business_card' => $member,
				'editmode' => isset($organisation['editmode']),
			));
		}
	}
	?>
</div>