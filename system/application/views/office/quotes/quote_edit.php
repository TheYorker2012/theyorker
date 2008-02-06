<div class='RightToolbar'>
	<h4>What's this?</h4>
	<div class="Entry">
		This page allows you to edit quotes <b>live</b>. Take care.
	</div>
</div>
<div class='blue_box'>
	<h2>edit quote</h2>
	<div name='quote_details_form' id='quote_details_form'>
		<form name='quote_form' action='/office/quotes/update/<?php echo $quote->quote_id; ?>' method='POST' class='form'>
			<fieldset>
				<label for='quote_text'>Quote:</label>
				<textarea id='quote_text' name='quote_text' cols="30" rows="6"><?php echo $quote->quote_text; ?></textarea>
				<br />
				<label for='quote_author'>Author:</label>
				<textarea id='quote_author' name='quote_author' cols="30" rows="2"><?php echo $quote->quote_author; ?></textarea>
				<br />
			<?php if($quote->quote_last_displayed_timestamp != null) { ?>
				<input type='hidden' id='quote_scheduled' name='quote_scheduled' value='1'/>
				<div id="quote_schedule_date_div">
			<?php } else { ?>
				<label for='quote_scheduled'>Scheduled:</label>
				<input type='checkbox' onchange="document.getElementById('quote_schedule_date_div').style.display = (this.checked ? 'block' : 'none');" id='quote_scheduled' name='quote_scheduled' value='1'/>
				<br />
				<div id="quote_schedule_date_div" style="display: none;">
			<?php } ?>
					<label for='quote_schedule_date'>Schedule Date:</label>
					<input type='text' id='quote_schedule_date' name='quote_schedule_date' value='<?php echo ($quote->quote_last_displayed_timestamp ? $quote->quote_last_displayed_timestamp : ''); ?>'/>
					<br />
				</div>
				<input name='name_cancel_button' type='button' onClick="document.location='/office/quotes/';" value='Cancel' class='button' />
				<input name='name_delete_button' type='submit' value='Delete' class='button' onclick="return confirm('Are you sure you want to perminently remove this quote?');" />
				<input name='name_update_button' type='submit' value='Update' class='button' />
			</fieldset>
		</form>
	</div>
</div>
