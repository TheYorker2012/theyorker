/*
 * Author: James Hogan
 * Copyright (c) 2008 The Yorker
 * Depends on javascript/css_classes.js
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
	this.m_spacers = ((null != mainEl)
			? [	CssCheck(mainEl, "hsp")?" ":
				CssCheck(mainEl, "hhy")?"-":
				null,
				CssCheck(mainEl, "vsp")?" ":
				CssCheck(mainEl, "vhy")?"-":
				null ]
			: [null,null]);
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
			if (this.m_letter != letter) {
				this.m_letter = letter;
				for (var i = 0; i < 2; ++i) {
					if (this.m_lights[i] != null) {
						if (this.m_lights[i].m_checking) {
							this.m_lights[i].showCheckResult(null);
						}
					}
				}
			}
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
		this.m_solution = letter;
	}
	this.solution = function()
	{
		return this.m_solution;
	}
	this.clear = function()
	{
		this.setLetter("");
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

	this.updateCheckClass = function()
	{
		var bin_classes	= ["inc", "chk", "cor"];
		var bins		= [false,       false,      false    ];
		for (var o = 0; o < 2; ++o) {
			var light = this.m_lights[o];
			if (null != light) {
				if (light.m_checking) {
					if (null == light.m_checkResult) {
						bins[1] = true;
					}
					else if (light.m_checkResult) {
						bins[2] = true;
					}
					else {
						bins[0] = true;
					}
				}
			}
		}
		for (var i = 0; i < bins.length; ++i) {
			for (var j = 0; j < this.m_els.length; ++j) {
				if (null != this.m_els[j]) {
					(bins[i]?CssAdd:CssRemove)(this.m_els[j], bin_classes[i]);
				}
			}
		}
	}

	this.space = function(orientation, enable)
	{
		if (this.m_spacers[orientation.val()] != enable) {
			// Change spacer class
			var opSp = ((enable==" ") ? CssAdd : CssRemove);
			var opHy = ((enable=="-") ? CssAdd : CssRemove);
			var clSp = (orientation.isHorizontal() ? "hsp" : "vsp");
			var clHy = (orientation.isHorizontal() ? "hhy" : "vhy");
			for (var i = 0; i < this.m_els.length; ++i) {
				if (null != this.m_els[i]) {
					opSp(this.m_els[i], clSp);
					opHy(this.m_els[i], clHy);
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
	this.m_clueHead = document.getElementById(name+"-"+orientation+"-head-"+x+"-"+y);
	this.m_clueInEls = [document.getElementById(name+"-"+orientation+"-clueinput0-"+x+"-"+y),
						document.getElementById(name+"-"+orientation+"-clueinput1-"+x+"-"+y)];
	this.m_clueEls = [	document.getElementById(name+"-"+orientation+"-cluetext0-"+x+"-"+y),
						document.getElementById(name+"-"+orientation+"-cluetext1-"+x+"-"+y)];
	this.m_clues = [null, null];
	this.m_wordlenEl = document.getElementById(name+"-"+orientation+"-wordlen-"+x+"-"+y);
	this.m_cluetextEl = null;
	this.m_inlineEl = document.getElementById(name+"-"+orientation+"-inline-"+x+"-"+y);
	this.m_complete = null;
	this.m_checking = false;
	this.m_checkResult = null;
	this.m_checkTimer = null;

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
			this.m_number = parseInt(innerText(this.m_numberEls[0]), 10);
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
			var spacer = cell.isSpaced(this.m_orientation);
			if (spacer != null) {
				val += spacer;
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
			var spacer = cell.isSpaced(this.m_orientation);
			if (spacer != null) {
				val += spacer;
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
			crossword(name).updateCompleteness();
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
		var overall = true;
		for (var i = 0; i < this.m_cells.length; ++i) {
			var result = this.m_cells[i].check();
			if (!result) {
				return result;
			}
			else if (null == result) {
				overall = null;
			}
		}
		return overall;
	}

	/// Indicate that the light is being checked
	this.startChecking = function()
	{
		if (this.m_checking && null == this.m_checkResult) {
			return;
		}
		if (this.m_checkTimer != null) {
			clearTimeout(this.m_checkTimer);
			this.m_checkTimer = null;
		}
		this.m_checkResult = null;
		this.m_checking = true;
		for (var i = 0; i < this.m_cells.length; ++i) {
			this.m_cells[i].updateCheckClass();
		}
	}
	/// Indicate that checking was cancelled
	this.cancelChecking = function()
	{
		if (!this.m_checking) {
			return;
		}
		if (this.m_checkTimer != null) {
			clearTimeout(this.m_checkTimer);
			this.m_checkTimer = null;
		}
		this.m_checking = false;
		for (var i = 0; i < this.m_cells.length; ++i) {
			this.m_cells[i].updateCheckClass();
		}
	}
	/// Provide the result of checking
	this.showCheckResult = function(timeout)
	{
		var result = false;
		if (this.m_checkTimer != null) {
			clearTimeout(this.m_checkTimer);
			this.m_checkTimer = null;
		}
		this.m_checkResult = this.check();
		for (var i = 0; i < this.m_cells.length; ++i) {
			this.m_cells[i].updateCheckClass();
		}
		if (null != timeout) {
			this.m_checkTimer = setTimeout(
				"crossword('"+name+"').lightCheckTimeout("+this.m_x+","+this.m_y+","+this.m_orientation+");",
				timeout
			);
		}
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
		var count = new Array();
		count.push(1);
		for (var i = 1; i < this.m_cells.length; ++i) {
			var space = this.m_cells[i].isSpaced(this.m_orientation);
			if (space == " ") {
				lens.push(count);
				count = new Array();
				count.push(1);
			}
			else if (space == "-") {
				count.push(1);
			}
			else {
				++count[count.length-1];
			}
		}
		lens.push(count);
		return lens;
	}
	this.updateWordLengths = function()
	{
		var lens = this.wordLengths();
		var lenStrs = new Array();
		for (var i = 0; i < lens.length; ++i) {
			lenStrs.push(lens[i].join("-"));
		}
		var lensStr = lenStrs.join(",");
		setInnerText(this.m_wordlenEl, lensStr);
	}

	/// Set the number, return whether changed
	this.setNumber = function(num)
	{
		if (this.m_number != num)
		{
			for (var i = 0; i < this.m_numberEls.length; ++i) {
				if (null != this.m_numberEls[i]) {
					setInnerText(this.m_numberEls[i], num);
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
			clue = innerText(this.m_clueEls[id]);
		}
		if (clue != "") {
			this.m_clues[id] = clue;
		}
		else {
			this.m_clues[id] = null;
		}
		//if (fromTextBox) {
		//	this.updateClue(id);
		//}
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
				setInnerText(this.m_clueEls[id], "");
			}
			else
			{
				setInnerText(this.m_clueEls[id], this.m_clues[id]);
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
	this.m_xySpaced = null;
	this.m_light = null;

	this.m_grid = [];
	this.m_gridRows = [];
	this.m_lights = [];

	this.m_changed = false;
	this.m_notify = document.getElementById(this.m_name+"-notify");
	this.m_notifyTimer = null;

	this.m_winnersAction = null;
	this.m_winnersInterval = null;
	this.m_winnersTimer = null;
	this.m_winnersNextPosition = null;
	this.m_autosaveAction = null;
	this.m_autosaveInterval = null;
	this.m_autosaveTimer = null;
	this.m_solutionsAction = null;
	this.m_solutionsAvailable = false;

	this.m_complete = null;
	// Dummy so lights can ping it as they get filled
	this.updateCompleteness = function() {}

	for (var x = 0; x < width; ++x) {
		this.m_grid[x] = [];
		for (var y = 0; y < height; ++y) {
			// Cell information
			this.m_grid[x][y] = new CrosswordCell(this.m_name, x, y);
		}
	}
	for (var y = 0; y < height; ++y) {
		this.m_gridRows[y] = document.getElementById(this.m_name+"-row-"+y);
	}
	for (var x = 0; x < width; ++x) {
		this.m_lights[x] = [];
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


	this.getCompleteness = function()
	{
		for (var y = 0; y < this.m_height; ++y) {
			for (var x = 0; x < this.m_width; ++x) {
				var cell = this.m_grid[x][y];
				if (!cell.isBlank()) {
					for (var o = 0; o < 2; ++o) {
						var light = this.m_lights[x][y][o];
						if (null != light) {
							if (!light.m_complete) {
								return false;
							}
						}
					}
				}
			}
		}
		return true;
	}

	/// Find whether the crossword is completely filled in
	this.updateCompleteness = function()
	{
		// Need a url to be able to submit marks
		if (this.m_autosaveAction != null) {
			var complete = this.getCompleteness();
			if (this.m_complete != complete) {
				this.m_complete = complete;
				var completeBox = document.getElementById(this.m_name+'-complete');
				if (null != completeBox) {
					completeBox.className="crosswordAjaxNotify "+(complete?"complete":"hidden");
				}
			}
		}
	}

	this.winnersTimeout = function()
	{
		if (this.m_winnersInterval != null) {
			var self = this;
			var ajax = new AJAXInteraction(this.m_winnersAction, {},
				function (responseXML) {
					var root = responseXML.documentElement;
					var expired = (innerText(root.getElementsByTagName("expired")[0]) == "yes");
					var positions = parseInt(innerText(root.getElementsByTagName("positions")[0]), 10);
					if (positions > 256) {
						positions = 256;
					}
					var winner_els = root.getElementsByTagName("winner");
					var winners = {};
					var next_position = 0;
					for (var i = 0; i < winner_els.length; ++i) {
						var winner_el = winner_els[i];
						var position = parseInt(winner_el.getAttribute("position"));
						winners[position] = innerText(winner_el);
						if (position >= next_position) {
							next_position = position + 1;
						}
					}
					// Clear winners
					var winners_ol = document.getElementById(self.m_name+"-winners");
					while (winners_ol.lastChild != null) {
						winners_ol.removeChild(winners_ol.lastChild);
					}
					var need_another_reload = false;
					for (var i = 0; i < positions; ++i) {
						if (expired && undefined == winners[i]) {
							break;
						}
						var li = document.createElement("li");
						li.className="winner"+i;
						if (undefined != winners[i]) {
							if (null != self.m_nextPosition && i >= self.m_nextPosition) {
								li.className = li.className+" new_winner";
								// need another reload to clear the new_winner class
								need_another_reload = true;
							}
							setInnerText(li, winners[i]);
						}
						else {
							li.appendChild(document.createTextNode('\u00A0')); // &nbsp;
						}
						winners_ol.appendChild(li);
					}
					self.m_nextPosition = next_position;
					if (!expired || need_another_reload) {
						self.resetWinnersTimer();
					}
					if (expired && null != self.m_solutionsAction) {
						self.m_solutionsAvailable = null;
						var checks_div = document.getElementById(self.m_name+"-checks");
						if (null != checks_div) {
							checks_div.style.display="";
						}
					}
				},
				function (status, text) {
					self.updateNotification("error", "reload of winners list failed: "+text, 2000);
					self.resetWinnersTimer();
				});
			ajax.doGet();
		}
	}
	this.resetWinnersTimer = function()
	{
		if (this.m_winnersInterval != null) {
			this.m_winnersTimer = setTimeout("crossword('"+this.m_name+"').winnersTimeout()", this.m_winnersInterval);
		}
	}
	this.setWinnersUpdateInterval = function(action, interval)
	{
		if (null != this.m_winnersTimer) {
			this.m_winnersTimer = null;
			this.m_winnersAction = null;
			this.m_winnersInterval = null;
			clearTimeout(this.m_winnersTimer);
		}
		if (interval != null) {
			interval = interval*1000;
			this.m_winnersAction = action;
			this.m_winnersInterval = interval;
			this.resetWinnersTimer();
		}
	}


	this.autosaveTimeout = function()
	{
		if (this.m_autosaveInterval != null) {
			if (this.m_changed) {
				this.autosave();
			}
			else {
				this.resetAutosaveTimer();
			}
		}
	}
	this.resetAutosaveTimer = function()
	{
		if (this.m_autosaveInterval != null) {
			this.m_autosaveTimer = setTimeout("crossword('"+this.m_name+"').autosaveTimeout()", this.m_autosaveInterval);
		}
	}
	this.setAutosaveInterval = function(action, interval)
	{
		if (null != this.m_autosaveTimer) {
			this.m_autosaveTimer = null;
			this.m_autosaveAction = null;
			this.m_autosaveInterval = null;
			clearTimeout(this.m_autosaveTimer);
		}
		if (interval != null) {
			interval = interval*1000;
			this.m_autosaveAction = action;
			this.m_autosaveInterval = interval;
			this.resetAutosaveTimer();
			// Show complete box if autosaving allowed (logged in)
			this.updateCompleteness();
		}
	}

	this.clearNotification = function()
	{
		this.updateNotification("","",null);
	}
	this.updateNotification = function(cls, message, timeout)
	{
		if (null !== this.m_notify) {
			this.m_notify.className="crosswordAjaxNotify "+cls;
			setInnerText(this.m_notify, message);
			if (this.m_notifyTimer != null) {
				clearTimout(this.m_notifyTimer);
				this.m_notifyTimer = null;
			}
			if (null !== timeout) {
				this.m_notifyTime = setTimeout("crossword('"+this.m_name+"').clearNotification();", timeout);
			}
		}
	}

	this.autosave = function()
	{
		var wasChanged = this.m_changed;
		this.m_changed = false;
		this.updateNotification("pending", "autosaving", null);
		var self = this;
		this.post(this.m_autosaveAction, "autosave",
				function() {
					self.updateNotification("success", "autosaved", 1000);
					self.resetAutosaveTimer();
				},
				function() {
					if (wasChanged) {
						self.m_changed = true;
					}
					self.resetAutosaveTimer();
				});
	}
	this.save = function(action)
	{
		if (null != this.m_autosaveTimer) {
			clearTimeout(this.m_autosaveTimer);
		}
		var wasChanged = this.m_changed;
		this.m_changed = false;
		this.updateNotification('pending', 'saving', null);
		var self = this;
		this.post(action, "save",
				function() {
					self.updateNotification("success", "saved", 10000);
					self.resetAutosaveTimer();
				},
				function() {
					if (wasChanged) {
						self.m_changed = true;
					}
					self.resetAutosaveTimer();
				});
	}
	this.submit = function()
	{
		if (null != this.m_autosaveTimer) {
			clearTimeout(this.m_autosaveTimer);
		}
		var completeBox = document.getElementById(this.m_name+'-complete');
		completeBox.className="crosswordAjaxNotify hidden";
		this.updateNotification('pending', 'submitting for marking', null);
		var self = this;
		this.post(this.m_autosaveAction, "submit",
				function(root) {
					var marks = root.getElementsByTagName('mark');
					if (marks.length < 1) {
						self.updateNotification("error", "submit failed: no mark returned", 10000);
					}
					else {
						var mark = (innerText(marks[0]) == 'correct');
						if (!mark) {
							self.updateNotification("error", "crossword is incorrect - please try again", 10000);
						}
						else {
							var winners = root.getElementsByTagName('winner');
							var winner = false;
							if (winners.length > 0) {
								winner = (innerText(winners[0]) == 'yes');
							}
							if (winner) {
								if (null != self.m_winnersTimer) {
									clearTimeout(self.m_winnersTimer);
									self.winnersTimeout();
								}
								self.updateNotification("success", "congratulations: you are a winner", null);
							}
							else {
								self.updateNotification("success", "well done: crossword is correct", null);
							}
						}
					}
					self.m_complete = null;
					self.updateCompleteness();
					self.resetAutosaveTimer();
				},
				function() {
					self.m_complete = null;
					self.updateCompleteness();
					self.resetAutosaveTimer();
				});
	}

	this.exportPost = function(post)
	{
		post[this.m_name+"[width]"]=this.m_width;
		post[this.m_name+"[height]"]=this.m_height;

		for (var y = 0; y < this.m_height; ++y) {
			for (var x = 0; x < this.m_width; ++x) {
				var cell = this.m_grid[x][y];
				if (!cell.isBlank()) {
					if (cell.isKnown()) {
						post[this.m_name+"[gr]["+x+"]["+y+"]"] = cell.letter();
					}
				}
			}
		}
	}

	this.post = function(action, opname, success_event, fail_event)
	{
		var post = {};
		post[this.m_name+"["+opname+"]"]=1;
		this.exportPost(post);

		var self = this;

		// Handle a response from the web server
		var postCallback = function(responseXML)
		{
			if (responseXML) {
				var root = responseXML.documentElement;
				// Get errors node
				var mainEls = root.childNodes;
				var anyErrors = false;
				for (var i = 0; i < mainEls.length; ++i) {
					var el = mainEls[i];
					if (el.tagName == "errors") {
						var errors = el.getElementsByTagName("error");
						if (errors.length > 0) {
							anyErrors = true;
						}
					}
				}
				if (!anyErrors) {
					if (null != success_event) {
						success_event(root);
					}
				}
				else {
					self.updateNotification("error", opname+" failed", null);
					if (null != fail_event) {
						fail_event(root);
					}
				}
			}
			else {
				self.updateNotification("error", opname+" failed: you aren't logged in", null);
				if (null != fail_event) {
					fail_event();
				}
			}
		}

		// Handle a response from the web server
		var postCallbackFail = function(status, text)
		{
			self.updateNotification("error", opname+" failed: "+text, null);
			if (null != fail_event) {
				fail_event();
			}
		}

		var ajax = new AJAXInteraction(action, post, postCallback, postCallbackFail);
		ajax.doPost();
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
			this.m_changed = true;
			if (null != this.m_light) {
				this.m_light.select(false);
			}
			if (this.m_x != -1 && this.m_y != -1) {
				this.m_light = this.m_grid[this.m_x][this.m_y].light(this.m_orientation.val());
				if (null != this.m_light) {
					this.m_light.select(true);
				}
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
		this.m_xySpaced = null;

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
			this.m_xySpaced = null;
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

	this.click = function(x, y)
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

	this.clueClick = function(x, y, o)
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
				this.m_xySpaced = " ";
			}
			else if (charStr == "-") {
				this.modifyValue(x, y, "");
				this.m_xySpaced = "-";
			}
			else if (charStr == "?") {
				if (this.m_light != null) {
					if (this.m_light.m_checking) {
						this.m_light.cancelChecking();
					}
					else {
						this.check("cur_light", false);
					}
				}
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

	this.setSolutionsAction = function(action, available)
	{
		this.m_solutionsAction = action;
		this.m_solutionsAvailable = available;
		if (available == null || available) {
			var checks_div = document.getElementById(this.m_name+"-checks");
			if (null != checks_div) {
				checks_div.style.display = "";
			}
		}
	}
	this.prefetchSolution = function(callback)
	{
		if (null == this.m_solutionsAvailable) {
			// Lets find out if they're available
			var self = this;
			this.updateNotification("pending", "loading solution", null);
			var ajax = new AJAXInteraction(this.m_solutionsAction, {},
				function(responseXML)
				{
					var root = responseXML.documentElement;
					var solution = root.getElementsByTagName("solution")[0];
					var available = (solution.getAttribute("available") == "yes");
					if (available) {
						var letters = solution.getElementsByTagName("letter");
						for (var i = 0; i < letters.length; ++i) {
							var letter = letters[i];
							var x = parseInt(letter.getAttribute("x"), 10);
							var y = parseInt(letter.getAttribute("y"), 10);
							var val = innerText(letter);
							self.m_grid[x][y].setSolution(val);
						}
					}
					self.m_solutionsAvailable = available;
					callback(self.m_solutionsAvailable);
				},
				function(status, text)
				{
					// Possibly try again later
					callback(false);
				}
			);
			ajax.doGet();
		}
		else {
			callback(this.m_solutionsAvailable);
		}
	}
	this.stopCheck = function(clear)
	{
		for (var x = 0; x < this.m_width; ++x) {
			for (var y = 0; y < this.m_height; ++y) {
				if (clear) {
					this.m_grid[x][y].clear();
				}
				for (var o = 0; o < 2; ++o) {
					var light = this.m_lights[x][y][o];
					if (null != light) {
						light.cancelChecking();
					}
				}
			}
		}
		if (clear) {
			this.m_changed = true;
		}
	}
	// type:{'all_lights','cur_light'}
	this.check = function(type, reveal)
	{
		var self = this;
		// Find the lights that need checking
		var lights = new Array();
		if (type == "cur_light") {
			if (this.m_light != null) {
				lights.push(this.m_light);
			}
		}
		else if (type == "all_lights") {
			for (var x = 0; x < this.m_width; ++x) {
				for (var y = 0; y < this.m_height; ++y) {
					for (var o = 0; o < 2; ++o) {
						var light = this.m_lights[x][y][o];
						if (null != light) {
							lights.push(light);
						}
					}
				}
			}
		}
		else {
			this.updateNotification("error", "check "+type+" not implemented", 10000);
			return;
		}
		// Mark as checking
		for (var i = 0; i < lights.length; ++i) {
			lights[i].startChecking();
		}
		// Ensure the solution is available
		this.prefetchSolution(function(worked) {
			if (worked) {
				for (var i = 0; i < lights.length; ++i) {
					if (reveal) {
						lights[i].solve();
						self.m_changed = true;
					}
					lights[i].showCheckResult(null);
				}
				self.clearNotification();
			}
			else {
				for (var i = 0; i < lights.length; ++i) {
					lights[i].cancelChecking();
				}
				self.updateNotification("error", "crossword solutions not available", 5000);
			}
		});
	}

	this.clear = function()
	{
		this.stopCheck(true);
	}

	this.lightCheckTimeout = function(x, y, o)
	{
		this.m_lights[x][y][o].cancelChecking();
	}
	
	this.inlineAnswersUpdated = function()
	{
		var checkbox = document.getElementById(this.m_name+"-clues-inline");
		if (null != checkbox) {
			var show_inline = checkbox.checked;
			var items = [
				document.getElementById(this.m_name+"-0-clues"),
				document.getElementById(this.m_name+"-1-clues")
			];
			for (var i = 0; i < items.length; ++i) {
				(show_inline?CssRemove:CssAdd)(items[i], "hideValues");
			}
		}
		return false;
	}

	this.clueTypeUpdated = function()
	{
		var cryptic_radio = document.getElementById(this.m_name+"-clues-show-cryptic");
		if (null != cryptic_radio) {
			var cryptic = cryptic_radio.checked;
			var items = [
				document.getElementById(this.m_name+"-0-clues"),
				document.getElementById(this.m_name+"-1-clues")
			];
			for (var i = 0; i < items.length; ++i) {
				(cryptic ? CssRemove : CssAdd)(items[i], "hideCryptic");
				(cryptic ? CssAdd : CssRemove)(items[i], "hideQuick");
			}
		}
		return false;
	}

	this.inlineAnswersUpdated();
	this.clueTypeUpdated();
}

// crosswordDeselect
function xwd(name)
{
	return crossword(name).changeCell(-1, -1, null, false);
}

// crosswordClick
function xwc(name, x, y)
{
	return crossword(name).click(x, y);
}

// crosswordClueClick
function xwcc(name, x, y, o)
{
	return crossword(name).clueClick(x, y, o);
}

function crosswordSelectLight(name, x, y, o, f)
{
	var xw = crossword(name);
	xw.changeCell(x, y, o, f);
	if (xw.orientation().isVertical() != (o == 1)) {
		xw.toggleOrientation();
	}
}

// crosswordKeyDown
function xwkd(name, x, y, e)
{
	return crossword(name).keyDown(x, y, e);
}

// crosswordKeyPress
function xwkp(name, x, y, e)
{
	return crossword(name).keyPress(x, y, e);
}

function crosswordStopCheck(name)
{
	return crossword(name).stopCheck(false);
}

function crosswordCheck(name, type, reveal)
{
	return crossword(name).check(type, reveal);
}

function crosswordClear(name)
{
	return crossword(name).clear();
}

function crosswordInlineAnswersUpdated(name)
{
	crossword(name).inlineAnswersUpdated();
}

function crosswordClueTypeUpdated(name)
{
	crossword(name).clueTypeUpdated();
}
