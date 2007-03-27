	<h2>information</h2>
	<form class="form" method="post" action="<?=$photoDetails->photo_id?>/save">
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
			<input type="hidden" id="tags">
			<div style="min-height:200px;">
				<div style="float:left;">
				<h3>Tagged as:</h3>
				 <ul class="sortabledemo" id="ctags" style="min-height:250px;width:125px;">
					<?php if ($photoTag->num_rows() > 0) foreach ($photoTag->result() as $tag):?>
					<li id="ctags_<?=$tag->tag_id?>"><?=$tag->tag_name?></li>
					<?php endforeach;?>
				 </ul>
				</div>
				 <div style="float:left;overflow-y: auto;overflow-x: hidden;">
				 <h3>All Tags:</h3>
				 <ul class="sortabledemo" id="atags" style="height:250px;width:125px;">
					<?php if ($tags->num_rows() > 0) foreach ($tags->result() as $tag):?>
					<li id="atags_<?=$tag->tag_id?>"><?=$tag->tag_name?></li>
					<?php endforeach;?>
				 </ul>
				</div>
			</div>
			<form id="addTagForm">
				<fieldset>
					<legend>Add new tag</legend>
					<input type="text" id="newtag" onKeypress="return checkKeypress(event)">
					<input type="button" value="Add" onClick="addTag();">
				</fieldset>
			</form>
			<label>Home Feature: </label>
				<input type='checkbox' name='onfrontpage' value="on" /><br />
			<label>Hidden: </label>
				<input type='checkbox' name='hidden' value="hide" /><br />
			<input type="submit" class="button" value="Save" />
		</fieldset>
	</form>
</div>
<div class="grey_box">
	<h2>previews</h2>
	<?php foreach($type as $image):?>
	<?=$image->image_type_name?> (<?=$image->image_type_width?>x<?=$image->image_type_height?>)<br />
	<img src="<?=site_url(imageLocation($photoDetails->photo_id, $image->image_type_codename))?>" /><br /><br />
	<?php endforeach;?>
	Full Size<br />
	<a href="<?=site_url(photoLocation($photoDetails->photo_id))?>">Click here to view</a><br /><br />
	Not happy with these thumbnails? <a href="<?=site_url('office/gallery/edit/'.$photoDetails->photo_id)?>">Click here</a> to re-thumbnail.
</div>
<script type="text/javascript">
// <![CDATA[

	function checkKeypress(e) {
		var e = (window.event) ? e : e;
		if (e.keyCode == 13) {
			addTag();
			updateList();
			return false;
		} else {
			return true;
		}
	}


	function AddClones() {
		if (count <= 5) {
			count++;
			var newClone = document.getElementById('source').cloneNode(true);
			newClone.id = '';
			newClone.style.display = 'block';
			var newField = newClone.childNodes;
			for (var i=0; i<newField.length; i++) {
				if (newField[i].name)
					newField[i].name = newField[i].name + count;
				if (newField[i].nodeType == '1') if (newField[i].getAttribute('for')) //needs to be asked in order
					newField[i].setAttribute('for', newField[i].getAttribute('for') + count);
			}
			var Spawn = document.getElementById('destination');
			Spawn.value = count;
			Spawn.parentNode.insertBefore(newClone, Spawn);

		}
	}



	function updateList() {
		$('tags').value = '';
		var tags = $('ctags').childNodes;
		for (var i=0; i<$('ctags').length; i++) {
			$('tags').value+= tags[i].innerHTML;
		}
	}

	function addTag() {
		Sortable.destroy($('ctags'));
		Sortable.destroy($('atags'));
		$('ctags').innerHTML += '<li class="orange" id="ntags_' + $('newtag').value + '">' + $('newtag').value + '</li>';
		Sortable.create("ctags",
		     {dropOnEmpty:true,containment:["ctags","atags", "ntags"],constraint:false,
		      onChange:updateList});
		Sortable.create("atags",
		     {dropOnEmpty:true,containment:["ctags","atags", "ntags"],constraint:false,
		     onChange:updateList});
		return true;
	}

  Sortable.create("ctags",
    {dropOnEmpty:true,containment:["ctags","atags", "ntags"],constraint:false,
     onChange:updateList});
  Sortable.create("atags",
    {dropOnEmpty:true,containment:["ctags","atags", "ntags"],constraint:false,
    onChange:updateList});

	updateList();
// ]]>
</script>