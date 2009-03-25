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
/// Toggle the style c1 in o
function CssToggle(o,c1)
{
	(CssCheck(o,c1) ? CssRemove : CssAdd)(o,c1);
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
	this.val = this.dy;
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

// Data per cell
function CrosswordCell(name, x, y)
{
	var mainEl = document.getElementById(name+"-"+x+"-"+y);
	var mainEd = document.getElementById(name+"-edit-"+x+"-"+y);

	this.m_letter = ((null != mainEd && null != mainEl && !CssCheck(mainEl, "blank"))
						? mainEd.value
						: null);
	this.m_solution = null;
	this.m_spacers = ((null != mainEl) ? [CssCheck(mainEl, "hsp"), CssCheck(mainEl, "vsp")] : [false,false]);
	this.m_lights = [null, null];
	this.m_els = [ mainEl, null, null ];
	this.m_eds = [ mainEd, null, null ];
	this.m_sup = document.getElementById(name+"-num-"+x+"-"+y);
	this.m_selected	= ((null != mainEl) ? CssCheck(mainEl, "selected") : false);
	this.m_focused	= ((null != mainEl) ? CssCheck(mainEl, "focus") : false);

	this.editBox = function()
	{
		return this.m_eds[0];
	}

	this.addEl = function(el)
	{
		if (el != null) {
			this.m_els.push(el);
		}
	}
	this.addEd = function(ed)
	{
		this.m_eds.push(ed);
	}

	this.setLetter = function(letter)
	{
		if (this.m_letter != letter) {
			// Change edit texts
			var strLetter = ((letter == null) ? "" : letter);
			for (var i = 0; i < this.m_eds.length; ++i) {
				var ed = this.m_eds[i];
				if (null != ed) {
					ed.value = strLetter;
				}
			}
			// Change blankness
			var op = ((letter == null) ? CssAdd : CssRemove);
			if ((this.m_letter == null) != (letter == null)) {
				for (var i = 0; i < this.m_els.length; ++i) {
					if (null != this.m_els[i]) {
						op(this.m_els[i], "blank");
					}
				}
			}
			// Change internally
			var oldLetter = this.m_letter;
			this.m_letter = letter;
			// Knowness
			if ((oldLetter != "") != (letter != "")) {
				for (var i = 0; i < 2; ++i) {
					if (this.m_lights[i] != null) {
						this.m_lights[i].updateCompleteness();
					}
				}
			}
		}
	}
	this.letter = function()
	{
		return this.m_letter;
	}
	this.isBlank = function()
	{
		return (this.m_letter == null);
	}
	this.isKnown = function()
	{
		return (this.m_letter != "");
	}
	this.setSolution = function(letter)
	{
		m_solution = letter;
	}
	this.solution = function()
	{
		return m_solution;
	}
	this.solve = function()
	{
		if (this.m_solution != null) {
			this.setLetter(this.m_solution);
		}
	}
	this.extract = function()
	{
		this.m_solution = this.m_letter;
	}
	this.check = function()
	{
		if (this.m_solution == null) {
			return null;
		}
		return (this.m_solution == this.m_letter);
	}

	this.space = function(orientation, enable)
	{
		if (this.m_spacers[orientation.val()] != enable) {
			// Change spacer class
			var op = (enable ? CssAdd : CssRemove);
			var cl = (orientation.isHorizontal() ? "hsp" : "vsp");
			for (var i = 0; i < this.m_els.length; ++i) {
				if (null != this.m_els[i]) {
					op(this.m_els[i], cl);
				}
			}
			// Change internally
			this.m_spacers[orientation.val()] = enable;
			if (null != this.m_lights[orientation.val()]) {
				this.m_lights[orientation.val()].updateWordLengths();
			}
		}
	}
	this.isSpaced = function(orientation)
	{
		return this.m_spacers[orientation];
	}

	this.select = function(selected)
	{
		if (this.m_selected != selected) {
			// Change selected class
			var op = (selected ? CssAdd : CssRemove);
			for (var i = 0; i < this.m_els.length; ++i) {
				if (null != this.m_els[i]) {
					op(this.m_els[i], "selected");
				}
			}
			// Change internally
			this.m_selected = selected;
		}
	}

	this.focus = function(focused, index)
	{
		if (focused) {
			var ed = this.m_eds[index];
			if (null != ed) {
				ed.focus();
			}
		}
		if (this.m_focused != focused) {
			// Change focus class
			var op = (focused ? CssAdd : CssRemove);
			for (var i = 0; i < this.m_els.length; ++i) {
				if (null != this.m_els[i]) {
					op(this.m_els[i], "focus");
				}
			}
			// Change internally
			this.m_focused = focused;
		}
	}

	this.setLight = function(orientation, light)
	{
		this.m_lights[orientation] = light;
	}
	this.light = function(orientation)
	{
		return this.m_lights[orientation];
	}
}

function CrosswordLight(name, x, y, orientation, cells, els, eds)
{
	this.m_x = 0;
	this.m_y = 0;
	this.m_orientation = orientation;
	this.m_numberEls = [cells[0].m_sup,
						document.getElementById(name+"-0-num-"+x+"-"+y),
						document.getElementById(name+"-1-num-"+x+"-"+y)];
	this.m_number = 0;
	this.m_cells = [];
	this.m_clueDiv = document.getElementById(name+"-"+orientation+"-clue-"+x+"-"+y);
	this.m_clueInEls = [document.getElementById(name+"-"+orientation+"-clueinput0-"+x+"-"+y),
						document.getElementById(name+"-"+orientation+"-clueinput1-"+x+"-"+y)];
	this.m_clueEls = [	document.getElementById(name+"-"+orientation+"-cluetext0-"+x+"-"+y),
						document.getElementById(name+"-"+orientation+"-cluetext1-"+x+"-"+y)];
	this.m_clues = [null, null];
	this.m_wordlenEl = document.getElementById(name+"-"+orientation+"-wordlen-"+x+"-"+y);
	this.m_cluetextEl = null;
	this.m_inlineEl = document.getElementById(name+"-"+orientation+"-inline-"+x+"-"+y);
	this.m_complete = null;

	this.setCells = function(x, y, cells, els, eds)
	{
		for (var i = 0; i < this.m_cells.length; ++i) {
			this.m_cells[i].setLight(this.m_orientation, null);
			this.m_cells[i].m_els[1+this.m_orientation] = null;
			this.m_cells[i].m_eds[1+this.m_orientation] = null;
		}
		this.m_cells = cells;
		if (cells.length > 0 && (this.m_x != x || this.m_y != y)) {
			this.m_numberEls[0] = cells[0].m_sup;
			this.m_number = parseInt(this.m_numberEls[0].textContent, 10);
		}
		this.m_x = x;
		this.m_y = y;
		for (var i = 0; i < this.m_cells.length; ++i) {
			this.m_cells[i].setLight(this.m_orientation, this);
			this.m_cells[i].m_els[1+this.m_orientation] = els[i];
			this.m_cells[i].m_eds[1+this.m_orientation] = eds[i];
		}
		this.updateWordLengths();
	}

	this.clean = function()
	{
		this.setCells(this.m_x, this.m_y, [], [], []);
	}

	this.select = function(selected, x, y)
	{
		for (var i = 0; i < this.m_cells.length; ++i) {
			this.m_cells[i].select(selected);
		}
		(selected ? CssAdd : CssRemove)(this.m_clueDiv, "selected");
	}

	/// Find the current value of this light
	this.value = function()
	{
		var val = "";
		for (var i = 0; i < this.m_cells.length; ++i) {
			var cell = this.m_cells[i];
			if (cell.isSpaced(this.m_orientation)) {
				val += " ";
			}
			if (!cell.isBlank()) {
				val += cell.letter();
			}
		}
		return val;
	}
	this.solution = function()
	{
		var val = "";
		for (var i = 0; i < this.m_cells.length; ++i) {
			var cell = this.m_cells[i];
			if (cell.isSpaced(this.m_orientation)) {
				val += " ";
			}
			if (!cell.isBlank()) {
				val += cell.solution();
			}
		}
		return val;
	}

	/// Find whether the light is completely filled in
	this.updateCompleteness = function()
	{
		var complete = true;
		for (var i = 0; i < this.m_cells.length; ++i) {
			if (!this.m_cells[i].isKnown()) {
				complete = false;
				break;
			}
		}
		if (this.m_complete != complete) {
			this.m_complete = complete;
			(complete ? CssAdd : CssRemove)(this.m_clueDiv, "complete")
		}
	}

	/// Write solution into grid
	this.solve = function()
	{
		for (var i = 0; i < this.m_cells.length; ++i) {
			this.m_cells[i].solve();
		}
	}
	/// Read solution from grid
	this.extract = function()
	{
		for (var i = 0; i < this.m_cells.length; ++i) {
			this.m_cells[i].extract();
		}
	}
	/// Check whether the value matches the solution
	this.check = function()
	{
		for (var i = 0; i < this.m_cells.length; ++i) {
			if (!m_cells[i].check()) {
				return false;
			}
		}
		return true;
	}

	/// Get length of light
	this.length = function()
	{
		return this.m_cells.length;
	}

	/// Get list of lengths for each word
	this.wordLengths = function()
	{
		var lens = new Array();
		var count = 1;
		for (var i = 1; i < this.m_cells.length; ++i) {
			if (this.m_cells[i].isSpaced(this.m_orientation)) {
				lens.push(count);
				count = 1;
			}
			else {
				++count;
			}
		}
		lens.push(count);
		return lens;
	}
	this.updateWordLengths = function()
	{
		var lens = this.wordLengths();
		var lensStr = lens.join(",");
		this.m_wordlenEl.textContent = lensStr;
	}

	/// Set the number, return whether changed
	this.setNumber = function(num)
	{
		if (this.m_number != num)
		{
			for (var i = 0; i < this.m_numberEls.length; ++i) {
				if (null != this.m_numberEls[i]) {
					this.m_numberEls[i].textContent = num;
				}
			}
			this.m_number = num;
			return true;
		}
		return false;
	}

	// Get the clues
	this.clues = function()
	{
		return this.m_clues;
	}
	// Set the clues
	this.setClues = function(clues)
	{
		this.m_clues = clues;
		this.updateClues();
	}
	// Extract a clue from the DOM
	this.extractClue = function(id)
	{
		var clue = "";
		var fromTextBox = false;
		if (null != this.m_clueInEls[id]) {
			clue = this.m_clueInEls[id].value;
			fromTextBox = true;
		}
		else if (null != this.m_clueEls[id]) {
			clue = this.m_clueEls[id].textContent;
		}
		if (clue != "") {
			this.m_clues[id] = clue;
		}
		else {
			this.m_clues[id] = null;
		}
		if (fromTextBox) {
			this.updateClue(id);
		}
	}
	// Split clues and return the second half
	this.splitClues = function()
	{
		var results = [];
		for (var i = 0; i < this.m_clues.length; ++i) {
			if (null == this.m_clues[i]) {
				results[i] = null;
			}
			else {
				var pos = this.m_clues[i].search(/\|/);
				if (-1 == pos) {
					results[i] = this.m_clues[i];
				}
				else {
					results[i] = this.m_clues[i].substr(pos+1);
					this.m_clues[i] = this.m_clues[i].substr(0, pos);
				}
			}
		}
		this.updateClues();
		return results;
	}
	// Merge some clues into this light
	this.mergeClues = function(clues)
	{
		for (var i = 0; i < this.m_clues.length; ++i) {
			if (null == this.m_clues[i]) {
				this.m_clues[i] = clues[i];
			}
			else if (null != clues[i]) {
				if (this.m_clues[i] != clues[i]) {
					this.m_clues[i] = this.m_clues[i]+"|"+clues[i];
				}
			}
		}
		this.updateClues();
	}
	// Update clue elements
	this.updateClue = function(id)
	{
		if (null != this.m_clueEls[id]) {
			if (null == this.m_clues[id]) {
				this.m_clueEls[id].textContent = "";
			}
			else
			{
				this.m_clueEls[id].textContent = this.m_clues[id];
			}
		}
		if (null != this.m_clueInEls[id]) {
			if (null == this.m_clues[id]) {
				this.m_clueInEls[id].value = "";
			}
			else
			{
				this.m_clueInEls[id].value = this.m_clues[id];
			}
		}
	}
	this.updateClues = function()
	{
		for (var i = 0; i < this.m_clues.length; ++i) {
			this.updateClue(i);
		}
	}

	for (var i = 0; i < this.m_clues.length; ++i) {
		this.extractClue(i);
	}
	this.setCells(x, y, cells, els, eds);
	this.updateCompleteness();
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
	this.m_inGrid = true;
	this.m_xyModified = false;
	this.m_xySpaced = false;
	this.m_light = new Array();

	this.m_grid = new Array();
	this.m_lights = new Array();

	for (var x = 0; x < width; ++x) {
		this.m_grid[x] = new Array();
		for (var y = 0; y < height; ++y) {
			// Cell information
			this.m_grid[x][y] = new CrosswordCell(this.m_name, x, y);
		}
	}
	for (var x = 0; x < width; ++x) {
		this.m_lights[x] = new Array();
		for (var y = 0; y < height; ++y) {
			this.m_lights[x][y] = [null, null];
			for (var dir = 0; dir < 2; ++dir) {
				// Light information
				var clue = document.getElementById(this.m_name+"-"+dir+"-clue-"+x+"-"+y);
				if (clue != null) {
					var cells = [];
					var els = [];
					var eds = [];
					var cx = x;
					var cy = y;
					while (cx < width && cy < height) {
						cell = this.m_grid[cx][cy];
						if (cell.isBlank()) {
							break;
						}
						cells[cells.length] = cell;
						els[els.length] = document.getElementById(this.m_name+"-"+dir+"-"+cx+"-"+cy);
						eds[eds.length] = document.getElementById(this.m_name+"-"+dir+"-edit-"+cx+"-"+cy);
						if (dir == 1) {
							++cy;
						}
						else {
							++cx;
						}
					}
					this.m_lights[x][y][dir] = new CrosswordLight(this.m_name, x, y, dir, cells, els, eds);
				}
			}
		}
	}

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

	this.post = function(action)
	{
		var post = {};

		for (var y = 0; y < this.m_height; ++y) {
			for (var x = 0; x < this.m_width; ++x) {
				var cell = this.m_grid[x][y];
				if (!cell.isBlank()) {
					if (cell.isKnown) {
						post[this.m_name+"[gr]["+x+"]["+y+"]"] = cell.letter();
					}
					for (var o = 0; o < 2; ++o) {
						if (cell.isSpaced(o)) {
							post[this.m_name+"[sp]["+x+"]["+y+"]["+o+"]"] = 1;
						}
						var light = this.m_lights[x][y][o];
						if (null != light) {
							post[this.m_name+"[li]["+x+"]["+y+"]["+o+"][len]"] = light.length();
							var clues = light.clues();
							for (var c = 0; c < clues.length; ++c) {
								if (null != clues[c] && clues[c] != "") {
									post[this.m_name+"[li]["+x+"]["+y+"]["+o+"][clues]["+c+"]"] = clues[c];
								}
							}
						}
					}
				}
			}
		}

		var ajax = new AJAXInteraction(action, post, this.postCallback);
		ajax.doGet();
	}

	// Handle a response from the web server
	this.postCallback = function(responseXML)
	{
		if (responseXML) {
			alert("callback");
		}
	}

	this.cell = function(x,y)
	{
		if (x >= 0 && x < this.m_width &&
			y >= 0 && y < this.m_height) {
			return this.m_grid[x][y];
		}
		else {
			return null;
		}
	}

	this.editBox = function(x,y)
	{
		var cell = this.cell(x,y);
		if (null != cell) {
			return cell.editBox();
		}
		else {
			return null;
		}
	}

	this.modifyValue = function(x, y, v)
	{
		var edit = this.editBox(x, y);
		var statusChanged = ((v == "") != (edit.value == ""));
		var cell = this.m_grid[x][y];
		cell.setLetter(v);
		if (statusChanged) {
			if (null != this.m_light) {
				this.m_light.select(false);
			}
			this.m_light = this.m_grid[this.m_x][this.m_y].light(this.m_orientation.val());
			if (null != this.m_light) {
				this.m_light.select(true);
			}
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
		if (null != this.m_light) {
			this.m_light.select(false);
		}
		
		// Switch orientation only if it's sensible
		this.m_orientation.toggle();
		if (null == this.editBox(this.m_x-this.m_orientation.dx(), this.m_y-this.m_orientation.dy()) &&
		    null == this.editBox(this.m_x+this.m_orientation.dx(), this.m_y+this.m_orientation.dy()))
		{
			this.m_orientation.toggle();
		}

		// Select new light
		this.m_light = this.m_grid[this.m_x][this.m_y].light(this.m_orientation.val());
		if (null != this.m_light) {
			this.m_light.select(true);
		}
	}

	this.changeCell = function(x, y, o, f)
	{
		if (x != this.m_x || y != this.m_y) {
			this.m_xyModified = false;
			this.m_xySpaced = false;
		}
		// Deselect previous light
		if (null != this.m_light) {
			this.m_light.select(false);
		}

		var cell = this.cell(this.m_x, this.m_y);
		if (null != cell) {
			cell.focus(false);
		}

		// Change cell
		this.m_x = x;
		this.m_y = y;
		if (this.editBox(x,y) != null) {
			// Switch orientation if it isn't sensible or if requested
			if ((o != null && o != this.m_orientation.val()) ||
				(null == this.editBox(x-this.m_orientation.dx(), y-this.m_orientation.dy()) &&
				 null == this.editBox(x+this.m_orientation.dx(), y+this.m_orientation.dy())))
			{
				this.m_orientation.toggle();
			}

			if (f) {
				this.m_grid[x][y].focus(true, this.m_inGrid ? 0 : 1+this.m_orientation.val());
			}

			// Select new light
			this.m_light = this.m_grid[x][y].light(this.m_orientation.val());
			if (null != this.m_light) {
				this.m_light.select(true);
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
			this.changeCell(x, y, null, true);
		}
	}

	this.click = function(x, y, e)
	{
		this.m_inGrid = true;
		if (this.m_x == x && this.m_y == y) {
			this.toggleOrientation();
		}
		else {
			this.changeCell(x, y, null, true);
		}
		this.m_xyModified = true;
	}

	this.clueClick = function(x, y, o, e)
	{
		this.m_inGrid = false;
		this.changeCell(x, y, o, true);
		this.m_xyModified = true;
	}

	this.deleteValue = function(x, y)
	{
		// Only move backwards if it was already blank
		if (!this.cell(x,y).isKnown()) {
			this.changeCellRelative(x, y, -this.orientation().dx(), -this.orientation().dy(), false);
			x = this.x();
			y = this.y();
		}
		this.modifyValue(x, y, "");
	}

	this.keyDown = function(x, y, e)
	{
		var keyCode = e.keyCode;
		// Control keys
		if (keyCode != 0) {
			if (keyCode == 8 /* backspace */) {
				// Only move backwards if it was already blank
				if (!this.m_xyModified || !this.cell(x,y).isKnown()) {
					this.changeCellRelative(x, y, -this.orientation().dx(), -this.orientation().dy(), false);
					x = this.x();
					y = this.y();
				}
				this.modifyValue(x, y, "");
				return false;
			}
			else if (keyCode == 46  /* delete (others)*/ ||
			         keyCode == 127 /* delete (konqueror) */) {
				this.deleteValue(x,y);
				return false;
			}
			// arrow keys
			else if (keyCode >= 37 && keyCode <= 40) {
				var dx = 0;
				var dy = 0;
				if (keyCode == 37 /* left arrow */) {
					dx = (this.m_inGrid ? -1 : -this.m_orientation.dx());
					dy = (this.m_inGrid ?  0 : -this.m_orientation.dy());
				}
				else if (keyCode == 38 /* up arrow */) {
					dy = (this.m_inGrid ? -1 : 0);
				}
				else if (keyCode == 39 /* right arrow */) {
					dx = (this.m_inGrid ? 1 : this.m_orientation.dx());
					dy = (this.m_inGrid ? 0 : this.m_orientation.dy());
				}
				else if (keyCode == 40 /* down arrow */) {
					dy = (this.m_inGrid ? 1 : 0);
				}
				this.changeCellRelative(x, y, dx, dy, this.m_inGrid);
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
	return crossword(name).changeCell(-1, -1, null, false);
}

function crosswordClick(name, x, y, e)
{
	return crossword(name).click(x, y, e);
}

function crosswordClueClick(name, x, y, o, e)
{
	return crossword(name).clueClick(x, y, o, e);
}

function crosswordSelectLight(name, x, y, o, f)
{
	var xw = crossword(name);
	xw.changeCell(x, y, o, f);
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

function crosswordToggleInlineAnswers()
{
	var items = document.getElementsByName("crosswordClues");
	var hideValues = CssCheck(items[0], "hideValues");
	for (var i = 0; i < items.length; ++i) {
		CssToggle(items[i], "hideValues");
	}
	var link = document.getElementById("toggleInlineAnswers");
	link.textContent = (hideValues ? "Hide inline answers" : "Show inline answers");
}

function crosswordToggleCrypticClues()
{
	var items = document.getElementsByName("crosswordClues");
	var hideCryptic = CssCheck(items[0], "hideCryptic");
	for (var i = 0; i < items.length; ++i) {
		CssToggle(items[i], "hideCryptic");
		CssToggle(items[i], "hideQuick");
	}
	var link = document.getElementById("toggleCrypticClues");
	link.textContent = (hideCryptic ? "Show quick clues" : "Show cryptic clues");
}

onLoadFunctions.push(function() {
	new Crossword("xw", 13, 13);
});

