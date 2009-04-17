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

	thisCrossword.Crossword_exportPost = thisCrossword.exportPost;
	thisCrossword.exportPost = function(post)
	{
		this.Crossword_exportPost(post);
		for (var y = 0; y < this.m_height; ++y) {
			for (var x = 0; x < this.m_width; ++x) {
				var cell = this.m_grid[x][y];
				if (!cell.isBlank()) {
					for (var o = 0; o < 2; ++o) {
						var space = cell.isSpaced(o);
						if (null != space) {
							post[this.m_name+"[sp]["+x+"]["+y+"]["+o+"]"] = space;
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
	}

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
			cell.space(this.m_orientation, (!precell.isBlank() && precell.isKnown()) ? this.m_xySpaced : null);
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
			setInnerText(this.m_grid[light.m_x][light.m_y].m_sup, "");
		}
		if (this.m_light == light) {
			this.m_light = null;
		}
		this.m_needRenumbering = true;
	}
	
	thisCrossword.addEventsToPreview = function(name, cx,cy, o, td,tdEd)
	{
		td.onclick = function()
		{
			xwcc(name, cx, cy, o);
		}
		tdEd.onkeypress = function(event)
		{
			return xwkp(name, cx, cy, event);
		}
		tdEd.onkeydown = function(event)
		{
			return xwkd(name, cx, cy, event);
		}
	}
	thisCrossword.moveLight = function(light, x, y, len)
	{
		var o = light.m_orientation;
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
			var cx = x+i*(1-o);
			var cy = y+i*o;
			var cell = this.m_grid[cx][cy];
			cells[cells.length] = cell;

			// Cell in inline preview
			var td = document.createElement("td");
			td.id = xwName+"-"+o+"-"+cx+"-"+cy;
			for (var or = 0; or < 2; ++or) {
				var space = cell.isSpaced(or);
				if (space == " ") {
					CssAdd(td, (or == 0 ? "hsp" : "vsp"));
				}
				else if (space == "-") {
					CssAdd(td, (or == 0 ? "hhy" : "vhy"));
				}
			}
			var tdDiv = document.createElement("div");
			var tdEd = document.createElement("input");
			tdEd.id = xwName+"-"+o+"-edit-"+cx+"-"+cy;
			tdEd.type = "text";
			tdEd.value = cell.letter();
			tdEd.cols=1;
			this.addEventsToPreview(xwName, cx,cy,o, td,tdEd);
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
			this.m_lights[light.m_x][light.m_y][o] = null;
			if (null == this.m_lights[light.m_x][light.m_y][1-o]) {
				setInnerText(this.m_grid[light.m_x][light.m_y].m_sup, "");
			}
			this.m_lights[x][y][o] = light;

			// Update events dependent on beginning of light
			light.m_clueHead.onclick = function(event) { crosswordSelectLight(xwName, x, y, o, true); }
			light.m_clueInEls[0].onfocus = function(event) { return crosswordSelectLight(xwName, x, y, o, false); }
			light.m_clueInEls[0].onchange = function(event) { return crosswordClueChanged(xwName, x, y, o, 0); }
			light.m_clueInEls[1].onfocus = function(event) { return crosswordSelectLight(xwName, x, y, o, false); }
			light.m_clueInEls[1].onchange = function(event) { return crosswordClueChanged(xwName, x, y, o, 1); }
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
		clueDiv.id = xwName+"-"+o+"-clue-"+x+"-"+y;
		CssAdd(clueDiv, "clueBox");
		{
			var clueHeader = document.createElement("div");
			clueHeader.id = xwName+"-"+o+"-head-"+x+"-"+y;
			CssAdd(clueHeader, "clueHeader");
			clueHeader.onclick = function(event) { crosswordSelectLight(xwName, x, y, o, true); }
			{
				var num = document.createElement("span");
				num.id = xwName+"-"+o+"-num-"+x+"-"+y;
				clueHeader.appendChild(num);

				clueHeader.appendChild(document.createTextNode(" "));

				var cluetext1 = document.createElement("span");
				cluetext1.id = xwName+"-"+o+"-cluetext0-"+x+"-"+y;
				CssAdd(cluetext1, "quickClue");
				clueHeader.appendChild(cluetext1);

				var cluetext2 = document.createElement("span");
				cluetext2.id = xwName+"-"+o+"-cluetext1-"+x+"-"+y;
				CssAdd(cluetext2, "crypticClue");
				clueHeader.appendChild(cluetext2);

				clueHeader.appendChild(document.createTextNode(" ("));

				var wordlen = document.createElement("span");
				wordlen.id = xwName+"-"+o+"-wordlen-"+x+"-"+y;
				clueHeader.appendChild(wordlen);

				clueHeader.appendChild(document.createTextNode(")"));
			}
			clueDiv.appendChild(clueHeader);

			var clueInputs = document.createElement("fieldset");
			CssAdd(clueInputs, "clueInputs");
			{
				var clueinput1 = document.createElement("input");
				clueinput1.id = xwName+"-"+o+"-clueinput0-"+x+"-"+y;
				clueinput1.type = "text";
				clueinput1.onfocus = function(event) { return crosswordSelectLight(xwName, x, y, o, false); }
				clueinput1.onchange = function(event) { return crosswordClueChanged(xwName, x, y, o, 0); }
				CssAdd(clueinput1, "quickClue");
				clueInputs.appendChild(clueinput1);

				var clueinput2 = document.createElement("input");
				clueinput2.id = xwName+"-"+o+"-clueinput1-"+x+"-"+y;
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
				tr.id = xwName+"-"+o+"-inline-"+x+"-"+y;
				CssAdd(tr, "small");
				previewTab.appendChild(tr);
			}
			clueDiv.appendChild(previewTab);
		}
		var box = document.getElementById(xwName+"-"+o+"-clues");
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
		var light = new CrosswordLight(xwName, x, y, o, cells, els, eds);
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
		this.m_lights[x][y][o].updateClue(c);
		return true;
	}

	thisCrossword.constructRow = function(y)
	{
		var name = this.m_name;
		var tr = document.createElement("tr");
		tr.id=name+"-row-"+y;
		return tr;
	}
	thisCrossword.constructCell = function(x,y)
	{
		var name = this.m_name;
		var td = document.createElement("td");
		td.id = name+"-"+x+"-"+y;
		CssAdd(td,"blank");
		td.onclick = function()
		{
			xwc(name, x, y);
		}
		var div = document.createElement("div");
		td.appendChild(div);
		var sup = document.createElement("sup");
		sup.id = name+"-num-"+x+"-"+y;
		div.appendChild(sup);
		var input = document.createElement("input");
		input.id = name+"-edit-"+x+"-"+y;
		input.type = "text";
		input.onkeypress = function(ev)
		{
			return xwkp(name, x, y, ev||event);
		}
		input.onkeydown = function(ev)
		{
			return xwkd(name, x, y, ev||event);
		}
		div.appendChild(input);
		return td;
	}
	thisCrossword.resize = function(w,h)
	{
		this.changeCell(-1,-1);
		// Delete all lights which will be removed anyway
		// This will reduce time spent reducing size of lights
		for (var x = 0; x < this.m_width; ++x) {
			for (var y = 0; y < this.m_height; ++y) {
				if (x >= w || y >= h) {
					for (var o = 0; o < 2; ++o) {
						var light = this.m_lights[x][y][o];
						if (light != null) {
							this.removeLight(light);
						}
					}
				}
			}
		}
		// Shrink horizontally
		while (w < this.m_width) {
			for (var i = 0; i < this.m_height; ++i) {
				this.modifyValue(this.m_width-1, i, null);
			}
			--this.m_width;
			for (var i = 0; i < this.m_height; ++i) {
				this.m_gridRows[i].removeChild(this.m_grid[this.m_width][i].m_els[0]);
			}
			delete this.m_grid[this.m_width];
			delete this.m_lights[this.m_width];
		}
		// Shrink vertically
		while (h < this.m_height) {
			for (var i = 0; i < this.m_width; ++i) {
				var light = this.m_lights[i][this.m_height-1][0];
				if (light != null) {
					this.removeLight(light);
				}
				this.modifyValue(i, this.m_height-1, null);
			}
			--this.m_height;
			for (var i = 0; i < this.m_width; ++i) {
				this.m_gridRows[this.m_height].removeChild(this.m_grid[i][this.m_height].m_els[0]);
				delete this.m_grid[i][this.m_height];
				delete this.m_lights[i][this.m_height];
			}
			this.m_gridRows[this.m_height].parentNode.removeChild(this.m_gridRows[this.m_height]);
			delete this.m_gridRows[this.m_height];
		}
		// Grow horizontally
		while (w > this.m_width) {
			this.m_grid[this.m_width] = [];
			this.m_lights[this.m_width] = [];
			for (var i = 0; i < this.m_height; ++i) {
				var td = this.constructCell(this.m_width, i);
				this.m_gridRows[i].appendChild(td);
				this.m_grid[this.m_width][i] = new CrosswordCell(this.m_name, this.m_width, i);
				this.m_lights[this.m_width][i] = [null, null];
			}
			++this.m_width;
		}
		// Shrink vertically
		while (h > this.m_height) {
			var tr = this.constructRow(this.m_height);
			this.m_gridRows[this.m_height] = tr;
			this.m_gridRows[this.m_height-1].parentNode.appendChild(tr);
			for (var i = 0; i < this.m_width; ++i) {
				var td = this.constructCell(i,this.m_height);
				tr.appendChild(td);
				this.m_grid[i][this.m_height] = new CrosswordCell(name, i, this.m_height);
				this.m_lights[i][this.m_height] = [null, null];
			}
			++this.m_height;
		}
	}

	return thisCrossword;
}

function crosswordClueChanged(name, x, y, o, c)
{
	return crossword(name).clueChanged(x, y, o, c);
}

function crosswordResize(name, wel,hel)
{
	var xw = crossword(name);
	var w = parseInt(wel.value,10);
	if (isNaN(w) || w != wel.value) {
		w = xw.width();
	}
	else if (w < 2) {
		w = 2;
	}
	else if (w > 30) {
		w = 30;
	}
	wel.value = w;
	var h = parseInt(hel.value,10);
	if (isNaN(h) || h != hel.value) {
		h = xw.height();
	}
	else if (h < 2) {
		h = 2;
	}
	else if (h > 30) {
		h = 30;
	}
	hel.value = h;
	return xw.resize(w,h);
}
