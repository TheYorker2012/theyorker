<div id="RightColumn">
	<h2 class="first">What's this?</h2>
	<div class="Entry">
		<p>Some bullshit about adding events.</p>

	</div>
	<h2>Hints</h2>
	<div class="Entry">
		<p>Weeeeeeeeee!</p>
	</div>
</div>

<script type="text/javascript">

function ShitFunction()
{
	var repeat_select = document.getElementById("repeat");
	var repeat_type = repeat_select.options[repeat_select.selectedIndex].value
	var repeat_div = document.getElementById("repeat_div");

	if (repeat_type == "none")
	{
		repeat_div.innerHTML = "";
	}
	else if (repeat_type == "daily")
	{
		repeat_div.innerHTML = '<fieldset><label for="">Every:</label><input type="text" size="3" value="1"> day(s)</fieldset>'
	}
	else if (repeat_type == "weekly")
	{
		repeat_div.innerHTML = '\
		<fieldset>\
		<label for="rw_oft">Every</label><input type="text" size="3" value="1"><br />\
		<label for="rw_mon">Monday</label><input id="rw_mon" type="checkbox"><br />\
		<label for="rw_tur">Tuesday</label><input id="rw_tur" type="checkbox"><br />\
		<label for="rw_wed">Wednesday</label><input id="rw_wed" type="checkbox"><br />\
		<label for="rw_thu">Thursday</label><input id="rw_thu" type="checkbox"><br />\
		<label for="rw_fri">Friday</label><input id="rw_fri" type="checkbox"><br />\
		<label for="rw_sat">Saturday</label><input id="rw_sat" type="checkbox"><br />\
		<label for="rw_sun">Sunday</label><input id="rw_sun" type="checkbox"><br />\
		</fieldset>';
	}
	else if (repeat_type == "yearly")
	{
		repeat_div.innerHTML = '<fieldset><label for="">Every:</label><input type="text" size="3" value="1"> year(s)</fieldset>'
	}
}

</script>

<div id="MainColumn">
<div class="BlueBox">
		<h2>add event</h2>
		<form id="" action="" method="post" class="">

		<fieldset>
		<!-- <br /> tags necessary for correct rendering in text based browsers -->
		<label for="a_authorname">What: </label>
		<input type="text" name="a_authorname" id="a_authorname" value="" size="30"/><br />
		
		<label for="a_authorname">Start: </label>
		<input type="text" name="a_authorname" id="a_authorname" value="29/03/2007" size="10"/>
		<input type="text" name="a_authorname" id="a_authorname" value="9.15" size="5"/>
		<label for="finish_date">Finish: </label>
		<input type="text" name="a_authorname" id="a_authorname" value="29/03/2007" size="10"/>
		<input type="text" name="a_authorname" id="a_authorname" value="10.15" size="5"/>
		<br />

		<label for="a_authorname">Where: </label>
		<input type="text" id="autocomplete" name="autocomplete_parameter"/>
		<span id="indicator1" style="display: none"><img src="/images/spinner.gif" alt="Working..." /></span>
		<div id="autocomplete_choices" class="autocomplete"></div>

		<label for="">Description:</label>
		<textarea</textarea><br />
		
		<label for="">Repeats:</label>
		<select onchange="ShitFunction()" id="repeat">
			<option value="none" selected="selected">None</option>
			<option value="daily">Daily</option>
			<option value="weekly">Weekly</option>
			<option value="yearly">Yearly</option>
		</select>
		<br /><br />
	</fieldset>
		<div id="repeat_div" style=""></div>
	<fieldset>
	</fieldset>
	<fieldset>
		<input class="button" type="submit" name="r_submit" id="r_submit" value="Submit" />
		<input class="button" type="reset" name="r_cancel" id="r_cancel" value="Cancel" onclick="hideFeedback();"./>
	</fieldset>
</form>
</div>
</div>