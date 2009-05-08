var oi_startPage = 'brief';
var oi_changePeriod = 20000;
var oi_changeDetector = false;
var oi_articleData = new Array();
var oi_photoData = new Array();
var oi_photoTypeData = new Array();
var oi_tagsSelected = new Array();
var oi_tagsData = new Array();
var oi_reporterData = new Array();
var oi_popupShown = false;

function setStartPage (page_id) {
	oi_startPage = page_id;
}

function setData (data) {
	oi_articleData = data['article'];
	oi_photoData = data['photos'];
	oi_photoTypeData = data['photo_types'];
	oi_tagsSelected = data['tags'];
	oi_tagsData = data['tag_groups'];
	oi_reporterData = data['reporters'];
}

function loadPage () {
	loadAllData();
	switchPage(oi_startPage);
}

function loadAllData () {
	loadArticleData(oi_articleData);
	loadPhotoData(oi_photoData, oi_articleData['thumbnail_photo_id'], oi_articleData['main_photo_id']);
	loadTagData(oi_tagsSelected, oi_tagsData);
	loadReporterData(oi_reporterData);

	oi_changeDetector = setTimeout(detectChanges, oi_changePeriod);
}

function leavePage () {
	return detectChanges(true);
}

function detectChanges (warnOnly) {
	if (warnOnly == undefined || warnOnly == null) {
		warnOnly = false;
	}
	// Find changes
	var new_article = detectArticleChanges();
	var new_photos = detectPhotoChanges();
	var new_tags = detectTagChanges();
	var new_reporters = detectReporterChanges ();

	// Save changes
	if (new_article != null || new_photos != null || new_tags != null || new_reporters != null) {
		if (warnOnly) {
			return 'WARNING: You have made some changes which have not been saved yet!';
		} else {
			ajaxStart();
			xajax__ajax(new_article, new_photos, new_tags, new_reporters);
		}
	}

	if (new_article != null) oi_articleData = new_article;
	if (new_photos != null) oi_photoData = new_photos;
	if (new_tags != null) oi_tagsSelected = new_tags;
	if (new_reporters != null) oi_reporterData = new_reporters;

	oi_changeDetector = setTimeout(detectChanges, oi_changePeriod);
}

function savedChanges () {
	ajaxEnd();
}

function loadArticleData (articleData) {
	// Populate form elements with data
	for (key in articleData) {
		var control = document.getElementById('article_' + key);
		if (control !== null && control !== undefined) {
			switch (control.tagName) {
				case 'INPUT':
				case 'TEXTAREA':
					control.value = articleData[key];
					break;
				case 'SELECT':
					for (var i = 0; i < control.length; i++) {
						if (control.options[i].value == articleData[key]) {
							control.selectedIndex = i;
							break;
						}
					}
					break;
				case 'DIV':
					control.innerHTML = articleData[key];
					break;
				default:
					alert('Unknown control type "' + control.tagName + '" for "' + key + '"');
			}
		}
	}
}

function detectArticleChanges () {
	var newArticleData = Object.clone(oi_articleData);
	var change = false;

	for (key in oi_articleData) {
		var control = document.getElementById('article_' + key);
		if (control !== null && control !== undefined) {
			switch (control.tagName) {
				case 'INPUT':
				case 'TEXTAREA':
					if (control.value != oi_articleData[key]) {
						change = true;
						newArticleData[key] = control.value;
					}
					break;
				case 'SELECT':
					if (control.options[control.selectedIndex].value != oi_articleData[key]) {
						change = true;
						newArticleData[key] = control.options[control.selectedIndex].value;
					}
					break;
				case 'DIV':
					break;
				default:
					alert('Unknown control type "' + control.tagName + '" for "' + key + '"');
			}
		}
	}

	if (change) {
		return newArticleData;
	} else {
		return null;
	}
}

