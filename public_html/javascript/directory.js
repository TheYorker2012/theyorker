var DirectoryEntries = new Array();
var letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

function createLetterJumpLinks() {
	var container = document.getElementById('LetterJump');
	var link;
	var curchar;

	for (var i = 0; i < letters.length; i++) {
		curchar = letters.charAt(i);
		link = document.createElement('a');
		link.appendChild(document.createTextNode(curchar));
		container.appendChild(link);
	
		if (document.getElementById('DirectoryList' + curchar) != null) {
			link.setAttribute('href', '#DirectoryList' + curchar);
		}
	}
}

function getNextDirEntry(element) {
	var id;
	while (element != null) {
		if (element.nodeType == 1) {
			id = element.getAttribute('id');
			if (
				element.nodeType == 1 && 
				id != null &&
				id != 'LetterJump' &&
				id != 'NotFound' &&
				id.substr(0, 13) != 'DirectoryList'
			) {
				return element;
			}
		}
		element = element.nextSibling;
	}
}

function DirectoryEntry(name, type, description, div) {
	this.name = name.toUpperCase();
	this.type = type.toUpperCase();
	this.description = description.toUpperCase();
	this.div = div;
}

function initDirectory() {
	var element;

	var entname;
	var enttype;
	var entdesc;

	var i = 0;

	element = document.getElementById('DirectoryMain');
	element = getNextDirEntry(element.firstChild);

	while (element != null) {
		
		/* entname = the h4 tag */
		entname = element.firstChild;
		while(entname.nodeType != 1)
			entname = entname.nextSibling;
		entdesc = entname.nextSibling;

		/* entname = the a tag in the h4 tag */
		entname = entname.firstChild;
		while(entname.nodeType != 1)
			entname = entname.nextSibling;

		enttype = entname.nextSibling;
		entname = entname.firstChild.data;

		/* enttype = the span tag in the h4 tag */
		while(enttype.nodeType != 1)
			enttype = enttype.nextSibling;

		enttype = enttype.firstChild.data;
		enttype = enttype.substr(1, enttype.length - 2);

		while(entdesc != null && entdesc.nodeType != 1)
			entdesc = entdesc.nextSibling;
		if (entdesc != null) {
			entdesc = entdesc.firstChild.data;
		} else {
			entdesc = '';
		}

		DirectoryEntries.push(new DirectoryEntry(entname, enttype, entdesc, element));

		element = getNextDirEntry(element.nextSibling);
	}

	createLetterJumpLinks();
}

function searchDirectory() {
	var categories = new Array();
	var freetext = '';
	var element;

	var i = 0;
	var j;

	while (true) {
		element = document.getElementById('filterCheck' + i);
		if (element == null)
			break;
		if (element.checked)
			categories.push(element.name.toUpperCase());
		i++;
	}

	element = document.getElementById('search');
	freetext = element.value.toUpperCase();

	var entry;
	var valid;
	var curLetter = 'A';
	var curLetterValid;
	var thisLetter;

	for (i = 0; i < DirectoryEntries.length; i++) {
		entry = DirectoryEntries[i];

		thisLetter = entry.name.charAt(0);
		if (thisLetter != curLetter) {
			element = document.getElementById('DirectoryList' + curLetter);
			element.style.display = curLetterValid ? 'block' : 'none';

			curLetter = thisLetter;
			curLetterValid = false;
		}

		valid = false;
		for (j = 0; j < categories.length; j++) {
			if (entry.type == categories[j]) {
				valid = true;
				break;
			}
		}

		if (valid && entry.name.indexOf(freetext) == -1 && entry.description.indexOf(freetext) == -1)
			valid = false;

		curLetterValid = curLetterValid | valid;
		entry.div.style.display = valid ? 'block' : 'none';
	}

	element = document.getElementById('DirectoryList' + curLetter);
	element.style.display = curLetterValid ? 'block' : 'none';
}

onLoadFunctions.push(initDirectory);
