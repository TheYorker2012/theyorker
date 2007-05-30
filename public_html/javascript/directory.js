var DirectoryEntries = new Array();

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
		enttype = entname.nextSibling;

		/* entname = the a tag in the h4 tag */
		entname = entname.firstChild;
		while(entname.nodeType != 1)
			entname = entname.nextSibling;
		entname = entname.firstChild.data;

		/* enttype = the div after the h4 tag */
		while(enttype.nodeType != 1)
			enttype = enttype.nextSibling;
		enttype = enttype.firstChild.data;

		entdesc = enttype
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
}

function searchDirectory() {
	var categories = new Array();
	var freetext = '';
	var element;
	var hasResults = false;

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

	for (i = 0; i < DirectoryEntries.length; i++) {
		entry = DirectoryEntries[i];

		valid = false;
		for (j = 0; j < categories.length; j++) {
			if (entry.type == categories[j]) {
				valid = true;
				break;
			}
		}

		if (valid && entry.name.indexOf(freetext) == -1 && entry.description.indexOf(freetext) == -1)
			valid = false;

		hasResults = hasResults | valid;
		entry.div.style.display = valid ? 'block' : 'none';
	}

	element = document.getElementById('NotFound');
	element.style.display = hasResults ? 'none' : 'block';
}

onLoadFunctions.push(initDirectory);
