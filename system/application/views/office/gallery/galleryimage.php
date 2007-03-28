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
			<input type="hidden" name="tags" id="tags" />
			<div>
				<div style="float:left;overflow-y: auto;overflow-x: hidden;">
				<h4>Tagged as:</h4>
				 <ul id="ctags" style="height:250px;width:125px;cursor: move;">
					<?php if ($photoTag->num_rows() > 0) foreach ($photoTag->result() as $tag):?>
					<li id="ctags_<?=$tag->tag_name?>"><?=$tag->tag_name?></li>
					<?php endforeach;?>
				 </ul>
				</div>
				<div style="float:left;overflow-y: auto;overflow-x: hidden;">
				<h4>All Tags:</h4>
				 <ul id="atags" style="height:250px;width:125px;cursor: move;">
					<?php if ($tags->num_rows() > 0) foreach ($tags->result() as $tag):?>
					<li id="atags_<?=$tag->tag_name?>"><?=$tag->tag_name?></li>
					<?php endforeach;?>
				 </ul>
				</div>
			</div>
			<br />
			<label for="newtag">New Tag</label>
				<input type="text" id="newtag" autocomplete="off" onChange="tag_suggest()" onKeypress="return checkKeypress(event)">
				<input type="button" value="Add" onClick="addTag();">
				<br />
				<div id="txt_result"></div>
			<label for="onfrontpage">Home Feature: </label>
				<input type='checkbox' name='onfrontpage' value="on" /><br />
			<label for="hidden">Hidden: </label>
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

	function tag_suggest() {
	    xajax_tag_suggest(escape($('newtag').value));
	}

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

	function updateList() {
		$('tags').value = '';
		var tags = $('ctags').childNodes;
		for (var i=0; i<tags.length; i++) {
			$('tags').value+= tags[i].innerHTML + '+';
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