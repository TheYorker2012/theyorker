	<h2>information</h2>
	<form class="form">
		<fieldset>
			<label for="title">Title: </label>
				<input type="text" name="title" value="man run over by tank" /><br />
			<label for="date">Date: </label>
				<input type="date" name="date" value="03/02/2007" /><br />
			<label for="photographer">Photographer: </label>
				<select name="photographer">
					<option>Steve</option>
					<option>Your Face</option>
					<option>Your Mum</option>
					<option>Dan</option>
					<option selected>Stephan Greebs</option>
					<option>Ian Cook</option>
				</select><br />
			<label for="tags">Tags: </label>
				<select multiple size="8" name="tags">
					<option>Stedsgve</option>
					<option>Youasdr Face</option>
					<option>YoDur Mum</option>
					<option>Dadn</option>
					<option>SteFSGjphan Greebs</option>
					<option selected>Ian Csdfook</option>
					<option>Dasfhn</option>
					<option>Stepafhan Greebs</option>
					<option>Ian fdhCook</option>
				</select><br />
			<label></label>
				<a href="#">+ Add More Tags</a><br />
			<label></label>
				<a href="#">- Delete Selected Tags</a><br />
			<label>Home Feature: </label>
				<input type='checkbox' name='onfrontpage' /><br />
			<label>Hidden: </label>
				<input type='checkbox' name='hidden' />
</div>
<div class="grey_box">
	<h2>previews</h2>
	Small Thumbnail (40 x 49)<br />
	<img src="/images/prototype/news/thumb2.jpg" /><br /><br />
	Medium Thumbnail (200 x 150)<br />
	<img src="/images/prototype/news/thumb1.jpg" /><br /><br />
	Large Image (400 x 350)<br />
	<img src="/images/prototype/news/thumb1.jpg" width="400px" /><br /><br />
	Full Size (n x n where n &gt;= 90)<br />
	<a href="#">Click here to view</a><br /><br />
	Not happy with these thumbnails? <a href="#">Click here</a> to rethumbnail.
