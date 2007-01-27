<p>Some useful text should go here:-</p>
<?php
foreach($data as $d) {
	echo $d;
}
?>

<h2>Dynamic image test</h2>
<p>
	Test of dynamically changing images or removing & re-applying the cropper
</p>
<div id="previewArea-0"></div>
<div id="previewArea-1"></div>

<div id="testWrap">
	<img src="images/photos/1.jpg" alt="test image" id="testImage" width="500" height="333" />
</div>
<form id="pictureCrop" action="javascript:void(null);" onsubmit="submitPicture();">
	<p>
		<label for="imageChoice">image:</label>
		<select name="imageChoice" id="imageChoice">
			<option value="castle.jpg|500|333|0">Castle0</option>
			<option value="castle.jpg|500|333|1">Castle1</option>
			<option value="poppy.jpg|311|466|0">Flower0</option>
		</select>
	</p>

	<p>
		<input type="button" id="removeCropper" value="Remove Cropper" />
		<input type="button" id="resetCropper" value="Reset Cropper" />
		<input id="submitButton" type="submit" value="Save"/>
	</p>


	<p>
		<label for="x1">x1:</label>
		<input type="text" name="x1" id="x1" />
	</p>
	<p>
		<label for="y1">y1:</label>
		<input type="text" name="y1" id="y1" />
	</p>
	<p>
		<label for="x2">x2:</label>
		<input type="text" name="x2" id="x2" />
	</p>
	<p>
		<label for="y2">y2:</label>
		<input type="text" name="y2" id="y2" />
	</p>
	<p>
		<label for="width">width:</label>
		<input type="text" name="width" id="width" />
	</p>
	<p>
		<label for="height">height</label>
		<input type="text" name="height" id="height" />
	</p>
</form>