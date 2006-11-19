<?php

//These variables will need to be passed from the controller
$term = 2;
$weekno = 5;
$day = 1;

?>

<br>
<table width="780" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center" width="150" valign="top">
		<table width="150">
		<tr>
		<td align="center" height="40" valign="middle">
		<big><b>Speakers</b></big> [Pic] <br>
		Speakers from inside and outside the university, open for anyone to attend.<br><br>
		<a href="http://www.york.ac.uk/depts/phil/gsp/seminar.htm">Web Site</a><br><br>
		</td>
		</tr>
		<?php
		for($weekcounter = 4; $weekcounter <= 10; $weekcounter++ )
		{
			$weekclass = "calendarweek";
			if ($weekno == $weekcounter) {
				$weekclass = "calendarweekselect";
			}
			
			?>
			<tr><td class="<?php echo $weekclass ?>" onClick="">Week <?php echo $weekcounter ?></td></tr>
			<?php
		}
		?>
		<tr>
		<td align="center" height="40" valign="middle">
		<a href="#">&lt;&lt;</a> Autumn Term <a href="#">&gt;&gt;</a>
		</td>
		</tr>
		</table>
	</td>
	<td align="center" valign="top">
		<?php 
		if ($subcontent_view=='') {
			$subcontent_view = 'listings/listing/week_view_detailed';
		}
		$this->load->view($subcontent_view);
		?>
	</td>
  </tr>
</table>