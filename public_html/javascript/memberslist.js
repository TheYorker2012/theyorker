var MemberEntries = new Array();

function getNextMemberEntry(element)
{
	var id;
	while (element != null) {
		if (element.nodeType == 1) {
			id = element.getAttribute('id');
			if (
				element.nodeType == 1 && 
				id != null &&
				id != 'NotFound'
			) {
				return element;
			}
		}
		element = element.nextSibling;
	}
}

function CreateNewMemberEntry(name, tr) {
	this.name = name.toUpperCase();
	this.tr = tr;
}

function initMemberList() {
	var element;
	var tdelement;
	
	var elementid;
	var membername;
	var memberfirstname;
	var membersurname;
	var memberemail;

	var i = 0;
	
	element = document.getElementById('MemberTable'); /* get the <tbody> element */
	element = getNextMemberEntry(element.firstChild); /* select the first <tr> element */

	while (element != null)	{
		/* <tr> element id */
		elementid = element.id;
	
		/* Get Name */
		tdelement = element.firstChild; /* get the first table data child */
		while(tdelement.nodeType != 1) /* skip any nodes which aren't html elements */
			tdelement = tdelement.nextSibling; /* find the td node with checkbox in it */
			
		/* Get User Firstname */
		tdelement = tdelement.nextSibling /* skip to the next node */
		while(tdelement.nodeType != 1) /* skip any nodes which aren't html elements */
			tdelement = tdelement.nextSibling; /* find the td node with user's firstname in it */
		memberfirstname = tdelement.firstChild; /* go inside the <a> tag */
		while(memberfirstname.nodeType != 1) /* skip any nodes which aren't html elements */
			memberfirstname = memberfirstname.nextSibling; /* find the node with user's firstname in it */
		if (memberfirstname.firstChild != null) { /* check to see if there is data*/
			memberfirstname = memberfirstname.firstChild.data; /* get the user's firstname */
		} {
			memberfirstname = ''; /* no data blank field */
		}
			
		/* Get User Firstname */
		tdelement = tdelement.nextSibling /* skip to the next node */
		while(tdelement.nodeType != 1) /* skip any nodes which aren't html elements */
			tdelement = tdelement.nextSibling; /* find the td node with user's firstname in it */
		membersurname = tdelement.firstChild; /* go inside the <a> tag */
		while(membersurname.nodeType != 1) /* skip any nodes which aren't html elements */
			membersurname = membersurname.nextSibling; /* find the node with user's firstname in it */
		if (membersurname.firstChild != null) { /* check to see if there is data*/
			membersurname = membersurname.firstChild.data; /* get the user's surname */
		} {
			membersurname = ''; /* no data blank field */
		}
		
		/* Set Name */
		membername = memberfirstname + ' ' + membersurname;
	
		MemberEntries.push(new CreateNewMemberEntry(membername, element));

		element = getNextMemberEntry(element.nextSibling);
	}
}

function searchMemberList() {
	var searchtext = '';
	var element;
	var hasResults = false;

	var i = 0;
	var j;

	element = document.getElementById('search');
	searchtext = element.value.toUpperCase();

	var entry;
	var valid;

	for (i = 0; i < MemberEntries.length; i++){
		entry = MemberEntries[i];

		valid = true;

		if (valid && entry.name.indexOf(searchtext) == -1)
			valid = false;

		hasResults = hasResults | valid;
		entry.tr.style.display = valid ? '' : 'none';
	}

	element = document.getElementById('NotFound');
	element.style.display = hasResults ? 'none' : '';
}

onLoadFunctions.push(initMemberList);