<div id="RightColumn">
	<h2 class="first">What's this?</h2>
	<div class="Entry">
		<?php if (isset($whats_this)) echo $whats_this; ?>
	</div>
	
<?php
	if(!empty($organisation['groups'])){
?>
<h2 class="first">Groups</h2>
	<div class="Entry">
<?php
		foreach ($organisation['groups'] as $group) {
?>
			<a href='<?php echo(htmlspecialchars($group['href'])); ?>'>
				<?php
				if (isset($business_card_group) && $business_card_group==$group['id']){
					echo '<b>';
				}
				echo(htmlspecialchars($group['name']));
				if (isset($business_card_group) && $business_card_group==$group['id']){
					echo '</b>';
				}
				?>
			</a><br />
<?php
		}
?>
	</div>
<?php
	}
?>

	<h2>Facts</h2>
	<div class="entry">
	<ul>
		<li>Number of members : <?php if (isset($number_of_members)) echo $number_of_members; ?></li>
		<li>Number of subscriptions : <?php if (isset($number_of_subscriptions)) echo $number_of_subscriptions; ?></li>
		<?php if (isset($number_of_members) && $number_of_members != 0){ ?>
		<li>Last member joined : <?php if (isset($last_joined)) echo $last_joined; ?></li>
		<li>Male:Female ratio : <?php if (isset($male_female_ratio)) echo $male_female_ratio; ?></li>
		<?php } ?>
		
	</ul>
	</div>
</div>

<div id="MainColumn">
	<div class="BlueBox">
<?php
	if(empty($organisation['groups'])) {
?>
		<div align="center">
			<b><?php if (isset($no_groups)) echo $no_groups; ?></b>
		</div>
<?php
	} else if (empty($organisation['cards'])) {
?>
		<div align="center">
			<b><?php if (isset($no_cards)) echo $no_cards; ?></b>
		</div>
<?php
	} else {
		foreach ($organisation['cards'] as $business_card) {
			$this->load->view('directory/business_card',array(
				'business_card' => $business_card,
				'editmode' => isset($organisation['editmode']),
			));
		}
	}
	?>
	</div>
</div>
