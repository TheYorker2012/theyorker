/** search.js
 * Provides AJAXified autosuggest
 * @author Mark Goodall <mark.goodall@gmail.com
 * @depends prototype scriptaculous
 **/


// This stores ids of the sections which can be toggled
// cleaner code, not more efficient
var showID = new Array("ajax-articles", "ajax-dir", "ajax-events", "ajax-york");

// Prevents mouseout code closing searchbox when field in focus
var disableClose = false;

function search_onBlur() {
	if ($('site-search').value == '') {
		$('site-search').value = 'search';
	}
	$('site-search').style.color = '#aaa';
	disableClose = false;
	if ($('ajax-results').style.display == "block" & !active) {
		new Effect.BlindUp($('ajax-results'));
	}
}

function search_onFocus() {
	if($('site-search').value == 'search') {
		$('site-search').value = '';
	} else if($('site-search').value != '') {
	}
	disableClose = true;
	$('site-search').style.color = '#000';
}

// Prevents constant ajax searches, only 600ms after last keypress
var lastKeypress;
function search_onKeyUp() {
	window.clearTimeout(lastKeypress);
	lastKeypress = window.setTimeout("search_Change()", 600);
}
function search_Change() {
	if ($('site-search').value != '') {
		$('ajax-results').style.display = 'block';
		$('ajax-results').innerHTML = '<div style="text-align:center"><img src="/images/prototype/prefs/loading.gif"/></div>';
		//Effect.BlindDown($('ajax-results'));
		//not sure why this wouldn't fire, something about the environment has changed thru timeout
		var ajax = new Ajax.Updater('ajax-results', '/api/sitesearch',
		                            {asynchronous:true,
		                             parameters:Form.serialize($('site_search')),
		                             onFailure:function() {$('ajax-results').innerHTML = 'Search failed, have you gone offline?';}});
	}
}

// toggles visibility of sections
// not persistent, would be easy to do using storage though
function search_noShow(item) {
	new Effect.toggle($(showID[item]), 'blind');
}

//prevents firing of multiple mouseout/overs because of nested tags
function isMouseLeaveOrEnter(e, handler) {
	var reltg = e.relatedTarget ? e.relatedTarget : e.type == 'mouseout' ? e.toElement : e.fromElement;
	while (reltg && reltg != handler) reltg = reltg.parentNode;
	return (reltg != handler);
}

var lastTimeout;
var active = false;
function search_Close(check) {
	if (check) {
		if (!active & !disableClose & $('ajax-results').style.display == 'block') {
			Effect.BlindUp($('ajax-results'));
		}
	} else {
		window.clearTimeout(lastTimeout);
		lastTimeout = window.setTimeout("search_Close(true)", 1000);
	}
}