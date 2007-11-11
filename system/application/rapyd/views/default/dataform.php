
<?=$form_begin?>

<table style="margin:0;width:100%;border-collapse:collapse;padding:0;">
  <tr>
    <td>
<?if($title!=""):?>
      <table style="margin:0;width:100%;border-collapse:collapse;padding:0;">
        <tr>
          <td class="mainheader"><?=$title?></td>
          <td class="mainheader" align="right"><?=$container_tr?></td>
        </tr>
      </table>
<?endif;?>
      <div class="mainbackground" style="padding:2px;clear:both">
      <div class="alert"><?=$error_string?></div>
      <table style="margin:0;width:98%;">
<?if (isset($groups)):?>
<?foreach ($groups as $group)://groups?>
<?if ($group["group_name"] != "ungrouped"):?>
        <tr>
          <td colspan="2" class="little"><?=$group["group_name"]?></td>
        </tr>
<?endif?>
<?foreach ($group["fields"] as $field)://fields?>
<?if ($field["status"] == "hidden"):?>
<?=$field["field"]?>
<?elseif (($field["type"] == "container")||($field["type"] == "iframe")):?>
        <tr <?=$field["field_tr"]?>>
          <td colspan="2"><?=$field["field"]?></td>
        </tr>
<?else:?>
        <tr <?=$field["field_tr"]?>>
          <td style="width:120px;padding:1px;" class="littletableheader"><?=$field["label"]?></td>
          <td style="padding:1px;" class="littletablerow" <?=$field["field_td"]?>><?=$field["field"]?>&nbsp;</td>
        </tr>
<?endif;?>
<?endforeach;//fields?>
<?if ($group["group_name"] != "ungrouped"):?>
        <tr>
          <td colspan="2"></td>
        </tr>
<?endif?>
<?endforeach;//groups?>
<?endif;?>
<?if(isset($message)):?>
        <tr>
          <td colspan="2" class="tablerow"><?=$message?></td>
        </tr>
<?endif;?>
      </table>
      <?=$form_scripts?>
      </div>
      <div class="mainfooter">
        <div>
          <div style="float:left"><?=$container_bl?></div>
          <div style="float:right"><?=$container_br?></div>
        </div><div style="clear:both;"></div>
      </div>
    </td>
  </tr>
</table>
<?=$form_end?>
