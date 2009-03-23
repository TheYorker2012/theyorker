<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript">
//<![CDATA[
var charts = new Array();
<?php echo($data); ?>

google.load("visualization", "1", {packages:["orgchart"]});
google.setOnLoadCallback(initChart);

function initChart() {
	var container = document.getElementById('more_charts');
	var chart_img = document.createElement('img');
	chart_img.src = '/images/icons/chart_organisation.png';
	chart_img.alt = 'Chart';
	for (var x = 0; x < charts.length; x++) {
		var link = document.createElement('a');
		link.href = '/office/organisation/' + x;
		link.appendChild(chart_img.cloneNode(false));
		link.appendChild(document.createTextNode(' ' + charts[x][0][0] + ' '));
		container.appendChild(link);
	}

	drawChart(<?php echo $type; ?>);
}

function drawChart(type) {
	if (type === undefined || type === null)
		type = 0;

	var row_count = 0;
	var data = new google.visualization.DataTable();
	data.addColumn('string', 'Name');
	data.addColumn('string', 'Manager');

	function insertRow(rows, parent) {
		for (var x = 0; x < rows.length; x++) {
			data.addRows(1);
			var temp_name = '';
			if (rows[x][0] != '') {
				temp_name = '<b>' + rows[x][0] + '</b><br />';
			}
			temp_name = temp_name + rows[x][1];
			data.setCell(row_count, 0, temp_name);
			data.setCell(row_count, 1, parent);
			row_count++;
			if (rows[x][2].length > 0) {
				insertRow(rows[x][2], temp_name);
			}
		}
	}

	insertRow(charts[type], '');
	document.getElementById('org_title').innerHTML = charts[type][0][0];

	var table = new google.visualization.OrgChart(document.getElementById('chart_div'));
	table.draw(data, {allowHtml: true, color: '#F1F1F2', selectionColor: '#E1E1E2'});
}
//]]>
</script>

<div class="BlueBox">
	<h2 id="org_title">The Yorker Organisation</h2>

	<div id="more_charts" style="text-align:center;margin-bottom:2.0em;"></div>
	<div id="chart_div"></div>
</div>

<style type="text/css">
.google-visualization-orgchart-node,
.google-visualization-orgchart-lineleft,
.google-visualization-orgchart-lineright,
.google-visualization-orgchart-linebottom
{
  border-color: #999;
}
</style>