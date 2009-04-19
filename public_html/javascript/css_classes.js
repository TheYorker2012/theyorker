// Javascript for modification of css classes
// Author: James Hogan (james_hogan at theyorker dot co dot uk)
// Copyright (C) 2007-2009 The Yorker

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
/// Toggle the style c1 in o
function CssToggle(o,c1)
{
	(CssCheck(o,c1) ? CssRemove : CssAdd)(o,c1);
}
