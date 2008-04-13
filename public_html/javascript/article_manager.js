var start_page = 'request';
var popup_shown = false;

function loadPage () {
	switchPage(start_page);
}

function switchPage (page_id) {
	if ((!popup_shown) && (document.getElementById('am_nav_' + page_id).className != 'disabled')) {
		/* Change selected menu option */
		var nav = document.getElementById('am_nav');
		for (var i = 0; i < nav.childNodes.length; i++) {
			if (nav.childNodes[i].className != 'disabled') {
				nav.childNodes[i].className = '';
			}
		}
		document.getElementById('am_nav_' + page_id).className = 'current';
		/* Change content displayed */
		var canvas = document.getElementById('am_pages');
		for (var i = 0; i < canvas.childNodes.length; i++) {
			if (canvas.childNodes[i].className == 'am_page') {
				canvas.childNodes[i].style.display = 'none';
			}
		}
		document.getElementById('am_page_' + page_id).style.display = 'block';
	}
	return false;
}

/* Used when suggestion becomes an article */
function enablePages () {
	var nav = document.getElementById('am_nav');
	for (var i = 0; i < nav.childNodes.length; i++) {
		if (nav.childNodes[i].className == 'disabled') {
			nav.childNodes[i].className = '';
		}
	}
}

/* Used when an article is only a suggestion */
function disablePages () {
	var nav = document.getElementById('am_nav');
	for (var i = 0; i < nav.childNodes.length; i++) {
		nav.childNodes[i].className = 'disabled';
	}
	document.getElementById('am_nav_request').className = 'current';
}

function showPopup (popup_id) {
	popup_shown = true;
	document.getElementById('am_popup_' + popup_id).style.display = 'block';
	document.getElementById('am_popup_container').style.display = 'block';
	document.getElementById('am_blackout').style.display = 'block';
}

function hidePopup (popup_id) {
	popup_shown = false;
	document.getElementById('am_popup_' + popup_id).style.display = 'none';
	document.getElementById('am_popup_container').style.display = 'none';
	document.getElementById('am_blackout').style.display = 'none';
}