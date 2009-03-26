<?php
$width = $crossword->crossword()->grid()->width();
$height = $crossword->crossword()->grid()->height();
?>
<script type="text/javascript">
onLoadFunctions.push(function() {
	Crossword("xw", <?php echo($width); ?>, <?php echo($height); ?>);
});
</script>
<div class="BlueBox">
<h2>Crossword</h2>
<?php
$crossword->Load();
?>
</div>
