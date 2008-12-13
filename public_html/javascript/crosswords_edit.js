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
	self = new Crossword(name, width, height);

	self.Crossword_changeCell = self.changeCell;
	self.changeCell = function(x, y)
	{
		this.Crossword_changeCell(x,y);
	}

	self.Crossword_updateCellFilled = self.updateCellFilled;
	self.updateCellFilled = function(x, y)
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

	return self;
}

grid = CrosswordEdit("xw", 13, 13);

