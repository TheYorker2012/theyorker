<script type='text/javascript'>
var data = new Array();
data[0] = "<img src='/images/prototype/news/test/camp_pool_big.jpg' alt='' title='' /><h3>Swimming Pool</h3><p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Integernec nunc. Integer ligula justo, suscipit a, sollicitudin id, placeratnon, dolor. Phasellus cursus pellentesque lorem. Nulla dui.";
data[1] = "<img src='/images/prototype/news/test/camp_guns_big.jpg' alt='' title='' /><h3>Anti-Arms</h3><p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Integernec nunc. Integer ligula justo, suscipit a, sollicitudin id, placeratnon, dolor. Phasellus cursus pellentesque lorem. Nulla dui.";
data[2] = "<img src='/images/prototype/news/test/camp_ants_big.jpg' alt='' title='' /><h3>Kill All Ants</h3><p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Integernec nunc. Integer ligula justo, suscipit a, sollicitudin id, placeratnon, dolor. Phasellus cursus pellentesque lorem. Nulla dui.";
data[3] = "<img src='/images/prototype/news/test/camp_budd_big.jpg' alt='' title='' /><h3>Buddhist Campus</h3><p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Integernec nunc. Integer ligula justo, suscipit a, sollicitudin id, placeratnon, dolor. Phasellus cursus pellentesque lorem. Nulla dui.";

function showCamp (id,colour) {
	document.getElementById('camp_0').style.backgroundColor = '#000';
	document.getElementById('camp_1').style.backgroundColor = '#000';
	document.getElementById('camp_2').style.backgroundColor = '#000';
	document.getElementById('camp_3').style.backgroundColor = '#000';
	document.getElementById('camp_' + id).style.backgroundColor = colour;
	document.getElementById('info').style.backgroundColor = colour;
	document.getElementById('info').innerHTML = data[id] + "<div style='clear:both;'></div>";
}
</script>
<style>
#campaign_container {
	width: 400px;
	background-color: #000;
	padding: 5px 5px 5px 0;
}

#campaign_container #options {
	width: 82px;
	float: left;
}

#campaign_container #options .campaign {
	width: 62px;
	padding: 5px 10px 5px 10px;
	margin: 0;
}

#campaign_container #options .campaign img {
	border: 1px #000 solid;
	padding: 0;
	margin: 0;
}

#campaign_container #info {
	width: 303px;
	margin: 0 5px 5px 0;	
	padding-left: 5px;
	float: left;
	background-color: #ff6a00;
	height: 228px;
}

#campaign_container #info img {
	float: right;
	padding: 0;
	margin: 0;
}

#campaign_container #info p, #campaign_container #info h3 {
	float: left;
	width: 145px;
	color: #000;
	padding: 0;
	margin: 0;
}
</style>


<div id='campaign_container'>
	<div id='options'><div
                class='campaign' id='camp_0' onMouseOver="showCamp('0','#ff6a00');" style='background-color: #ff6a00;'><img src='/images/prototype/news/test/camp_pool.jpg' alt='' title='' /></div><div
                class='campaign' id='camp_1' onMouseOver="showCamp('1','#08c0ef');"><img src='/images/prototype/news/test/camp_guns.jpg' alt='' title='' /></div><div
                class='campaign' id='camp_2' onMouseOver="showCamp('2','#93969a');"><img src='/images/prototype/news/test/camp_ants.jpg' alt='' title='' /></div><div
                class='campaign' id='camp_3' onMouseOver="showCamp('3','#ffffff');"><img src='/images/prototype/news/test/camp_budd.jpg' alt='' title='' /></div>
	</div>
	<div id='info'><img src='/images/prototype/news/test/camp_pool_big.jpg' alt='' title='' /><h3>Swimming Pool</h3><p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Integernec nunc. Integer ligula justo, suscipit a, sollicitudin id, placeratnon, dolor. Phasellus cursus pellentesque lorem. Nulla dui.</div>
	<div style='clear:both;'></div>
</div>
