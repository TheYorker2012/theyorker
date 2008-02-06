<div id='newsnav'>
	<ul id='newsnavlist'>
	<li><a href='<?php echo site_url('admin/'); ?>'><img src='/images/prototype/news/archive.png' alt='Admin' title='Admin' /> Admin</a></li>
	<li><a href='<?php echo site_url('admin/news/'); ?>'><img src='/images/prototype/news/uk.png' alt='News' title='News' /> News</a></li>
	<li><a href='<?php echo site_url('admin/news/request/view/1'); ?>' id='current'><img src='/images/prototype/news/view.png' alt='View Request' title='View Request' /> View Request</a></li>
	</ul>
</div>
<div id='clear'>&nbsp;</div>

<script src='/javascript/prototype.js' type='text/javascript'></script>
<script src='/javascript/scriptaculous.js' type='text/javascript'></script>
<script type='text/javascript'>
function AddComment () {
    Effect.BlindUp('new_comment');
    var list = document.getElementById('comments_list');
    var text = document.getElementById('c_text');
    var now_date = new Date();
    var use_date = now_date.getDate() + "/" + (now_date.getMonth() + 1) + "/" + now_date.getFullYear() + " - " + now_date.getHours() + ":" + now_date.getMinutes();
    list.innerHTML += "<div class='comment'><div class='date'>" + use_date + "</div><div class='name'>Current User</div><div class='text'>" + text.value + "</div></div>";
    text.value = '';
}
function AddQuote () {
    Effect.BlindUp('new_quote');
    var list = document.getElementById('quotes_list');
    var text = document.getElementById('c_quote');
    var person = document.getElementById('c_person');
    var title = document.getElementById('c_title');
    list.innerHTML += "<div class='quote' style='width: 30%; float: left;'><img src='/images/prototype/news/quote_open.png' alt='Quote' title='Quote' />" + text.value + "<img src='/images/prototype/news/quote_close.png' alt='Quote' title='Quote' /><br /><span class='author'>" + person.value + " - " + title.value + "</span></div>";
    text.value = '';
    person.value = '';
    title.value = '';
}
function CancelComment() {
    Effect.BlindUp('new_comment');
    document.getElementById('c_text').value = '';
}
function CancelQuote() {
    Effect.BlindUp('new_quote');
    document.getElementById('c_quote').value = '';
    document.getElementById('c_person').value = '';
    document.getElementById('c_title').value = '';
}
</script>

<form name='' id='' action='' method='' class='form'>
    <fieldset>
        <legend>Request Details</legend>
		<label for='r_title'>Title:</label>
		<input type='text' name='r_title' id='r_title' value='' size='30' />
		<br />
		<label for='r_brief'>Brief:</label>
		<textarea name='r_brief' id='r_brief' cols='40' rows='5'></textarea>
	    <br />
		<label for='r_deadline_day'>Deadline:</label>
		<select name='r_deadline_day' id='r_deadline_day' size='1'>
		    <option value='' selected='selected'></option>
			<?php
			for ($day = 1; $day <= 31; $day++) {
			    echo("<option value='$day'>$day</option>");
			} ?>
		</select> /
		<select name='r_deadline_month' id='r_deadline_month' size='1'>
		    <option value='' selected='selected'></option>
			<?php
			for ($month = 1; $month <= 12; $month++) {
			    echo("<option value='$month'>$month</option>");
			} ?>
		</select>
		<br />
		<label for='r_requested_by'>Requested by:</label>
		<span id='r_requested_by'>Chris Travis</span>
		<br />
	 	<label for='r_box'>Box:</label>
		<select name='r_box' id='r_box' size='1'>
  			<option value='News' selected='selected'>News</option>
  		   	<option value='Features'>Features</option>
			<option value='Lifestyle'>Lifestyle</option>
		</select>
  		<br />
		<label for='r_reporter'>Reporter(s):</label>
		<span>Dan Ashby</span>
		<span>Chris Travis</span>
		<br />
    </fieldset>
    
    <fieldset>
        <legend>Article</legend>
		<label for='r_headline'>Headline:</label>
		<input type='text' name='r_headline' id='r_headline' value='' size='30' />
		<br />
		<label for='r_article'>Article:</label>
		<textarea name='r_article' id='r_article' cols='45' rows='10'></textarea>
	    <br />
    </fieldset>

	<fieldset>
        <legend>Pull Quotes</legend>
        <div id='quotes_list'>
            <div class='quote' style='width: 30%; float: left;'>
			    <img src='/images/prototype/news/quote_open.png' alt='Quote' title='Quote' />
				Clearly there is a possibility that for one reason or another he is on land and has not come to our notice
				<img src='/images/prototype/news/quote_close.png' alt='Quote' title='Quote' />
				<br /><span class='author'>Supt Darren Curtis</span>
	        </div>
        </div>
        <div class='clear'>&nbsp;</div>
        <div class='link_right'><a onClick="Effect.BlindDown('new_quote');">New quote...</a></div>
		<div id='new_quote' style='display:none'>
		    <label for='c_quote'>Quote:</label>
		    <input type='text' name='c_quote' id='c_quote' value='' size='30' />
		    <br />
 		    <label for='c_person'>Person:</label>
		    <input type='text' name='c_person' id='c_person' value='' />
		    <br />
 		    <label for='c_title'>Title:</label>
		    <input type='text' name='c_title' id='c_title' value='' />
		    <br />
			<label for='c_submit_quote'></label>
 			<input type='button' name='c_submit_quote' id='c_submit_quote' value='Add Quote' class='button' onClick='AddQuote();' />
 			<input type='button' name='c_clear_quote' id='c_clear_quote' value='Cancel' class='button' onClick='CancelQuote();' />
		</div>
	</fieldset>

    <fieldset>
        <legend>Comments</legend>
        <div id='comments_list'>
	        <div class='comment'>
			    <div class='date'>3rd December 2006 - 22:26</div>
			    <div class='name'>Jane Doe</div>
			    <div class='text'>I heard this was going to be the event of the year!</div>
	        </div>
	        <div class='comment'>
			    <div class='date'>4th December 2006 - 23:51</div>
			    <div class='name'>Alan Smith</div>
			    <div class='text'>Me too, i think it was me that told you!</div>
	        </div>
        </div>
		<div class='link_right'><a onClick="Effect.BlindDown('new_comment');">New comment...</a></div>
		<div id='new_comment' style='display:none'>
		    <label for='c_text'>Comment:</label>
		    <textarea name='c_text' id='c_text' cols='30' rows='3'></textarea>
		    <br />
			<label for='c_submit_comment'></label>
 			<input type='button' name='c_submit_comment' id='c_submit_comment' value='Add Comment' class='button' onClick='AddComment();' />
 			<input type='button' name='c_clear_comment' id='c_clear_comment' value='Cancel' class='button' onClick='CancelComment();' />
		</div>
    </fieldset>

</form>