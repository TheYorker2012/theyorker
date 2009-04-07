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
				id != 'NotFound' &&
				id != 'VIP' &&
				id != 'Office'
			) {
				return element;
			}
		}
		element = element.nextSibling;
	}
}

function CreateNewMemberEntry(name, email, confirmation, payment, businesscard, vip, byline, access, tr, checkbox) {
	this.name = name.toLowerCase();
	this.email = email.toLowerCase();
	this.confirmation = confirmation.toLowerCase();
	this.payment = payment.toLowerCase();
	this.businesscard = businesscard.toLowerCase();
	this.vip = vip.toLowerCase();
	this.byline = byline.toLowerCase();
	this.access = access.toLowerCase();
	this.tr = tr;
	this.checkbox = checkbox;
	this.visible = true;
}

function initMemberList() {
	var element;
	var tdelement;
	
	var elementid;
	var membercheckbox;
	var membername;
	var memberfirstname;
	var membersurname;
	var memberemail;
	var memberconfirmed;
	var memberpaid;
	var memberbusinesscard;
	var membervip;
	var memberbyline;
	var memberaccess;

	var i = 0;
	
	var vipelement = document.getElementById('VIP');
	var officeelement = document.getElementById('Office');
	if (vipelement == null) {
		Mode = 'Office';
	}
	else if (officeelement == null) {
		Mode = 'VIP';
	}
	
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
		
		/* Get Confirmed */
		tdelement = tdelement.nextSibling /* skip to the next node */
		while(tdelement.nodeType != 1) /* skip any nodes which aren't html elements */
			tdelement = tdelement.nextSibling; /* find the td node with user's confirmed status in it */
		memberconfirmed = tdelement.firstChild; /* go inside the hidden <div> tag */
		while(memberconfirmed.nodeType != 1) /* skip any nodes which aren't html elements */
			memberconfirmed = memberconfirmed.nextSibling; /* find the node with user's confirmed status in it */
		if (memberconfirmed.firstChild != null) { /* check to see if there is data*/
			memberconfirmed = memberconfirmed.firstChild.data; /* get the user's confirmed status */
		} else {
			memberconfirmed = ''; /* no data blank field */
		}
		
		/* Get Paid */
		tdelement = tdelement.nextSibling /* skip to the next node */
		while(tdelement.nodeType != 1) /* skip any nodes which aren't html elements */
			tdelement = tdelement.nextSibling; /* find the td node with user's paid status in it */
		memberpaid = tdelement.firstChild; /* go inside the hidden <div> tag */
		while(memberpaid.nodeType != 1) /* skip any nodes which aren't html elements */
			memberpaid = memberpaid.nextSibling; /* find the node with user's paid status in it */
		if (memberpaid.firstChild != null) { /* check to see if there is data*/
			memberpaid = memberpaid.firstChild.data; /* get the user's paid status */
		} else {
			memberpaid = ''; /* no data blank field */
		}
		
		/* Get Business Card */
		tdelement = tdelement.nextSibling /* skip to the next node */
		while(tdelement.nodeType != 1) /* skip any nodes which aren't html elements */
			tdelement = tdelement.nextSibling; /* find the td node with user's business card status in it */
		memberbusinesscard = tdelement.firstChild; /* go inside the hidden <div> tag */
		while(memberbusinesscard.nodeType != 1) /* skip any nodes which aren't html elements */
			memberbusinesscard = memberbusinesscard.nextSibling; /* find the node with user's business card status in it */
		if (memberbusinesscard.firstChild != null) { /* check to see if there is data*/
			memberbusinesscard = memberbusinesscard.firstChild.data; /* get the user's business card status */
		} else {
			memberbusinesscard = ''; /* no data blank field */
		}
		
		if (Mode == 'VIP') {
			/* Get VIP Access */
			tdelement = tdelement.nextSibling /* skip to the next node */
			while(tdelement.nodeType != 1) /* skip any nodes which aren't html elements */
				tdelement = tdelement.nextSibling; /* find the td node with user's vip access in it */
			membervip = tdelement.firstChild; /* go inside the hidden <div> tag */
			while(membervip.nodeType != 1) /* skip any nodes which aren't html elements */
				membervip = membervip.nextSibling; /* find the node with user's vip access in it */
			if (membervip.firstChild != null) { /* check to see if there is data*/
				membervip = membervip.firstChild.data; /* get the user's vip access */
			} else {
				membervip = ''; /* no data blank field */
			}

			/* set office vars to blank */
			memberbyline = '';
			memberaccess = '';
		}
		else if (Mode == 'Office') {
			/* Get Byline */
			tdelement = tdelement.nextSibling /* skip to the next node */
			while(tdelement.nodeType != 1) /* skip any nodes which aren't html elements */
				tdelement = tdelement.nextSibling; /* find the td node with user's byline status in it */
			memberbyline = tdelement.firstChild; /* go inside the hidden <div> tag */
			while(memberbyline.nodeType != 1) /* skip any nodes which aren't html elements */
				memberbyline = memberbyline.nextSibling; /* find the node with user's byline status in it */
			if (memberbyline.firstChild != null) { /* check to see if there is data*/
				memberbyline = memberbyline.firstChild.data; /* get the user's byline status */
			} else {
				memberbyline = ''; /* no data blank field */
			}
			
			/* Get Office Access */
			tdelement = tdelement.nextSibling /* skip to the next node */
			while(tdelement.nodeType != 1) /* skip any nodes which aren't html elements */
				tdelement = tdelement.nextSibling; /* find the td node with user's office access in it */
			memberaccess = tdelement.firstChild; /* go inside the hidden <div> tag */
			while(memberaccess.nodeType != 1) /* skip any nodes which aren't html elements */
				memberaccess = memberaccess.nextSibling; /* find the node with user's office access in it */
			if (memberaccess.firstChild != null) { /* check to see if there is data*/
				memberaccess = memberaccess.firstChild.data; /* get the user's office access */
			} else {
				memberaccess = ''; /* no data blank field */
			}
			
			/* set vip vars to blank */
			membervip = '';
		}
		
		MemberEntries.push(new CreateNewMemberEntry(membername,
													memberemail,
													memberconfirmed,
													memberpaid,
													memberbusinesscard,
													membervip,
													memberbyline,
													memberaccess,
													element,
													membercheckbox));

		element = getNextMemberEntry(element.nextSibling);
	}
}

