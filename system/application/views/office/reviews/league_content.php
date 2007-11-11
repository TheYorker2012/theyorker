<style type="text/css" media="screen">
  li.listItem {
    cursor: move;
  }
</style>

<div class="blue_box">
	<div style="min-height:200px;">
	<div style="float:left;overflow-y: auto;overflow-x: hidden;">
	<h3><label for="league-search">Search</label></h3>
		<form>
			<input type="text" name="leagueSearch" onKeyup="review_suggest()" />
		</form>
		<ul id="newarticle" style="height:250px;width:125px;padding:0px;margin:0px;"></ul>
	</div>
	<div style="float:left;">
		<h3>League</h3>
		<ol id="article" style="min-height:250px;width:125px;padding:0px;margin:0px;">
		<?php foreach ($leagueEntry as $entry) { ?>
			<li class="listItem" id="<?=$entry->id?>"><?=$entry->name?></li>
		<?php } ?>
		</ol>
		<img src="images/icons/trash.png" alt="Trash Can" id="trash">
	</div>
	</div>
	<hr style="clear:both" />
	<form>
		<input type="hidden" id="articles" name="articles" />
		<input type="submit" value="save" />
	</form>
</div>
 <script type="text/javascript">
 // <![CDATA[

	function review_suggest() {
		xajax_review_suggest(escape($('leagueSearch').value));
	}

Droppables.add('trash', {
	containment: ['article', 'newarticle'],
	onDrop: function(element) 
		{
			element.parentNode.removeChild(element);
			updateList();
		}
	});

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
		var List = $('article').childNodes;
		$('articles').value = "";
		for (var i=0; i<List.length; i++) {
			if (List[i].nodeType == 1) {
				$('articles').value = $('articles').value + '+' + List[i].id;
			}
		}
	}

	Sortable.create("newarticle",
	  {dropOnEmpty:true,containment:["article","newarticle"],constraint:false,
	   onChange:updateList});
	Sortable.create("article",
	  {dropOnEmpty:true,containment:["article","newarticle"],constraint:false,
	  onChange:updateList});

	updateList();
 // ]]>
 </script>