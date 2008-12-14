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

	thisCrossword.Crossword_changeCell = thisCrossword.changeCell;
	thisCrossword.changeCell = function(x, y)
	{
		this.Crossword_changeCell(x,y);
	}

	thisCrossword.Crossword_updateCellFilled = thisCrossword.updateCellFilled;
	thisCrossword.updateCellFilled = function(x, y)
	{
		// Set blankness
		var editBox = this.editBox(x, y);
		if (null != editBox) {
			var cell = this.cell(x, y);
			if (null != cell) {
				if (editBox.value == "") {
					CssAdd(cell, "blank");
				}
				else {
					CssRemove(cell, "blank");
				}
			}
		}
	}

	return thisCrossword;
}

grid = CrosswordEdit("xw", 13, 13);
