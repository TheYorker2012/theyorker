// Javascript for date selector
// Author: James Hogan (james_hogan at theyorker dot co dot uk)
// Copyright (C) 2009 The Yorker
// Depends on javascript/css_classes.js

function input_enabled_changed(name)
{
	var en = document.getElementsByName(name+"[_enabled]")[0].checked;
	var el = document.getElementById(name);
	if (en) {
		el.style.display="";
	}
	else {
		el.style.display="none";
	}
}

function input_error_mouse(name, over)
{
	var err = document.getElementById(name+"__error");
	if (err != null) {
		if (over) {
			CssAdd(err,"mover");
		}
		else {
			CssRemove(err, "mover");
		}
	}
}
