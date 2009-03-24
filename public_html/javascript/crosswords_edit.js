/*
 * Author: James Hogan
 * Copyright (c) 2008 The Yorker
 */

/*
 * Enhances basic playable crosswords to make them editable
 * features
 */

function CrosswordEdit(name, width, height)
{
	thisCrossword = new Crossword(name, width, height);

	thisCrossword.m_needRenumbering = false;

	thisCrossword.deleteValue = function(x,y)
	{
		var cell = this.m_grid[x][y];
		this.modifyValue(x,y,((!cell.isBlank && cell.isKnown()) ? "" : null));
	}

	thisCrossword.Crossword_toggleOrientation = thisCrossword.toggleOrientation;
	thisCrossword.toggleOrientation = function()
	{
		var next = this.cell(this.m_x+this.m_orientation.dx(), this.m_y+this.m_orientation.dy());
		if (null != next) {
			next.select(false);
		}

		this.Crossword_toggleOrientation();

		next = this.cell(this.m_x+this.m_orientation.dx(), this.m_y+this.m_orientation.dy());
		if (null != next) {
			next.select(true);
		}
	}

	thisCrossword.Crossword_changeCell = thisCrossword.changeCell;
	thisCrossword.changeCell = function(x, y, o, f)
	{
		var next = this.cell(this.m_x+this.m_orientation.dx(), this.m_y+this.m_orientation.dy());
		if (null != next) {
			next.select(false);
		}

		this.Crossword_changeCell(x, y, o, f);

		next = this.cell(x+this.m_orientation.dx(), y+this.m_orientation.dy());
		if (null != next) {
			next.select(true);
		}
	}

	thisCrossword.Crossword_modifyValue = thisCrossword.modifyValue;
	thisCrossword.modifyValue = function(x, y, v)
	{
		var cell = this.cell(x, y);
		var oldVal = cell.letter();
		this.Crossword_modifyValue(x, y, v);
		var precell = this.cell(x-this.m_orientation.dx(), y-this.m_orientation.dy());
		if (null != precell) {
			cell.space(this.m_orientation, this.m_xySpaced && !precell.isBlank() && precell.isKnown());
		}
		// Deleting a cell?
		if (null != oldVal && null == v) {
			for (var o = 0; o < 2; ++o) {
				var light = cell.light(o);
				if (null != light) {
					// if at beginning or end, reduce size to minimum of 2
					var dx = 1-o;
					var dy = o;
					var len = light.length();
					var len1 = ((o == 0) ? x - light.m_x : y - light.m_y);
					var len2 = len-len1-1;
					if (len1 < 2 && len2 < 2) {
						this.removeLight(light);
					}
					else if (len1 < 2) {
						this.moveLight(light, light.m_x+(1+len1)*dx, light.m_y+(1+len1)*dy, len2);
					}
					else if (len2 < 2) {
						this.moveLight(light, light.m_x, light.m_y, len1);
					}
					// if in middle, split, trying to split clues if possible
					else {
						this.moveLight(light, light.m_x, light.m_y, len1);
						this.cloneLight(light, light.m_x+(1+len1)*dx, light.m_y+(1+len1)*dy, len2);
					}
				}
			}
		}
		// Creating a cell?
		if (null == oldVal && null != v) {
			for (var o = 0; o < 2; ++o) {
				var dx = 1-o;
				var dy = o;
				var prevCell = this.cell(x-dx,y-dy);
				var nextCell = this.cell(x+dx,y+dy);
				var prev = ((null != prevCell) ? prevCell.light(o) : null);
				var next = ((null != nextCell) ? nextCell.light(o) : null);
				var len1 = 0;
				var len2 = 0;
				if (null != prev) {
					len1 = prev.length();
				}
				else if (null != prevCell && !prevCell.isBlank()) {
					len1 = 1;
				}
				if (null != next) {
					len2 = next.length();
				}
				else if (null != nextCell && !nextCell.isBlank()) {
					len2 = 1;
				}
				var len = len1+len2+1;
				var sx = x - dx*len1;
				var sy = y - dy*len1;
				// if between, merge, trying to merge clues if possible
				if (null != prev && null != next) {
					this.mergeLights(prev, next, sx, sy, len);
				}
				// if before or after, increase size
				else if (null != prev) {
					this.moveLight(prev, sx, sy, len);
				}
				else if (null != next) {
					this.moveLight(next, sx, sy, len);
				}
				// if alone, check if can create a new light
				else if (len > 1) {
					this.spawnLight(sx, sy, o, len);
				}
			}
		}
		// Lights may have moved
		this.renumber();
	}

	thisCrossword.removeLight = function(light)
	{
		light.select(false);
		light.clean();
		// Remove clue object
		var box = document.getElementById(this.m_name+"-"+light.m_orientation+"-clues");
		box.removeChild(light.m_clueDiv);
		light.m_clueDiv = null;
		// Clear global references
		this.m_lights[light.m_x][light.m_y][light.m_orientation] = null;
		if (null == this.m_lights[light.m_x][light.m_y][1-light.m_orientation]) {
			this.m_grid[light.m_x][light.m_y].m_sup.textContent = "";
		}
		if (this.m_light == light) {
			this.m_light = null;
		}
		this.m_needRenumbering = true;
	}
	
	thisCrossword.addEventsToPreview = function(name, cx,cy, o, td,tdEd)
	{
		td.onclick = function(event)
		{
			return crosswordClueClick(name, cx, cy, o, event);
		}
		tdEd.onkeypress = function(event)
		{
			return crosswordKeyPress(name, cx, cy, event);
		}
		tdEd.onkeydown = function(event)
		{
			return crosswordKeyDown(name, cx, cy, event);
		}
	}
	thisCrossword.moveLight = function(light, x, y, len)
	{
		// Letters need changing
		var inlineTr = light.m_inlineEl;
		while (inlineTr.firstChild != null) {
			inlineTr.removeChild(inlineTr.firstChild);
		}
		var cells = [];
		var els = [];
		var eds = [];
		var xwName = this.m_name;
		for (var i = 0; i < len; ++i) {
			var cx = x+i*(1-light.m_orientation);
			var cy = y+i*light.m_orientation;
			var cell = this.m_grid[cx][cy];
			cells[cells.length] = cell;

			// Cell in inline preview
			var td = document.createElement("td");
			td.id = xwName+"-"+light.m_orientation+"-"+cx+"-"+cy;
			for (var o = 0; o < 2; ++o) {
				if (cell.isSpaced(o)) {
					CssAdd(td, (o == 0 ? "hsp" : "vsp"));
				}
			}
			var tdDiv = document.createElement("div");
			var tdEd = document.createElement("input");
			tdEd.id = xwName+"-"+light.m_orientation+"-edit-"+cx+"-"+cy;
			tdEd.type = "text";
			tdEd.value = cell.letter();
			tdEd.maxlength = 2;
			tdEd.cols=1;
			this.addEventsToPreview(xwName, cx,cy,light.m_orientation, td,tdEd);
			tdDiv.appendChild(tdEd);
			td.appendChild(tdDiv);
			inlineTr.appendChild(td);

			els[i] = td;
			eds[i] = tdEd;
		}

		// If the first cell has moved more stuff needs changing
		var baseMoved = (x != light.m_x || y != light.m_y);
		if (baseMoved) {
			this.m_needRenumbering = true;
			this.m_lights[light.m_x][light.m_y][light.m_orientation] = null;
			if (null == this.m_lights[light.m_x][light.m_y][1-light.m_orientation]) {
				this.m_grid[light.m_x][light.m_y].m_sup.textContent = "";
			}
			this.m_lights[x][y][light.m_orientation] = light;
			// Rename clue stuff
		}

		light.select(false);
		light.setCells(x, y, cells, els, eds);
	}

	thisCrossword.cloneLight = function(light, x, y, len)
	{
		var newLight = this.spawnLight(x, y, light.m_orientation, len);
		newLight.setClues(light.splitClues());
		return newLight;
	}

	thisCrossword.spawnLight = function(x, y, o, len)
	{
		var xwName = this.m_name;

		// Create dom structures
		var clueDiv = document.createElement("div");
		clueDiv.id = this.m_name+"-"+o+"-clue-"+x+"-"+y;
		CssAdd(clueDiv, "clueBox");
		{
			var clueHeader = document.createElement("div");
			CssAdd(clueHeader, "clueHeader");
			clueHeader.onclick = function(event) { crosswordSelectLight(xwName, x, y, o, true); }
			{
				var num = document.createElement("span");
				num.id = this.m_name+"-"+o+"-num-"+x+"-"+y;
				clueHeader.appendChild(num);

				clueHeader.appendChild(document.createTextNode(" "));

				var cluetext1 = document.createElement("span");
				cluetext1.id = this.m_name+"-"+o+"-cluetext0-"+x+"-"+y;
				CssAdd(cluetext1, "quickClue");
				clueHeader.appendChild(cluetext1);

				var cluetext2 = document.createElement("span");
				cluetext2.id = this.m_name+"-"+o+"-cluetext1-"+x+"-"+y;
				CssAdd(cluetext2, "crypticClue");
				clueHeader.appendChild(cluetext2);

				clueHeader.appendChild(document.createTextNode(" ("));

				var wordlen = document.createElement("span");
				wordlen.id = this.m_name+"-"+o+"-wordlen-"+x+"-"+y;
				clueHeader.appendChild(wordlen);

				clueHeader.appendChild(document.createTextNode(")"));
			}
			clueDiv.appendChild(clueHeader);

			var clueInputs = document.createElement("fieldset");
			CssAdd(clueInputs, "clueInputs");
			{
				var clueinput1 = document.createElement("input");
				clueinput1.id = this.m_name+"-"+o+"-clueinput0-"+x+"-"+y;
				clueinput1.type = "text";
				clueinput1.onfocus = function(event) { return crosswordSelectLight(xwName, x, y, o, false); }
				clueinput1.onchange = function(event) { return crosswordClueChanged(xwName, x, y, o, 0); }
				CssAdd(clueinput1, "quickClue");
				clueInputs.appendChild(clueinput1);

				var clueinput2 = document.createElement("input");
				clueinput2.id = this.m_name+"-"+o+"-clueinput1-"+x+"-"+y;
				clueinput2.type = "text";
				clueinput2.onfocus = function(event) { return crosswordSelectLight(xwName, x, y, o, false); }
				clueinput2.onchange = function(event) { return crosswordClueChanged(xwName, x, y, o, 1); }
				CssAdd(clueinput2, "crypticClue");
				clueInputs.appendChild(clueinput2);
			}
			clueDiv.appendChild(clueInputs);

			var previewTab = document.createElement("table");
			CssAdd(previewTab, "crossword");
			{
				var tr = document.createElement("tr");
				tr.id = this.m_name+"-"+o+"-inline-"+x+"-"+y;
				CssAdd(tr, "small");
				previewTab.appendChild(tr);
			}
			clueDiv.appendChild(previewTab);
		}
		var box = document.getElementById(this.m_name+"-"+o+"-clues");
		box.appendChild(clueDiv);

		// Create internal structures
		var cells = [];
		var els = [];
		var eds = [];
		var dx = 1-o;
		var dy = o;
		for (var i = 0; i < len; ++i) {
			var cx = x + i*dx;
			var cy = y + i*dy;
			cells[cells.length] = this.m_grid[cx][cy];
			els[els.length] = null;
			eds[eds.length] = null;
		}
		var light = new CrosswordLight(this.m_name, x, y, o, cells, els, eds);
		this.m_lights[x][y][o] = light
		this.moveLight(light, x, y, len);
		// This new light will need moving into place, this will trigger reordering
		light.m_number = 0;
		this.m_needRenumbering = true;
		return light;
	}

	thisCrossword.mergeLights = function(light1, light2, x, y, len)
	{
		light1.mergeClues(light2.clues());
		this.removeLight(light2);
		this.moveLight(light1, light1.m_x, light1.m_y, len);
	}

	thisCrossword.renumber = function()
	{
		if (this.m_needRenumbering) {
			var count = 1;
			var changed = false;
			for (var y = 0; y < this.m_height; ++y) {
				for (var x = 0; x < this.m_width; ++x) {
					var lights = this.m_lights[x][y];
					var inc = false;
					for (var o = 0; o < 2; ++o) {
						if (lights[o] != null) {
							if (lights[o].setNumber(count)) {
								changed = true;
							}
							inc = true;
						}
					}
					if (inc) {
						++count;
					}
				}
			}
			this.m_needRenumbering = false;
			// May also need reordering
			if (changed) {
				this.reorder();
			}
		}
	}

	thisCrossword.reorder = function()
	{
		for (var o = 0; o < 2; ++o) {
			var box = document.getElementById(this.m_name+"-"+o+"-clues");
			for (var y = 0; y < this.m_height; ++y) {
				for (var x = 0; x < this.m_width; ++x) {
					var light = this.m_lights[x][y][o];
					if (null != light) {
						box.removeChild(light.m_clueDiv);
						box.appendChild(light.m_clueDiv);
					}
				}
			}
		}
	}

	thisCrossword.clueChanged = function(x, y, o, c)
	{
		this.m_lights[x][y][o].extractClue(c);
		return true;
	}

	return thisCrossword;
}

function crosswordClueChanged(name, x, y, o, c)
{
	return crossword(name).clueChanged(x, y, o, c);
}

onLoadFunctions.push(function() {
	grid = CrosswordEdit("xw", 13, 13);
});
