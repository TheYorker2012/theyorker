// Javascript for calendar subscriptions page
// Author: James Hogan (james at albanarts dot com)
// Copyright (C) 2008 The Yorker

// History:
//  * initial commit 30th Jan 2008

// Manages:
//  * js organisation filtering

// [0] : Org Name
// [1] : Parent
// [2] : Teams
// [3] : subscribed (bool)
// [4] : calendar subscribed (bool)

var calsub_orgs = null;

/// Swap style c1 with c2 in o
function CssSwap(o,c1,c2)
{
	o.className = !CssCheck(o,c1)
		? o.className.replace(c2,c1)
		: o.className.replace(c1,c2);
}
/// Add style c1 to o
function CssAdd(o,c1)
{
	if(!CssCheck(o,c1)) {
		o.className+=o.className?' '+c1:c1;
	}
}
/// Remove style c1 from o
function CssRemove(o,c1)
{
	var rep=o.className.match(' '+c1)?' '+c1:c1;
	o.className=o.className.replace(rep,'');
}
/// Check if style c1 is in o
function CssCheck(o,c1)
{
	return new RegExp('\\b'+c1+'\\b').test(o.className);
}

function calsub_filter_orgs(input)
{
	var filter          = document.getElementById('org_filter');
	var filter_member   = document.getElementById('org_filter_member');
	var filter_calendar = document.getElementById('org_filter_calendar');
	if (calsub_orgs && filter && filter_member && filter_calendar) {
		var force_visibility = {};
		var searchtext = filter.value.toLowerCase();
		var member   = (filter_member.value   != '0') ? (filter_member.value   == 'yes') : null;
		var calendar = (filter_calendar.value != '0') ? (filter_calendar.value == 'yes') : null;
		for (var shortname in calsub_orgs) {
			var visibility = (undefined != force_visibility[shortname]);
			var matched = false;
			var match_subscription =
				(member == null || member == calsub_orgs[shortname][3]) &&
				(calendar == null || calendar == calsub_orgs[shortname][4]);
			// No text = no filter
			if (searchtext == '') {
				visibility = match_subscription;
				matched = match_subscription;
			}
			// Matching filter?
			else if (calsub_orgs[shortname][0].toLowerCase().indexOf(searchtext) != -1) {
				visibility = match_subscription;
				matched = match_subscription;
			}
			// Highlight if matched
			var cur_tr = document.getElementById('calsub_org_'+shortname);
			if (cur_tr) {
				if (matched) {
					CssRemove(cur_tr, 'unmatch');
				} else {
					CssAdd(cur_tr, 'unmatch');
				}
			}
			// Make all children visible
			if (visibility) {
				for (var index in calsub_orgs[shortname][2]) {
					force_visibility[calsub_orgs[shortname][2][index]] = true;
				}
			}
			// Make parents visible also
			var cur_shortname = shortname;
			while (cur_shortname) {
				if (visibility || cur_shortname == shortname) {
					var tr = document.getElementById('calsub_org_'+cur_shortname);
					if (tr) {
						tr.style.display = (visibility ? '' : 'none');
					}
					cur_shortname = calsub_orgs[cur_shortname][1];
				} else {
					break;
				}
			}
		}
	}
}