// For inline page property adjustment

function PPEditSubmitWikitext(id, page, property, type, action)
{
	var source = document.getElementById('ppedit_wikitext_value_'+id);
	var data = {};
	data['action']		= action;
	data['pageid']		= page;
	data['property']	= property;
	data['type']		= type;
	data['text']		= source.value;
	var ajax = new AJAXInteraction('/admin/pages/inlineedit', data,
		function (responseXML) {
			if (responseXML) {
				var preview_target = document.getElementById('pp_'+type+'_preview_'+id);
				var inline_page_edit = responseXML.getElementsByTagName('inline_page_edit');
				if (inline_page_edit.length > 0) {
					var permission = inline_page_edit[0].attributes.getNamedItem('permission').value;
					var saved = inline_page_edit[0].attributes.getNamedItem('saved').value;
					if (permission != '1') {
						preview_target.innerHTML = 'You do not currently have editor privilages. Please make sure you are logged in to the office and try again.';
					} else {
						var previews = responseXML.getElementsByTagName('preview');
						if (previews.length > 0) {
							preview_target.innerHTML = previews[0].firstChild.nodeValue;
							if (saved == '1') {
								// change the content on the main property box
								var actual_target = document.getElementById('pp_'+type+'_'+id);
								actual_target.innerHTML = previews[0].firstChild.nodeValue;
								// reset the value of the text box to the new value
								var children = source.childNodes;
								if (children.length > 0) {
									source.removeChild(children[0]);
								}
								source.appendChild(document.createTextNode(data['text']));
								// switch to the default view
								PPEditToggle(id);
							}
						}
					}
				}
			}
		}
	);
	ajax.doPost();
	return false;
}
// toggle the edit mode of an inline edit page property
function PPEditToggle(id)
{
	var edit_div = document.getElementById('ppedit_wikitext_'+id);
	var main_div = document.getElementById('pp_wikitext_'+id);
	if (edit_div.style.display=='none') {
		edit_div.style.display='block';
		main_div.style.display='none';
	} else {
		edit_div.style.display='none';
		main_div.style.display='block';
	}
	return false;
}
