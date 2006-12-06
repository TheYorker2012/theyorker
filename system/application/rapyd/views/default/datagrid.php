<table style="margin:0;width:100%;border-collapse:collapse;padding:0;">
  <tr>
    <td>
      <table style="margin:0;width:100%;border-collapse:collapse;padding:0;">
        <tr>
          <td class="mainheader"><?=$title?></td>
          <td class="mainheader" align="right"><?=$container_tr?></td>
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
                <td class="tableheader_clean"><?=$column["label"]?></td>
                <td class="tableheader_clean" style="width:28px">
                  <a href="<?=$column["orderby_asc_url"]?>"><img src="<?=RAPYD_IMAGES?>orderbyasc.gif" border="0"></a><a href="<?=$column["orderby_desc_url"]?>"><img src="<?=RAPYD_IMAGES?>orderbydesc.gif" border="0"></a>
                </td>
              </tr>
            </table>
          </td>
<?elseif ($column["type"] == "clean"):?>
          <td <?=$column["attributes"]?>><?=$column["label"]?></td>
<?elseif (in_array($column["type"], array("normal"))):?>
          <td class="tableheader" <?=$column["attributes"]?>><?=$column["label"]?></td>
<?endif;?>
<?endforeach;//table-header?>
        </tr>
<?if (count($rows)>0)://table-rows?>
  <?$rowcount=0;?>
<?foreach ($rows as $row):?>
  <?$rowcount++;?>
        <tr <? if($rowcount % 2){ echo 'class="odd"';}else{ echo 'class="even"';} ?>>
<?foreach ($row as $cell):?>
<?if ($cell["type"] == "detail"):?>
          <td <?=$cell["attributes"]?> class="littletablerow" ><a href="<?=$cell["link"]?>"><?=$cell["field"]?><img src="<?=RAPYD_IMAGES?>elenco.gif" width="16" height="16" border="0" align="absmiddle" /></a></td>
<?elseif ($cell["type"] == "clean"):?>
          <td <?=$cell["attributes"]?>><?=$cell["field"]?></td>
<?else:?>
          <td <?=$cell["attributes"]?> class="littletablerow"><?=$cell["field"]?>&nbsp;</td>
<?endif;?>
<?endforeach;?>
        </tr>
<?endforeach;?>
<?endif;//table-rows?>
      </table>
      </div>
      <div class="mainbackground"><div class="pagenav"><?=$pager?></div></div>
      <div class="mainfooter">
        <div>
          <div style="float:left"><?=$container_bl?></div>
          <div style="float:right"><?=$container_br?></div>
        </div><div style="clear:both;"></div>
      </div>

    </td>
  </tr>
</table>