function loadReporterData (reporters) {
	var container = document.getElementById('reporter_container');
	container.innerHTML = '';

	for (var x = 0; x < reporters.length; x++) {
		var lbl = document.createElement('label');
		lbl.appendChild(document.createTextNode('Reporter:'));
		container.appendChild(lbl);

		var rname = document.createElement('div');
		rname.className = 'input';
		rname.appendChild(document.createTextNode(reporters[x]['user_name']));
		container.appendChild(rname);

		var rbylines = document.createElement('select');
		rbylines.id = 'byline' + reporters[x]['user_id'];
		var byline_selected = false;
		if (reporters[x]['byline_id'] !== null && reporters[x]['byline_id'] != '') {
			byline_selected = reporters[x]['byline_id'];
		} else if (reporters[x]['default_byline_id'] !== null && reporters[x]['default_byline_id'] != '') {
			byline_selected = reporters[x]['default_byline_id'];
		}
		for (var y = 0; y < reporters[x]['bylines'].length; y++) {
			var group_name = reporters[x]['bylines'][y]['group_name'];
			if (reporters[x]['bylines'][y]['user_id'] == null) group_name = 'GLOBAL BYLINE';
			rbylines.options[rbylines.length] = new Option(
				reporters[x]['bylines'][y]['name'] + ' - ' + reporters[x]['bylines'][y]['title'] + ' (' + group_name + ')',
				reporters[x]['bylines'][y]['byline_id'],
				(byline_selected == reporters[x]['bylines'][y]['byline_id'])
			);
		}
		container.appendChild(rbylines);
		
		var rdelete = document.createElement('input');
		rdelete.type = 'button';
		rdelete.id = 'd' + reporters[x]['user_id'];
		rdelete.value = 'Remove Reporter';
		rdelete.onclick = function () { removeReporter(this); };
		container.appendChild(rdelete);
	}
}

function detectReporterChanges () {
	var newReporterData = Object.clone(oi_reporterData);
	var change = false;

	for (var x = 0; x < oi_reporterData.length; x++) {
		var control = $('byline' + oi_reporterData[x]['user_id']);
		if (oi_reporterData[x]['byline_id'] != control.options[control.selectedIndex].value) {
			newReporterData[x]['byline_id'] = control.options[control.selectedIndex].value;
			change = true;
		}
	}

	if (change) {
		return newReporterData;
	} else {
		return null;
	}
}

function loadTagData (tagsSelected, tagGroups) {
	var tag_container = document.getElementById('custom_tags');

	for (var group in tagGroups) {
		var himg = document.createElement('img');
		himg.className = 'title';
		himg.src = '/images/version2/office/icon_article.png';
		tag_container.appendChild(himg);
		var heading = document.createElement('h2');
		heading.appendChild(document.createTextNode(group));
		tag_container.appendChild(heading);

		for (var x = 0; x < tagGroups[group].length; x++) {
			var tag_slot = document.createElement('div');
			tag_slot.className = 'tag_slot';

			var selection = document.createElement('input');
			selection.type = 'checkbox';
			selection.id = 'tag' + tagGroups[group][x]['id'];
			if (tagsSelected[tagGroups[group][x]['id']] !== undefined) {
				selection.checked = true;
			}
			tag_slot.appendChild(selection);

			var lbl = document.createElement('label');
			lbl.htmlFor = 'tag' + tagGroups[group][x]['id'];
			lbl.appendChild(document.createTextNode(tagGroups[group][x]['name']));
			tag_slot.appendChild(lbl);

			tag_container.appendChild(tag_slot);
		}

		var hclear = document.createElement('div');
		hclear.className = 'clear';
		tag_container.appendChild(hclear);
	}

	var alltag_container = document.getElementById('all_tags');
	for (var tag in tagsSelected) {
		if (typeof tagsSelected[tag] == 'function') continue;
		if (document.getElementById('tag' + tag) == undefined) {
			var new_slot = document.createElement('div');
			new_slot.className = 'tag_slot';

			var selection = document.createElement('input');
			selection.type = 'checkbox';
			selection.id = 'tag' + tag;
			selection.checked = true;
			new_slot.appendChild(selection);

			var lbl = document.createElement('label');
			lbl.htmlFor = 'tag' + tag;
			lbl.appendChild(document.createTextNode(tagsSelected[tag]));
			new_slot.appendChild(lbl);

			alltag_container.appendChild(new_slot);
		}
	}
}

function detectTagChanges () {
	//var newTagData = Object.clone(oi_tagsSelected);
	var newTagData = new Array();
	var change = false;

	// Set tag categories
	for (var group in oi_tagsData) {
		for (var x = 0; x < oi_tagsData[group].length; x++) {
			var tag_input = $('tag' + oi_tagsData[group][x]['id']);
			if (tag_input.checked == true) {
				newTagData[oi_tagsData[group][x]['id']] = oi_tagsData[group][x]['name'];
				// Check for new tags
				if (oi_tagsSelected[oi_tagsData[group][x]['id']] == undefined) {
					change = true;
				}
			}
		}
	}

	// Custom tags && check for deleted tags
	for (var tagID in oi_tagsSelected) {
		if (typeof oi_tagsSelected[tagID] == 'function') continue;
		var tag_input = $('tag' + tagID);
		if (tag_input.checked == true) {
			// Tag is still selected
			newTagData[tagID] = oi_tagsSelected[tagID];
		} else {
			// Tag has been deleted
			change = true;
		}
	}

	if (change) {
		return newTagData;
	} else {
		return null;
	}
}

