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
		this.modifyValue(x,y,null);
	}

	thisCrossword.Crossword_changeCell = thisCrossword.changeCell;
	thisCrossword.changeCell = function(x, y)
	{
		this.Crossword_changeCell(x,y);
	}

	thisCrossword.Crossword_modifyValue = thisCrossword.modifyValue;
	thisCrossword.modifyValue = function(x, y, v)
	{
		this.Crossword_modifyValue(x, y, v);
		var cell = this.cell(x, y);
		if (null != cell) {
			var precell = this.cell(x-this.m_orientation.dx(), y-this.m_orientation.dy());
			if (null != precell) {
				cell.space(this.m_orientation, this.m_xySpaced && !precell.isBlank() && precell.isKnown());
			}
		}
	}

	return thisCrossword;
}

onLoadFunctions.push(function() {
	grid = CrosswordEdit("xw", 13, 13);
});
