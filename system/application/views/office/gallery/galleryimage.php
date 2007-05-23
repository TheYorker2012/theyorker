<div class="blue_box">
	<h2>information</h2>
	<form class="form" method="post" action="<?=site_url('office/gallery/show/'.$photoDetails->photo_id.'/save')?>">
		<fieldset>
			<label for="title">Title: </label>
				<input type="text" name="title" value="<?=$photoDetails->photo_title?>" /><br />
			<label for="date">Date: </label>
				<input type="date" name="date" value="<?=$photoDetails->photo_timestamp?>" /><br />
			<label for="photographer">Photographer: </label>
				<select name="photographer">
					<?php if ($photographer->num_rows() > 0) foreach($photographer->result() as $person): ?>
					<option value="<?=$person->user_entity_id?>" <?php if ($person->user_entity_id == $photoDetails->photo_author_user_entity_id) echo 'selected';?>><?=$person->user_firstname.' '.$person->user_surname?></option>
					<?php endforeach;?>
				</select><br />
			<input type="hidden" name="tags" id="tags" />
			<div>
				<div style="float:left;">
					<h4>Add Tag</h4>
					<input type="text" id="newtag" autocomplete="off" size="15" onKeyup="tag_suggest()" onKeypress="return checkKeypress(event)" />
					<input type="button" value="Add" onClick="addTag();updateList();" /><br />
					<div style="overflow-y: auto;overflow-x: hidden;">
						<ul id="ntags" style="height:250px; width:190px;list-style: none outside none;">
						</ul>
					</div>
				</div>
				<div style="float:left">
					<h4>Tagged as:</h4>
					<div style="overflow-y: auto;overflow-x: hidden;">
						<ul id="ctags" style="height:250px;width:190px;list-style: none outside none;">
							<?php if ($photoTag->num_rows() > 0) foreach ($photoTag->result() as $tag):?>
							<li name="list_<?=$tag->tag_name?>" id="<?=$tag->tag_name?>"><a onClick="deleteTag('<?=$tag->tag_name?>')"><img src="/images/icons/delete.png" alt="Remove" title="Remove" /> <?=$tag->tag_name?></a></li>
							<?php endforeach;?>
						</ul>
					</div>
				</div>
			</div>
			<br />
			<label for="hidden">Deleted: </label>
				<input type='checkbox' name='hidden' value="hide" <?php if ($photoDetails->photo_deleted == 1) echo "checked";?> /><br />
				<label for="hidden-gallery">Removed from Gallery: </label>
					<input type='checkbox' name='hidden-gallery' value="hide" <?php if ($photoDetails->photo_gallery == 1) echo "checked";?> /><br />
			<input type="submit" class="button" value="Save" />
		</fieldset>
	</form>
</div>
<div class="grey_box">
	<h2>previews</h2>
	<?php foreach($type as $image):?>
	<?=$image->image_type_name?> (<?=$image->image_type_width?>x<?=$image->image_type_height?>)<br />
	<?=$this->image->getThumb($photoDetails->photo_id, $image->image_type_codename)?><br /><br />
	<?php endforeach;?>
	Full Size<br />
	<a href="<?=site_url('photos/full/'.$photoDetails->photo_id)?>">Click here to view</a><br /><br />
	Not happy with these thumbnails? <a href="<?=site_url('office/gallery/edit/'.$photoDetails->photo_id)?>">Click here</a> to re-thumbnail.
</div>
<script type="text/javascript">
// <![CDATA[

	function tag_suggest() {
	    xajax_tag_suggest(escape($('newtag').value));
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
		$('tags').value = '';
		var tags = $('ctags').childNodes;
		for (var i=0; i<tags.length; i++) {
			if (tags[i].nodeType == '1') {
				$('tags').value+= tags[i].id + '+';
			}
		}
	}

	function deleteTag(tagID) {
		var tag = document.getElementsByName('list_'+tagID);
		for (var i=0; i<tag.length; i++) {
			tag[i].parentNode.removeChild(tag[i]);
		}
		updateList();
	}
	
	function setTag(tagID) {
		$('newtag').value = tagID;
	}
	
	function notInList(searchItem) {
		var tags = $('ctags').childNodes;
		for (var i=0; i<tags.length; i++) {
			if (tags[i].id == searchItem) {
				return false;
			}
		}
		return true;
	}

	function addTag() {
		if ($('newtag').value != "" && notInList($('newtag').value)) {
			$('ctags').innerHTML += '<li class="orange" name="list_'+$('newtag').value+'" id="' + $('newtag').value + '"><a onClick="deleteTag(\'' + $('newtag').value + '\')"><img src="/images/icons/delete.png" alt="Remove" title="Remove" /> ' + $('newtag').value + '</a></li>';
			$('newtag').value = "";
			updateList()
			return true;
		}
	}

	$('tags').value = '';
	var tags = $('ctags').childNodes;
	for (var i=0; i<tags.length; i++) {
		if (tags[i].nodeType == '1') {
			$('tags').value+= tags[i].id + '+';
		}
	}

// ]]>
</script>