// Javascript for input selector
// Author: James Hogan (james_hogan at theyorker dot co dot uk)
// Copyright (C) 2009 The Yorker
// Depends on javascript/css_classes.js

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
