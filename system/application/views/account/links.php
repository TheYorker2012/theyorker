<div id="RightColumn">
	<h2 class="first">My Links</h2>
	<div class="Entry" id="links">
		<?php if ($link->num_rows() > 0) { foreach($link->result() as $picture) {?>
				<a href="<?=$picture->link_url?>"><?=imageLocTag($picture->link_image_id, 'link', false, $picture->link_url)?></a>
		<?php } } else { ?>
				<a href="http://theyorker.co.uk">You have no links :(</a>
		<?php }?>
	</div>
	<h2>Remove Links</h2>
	<div class="Entry">
		<table border="0">
		<tr>
		<td>
			<img title="Bin" src="/images/prototype/prefs/trash.png" width="48" height="48">
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
   Sortable.create("links",
     {tag:'img',overlap:'horizontal',constraint: false,
      onUpdate:function(){
	  		alert(Sortable.serialize("links"));
      }
    })
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
			<table style="width:100%;">
	            <tbody>
					<tr>
						<td><a href="#"><img title="Add to Homepage" src="/images/icons/add.png" width="16" height="16"></a></td>
						<td>Google <a href="http://www.google.co.uk/" target="_blank"><img src="/images/icons/link_go.png" alt="Open site" title="Open site" /></a></td>
						</tr>
					<tr>
						<td><a href="#"><img title="Add to Homepage" src="/images/icons/add.png" width="16" height="16"></a></td>
						<td>University Of York <a href="http://www.google.co.uk/" target="_blank"><img src="/images/icons/link_go.png" alt="Open site" title="Open site" /></a></td>
						</tr>
					<tr>
						<td><a href="#"><img title="Add to Homepage" src="/images/icons/add.png" width="16" height="16"></a></td>
						<td>York University Students' Union <a href="http://www.google.co.uk/" target="_blank"><img src="/images/icons/link_go.png" alt="Open site" title="Open site" /></a></td>
						</tr>
					<tr>
						<td><a href="#"><img title="Add to Homepage" src="/images/icons/add.png" width="16" height="16"></a></td>
						<td>Facebook <a href="http://www.google.co.uk/" target="_blank"><img src="/images/icons/link_go.png" alt="Open site" title="Open site" /></a></td>
						</tr>
					<tr>
						<td><a href="#"><img title="Add to Homepage" src="/images/icons/add.png" width="16" height="16"></a></td>
						<td>Hotmail <a href="http://www.google.co.uk/" target="_blank"><img src="/images/icons/link_go.png" alt="Open site" title="Open site" /></a></td>
						</tr>
					<tr>
						<td><a href="#"><img title="Add to Homepage" src="/images/icons/add.png" width="16" height="16"></a></td>
						<td>Google UK <a href="http://www.google.co.uk/" target="_blank"><img src="/images/icons/link_go.png" alt="Open site" title="Open site" /></a></td>
						</tr>
					<tr>
						<td><a href="#"><img title="Add to Homepage" src="/images/icons/add.png" width="16" height="16"></a></td>
						<td>Wikipedia <a href="http://www.google.co.uk/" target="_blank"><img src="/images/icons/link_go.png" alt="Open site" title="Open site" /></a></td>
						</tr>	

					<tr>
						<td><a href="#"><img title="Add to Homepage" src="/images/icons/add.png" width="16" height="16"></a></td>
						<td>Google <a href="http://www.google.co.uk/" target="_blank"><img src="/images/icons/link_go.png" alt="Open site" title="Open site" /></a></td>
						</tr>
					<tr>
						<td><a href="#"><img title="Add to Homepage" src="/images/icons/add.png" width="16" height="16"></a></td>
						<td>University Of York <a href="http://www.google.co.uk/" target="_blank"><img src="/images/icons/link_go.png" alt="Open site" title="Open site" /></a></td>
						</tr>
					<tr>
						<td><a href="#"><img title="Add to Homepage" src="/images/icons/add.png" width="16" height="16"></a></td>
						<td>York University Students' Union <a href="http://www.google.co.uk/" target="_blank"><img src="/images/icons/link_go.png" alt="Open site" title="Open site" /></a></td>
						</tr>
					<tr>
						<td><a href="#"><img title="Add to Homepage" src="/images/icons/add.png" width="16" height="16"></a></td>
						<td>Facebook <a href="http://www.google.co.uk/" target="_blank"><img src="/images/icons/link_go.png" alt="Open site" title="Open site" /></a></td>
						</tr>
					<tr>
						<td><a href="#"><img title="Add to Homepage" src="/images/icons/add.png" width="16" height="16"></a></td>
						<td>Hotmail <a href="http://www.google.co.uk/" target="_blank"><img src="/images/icons/link_go.png" alt="Open site" title="Open site" /></a></td>
						</tr>
					<tr>
						<td><a href="#"><img title="Add to Homepage" src="/images/icons/add.png" width="16" height="16"></a></td>
						<td>Google UK <a href="http://www.google.co.uk/" target="_blank"><img src="/images/icons/link_go.png" alt="Open site" title="Open site" /></a></td>
						</tr>
					<tr>
						<td><a href="#"><img title="Add to Homepage" src="/images/icons/add.png" width="16" height="16"></a></td>
						<td>Wikipedia <a href="http://www.google.co.uk/" target="_blank"><img src="/images/icons/link_go.png" alt="Open site" title="Open site" /></a></td>
						</tr>	

					<tr>
						<td><a href="#"><img title="Add to Homepage" src="/images/icons/add.png" width="16" height="16"></a></td>
						<td>Google <a href="http://www.google.co.uk/" target="_blank"><img src="/images/icons/link_go.png" alt="Open site" title="Open site" /></a></td>
						</tr>
					<tr>
						<td><a href="#"><img title="Add to Homepage" src="/images/icons/add.png" width="16" height="16"></a></td>
						<td>University Of York <a href="http://www.google.co.uk/" target="_blank"><img src="/images/icons/link_go.png" alt="Open site" title="Open site" /></a></td>
						</tr>
					<tr>
						<td><a href="#"><img title="Add to Homepage" src="/images/icons/add.png" width="16" height="16"></a></td>
						<td>York University Students' Union <a href="http://www.google.co.uk/" target="_blank"><img src="/images/icons/link_go.png" alt="Open site" title="Open site" /></a></td>
						</tr>
					<tr>
						<td><a href="#"><img title="Add to Homepage" src="/images/icons/add.png" width="16" height="16"></a></td>
						<td>Facebook <a href="http://www.google.co.uk/" target="_blank"><img src="/images/icons/link_go.png" alt="Open site" title="Open site" /></a></td>
						</tr>
					<tr>
						<td><a href="#"><img title="Add to Homepage" src="/images/icons/add.png" width="16" height="16"></a></td>
						<td>Hotmail <a href="http://www.google.co.uk/" target="_blank"><img src="/images/icons/link_go.png" alt="Open site" title="Open site" /></a></td>
						</tr>
					<tr>
						<td><a href="#"><img title="Add to Homepage" src="/images/icons/add.png" width="16" height="16"></a></td>
						<td>Google UK <a href="http://www.google.co.uk/" target="_blank"><img src="/images/icons/link_go.png" alt="Open site" title="Open site" /></a></td>
						</tr>
					<tr>
						<td><a href="#"><img title="Add to Homepage" src="/images/icons/add.png" width="16" height="16"></a></td>
						<td>Wikipedia <a href="http://www.google.co.uk/" target="_blank"><img src="/images/icons/link_go.png" alt="Open site" title="Open site" /></a></td>
						</tr>	
			    </tbody>
			</table>
		</div>
	</div>
</div>