function loadPhotoData (photoData, thumbnail_id, intro_id) {
	var container = document.getElementById('photo_container');
	for (var x = 0; x < photoData.length; x++) {
		// Slot
		var slot = document.createElement('div');
		slot.className = 'photo_slot';
		container.appendChild(slot);
		// Photo preview
		var slot_img = document.createElement('img');
		slot_img.src = '/photos/medium/' + photoData[x]['photo_id'];
		slot_img.alt = photoData[x]['photo_alt'];
		var slot_link = document.createElement('a');
		slot_link.href = '/office/gallery/show/' + photoData[x]['photo_id'];
		slot_link.appendChild(slot_img);
		slot.appendChild(slot_link);
		// Photo title
		var slot_heading = document.createElement('div');
		slot_heading.innerHTML = 'Photo Number #' + photoData[x]['photo_number'];
		slot.appendChild(slot_heading);
		// Photo Caption
		var slot_label = document.createElement('label');
		slot_label.htmlFor = 'photoCaption' + photoData[x]['request_id'];
		slot_label.appendChild(document.createTextNode('Caption:'));
		slot.appendChild(slot_label);
		var slot_input = document.createElement('input');
		slot_input.type = 'text';
		slot_input.id = 'photoCaption' + photoData[x]['request_id'];
		slot_input.value = photoData[x]['photo_caption'];
		slot.appendChild(slot_input);
		// Photo ALT
		var slot_label = document.createElement('label');
		slot_label.htmlFor = 'photoALT' + photoData[x]['request_id'];
		slot_label.appendChild(document.createTextNode('ALT / Hover:'));
		slot.appendChild(slot_label);
		var slot_input = document.createElement('input');
		slot_input.type = 'text';
		slot_input.id = 'photoALT' + photoData[x]['request_id'];
		slot_input.value = photoData[x]['photo_alt'];
		slot.appendChild(slot_input);
		// Photo size
		var slot_label = document.createElement('label');
		slot_label.htmlFor = 'photoSize' + photoData[x]['request_id'];
		slot_label.appendChild(document.createTextNode('Size:'));
		slot.appendChild(slot_label);
		var slot_size = document.createElement('select');
		slot_size.id = 'photoSize' + photoData[x]['request_id'];
		for (var y = 0; y < oi_photoTypeData.length; y++) {
			slot_size.options[slot_size.length] = new Option(oi_photoTypeData[y]['name'], oi_photoTypeData[y]['id'], (oi_photoTypeData[y]['id'] == photoData[x]['photo_type']));
		}
		slot.appendChild(slot_size);
		// Thumbnail?
		var slot_label = document.createElement('label');
		slot_label.appendChild(document.createTextNode('Thumbnail:'));
		slot.appendChild(slot_label);
		var slot_thumb = document.createElement('input');
		slot_thumb.type = 'checkbox';
		slot_thumb.id = 'thumbnail' + photoData[x]['photo_number'];
		slot_thumb.value = photoData[x]['photo_number'];
		if (thumbnail_id == photoData[x]['photo_number']) {
			slot_thumb.checked = true;
		}
		slot_thumb.onmouseup = function () {
			if (this.checked == false) {
				if ($('article_thumbnail_photo_id').value != null && $('article_thumbnail_photo_id').value != '') {
					$('thumbnail' + $('article_thumbnail_photo_id').value).checked = false;
				}
				$('article_thumbnail_photo_id').value = this.value;
			} else {
				$('article_thumbnail_photo_id').value = null;
			}
		};
		slot.appendChild(slot_thumb);
		// Main Photo?
		var slot_label = document.createElement('label');
		slot_label.appendChild(document.createTextNode('Intro Photo:'));
		slot.appendChild(slot_label);
		var slot_thumb = document.createElement('input');
		slot_thumb.type = 'checkbox';
		slot_thumb.id = 'main' + photoData[x]['photo_number'];
		slot_thumb.value = photoData[x]['photo_number'];
		if (intro_id == photoData[x]['photo_number']) {
			slot_thumb.checked = true;
		}
		slot_thumb.onmouseup = function () {
			if (this.checked == false) {
				if ($('article_main_photo_id').value != null && $('article_main_photo_id').value != '') {
					$('main' + $('article_main_photo_id').value).checked = false;
				}
				$('article_main_photo_id').value = this.value;
			} else {
				$('article_main_photo_id').value = null;
			}
		};
		slot.appendChild(slot_thumb);
		// Insert button
		var insert_button = document.createElement('input');
		insert_button.id = 'p' + photoData[x]['photo_number'];
		insert_button.type = 'button';
		insert_button.value = 'Insert into article';
		insert_button.className = 'photo_insert';
		insert_button.onclick = function () { insertImageTag('article_content_wikitext', this.id.substr(1)); switchPage('article'); };
		slot.appendChild(insert_button);
	}
}

