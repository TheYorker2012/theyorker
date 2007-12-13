
<?if ($title!=""):?>
<table style="margin:0;width:100%;border-collapse:collapse;padding:0;">
	<tr>
		<td class="mainheader"><?php echo $title?></td>
		<td class="mainheader" align="right"><?php echo $container_tr?></td>
	</tr>
</table>
<?endif;?>
<table width="100%" cellspacing="0" cellpadding="0">
<?if (count($trs)>0)://table-rows?>
<?foreach ($trs as $tds):?>
  <tr>
<?foreach ($tds as $td):?>
    <td <?php echo $td["attributes"]?>><?php echo $td["content"]?></td>
<?endforeach;?>
  </tr>
<?endforeach;?>
<?endif;//table-rows?>
</table>
<?if (isset($pager)):?>
<div class="mainbackground"><div class="pagenav"><?php echo $pager?></div></div>
<?endif?>
<?if ($title!=""):?>
<div class="mainfooter">
	<div>
		<div style="float:left"><?php echo $container_bl?></div>
		<div style="float:right"><?php echo $container_br?></div>
	</div><div style="clear:both;"></div>
</div>
<?endif;?>
