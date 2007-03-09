<script type="text/javascript">
function loadWebmail() {
	document.getElementById('username').value = document.getElementById('user').value;
	document.getElementById('password').value = document.getElementById('pass').value;
	document.getElementById('webmail').submit();
	return false;
}

function checkEmail () {
	var user = document.getElementById('user').value;
	var pass = document.getElementById('pass').value;
	if ((user == '') || (pass == '')) {
		window.alert('Please enter both your username and password!');
	} else {
		document.getElementById('check_load').className = 'ajax_loading show';
		document.getElementById('email_container').className = 'hide';
		xajax__checkEmail(user,pass);
	}
	return false;
}

function msgError(error) {
	document.getElementById('check_load').className = 'ajax_loading hide';
	window.alert(error);
}

function checkedEmails(count) {
	document.getElementById('check_load').className = 'ajax_loading hide';
	document.getElementById('email_count').innerHTML = count;
	document.getElementById('email_container').className = 'show';
}
</script>

<div class="RightToolbar">
	<h4>IMAP E-Mail Checker</h4>
	If you enter your UoY Computing Services username and password this page will tell
	you how many unread emails are awaiting you in your inbox.
</div>

<form id="imap_form" action="#" method="post" onsubmit="return checkEmail();" class="form">
	<div class="blue_box">
		<h2>IMAP E-Mail Checker</h2>
		<fieldset>
			<label for="user">Username:</label>
			<input type="text" name="user" id="user" value="" />
			<br />
			<label for="pass">Password:</label>
			<input type="password" name="pass" id="pass" value="" />
			<br />
			<input type="submit" name="check" id="check" value="Check E-Mail" class="button" />
		</fieldset>
	</div>
</form>


<form action="https://webmail1.york.ac.uk/" method="post" name="login" id="webmail">
	<div id="check_load" class="ajax_loading hide">
		<img src="/images/prototype/prefs/loading.gif" alt="Checking" title="Checking" /> Checking your e-mail account...
	</div>
	<div id="email_container" class="hide">
	No. of unread emails = <span id='email_count'>#</span><br />
	<a href="https://webmail1.york.ac.uk/" onclick="return loadWebmail();">Click here to use UoY Webmail</a>
	</div>
	<input type="hidden" name="username" id="username" value="" />
	<input type="hidden" name="password" id="password" value="" />
	<input type="hidden" name="login" value="Login" />
</form>