function searchMemberList() {
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
		
		/* apply the search text filter*/
		valid = valid & entry.name.indexOf(searchtext) != -1;
		
		/* apply confirmation filter */
		element = document.getElementById('filter_confirmation');
		element != null ? filter = element.value : filter = 'all';
		valid = valid & (filter == entry.confirmation | filter == 'all');
		
		/* apply payment filter */
		element = document.getElementById('filter_payment');
		element != null ? filter = element.value : filter = 'all';
		valid = valid & (filter == entry.payment | filter == 'all');
		
		/* apply business card filter */
		element = document.getElementById('filter_businesscard');
		element != null ? filter = element.value : filter = 'all';
		valid = valid & (filter == entry.businesscard | filter == 'all');
		
		if (Mode == 'VIP') {
			/* apply vip filter */
			element = document.getElementById('filter_vip');
			element != null ? filter = element.value : filter = 'all';
			valid = valid & (filter == entry.vip | filter == 'all');
		}
		else if (Mode == 'Office') {
			/* apply byline filter */
			element = document.getElementById('filter_byline');
			element != null ? filter = element.value : filter = 'all';
			valid = valid & (filter == entry.byline | filter == 'all');
			
			/* apply access filter */
			element = document.getElementById('filter_officeaccess');
			element != null ? filter = element.value : filter = 'all';
			valid = valid & (filter == entry.access | filter == 'all');
		}

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

onLoadFunctions.push(initMemberList);