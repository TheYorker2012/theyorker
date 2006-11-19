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
		<td align="center" height="100" valign="middle">
		<div class="calendarchannels">
		<form>
		<big><u>Academic</u> <input type="checkbox"></big>
		<table width="130">
		<tr><td width="20"><input type="checkbox" checked></td><td><div class="calendarchannel">Theory of Computing</div></td></tr>	
		<tr><td><input type="checkbox" checked></td><td><div class="calendarchannel">Operating Systems</div></td></tr>	
		<tr><td><input type="checkbox" checked></td><td><div class="calendarchannel">Chips to Systems</div></td></tr>	
		<tr><td><input type="checkbox" checked></td><td><div class="calendarchannel">Modelling and System Design</div></td></tr>	
		</table>
		<big><u>Societies</u> <input type="checkbox"></big>
		<table width="130">
		<tr><td width="20"><input type="checkbox" ></td><td><div class="calendarchannel">DanceSport</div></td></tr>
		<tr><td><input type="checkbox" checked></td><td><div class="calendarchannel">The Yorker</div></td></tr>
		</table>
		<big><u>Atheletics Union</u> <input type="checkbox"></big>
		<table width="130">
		<tr><td width="20"><input type="checkbox" ></td><td><div class="calendarchannel">Poll Exercise</div></td></tr>
		<tr><td><input type="checkbox" ></td><td><div class="calendarchannel">Rugby</div></td></tr>
		</table>
		<big><u>Venues</u> <input type="checkbox"></big>
		<table width="130">
		<tr><td width="20"><input type="checkbox" ></td><td><div class="calendarchannel">Toffs</div></td></tr>
		</table>
		</form>
		</div>
		</td>
		</tr>
		<tr>
		<td align="center" height="40" valign="middle">
		<small><a href="#">&lt;&lt;</a> Autumn Term <a href="#">&gt;&gt;</a></small>
		</td>
		</tr>
		<tr>
		<?php
		for($i = 1; $i <= 10; $i++ )
		{
			$weekclass = "calendarweek";
			if ($weekno == $i) {
				$weekclass = "calendarweekselect";
			}
			
			?>
			<tr><td class="<?php echo $weekclass ?>" onClick="location.href='<?php echo site_url('/prototype/sframe/listings/calendar/calendar/week_view')?>';">Week <?php echo $i ?></td></tr>
			<?php
		}
		?>
		</table>
	</td>
	<td align="center" valign="top">
		<?php 
		if ($subcontent_view=='') {
			$subcontent_view = 'listings/calendar/week_view';
		}
		$this->load->view($subcontent_view);
		?>
	</td>
  </tr>
</table>