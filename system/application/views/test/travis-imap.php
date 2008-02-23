<script type='text/javascript' src='/javascript/prototype.js'></script>
<script type='text/javascript' src='/javascript/scriptaculous.js?load=effects'></script>
<style>
#york_imap_notice {
	width: 120px;
	text-align: center;
	color: #fff;
	font-size: 10px;
	font-weight: bold;
	background-color: #0e6633;
	padding: 2px 2px 2px 0;
	border: 1px solid #0e6633;
}
#york_imap {
	width: 120px;
	text-align: right;
	background: url('/images/prototype/news/test/webmail.jpg');
	background-repeat: no-repeat;
	font-size: 10px;
	padding: 4px 2px 4px 0;
	border: 1px solid #0e6633;
}
#york_imap #email_count {
	font-weight: bold;
}

#york_imap_header {
	font-size: small;
	padding: 0 2px;
	background-color: #0e6633;
	color: #fff;
}
#york_imap_update {
	text-align: right;
	font-size: x-small;
	border: 1px solid #0e6633;
	border-top: 0;
	padding: 0 2px;
}
#york_imap_box {
	font-size: small;
	border: 1px solid #0e6633;
	border-bottom: 0;
	padding: 0 2px;
}
.york_imap_email {
	text-align: left;
	overflow: hidden;
	border-bottom: 1px solid #0e6633;
}
.york_imap_email .york_imap_sender {
	font-size: x-small;
}
.york_imap_email .york_imap_subject {
	overflow: hidden;
}
.york_imap_email .york_imap_date {
	padding-left: 5px;
	background-color: #fff;
	text-align: right;
	float: right;
}
</style>

<script type="text/javascript">
function showNotice() {
	Effect.BlindDown("york_imap_notice");
	setTimeout("flashNotice()",2000);
	setTimeout("hideNotice()",10000);
	return false;
}
function flashNotice() {
	Effect.Pulsate("york_imap_notice");
}
function hideNotice() {
	Effect.Fade("york_imap_notice");
}

var york_inbox = new Array();
var york_inbox_count = 0;

function inboxContents() {
	var container = document.getElementById('york_imap_box');
	var tmp = '';
	container.innerHTML = '';
	for (var x=0; x<=(york_inbox_count-1);x++) {
		tmp = '<div class="york_imap_email"><span class="york_imap_date">' + york_inbox[x][1] + '</span><div class="york_imap_subject">';
		if (york_inbox[x][0] == 1) {
			tmp = tmp + '<b>';
		}
		tmp = tmp + york_inbox[x][2];
		if (york_inbox[x][0] == 1) {
			tmp = tmp + '</b>';
		}
		tmp = tmp + '</div><div class="york_imap_sender">- ' + york_inbox[x][3] + '</div></div>' + container.innerHTML;
		container.innerHTML = tmp;
	}
	york_inbox = new Array();
	york_inbox_count = 0;
}
function inboxChecked(time) {
	document.getElementById('york_imap_checked').innerHTML = time;
}
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
	showNotice();
}
</script>

<div class="RightToolbar">
	<h4>IMAP E-Mail Checker</h4>
	If you enter your UoY Computing Services username and password this page will tell
	you how many unread emails are awaiting you in your inbox.
	<br /><br />

	<div align="center">
		<div id="york_imap">
			<a href="https://webmail1.york.ac.uk/" onclick="return loadWebmail();"><span class="black"><span id="email_count">0</span> Unread</span></a>
		</div>
		<div id="york_imap_notice" style="display:none;">
			You have a new e-mail!
		</div>

		<br /><br /><br />
		<a href="https://webmail1.york.ac.uk/" onclick="return loadWebmail();"><img src="/images/prototype/news/test/webmail_large.jpg" alt="UoY Webmail" title="UoY Webmail" /></a>
		<div id="york_imap_header">
			<div class="york_imap_email">
				<span class="york_imap_date" style="background-color:#0e6633;"><b>Time</b></span>
				<b>Subject</b>
				<div class="york_imap_sender"><b>- Sender</b></div>
			</div>
		</div>
		<div id="york_imap_box"></div>
		<div id="york_imap_update"><br />Last check @ <span id="york_imap_checked">Never</span>
		</div>
	</div>
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

<div id="check_load" class="ajax_loading hide" style="width:400px;">
	<img src="/images/prototype/prefs/loading.gif" alt="Checking" title="Checking" /> Checking your e-mail account...
</div>

<form action="https://webmail1.york.ac.uk/" method="post" id="webmail">
	<input type="hidden" name="username" id="username" value="" />
	<input type="hidden" name="password" id="password" value="" />
	<input type="hidden" name="login" value="Login" />
</form>