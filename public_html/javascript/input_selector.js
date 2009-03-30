// Javascript for input selector
// Author: James Hogan (james_hogan at theyorker dot co dot uk)
// Copyright (C) 2009 The Yorker

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
/// Toggle the style c1 in o
function CssToggle(o,c1)
{
	(CssCheck(o,c1) ? CssRemove : CssAdd)(o,c1);
}

var input_selector_visible = null;

function input_selector_init(name)
{
	var selector = document.getElementById(name);
	CssAdd(selector, "selector_invisible");
	CssAdd(selector, "selector_absolute");
}

function input_selector_click(name)
{
	var selector = document.getElementById(name);
	if (input_selector_visible == selector) {
		CssAdd(selector, "selector_invisible");
		input_selector_visible = null;
	}
	else {
		if (input_selector_visible != null) {
			CssAdd(input_selector_visible, "selector_invisible");
		}
		CssRemove(selector, "selector_invisible");
		input_selector_visible = selector;
	}
}
