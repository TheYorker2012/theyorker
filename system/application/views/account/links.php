<form>
	<input type="hidden" name="linklist" id="linklist" />
</form>
<div id="RightColumn">
	<h2 class="first">My Links</h2>
	<div class="Entry" id="links">
		<?php if ($link->num_rows() > 0) foreach($link->result() as $picture) {?>
			<?=imageLocTag($picture->link_image_id, 'link', false, $picture->link_name, null, $picture->image_file_extension, 'links_'.$picture->link_id)?>
		<?php }?>
	</div>
	<h2>Remove Links</h2>
	<div class="Entry">
		<table border="0">
		<tr>
		<td>
			<img id="bin" title="Bin" src="/images/prototype/prefs/trash.png" width="48" height="48">
		</td>
		<td>
			Drag and drop links into the bin to remove them from your home page
		</td>
		</tr>
		</table>
	</div>
	<h2>Custom Links</h2>
	<div class="Entry">
		Custom links allow you to add a link that is not in the list.<br />
		<form action="/account/customlink">
		<input type="submit" value="Create Link" class="button" />
		</form>
	</div>
</div>

 <script type="text/javascript" language="javascript">
 // <![CDATA[

	Droppables.add('bin', {
		overlap:'horizontal',
		onDrop:function(element) {
			Sortable.destroy("links");
			element.parentNode.removeChild(element);
			Sortable.create("links",
				{tag:'img',overlap:'horizontal',constraint: false, onUpdate:updateList});
			updateList();
		}
	});

	function updateList() {
		$('linklist').value = '';
		var tags = $('links').childNodes;
		for (var i=0; i<tags.length; i++) {
			if (tags[i].nodeType == '1') {
				$('linklist').value+= tags[i].id + '+';
			}
		}
		
		xajax_links_update(escape($('linklist').value));
	}
	
	Sortable.create("links", {
		tag:'img',overlap:'horizontal',constraint: false,onUpdate:updateList});
 // ]]>
 </script>

<div id="MainColumn">
	<div class="BlueBox">
		<h2>Add Links</h2>
		<form>
		<fieldset>
			<label for="sname"> Search: </label>
			<input type="text" id="sname" name="sname" value="" />
			<br />
		</fieldset>
		</form>
		<div style="height:200px;overflow-x: hidden;overflow-y:scroll;overflow:-moz-scrollbars-vertical !important;">
			<?php foreach ($AllLinks->result() as $option) {?>
						<a href="links/add/<?=$option->link_id?>"><img title="Add to Homepage" alt="Add" src="/images/icons/add.png" width="16" height="16"></a>
						<?=$option->link_name?> <a href="<?=$option->link_url?>" target="_blank"><img src="/images/icons/link_go.png" alt="Open site" title="Open site" /></a><br />
			<?php } ?>
		</div>
	</div>
</div>
