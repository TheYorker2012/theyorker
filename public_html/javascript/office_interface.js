var start_page = 'announcements';
var popup_shown = false;

function loadPage () {
	switchPage(start_page);
}

function switchPage (page_id) {
	if ((!popup_shown) && (document.getElementById('nav_' + page_id).className != 'disabled')) {
		/* Change selected menu option */
		var nav = document.getElementById('office_nav');
		for (var i = 0; i < nav.childNodes.length; i++) {
			if (nav.childNodes[i].className != 'disabled') {
				nav.childNodes[i].className = '';
			}
		}
		document.getElementById('nav_' + page_id).className = 'current';
		/* Change content displayed */
		var canvas = document.getElementById('office_pages');
		for (var i = 0; i < canvas.childNodes.length; i++) {
			if (canvas.childNodes[i].className == 'office_page') {
				canvas.childNodes[i].style.display = 'none';
			}
		}
		document.getElementById('page_' + page_id).style.display = 'block';
	}
	return false;
}