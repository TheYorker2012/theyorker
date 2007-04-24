<div id="RightColumn">
	<h2 class="first">My Links</h2>
	<div class="Entry" id="links">
		<a href="http://www.york.ac.uk/"><img title="University Of York" src="/images/prototype/homepage/links/york.gif" width="50" height="50"></a>
		<a href="http://www.yusu.org/"><img title="York University Students' Union" src="/images/prototype/homepage/links/yusu.gif" width="50" height="50"></a>
		<a href="http://www.facebook.com/"><img title="Facebook" src="/images/prototype/homepage/links/facebook.gif" width="50" height="50"></a>
		<a href="http://www.hotmail.co.uk/"><img title="Hotmail" src="/images/prototype/homepage/links/hotmail.jpg" width="50" height="50"></a>
		<a href="http://news.bbc.co.uk/"><img title="BBC News" src="/images/prototype/homepage/links/bbc.gif" width="50" height="50"></a>
		<a href="http://www.google.co.uk/"><img title="Google UK" src="/images/prototype/homepage/links/google.jpg" width="50" height="50"></a>
		<a href="http://en.wikipedia.org/"><img title="Wikipedia" src="/images/prototype/homepage/links/wiki.png" width="50" height="50"></a>
		<a href="http://gmail.google.com/"><img title="Gmail" src="/images/prototype/homepage/links/gmail.gif" width="50" height="50"></a>
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
