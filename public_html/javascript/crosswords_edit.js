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

	thisCrossword.deleteValue = function(x,y)
	{
		var cell = this.m_grid[x][y];
		this.modifyValue(x,y,((!cell.isBlank && cell.isKnown()) ? "" : null));
	}

	thisCrossword.Crossword_changeCell = thisCrossword.changeCell;
	thisCrossword.changeCell = function(x, y)
	{
		this.Crossword_changeCell(x,y);
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
					if (len <= 2) {
						this.removeLight(light);
					}
					else if (x == light.m_x && y == light.m_y) {
						this.moveLight(light, x+dx, y+dy, len-1);
					}
					else if (x == light.m_x+dx*(len-1) && y == light.m_y+dy*(len-1)) {
						this.moveLight(light, light.m_x, light.m_y, len-1);
					}
					// if in middle, split, trying to split clues if possible
					else {
						
					}
				}
			}
		}
		// Creating a cell?
		if (null == oldVal && null != v) {
			for (var o = 0; o < 2; ++o) {
				var dx = 1-o;
				var dy = o;
				var prev = this.cell(x-dx,y-dy);
				var next = this.cell(x+dx,y+dy);
				if (null != prev) {
					prev = prev.light(o);
				}
				if (null != next) {
					next = next.light(o);
				}
				// if between, merge, trying to merge clues if possible
				if (null != prev && null != next) {
				}
				// if before or after, increase size
				else if (null != prev) {
					this.moveLight(prev, prev.m_x, prev.m_y, prev.length()+1);
				}
				else if (null != next) {
					this.moveLight(next, x, y, next.length()+1);
				}
				// if alone, check if can create a new light
			}
		}
	}

	thisCrossword.removeLight = function(light)
	{
		light.select(false);
		light.clean();
		this.m_lights[light.m_x][light.m_y][light.m_orientation] = null;
		if (this.m_light == light) {
			this.m_light = null;
		}
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
			this.m_lights[light.m_x][light.m_y][light.m_orientation] = null;
			if (null == this.m_lights[light.m_x][light.m_y][1-light.m_orientation]) {
				this.m_grid[light.m_x][light.m_y].m_sup.textContent = "";
			}
			this.m_lights[x][y][light.m_orientation] = light;
			// Rename clue stuff
		}

		light.select(false);
		light.setCells(x, y, cells, els, eds);

		if (baseMoved) {
			// Update numbering
			this.renumber();
		}
	}

	thisCrossword.renumber = function()
	{
		var count = 1;
		for (var y = 0; y < this.m_height; ++y) {
			for (var x = 0; x < this.m_width; ++x) {
				var lights = this.m_lights[x][y];
				var inc = false;
				for (var o = 0; o < 2; ++o) {
					if (lights[o] != null) {
						lights[o].setNumber(count);
						inc = true;
					}
				}
				if (inc) {
					++count;
				}
			}
		}
	}

	return thisCrossword;
}

onLoadFunctions.push(function() {
	grid = CrosswordEdit("xw", 13, 13);
});
