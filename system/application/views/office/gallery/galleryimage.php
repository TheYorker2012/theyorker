<div class="BlueBox">
	<h2>information</h2>
	<form class="form" method="post" action="<?=site_url('office/gallery/show/'.$photoDetails->photo_id.'/save')?>">
		<fieldset>
			<label for="title">Title: </label>
				<input type="text" name="title" value="<?php echo(xml_escape($photoDetails->photo_title)); ?>" /><br />
			<label for="date">Date: </label>
				<input type="date" name="date" value="<?php echo($photoDetails->photo_timestamp); ?>" /><br />
			<label for="photographer">Photographer: </label>
				<select name="photographer">
					<?php if ($photographer->num_rows() > 0) {
						foreach($photographer->result() as $person) { ?>
					<option value="<?php echo($person->user_entity_id); ?>" <?php if ($person->user_entity_id == $photoDetails->photo_author_user_entity_id) echo('selected="selected"'); ?>><?php echo(xml_escape($person->user_firstname.' '.$person->user_surname)); ?></option>
					<?php }
					} ?>
				</select><br />
			<input type="hidden" name="tags" id="tags" />
			<div>
				<div style="float:left;">
					<h4>Add Tag</h4>
					<input type="text" id="newtag" autocomplete="off" size="15" onKeyup="tag_suggest()" onKeypress="return checkKeypress(event)" />
					<input type="button" value="Add" onClick="addTag();updateList();" /><br />
					<div style="overflow-y: auto;overflow-x: hidden;">
						<ul id="ntags" style="height:250px; width:180px;list-style: none outside none;">
						</ul>
					</div>
				</div>
				<div style="float:left">
					<h4>Tagged as:</h4>
					<div style="overflow-y: auto;overflow-x: hidden;">
						<ul id="ctags" style="height:250px;width:180px;list-style: none outside none;">
							<?php
							if ($photoTag->num_rows() > 0) {
								foreach ($photoTag->result() as $tag) {
									$tag_name_xml = xml_escape($tag->tag_name); ?>
							<li name="list_<?php echo($tag_name_xml); ?>" id="<?php echo($tag_name_xml); ?>"><a onClick="deleteTag('<?php echo($tag_name_xml); ?>')"><img src="/images/icons/delete.png" alt="Remove" title="Remove" /> <?php echo($tag_name_xml); ?></a></li>
							<?php }
							} ?>
						</ul>
					</div>
				</div>
			</div>
			<br />
			<label for="hidden">Deleted: </label>
				<input type='checkbox' name='hidden' value="hide" <?php if ($photoDetails->photo_deleted == 1) echo('checked="checked"'); ?> /><br />
				<label for="hidden-gallery">Removed from Gallery: </label>
					<input type='checkbox' name='hidden-gallery' value="hide" <?php if ($photoDetails->photo_gallery == 1) echo('checked="checked"'); ?> /><br />
			<input type="submit" class="button" value="Save" />
		</fieldset>
	</form>
</div>
<div class="BlueBox">
	<h2>previews</h2>
	<?php foreach($type as $image) {?>
	<?php 	echo('<p>'.xml_escape($image->image_type_name)); ?> (<?php echo($image->image_type_width); ?>x<?php echo($image->image_type_height); ?>)</p>
	<?php 	echo($this->image->getThumb($photoDetails->photo_id, $image->image_type_codename)); ?>
	<?php } ?>
	<p>
	Full Size<br />
	<a href="<?php echo(site_url('photos/full/'.$photoDetails->photo_id)); ?>">Click here to view</a><br /><br />
	Not happy with these thumbnails? <a href="<?php echo(site_url('office/gallery/edit/'.$photoDetails->photo_id)); ?>">Click here</a> to re-thumbnail.
	</p>
</div>
<script type="text/javascript">
// <![CDATA[

	function tag_suggest() {
		var new_val = document.getElementById('newtag').value;
	    xajax_tag_suggest(escape(new_val));
	}

	function checkKeypress(e) {
		var e = (window.event) ? e : e;
		if (e.keyCode == 13) {
			addTag();
			return false;
		} else {
			return true;
		}
	}

	function updateList() {
		var tags_el = document.getElementById('tags');
		tags_el.value = '';
		var tags = tags_el.childNodes;
		for (var i=0; i<tags.length; i++) {
			if (tags[i].nodeType == '1') {
				tags_el.value += tags[i].id + '+';
			}
		}
	}

	function deleteTag(tagID) {
		var tags = document.getElementsByName('list_'+tagID);
		for (var i = 0; i < tags.length; ++i) {
			tags[i].parentNode.removeChild(tags[i]);
		}
		updateList();
	}
	
	function setTag(tagID) {
		var newtag_el = document.getElementById('newtag');
		newtag_el.value = tagID;
	}
	
	function notInList(searchItem) {
		var ctags = document.getElementById('ctags').childNodes;
		for (var i=0; i<tags.length; i++) {
			if (tags[i].id == searchItem) {
				return false;
			}
		}
		return true;
	}

	function addTag() {
		var newtag_el = document.getElementById('newtag');
		var newtagval = newtag_el.value;
		if (newtagval != "" && notInList(newtagval)) {
			var ctags_el = document.getElementById('ctags');
			
			var li_elem = document.createElement('li');
			ctags_el.appendChild(li_elem);
			{
				li_elem.className = "orange";
				li_elem.setAttribute('name', "list_"+newtagval);
				li_elem.id = newtagval;
				
				var a_elem = document.createElement('a');
				li_elem.appendChild(a_elem);
				{
					a_elem.onclick = function () {
						deleteTag(newtagval);
					}
					
					var img_elem = document.createElement('img');
					a_elem.appendChild(img_elem);
					{
						img_elem.src = "/images/icons/delete.png";
						img_elem.alt = "Remove";
						img_elem.title = "Remove";
					}
					
					a_elem.appendChild(document.createTextNode(newtagval));
				}
			}
			
			newtag_el.value = "";
			updateList()
			return true;
		}
	}

	var tags_el = document.getElementById('tags');
	var ctags_el = document.getElementById('ctags');
	tags_el.value = '';
	var tags = ctags_el.childNodes;
	for (var i=0; i<tags.length; i++) {
		if (tags[i].nodeType == "1") {
			tags_el.value += tags[i].id + '+';
		}
	}

// ]]>
</script>