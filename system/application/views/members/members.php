<script type="text/javascript">
	function advancedFilters() {
		var element;
		var element_show;
		var element_hide;
		element = document.getElementById('AdvancedFilter');
		element_show = document.getElementById('ShowAdvancedFilter');
		element_hide = document.getElementById('HideAdvancedFilter');
		if (element.style.display == 'none') {
			element.style.display = 'block';
			element_show.style.display = 'none';
			element_hide.style.display = 'block';
		}
		else {
			element.style.display = 'none';
			element_show.style.display = 'block';
			element_hide.style.display = 'none';
		}
	}
</script>

<div id="RightColumn">
	<h2 class="first">What's this?</h2>
	<div class="Entry">
		<?php echo $main_text; ?>
	</div>
	
	<h2>Search</h2>
	<div class="Entry">
		<input type="text" name="search" id="search" onkeyup="searchMemberList();" />
	</div>
	<div class="Entry">
		<div id="ShowAdvancedFilter" style="display: block">
			<small><a href="#" onclick="advancedFilters();">show advanced filters</a></small>
		</div>
		<div id="HideAdvancedFilter" style="display: none">
			<small><a href="#" onclick="advancedFilters();">hide advanced filters</a></small>
		</div>
	</div>
	<div id="AdvancedFilter" style="display: none;">
		<h2>Advanced Filters</h2>
		<div class="Entry">
		<label for="filter_confirmation">Conformation, Showing:</label>
		<select id="filter_confirmation" onchange="searchMemberList();">
			<option value="all" selected="selected">All</option>
			<option value="confirmed">Confirmed</option>
			<option value="approval">Waiting for approval</option>
			<option value="invitation">Invitation sent</option>
		</select>
		<label for="filter_payment">Payment, Showing:</label>
		<select id="filter_payment" onchange="searchMemberList();">
			<option value="all" selected="selected">All</option>
			<option value="paid">Paid</option>
			<option value="notpaid">Non-paid</option>
		</select>
		<label for="filter_businesscard">Business Card, Showing:</label>
		<select id="filter_businesscard" onchange="searchMemberList();">
			<option value="all" selected="selected">All</option>
			<option value="ok">Has business card</option>
			<option value="approval">Waiting for approval</option>
			<option value="expired">Business card expired</option>
			<option value="none">No business card</option>
		</select>
<?php if ('manage' !== VipMode()) { ?>
		<label for="filter_vip">VIP, Showing:</label>
		<select id="filter_vip" onchange="searchMemberList();">
			<option value="all" selected="selected">All</option>
			<option value="vip">Is a VIP</option>
			<option value="requested">Has requested to be a VIP</option>
			<option value="none">Is not a VIP</option>
		</select>
<?php } else { ?>
		<label for="filter_byline">Byline, Showing:</label>
		<select id="filter_byline" onchange="searchMemberList();">
			<option value="all" selected="selected">All</option>
			<option value="ok">Has byline</option>
			<option value="approval">Waiting for approval</option>
			<option value="expired">Byline expired</option>
			<option value="none">No byline</option>
		</select>
		<label for="filter_officeaccess">Office Access, Showing:</label>
		<select id="filter_officeaccess" onchange="searchMemberList();">
			<option value="all" selected="selected">All</option>
			<option value="editor">Editor</option>
			<option value="writer">Writer</option>
			<option value="none">None</option>
		</select>
<?php } ?>
		</div>
	</div>
<?php
/*
	if (!empty($organisation['subteams'])) {
		echo '<optgroup label="In team:">';
		EchoTeamFilterOptions($organisation, '', FALSE);
		echo '</optgroup>';
	}*/
?>
	</p>
</div>
<div id="MainColumn">
<?php $this->load->view('members/members_list');?>
</div>

<?php
/*
echo('<div class="BlueBox"><pre>');
print_r($data);
echo('</pre></div>');
*/
?>
