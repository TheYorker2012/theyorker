<table style="margin:0;width:100%;border-collapse:collapse;padding:0;">
  <tr>
    <td>
      <table style="margin:0;width:100%;border-collapse:collapse;padding:0;">
        <tr>
          <td class="mainheader"><?php echo $title?></td>
          <td class="mainheader" align="right"><?php echo $container_tr?></td>
        </tr>
      </table>

      <div class="mainbackground" style="padding:2px;clear:both;">
      <table width="100%" cellpadding="1">
        <tr>
<?foreach ($headers as $column)://table-header?>
<?if (in_array($column["type"], array("orderby","detail"))):?>
          <td class="tableheader">
            <table style="width:100%; border-collapse:collapse;">
              <tr>
                <td class="tableheader_clean"><?php echo $column["label"]?></td>
                <td class="tableheader_clean" style="width:28px">
                  <a href="<?php echo $column["orderby_asc_url"]?>"><img src="<?php echo RAPYD_IMAGES?>orderbyasc.gif" border="0"></a><a href="<?php echo $column["orderby_desc_url"]?>"><img src="<?php echo RAPYD_IMAGES?>orderbydesc.gif" border="0"></a>
                </td>
              </tr>
            </table>
          </td>
<?elseif ($column["type"] == "clean"):?>
          <td <?php echo $column["attributes"]?>><?php echo $column["label"]?></td>
<?elseif (in_array($column["type"], array("normal"))):?>
          <td class="tableheader" <?php echo $column["attributes"]?>><?php echo $column["label"]?></td>
<?endif;?>
<?endforeach;//table-header?>
        </tr>
<?if (count($rows)>0)://table-rows?>
  <?$rowcount=0;?>
<?foreach ($rows as $row):?>
  <?$rowcount++;?>
        <tr <?php if($rowcount % 2){ echo 'class="odd"';}else{ echo 'class="even"';} ?>>
<?foreach ($row as $cell):?>
<?if ($cell["type"] == "detail"):?>
          <td <?php echo $cell["attributes"]?> class="littletablerow" ><a href="<?php echo $cell["link"]?>"><?php echo $cell["field"]?><img src="<?php echo RAPYD_IMAGES?>elenco.gif" width="16" height="16" border="0" align="absmiddle" /></a></td>
<?elseif ($cell["type"] == "clean"):?>
          <td <?php echo $cell["attributes"]?>><?php echo $cell["field"]?></td>
<?else:?>
          <td <?php echo $cell["attributes"]?> class="littletablerow"><?php echo $cell["field"]?>&nbsp;</td>
<?endif;?>
<?endforeach;?>
        </tr>
<?endforeach;?>
<?endif;//table-rows?>
      </table>
      </div>
      <div class="mainbackground"><div class="pagenav"><?php echo $pager?></div></div>
      <div class="mainfooter">
        <div>
          <div style="float:left"><?php echo $container_bl?></div>
          <div style="float:right"><?php echo $container_br?></div>
        </div><div style="clear:both;"></div>
      </div>

    </td>
  </tr>
</table>