var MemberEntries = new Array();
var Mode;

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

function CreateNewMemberEntry(name, email, organisation, vip, tr, checkbox) {
	this.name = name.toLowerCase();
	this.email = email.toLowerCase();
	this.organisation = organisation.toLowerCase();
	this.vip = vip.toLowerCase();
	this.tr = tr;
	this.checkbox = checkbox;
	this.visible = true;
}

function initVIPList() {
	var element;
	var tdelement;
	
	var elementid;
	var membercheckbox;
	var membername;
	var memberfirstname;
	var membersurname;
	var memberemail;
	var memberorganisation;
	var membervip;

	var i = 0;
	
	element = document.getElementById('MemberTable'); /* get the <tbody> element */
	element = getNextMemberEntry(element.firstChild); /* select the first <tr> element */

	while (element != null)	{
		/* <tr> element id */
		elementid = element.id;
	
		/* Get Checkbox */
		tdelement = element.firstChild; /* get the first table data child */
		while(tdelement.nodeType != 1) /* skip any nodes which aren't html elements */
			tdelement = tdelement.nextSibling; /* find the td node with checkbox in it */
		membercheckbox = tdelement.firstChild;
		while(membercheckbox.nodeType != 1) /* skip any nodes which aren't html elements */
			membercheckbox = membercheckbox.nextSibling; /* find the td node with checkbox in it */
			
		/* Get User Firstname */
		tdelement = tdelement.nextSibling /* skip to the next node */
		while(tdelement.nodeType != 1) /* skip any nodes which aren't html elements */
			tdelement = tdelement.nextSibling; /* find the td node with user's firstname in it */
		memberfirstname = tdelement.firstChild; /* go inside the <a> tag */
		while(memberfirstname.nodeType != 1) /* skip any nodes which aren't html elements */
			memberfirstname = memberfirstname.nextSibling; /* find the node with user's firstname in it */
		if (memberfirstname.firstChild != null) { /* check to see if there is data*/
			memberfirstname = memberfirstname.firstChild.data; /* get the user's firstname */
		} else {
			memberfirstname = ''; /* no data blank field */
		}
			
		/* Get User Firstname */
		tdelement = tdelement.nextSibling /* skip to the next node */
		while(tdelement.nodeType != 1) /* skip any nodes which aren't html elements */
			tdelement = tdelement.nextSibling; /* find the td node with user's surname in it */
		membersurname = tdelement.firstChild; /* go inside the <a> tag */
		while(membersurname.nodeType != 1) /* skip any nodes which aren't html elements */
			membersurname = membersurname.nextSibling; /* find the node with user's surname in it */
		if (membersurname.firstChild != null) { /* check to see if there is data*/
			membersurname = membersurname.firstChild.data; /* get the user's surname */
		} else {
			membersurname = ''; /* no data blank field */
		}
		
		/* Set Name */
		membername = memberfirstname + ' ' + membersurname;
		
		//TODO: it is possible for emails to be missing and just show the username as plain text, where this would break
		/* Get User Email */
		tdelement = tdelement.nextSibling /* skip to the next node */
		while(tdelement.nodeType != 1) /* skip any nodes which aren't html elements */
			tdelement = tdelement.nextSibling; /* find the td node with user's email in it */
		memberemail = tdelement.firstChild; /* go inside the <a> tag */
		while(memberemail.nodeType != 1) /* skip any nodes which aren't html elements */
			memberemail = memberemail.nextSibling; /* find the node with user's email in it */
		if (memberemail.firstChild != null) { /* check to see if there is data*/
			memberemail = memberemail.firstChild.data; /* get the user's email  */
		} else {
			memberemail = ''; /* no data blank field */
		}
		
		/* Get Organisation  */
		tdelement = tdelement.nextSibling /* skip to the next node */
		while(tdelement.nodeType != 1) /* skip any nodes which aren't html elements */
			tdelement = tdelement.nextSibling; /* find the td node with user's organisation in it */
		memberorganisation = tdelement.firstChild; /* go inside the <a> tag */
		while(memberorganisation.nodeType != 1) /* skip any nodes which aren't html elements */
			memberorganisation = memberorganisation.nextSibling; /* find the node with user's organisation in it */
		if (memberorganisation.firstChild != null) { /* check to see if there is data*/
			memberorganisation = memberorganisation.firstChild.data; /* get the user's organisation  */
		} else {
			memberorganisation = ''; /* no data blank field */
		}
		
		/* Get VIP Status */
		tdelement = tdelement.nextSibling /* skip to the next node */
		while(tdelement.nodeType != 1) /* skip any nodes which aren't html elements */
			tdelement = tdelement.nextSibling; /* find the td node with user's vip status in it */
		membervip = tdelement.firstChild; /* go inside the hidden <div> tag */
		while(membervip.nodeType != 1) /* skip any nodes which aren't html elements */
			membervip = membervip.nextSibling; /* find the node with user's vip status in it */
		if (membervip.firstChild != null) { /* check to see if there is data*/
			membervip = membervip.firstChild.data; /* get the user's vip status */
		} else {
			membervip = ''; /* no data blank field */
		}
		
		MemberEntries.push(new CreateNewMemberEntry(membername,
													memberemail,
													memberorganisation,
													membervip,
													element,
													membercheckbox));

		element = getNextMemberEntry(element.nextSibling);
	}
}

function searchVIPList() {
	var searchtext = '';
	var element;
	var hasResults = false;
	
	var filter;

	var i = 0;
	var j;

	element = document.getElementById('search');
	searchtext = element.value.toLowerCase();

	var entry;
	var valid;

	for (i = 0; i < MemberEntries.length; i++){
		entry = MemberEntries[i];
		
		valid = true;
		
		/* apply the search text filter on usernames */
		valid = valid & entry.name.indexOf(searchtext) != -1;
		
		/* apply the search text filter on organisation names */
		valid = valid | entry.organisation.indexOf(searchtext) != -1;
		
		/* apply vip filter */
		element = document.getElementById('filter_vip_status');
		element != null ? filter = element.value : filter = 'all';
		valid = valid & (filter == entry.vip | filter == 'all');

		/* set the table row style from the results */
		hasResults = hasResults | valid;
		entry.tr.style.display = valid ? '' : 'none';
		entry.visible = valid ? true : false;
	}

	element = document.getElementById('NotFound');
	element.style.display = hasResults ? 'none' : '';
}

function checkVisibleRows() {
	var element;
	var entry;
	
	var i = 0;
	
	/* get the clicked select all checkbox */
	element = document.getElementById('UserSelectAllNone');
	
	/* check every member entry */
	for (i = 0; i < MemberEntries.length; i++){
		entry = MemberEntries[i];
		if (entry.visible) { /* if the entry is displayed */
			entry.checkbox.checked = element.checked; /* set the checked value to the new value */
		}
		else {
			entry.checkbox.checked = false; /* otherwise false */
		}
	}
}

onLoadFunctions.push(initVIPList);