// MediaWiki JavaScript support functions

var clientPC = navigator.userAgent.toLowerCase(); // Get client info
var is_gecko = ((clientPC.indexOf('gecko')!=-1) && (clientPC.indexOf('spoofer')==-1)
                && (clientPC.indexOf('khtml') == -1) && (clientPC.indexOf('netscape/7.0')==-1));
var is_safari = ((clientPC.indexOf('applewebkit')!=-1) && (clientPC.indexOf('spoofer')==-1));
var is_khtml = (navigator.vendor == 'KDE' || ( document.childNodes && !document.all && !navigator.taintEnabled ));


// this function generates the actual toolbar buttons with localized text
// we use it to avoid creating the toolbar where javascript is not enabled
function addButton(mwEditButtons, imageFile, speedTip, tagOpen, tagClose, sampleText, imageId) {
	// Don't generate buttons for browsers which don't fully
	// support it.
	mwEditButtons[mwEditButtons.length] =
		{"imageId": imageId,
		 "imageFile": imageFile,
		 "speedTip": speedTip,
		 "tagOpen": tagOpen,
		 "tagClose": tagClose,
		 "sampleText": sampleText};
}

function addFunction(mwEditButtons, imageFile, speedTip, theFunction, imageId) {
	// Don't generate buttons for browsers which don't fully
	// support it.
	mwEditButtons[mwEditButtons.length] =
		{"imageId": imageId,
		 "imageFile": imageFile,
		 "speedTip": speedTip,
		 "theFunction": theFunction
		 };
}

// this function generates the actual toolbar buttons with localized text
// we use it to avoid creating the toolbar where javascript is not enabled
function mwInsertEditButton(parent, item, textarea) {
	var image = document.createElement("img");
	image.width = 23;
	image.height = 22;
	image.className = "mw-toolbar-editbutton";
	if (item.imageId) image.id = item.imageId;
	image.src = item.imageFile;
	image.border = 0;
	image.alt = item.speedTip;
	image.title = item.speedTip;
	image.style.cursor = "pointer";
	if (item.theFunction) {
		image.onclick = function() {
			item.theFunction(textarea);
			return false;
		};	
	} else {
		image.onclick = function() {
			insertTags(item.tagOpen, item.tagClose, item.sampleText, textarea);
			return false;
		};
	}
	parent.appendChild(image);
	return true;
}

function insertYouTube(textarea) {
	//Get the url from the user
	var ytURL = prompt('Please enter the url of the YouTube clip:','');
	if (!ytURL) return false;
	if (ytURL.indexOf('v=')==-1) {
		alert('URL Invalid! Please enter a valid YouTube url.');
		return false;
	}
	
	//Get the v string from the url
	var ytClipId = ytURL.substring(ytURL.indexOf('v=')+2,ytURL.length);
	
	//Strip out any other parameters if there are any
	if (ytClipId.indexOf('&')!=-1) ytClipId = ytClipId.substring(0,ytClipId.indexOf('&'));
	
	//Insert the string into the textarea
	insertTags('\n\n[[youtube:' + ytClipId + ']]\n\n', '', '', textarea);
}

function mwSetupToolbar(toolbar, textarea, extrabuttons) {

	var mwEditButtons = [];
	
	addButton(mwEditButtons,'/images/icons/button_bold.png','Bold text','\'\'\'','\'\'\'','Bold text','mw-editbutton-bold');
	addButton(mwEditButtons,'/images/icons/button_italic.png','Italic text','\'\'','\'\'','Italic text','mw-editbutton-italic');
	addButton(mwEditButtons,'/images/icons/button_ordered.png','Numbered list','\n# ','\n','List item','mw-editbutton-numberlist');
	addButton(mwEditButtons,'/images/icons/button_unordered.png','Bulleted lost','\n* ','\n','List item','mw-editbutton-bulletlist');
	addButton(mwEditButtons,'/images/icons/button_extlink.png','External link (remember http:// prefix)','[',']','http://www.example.com title of link','mw-editbutton-extlink');
	addButton(mwEditButtons,'/images/icons/button_quote.png','Pull quote','\n"""','""" quote author\n','Quote text.','mw-editbutton-pullquote');
	addFunction(mwEditButtons,'/images/icons/button_youtube.gif','YouTube Video', insertYouTube ,'mw-editbutton-youtube');

	if(extrabuttons) {
		addButton(mwEditButtons,'/images/icons/button_factbox.png','Fact box','\n{{factbox:Title of Box|\n','\n}}\n','Factbox body text.\nFactbox body text.','mw-editbutton-factbox');
		addButton(mwEditButtons,'/images/icons/button_headline.png','Level 2 heading','\n== ',' ==\n','Heading text','mw-editbutton-heading');
		addButton(mwEditButtons,'/images/icons/button_enter.png','Force single line break','{{br}}\n','','','mw-editbutton-linebreak');
	}

	var toolbar = document.getElementById(toolbar);
	if (!toolbar) { return false; }

	var textbox = document.getElementById(textarea);
	if (!textbox) { return false; }

	// Don't generate buttons for browsers which don't fully
	// support it.
	if (!document.selection && textbox.selectionStart === null) {
		return false;
	}

	for (var i = 0; i < mwEditButtons.length; i++) {
		mwInsertEditButton(toolbar, mwEditButtons[i], textarea);
	}

	return true;
}

// apply tagOpen/tagClose to selection in textarea,
// use sampleText instead of selection if there is none
// copied and adapted from phpBB
function insertTags(tagOpen, tagClose, sampleText, textarea) {

	var txtarea = document.getElementById(textarea);


	// IE
	if (document.selection  && !is_gecko) {
		var theSelection = document.selection.createRange().text;
		if (!theSelection) {
			theSelection=sampleText;
		}
		txtarea.focus();
		if (theSelection.charAt(theSelection.length - 1) == " ") { // exclude ending space char, if any
			theSelection = theSelection.substring(0, theSelection.length - 1);
			document.selection.createRange().text = tagOpen + theSelection + tagClose + " ";
		} else {
			document.selection.createRange().text = tagOpen + theSelection + tagClose;
		}

	// Mozilla
	} else if(txtarea.selectionStart || txtarea.selectionStart == '0') {
		var replaced = false;
		var startPos = txtarea.selectionStart;
		var endPos = txtarea.selectionEnd;
		if (endPos-startPos) {
			replaced = true;
		}
		var scrollTop = txtarea.scrollTop;
		var myText = (txtarea.value).substring(startPos, endPos);
		if (!myText) {
			myText=sampleText;
		}
		var subst;
		if (myText.charAt(myText.length - 1) == " ") { // exclude ending space char, if any
			subst = tagOpen + myText.substring(0, (myText.length - 1)) + tagClose + " ";
		} else {
			subst = tagOpen + myText + tagClose;
		}
		txtarea.value = txtarea.value.substring(0, startPos) + subst +
			txtarea.value.substring(endPos, txtarea.value.length);
		txtarea.focus();
		//set new selection
		if (replaced) {
			var cPos = startPos+(tagOpen.length+myText.length+tagClose.length);
			txtarea.selectionStart = cPos;
			txtarea.selectionEnd = cPos;
		} else {
			txtarea.selectionStart = startPos+tagOpen.length;
			txtarea.selectionEnd = startPos+tagOpen.length+myText.length;
		}
		txtarea.scrollTop = scrollTop;

	// All other browsers get no toolbar.
	// There was previously support for a crippled "help"
	// bar, but that caused more problems than it solved.
	}
	// reposition cursor if possible
	if (txtarea.createTextRange) {
		txtarea.caretPos = document.selection.createRange().duplicate();
	}
}

