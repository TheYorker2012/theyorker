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

function insertMediaPlayer(textarea) {
	var input = document.getElementById(textarea);
	var prompt = document.createElement('div');
	prompt.id = 'mp_prompt';
	prompt.style.position = 'fixed';
	prompt.style.top = input.offsetTop + 'px';
	prompt.style.left = input.offsetLeft + 'px';
	prompt.style.height = input.offsetHeight + 'px';
	prompt.style.width = input.offsetWidth + 'px';
	prompt.style.border = '1px #999 solid';
	prompt.style.backgroundColor = '#fff';
	prompt.style.textAlign = 'center';
	var loader = document.createElement('img');
	loader.src = '/images/prototype/prefs/loading.gif';
	prompt.appendChild(loader);
	prompt.appendChild(document.createTextNode(' Finding all available media files...'));
	document.getElementById('toolbar').appendChild(prompt);
	xajax__getMediaFiles();
}

function insertMediaPlayerOptions (opt) {
	var prompt = document.getElementById('mp_prompt');
	prompt.innerHTML = '';
	prompt.style.textAlign = 'left';
	for (var opt_index = 0; opt_index < opt.length; opt_index++) {
		var file_option = document.createElement('div');
		var file_image = document.createElement('img');
		file_image.src = '/images/icons/music.png';
		var file_link = document.createElement('a');
		file_link.style.cursor = 'pointer';
		eval("file_link.onclick = function () { insertMediaPlayerLink('article_content_wikitext', '" + opt[opt_index][1] + "'); return false; }");
		file_link.appendChild(document.createTextNode(' ' + opt[opt_index][0]));
		file_option.appendChild(file_image);
		file_option.appendChild(file_link);
		prompt.appendChild(file_option);
	}
}

function insertMediaPlayerLink (textarea, file) {
	//Insert the string into the textarea
	insertTags('\n\n[[media:' + file + ']]\n\n', '', '', textarea);
	// Hide media player prompt
	document.getElementById('toolbar').removeChild(document.getElementById('mp_prompt'));
}

function previewWikitext(textarea, previewDiv, previewUrl) {
	var textarea_el = document.getElementById(textarea);
	var previewDiv_el = document.getElementById(previewDiv);
	previewDiv_el.style.display = "";
	setInnerText(previewDiv_el, "Loading preview");
	var ajax = new AJAXInteraction(previewUrl, {input_wikitext_preview: textarea_el.value},
		function(responseXML) {
			var root = responseXML.documentElement;
			previewDiv_el.innerHTML = innerText(root);
		},
		function(status,message) {
			setInnerText(previewDiv_el, "Preview failed. "+status+": "+message);
		});
	ajax.doPost();
}

function mwSetupToolbar(toolbar, textarea, extrabuttons, previewDivUrl) {

	var mwEditButtons = [];
	
	addButton(mwEditButtons,'/images/icons/button_bold.png','Bold text','\'\'\'','\'\'\'','Bold text','mw-editbutton-bold');
	addButton(mwEditButtons,'/images/icons/button_italic.png','Italic text','\'\'','\'\'','Italic text','mw-editbutton-italic');
	addButton(mwEditButtons,'/images/icons/button_ordered.png','Numbered list','\n# ','\n','List item','mw-editbutton-numberlist');
	addButton(mwEditButtons,'/images/icons/button_unordered.png','Bulleted list','\n* ','\n','List item','mw-editbutton-bulletlist');
	addButton(mwEditButtons,'/images/icons/button_extlink.png','External link (remember http:// prefix)','[',']','http://www.example.com title of link','mw-editbutton-extlink');
	addButton(mwEditButtons,'/images/icons/button_quote.png','Pull quote','\n"""','""" quote author\n','Quote text.','mw-editbutton-pullquote');
	addFunction(mwEditButtons,'/images/icons/button_youtube.gif','YouTube Video', insertYouTube ,'mw-editbutton-youtube');
	addFunction(mwEditButtons,'/images/icons/button_media.png','Media Player', insertMediaPlayer,'mw-editbutton-mediaplayer');
	addButton(mwEditButtons,'/images/icons/button_headline.png','Heading','\n=== ',' ===\n','Heading text','mw-editbutton-heading');

	if(extrabuttons) {
		addButton(mwEditButtons,'/images/icons/button_factbox.png','Fact box','\n{{factbox:Title of Box|\n','\n}}\n','* Fact one.\n* Fact two.','mw-editbutton-factbox');
		addButton(mwEditButtons,'/images/icons/button_enter.png','Force single line break','{{br}}\n','','','mw-editbutton-linebreak');
	}

	// Button to generate preview
	if (previewDivUrl) {
		addFunction(mwEditButtons,'/images/icons/button_preview.png','Preview wikitext',
			function(textarea) {return previewWikitext(textarea,previewDivUrl[0],previewDivUrl[1]);},'mw-editbutton-preview');
	}

	var toolbar = document.getElementById(toolbar);
	if (!toolbar) { return false; }

	var textbox = document.getElementById(textarea);
	if (!textbox) { return false; }

	// Don't generate buttons for browsers which don't fully support it.
	// This check had to be removed for article editing to work
	//if (!document.selection && textbox.selectionStart === null) {
	//	return false;
	//}

	for (var i = 0; i < mwEditButtons.length; i++) {
		mwInsertEditButton(toolbar, mwEditButtons[i], textarea);
	}

	return true;
}


function insertImageTag(textarea, imageNumber) {
	// If this doesn't work, the text box must not be visible
	var txtarea = document.getElementById(textarea);
	try {
		if (txtarea.selectionStart === null) {
			alert('To insert an image, please go to the body tab and place the cursor somewhere in the main article.') ;
			return false;
		}
	} catch (error) {
		alert('To insert an image, please go to the body tab and place the cursor somewhere in the main article.') ;
		return false;
	}
	insertTags('[[image:' + imageNumber + '|right|caption here]]\n','','',textarea);
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

