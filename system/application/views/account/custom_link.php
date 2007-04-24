<div id="RightColumn">
	<h2 class="first">Link Nomination</h2>
	<div class="Entry">
			You may nominate your link for addition to the list. Please note that we do not accept personal homepages or other minor sites.
	</div>
</div>
<div id="MainColumn">
	<div class="BlueBox">
		<h2>Add Custom Link</h2>
		To add a link that is not in the list, enter it here.
		<form action="customlink" method="post">
		<fieldset>
			<label for="lname"> Link Name: </label>
			<input type="text" id="lname" name="lname" value="" default />
			<br />
			<label for="lurl"> Link URL: </label>
			<input type="text" id="lurl" name="lurl" value="http://" />
			<br />
			<label for="lnominate"> Nominate Link: </label>
			<input type="checkbox" id="lnominate" name="lnominate" />
			<br />
			<input type="button" value="Back" class="button" onClick="window.location='/account/links';"> <input type="submit" value="Create Link" class="button"> 
		</fieldset>
		</form>
	</div>
</div>
