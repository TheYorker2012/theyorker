var oi_startPage = 'brief';
var oi_articleData = new Array();
var oi_photoData = new Array();
var oi_photoTypeData = new Array();
var oi_popupShown = false;

function setStartPage (page_id) {
	oi_startPage = page_id;
}

function setData (data) {
	oi_articleData = data['article'];
	oi_photoData = data['photos'];
	oi_photoTypeData = data['photo_types'];
}

function loadPage () {
	loadArticleData(oi_articleData);
	loadPhotoData(oi_photoData, oi_articleData['thumbnail_photo_id'], oi_articleData['main_photo_id']);
	switchPage(oi_startPage);
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
		slot_label.appendChild(document.createTextNode('Caption:'));
		slot.appendChild(slot_label);
		var slot_input = document.createElement('input');
		slot_input.type = 'text';
		slot_input.value = photoData[x]['photo_caption'];
		slot.appendChild(slot_input);
		// Photo ALT
		var slot_label = document.createElement('label');
		slot_label.appendChild(document.createTextNode('ALT / Hover:'));
		slot.appendChild(slot_label);
		var slot_input = document.createElement('input');
		slot_input.type = 'text';
		slot_input.value = photoData[x]['photo_alt'];
		slot.appendChild(slot_input);
		// Photo size
		var slot_label = document.createElement('label');
		slot_label.appendChild(document.createTextNode('Size:'));
		slot.appendChild(slot_label);
		var slot_size = document.createElement('select');
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
		if (thumbnail_id == photoData[x]['photo_number']) {
			slot_thumb.checked = true;
		}
		slot.appendChild(slot_thumb);
		// Main Photo?
		var slot_label = document.createElement('label');
		slot_label.appendChild(document.createTextNode('Intro Photo:'));
		slot.appendChild(slot_label);
		var slot_thumb = document.createElement('input');
		slot_thumb.type = 'checkbox';
		if (intro_id == photoData[x]['photo_number']) {
			slot_thumb.checked = true;
		}
		slot.appendChild(slot_thumb);
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