/*
 * Author: James Hogan
 * Copyright (c) 2008 The Yorker
 */

/*
 * features
 *  show cross word
 *  can type letters into boxes
 *  arrow navigation
 *  typing changes focus to next box
 *  clues select and clickable
 *  can submit completed crossword (with name/email?)
 *  if its an old crossword, can check/reveal a word/enture puzzle
 *  timer (just for curiosity, not sent with score)
 */

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

function Orientation(orientation)
{
	this.m_orientation = orientation;

	this.isHorizontal = function()
	{
		return this.m_orientation == 0;
	}
	this.isVertical = function()
	{
		return this.m_orientation == 1;
	}
	this.toggle = function()
	{
		this.m_orientation = 1-this.m_orientation;
	}
	this.dx = function()
	{
		return this.isHorizontal() ? 1 : 0;
	}
	this.dy = function()
	{
		return this.isVertical() ? 1 : 0;
	}
}

function newHorizontal()
{
	return new Orientation(0);
}

function newVertical()
{
	return new Orientation(1);
}

// Data for each crossword (by name)
var crosswords = {};

function crossword(name)
{
	return crosswords[name];
}

function Crossword(name, width, height)
{
	crosswords[name] = this;

	this.m_name = name;
	this.m_width = width;
	this.m_height = height;

	this.m_x = -1;
	this.m_y = -1;
	this.m_orientation = newHorizontal();
	this.m_xyModified = false;
	this.m_xySpaced = false;
	this.m_light = [];

	this.width = function()
	{
		return this.m_width;
	}
	this.height = function()
	{
		return this.m_height;
	}

	this.x = function()
	{
		return this.m_x;
	}
	this.y = function()
	{
		return this.m_y;
	}
	this.orientation = function()
	{
		return this.m_orientation;
	}

	this.editBox = function(x, y)
	{
		return document.getElementById(this.m_name+"-edit-"+x+"-"+y);
	}

	this.clue = function(x, y, o)
	{
		return document.getElementById(this.m_name+"-clue-"+x+"-"+y+"-"+(o.isVertical()?1:0));
	}

	this.clueOf = function(x, y, o)
	{
		var dx = o.dx();
		var dy = o.dy();
		while (true) {
			x -= dx;
			y -= dy;
			if (this.isCellBlank(x,y)) {
				x += dx;
				y += dy;
				break;
			}
		}
		return this.clue(x, y, o);
	}

	this.cell = function(x, y)
	{
		return document.getElementById(this.m_name+"-"+x+"-"+y);
	}

	// Get a list of cells in a light, the first being the one specified
	this.light = function(x, y, orientation, editBox)
	{
		var result = []
		var cell = (editBox ? this.editBox(x, y) : this.cell(x, y));
		var blank = this.isCellBlank(x, y);
		if (null != cell) {
			result.push(cell);
			// First go backwards
			var cx = x;
			var cy = y;
			var dx = orientation.dx();
			var dy = orientation.dy();
			while (true) {
				cx -= dx;
				cy -= dy;
				if (this.isCellBlank(cx,cy)) {
					break;
				}
				cell = (editBox ? this.editBox(cx, cy) : this.cell(cx, cy));
				if (null != cell) {
					result.push(cell);
				}
				else {
					break;
				}
			};
			if (!blank) {
				// Now go forwards
				cx = x;
				cy = y;
				while (true) {
					cx += dx;
					cy += dy;
					if (this.isCellBlank(cx,cy)) {
						break;
					}
					cell = (editBox ? this.editBox(cx, cy) : this.cell(cx, cy));
					if (null != cell) {
						result.push(cell);
					}
					else {
						break;
					}
				}
			}
			else {
				// Only go forwards one
				cx = x + dx;
				cy = y + dy;
				cell = (editBox ? this.editBox(cx, cy) : this.cell(cx, cy));
				if (null != cell) {
					result.push(cell);
				}
			}
		}
		return result;
	}

	this.setClueComplete = function(x, y, o, completed)
	{
		var clue = this.clueOf(x, y, o);
		if (null != clue) {
			if (completed) {
				CssAdd(clue, "complete");
			}
			else {
				CssRemove(clue, "complete");
			}
		}
	}

	this.isClueComplete = function(x, y, o)
	{
		var boxes = this.light(x, y, o, this.value, true);
		for (var i = 0; i < boxes.length; ++i) {
			if (boxes[i].value == "") {
				return false;
			}
		}
		return true;
	}

	this.updateClueComplete = function(x, y, o)
	{
		this.setClueComplete(x, y, o, this.isClueComplete(x, y, o));
	}

	this.updateCellFilled = function(x, y)
	{
		this.updateClueComplete(x, y, newVertical()); 
		this.updateClueComplete(x, y, newHorizontal()); 
	}

	this.selectLight = function(light)
	{
		if (null != light[0]) {
			CssAdd(light[0], "focus");
		}
		for (var i = 0; i < light.length; ++i) {
			CssAdd(light[i], "selected");
		}
	}
	this.deselectLight = function(light)
	{
		if (null != light[0]) {
			CssRemove(light[0], "focus");
		}
		for (var i = 0; i < light.length; ++i) {
			CssRemove(light[i], "selected");
		}
	}

	this.isCellBlank = function(x, y)
	{
		var cell = this.cell(x,y);
		return null == cell || CssCheck(cell,'blank');
	}

	this.value = function(x, y)
	{
		var edit = this.editBox(x, y);
		return edit.value;
	}

	this.modifyValue = function(x, y, v)
	{
		var edit = this.editBox(x, y);
		var statusChanged = ((v == "") != (edit.value == ""));
		edit.value = v;
		if (statusChanged) {
			this.updateCellFilled(x, y);
			this.deselectLight(this.m_light);
			this.m_light = this.light(this.m_x, this.m_y, this.m_orientation, false);
			this.selectLight(this.m_light);
		}
		if (x == this.m_x && y == this.m_y) {
			this.m_xyModified = (v != "");
		}
		else {
			this.m_xyModified = true;
		}
	}

	this.toggleOrientation = function()
	{
		this.m_xySpaced = false;

		// Deselect previous light
		this.deselectLight(this.m_light);
		var clue = this.clueOf(this.m_x, this.m_y, this.m_orientation);
		if (null != clue) {
			CssRemove(clue, "selected");
		}
		
		// Switch orientation only if it's sensible
		this.m_orientation.toggle();
		if (null == this.editBox(this.m_x-this.m_orientation.dx(), this.m_y-this.m_orientation.dy()) &&
		    null == this.editBox(this.m_x+this.m_orientation.dx(), this.m_y+this.m_orientation.dy()))
		{
			this.m_orientation.toggle();
		}

		// Select new light
		this.m_light = this.light(this.m_x, this.m_y, this.m_orientation, false);
		this.selectLight(this.m_light);
		clue = this.clueOf(this.m_x, this.m_y, this.m_orientation);
		if (null != clue) {
			CssAdd(clue, "selected");
		}
	}

	this.changeCell = function(x, y)
	{
		var edit = this.editBox(x, y);
		if (x != this.m_x || y != this.m_y) {
			this.m_xyModified = false;
			this.m_xySpaced = false;
		}
		// Deselect previous light
		this.deselectLight(this.m_light);
		var clue = this.clueOf(this.m_x, this.m_y, this.m_orientation);
		if (null != clue) {
			CssRemove(clue, "selected");
		}

		// Change cell
		this.m_x = x;
		this.m_y = y;
		if (null != edit) {
			edit.focus();

			// Switch orientation if it isn't sensible
			if (null == this.editBox(x-this.m_orientation.dx(), y-this.m_orientation.dy()) &&
				null == this.editBox(x+this.m_orientation.dx(), y+this.m_orientation.dy()))
			{
				this.m_orientation.toggle();
			}

			// Select new light
			this.m_light = this.light(x, y, this.m_orientation, false);
			this.selectLight(this.m_light);
			clue = this.clueOf(x, y, this.m_orientation);
			if (null != clue) {
				CssAdd(clue, "selected");
			}
		}
	}

	this.changeCellRelative = function(x, y, dx, dy, ghost)
	{
		// Find next box
		var edit;
		do {
			x += dx;
			y += dy;
			edit = this.editBox(x, y);
		} while (ghost && null == edit && x >= 0 && y >= 0 && x < this.m_width && y < this.m_height);
		if (null != edit) {
			this.changeCell(x, y);
		}
	}

	this.click = function(x, y, e)
	{
		if (this.m_x == x && this.m_y == y) {
			this.toggleOrientation();
		}
		else {
			this.changeCell(x, y);
		}
	}

	this.keyDown = function(x, y, e)
	{
		var keyCode = e.keyCode;
		// Control keys
		if (keyCode != 0) {
			if (keyCode == 8 /* backspace */) {
				// If the cell not modified, go backwards first
				var modified = this.m_xyModified;
				this.changeCellRelative(x, y, -this.orientation().dx(), -this.orientation().dy(), false);
				if (!modified) {
					x = this.x();
					y = this.y();
				}
				this.modifyValue(x, y, "");
				return false;
			}
			else if (keyCode == 46  /* delete (others)*/ ||
			         keyCode == 127 /* delete (konqueror) */) {
				var val = this.value(x, y);
				// Only move backwards if it was already blank
				if (val == "") {
					this.changeCellRelative(x, y, -this.orientation().dx(), -this.orientation().dy(), false);
					x = this.x();
					y = this.y();
				}
				this.modifyValue(x, y, "");
				return false;
			}
			// arrow keys
			else if (keyCode >= 37 && keyCode <= 40) {
				var dx = 0;
				var dy = 0;
				if (keyCode == 37 /* left arrow */) {
					dx = -1;
				}
				else if (keyCode == 38 /* up arrow */) {
					dy = -1;
				}
				else if (keyCode == 39 /* right arrow */) {
					dx = 1;
				}
				else if (keyCode == 40 /* down arrow */) {
					dy = 1;
				}
				this.changeCellRelative(x, y, dx, dy, true);
				this.m_xyModified = true;
			}
		}
		return true;
	}

	this.keyPress = function(x, y, e)
	{
		var charCode = e.which;
		if (undefined == charCode) {
			charCode = e.keyCode;
		}
		// Character keys
		if (charCode != 0 && !e.ctrlKey && !e.altKey && !e.metaKey) {
			var charStr = String.fromCharCode(charCode).toUpperCase();
			if (charStr == " ") {
				this.modifyValue(x, y, "");
				this.m_xySpaced = true;
			}
			else {
				var charcheck = /[a-zA-Z ]/;
				var valid = charcheck.test(charStr);
				if (valid) {
					this.modifyValue(x, y, charStr);
					this.changeCellRelative(x, y, this.orientation().dx(), this.orientation().dy(), false);
				}
			}
			return false;
		}
		return true;
	}
}

function crosswordDeselect(name, e)
{
	return crossword(name).changeCell(-1, -1);
}

function crosswordClick(name, x, y, e)
{
	return crossword(name).click(x, y, e);
}

function crosswordSelectLight(name, x, y, o, e)
{
	var xw = crossword(name);
	xw.changeCell(x, y);
	if (xw.orientation().isVertical() != (o == 1)) {
		xw.toggleOrientation();
	}
}

function crosswordKeyDown(name, x, y, e)
{
	return crossword(name).keyDown(x, y, e);
}

function crosswordKeyPress(name, x, y, e)
{
	return crossword(name).keyPress(x, y, e);
}

new Crossword("xw", 13, 13);