function detectPhotoChanges () {
	var newPhotoData = Object.clone(oi_photoData);
	var change = false;

	for (var x = 0; x < oi_photoData.length; x++) {
		// Caption
		if ($('photoCaption' + oi_photoData[x]['request_id']).value != oi_photoData[x]['photo_caption']) {
			newPhotoData[x]['photo_caption'] = $('photoCaption' + oi_photoData[x]['request_id']).value;
			change = true;
		}
		// ALT
		if ($('photoALT' + oi_photoData[x]['request_id']).value != oi_photoData[x]['photo_alt']) {
			newPhotoData[x]['photo_alt'] = $('photoALT' + oi_photoData[x]['request_id']).value;
			change = true;
		}
		// Size
		var size_input = $('photoSize' + oi_photoData[x]['request_id']);
		if (size_input.options[size_input.selectedIndex].value != oi_photoData[x]['photo_type']) {
			newPhotoData[x]['photo_type'] = size_input.options[size_input.selectedIndex].value;
			change = true;
		}
	}

	if (change) {
		return newPhotoData;
	} else {
		return null;
	}
}

function switchPage (page_id) {
	if ((!oi_popupShown) && (document.getElementById('nav_' + page_id).className != 'disabled')) {
		/* Change selected menu option */
		var nav = document.getElementById('office_nav');
		for (var i = 0; i < nav.childNodes.length; i++) {
			if (nav.childNodes[i].className != 'disabled') {
				nav.childNodes[i].className = '';
			}
		}
		document.getElementById('nav_' + page_id).className = 'current';
		/* Change content displayed */
		var canvas = document.getElementById('office_pages');
		for (var i = 0; i < canvas.childNodes.length; i++) {
			if (canvas.childNodes[i].className == 'office_page') {
				canvas.childNodes[i].style.display = 'none';
			}
		}
		document.getElementById('page_' + page_id).style.display = 'block';
	}
	return false;
}

function publishArticle () {
	window.location = '/office/article/publish/' + oi_articleData['id'] + '/' + $('publish').value;
}

function changeEditor () {
	$('article_editor_name').className = 'hide';
	$('changeEditor').className = 'hide';
	$('article_editor_user_id').className = 'show';
}

function changedEditor () {
	var control = $('article_editor_user_id');
	control.className = 'hide';
	$('article_editor_name').innerHTML = control.options[control.selectedIndex].text;
	$('article_editor_name').className = 'input';
	$('changeEditor').className = 'show';
}

function addReporter (control) {
	var reporter_id = control.options[control.selectedIndex].value;
	if (reporter_id == -1) return;
	control.selectedIndex = 0;
	$('reporter_prompt').className = 'show';
	$('reporter_control').className = 'hide';
	ajaxStart();
	xajax__reporterChange(reporter_id, 'add');
}

function removeReporter (control) {
	var reporter_id = control.id.substr(1);
	if (reporter_id == '') return;
	ajaxStart();
	xajax__reporterChange(reporter_id, 'remove');
}

function callbackReporters (reporters) {
	oi_reporterData = reporters;
	loadReporterData(oi_reporterData);
	ajaxEnd();
}

function ajaxStart () {
	var indicator = document.getElementById('office_sidebar_wait');
	var saveButton = document.getElementById('office_sidebar_save');
	indicator.className = '';
	saveButton.className = 'hide';
}

function ajaxEnd () {
	var indicator = document.getElementById('office_sidebar_wait');
	var saveButton = document.getElementById('office_sidebar_save');
	indicator.className = 'hide';
	saveButton.className = '';
}

function errorPermission (permission) {
	alert('ERROR: Unable to save the changes to this article as you do not have the required privilege (' + permission + ') to do so.');
	loadAllData();
	ajaxEnd();
}

function errorInUse (user_name) {
	alert('ERROR: Sorry, this article is currently being edited by ' + user_name + '! Please try again later.');
	loadAllData();
	ajaxEnd();
}
