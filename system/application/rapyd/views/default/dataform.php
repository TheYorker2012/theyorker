
<?php echo $form_begin?>

<table style="margin:0;width:100%;border-collapse:collapse;padding:0;">
  <tr>
    <td>
<?if($title!=""):?>
      <table style="margin:0;width:100%;border-collapse:collapse;padding:0;">
        <tr>
          <td class="mainheader"><?php echo $title?></td>
          <td class="mainheader" align="right"><?php echo $container_tr?></td>
        </tr>
      </table>
<?endif;?>
      <div class="mainbackground" style="padding:2px;clear:both">
      <div class="alert"><?php echo $error_string?></div>
      <table style="margin:0;width:98%;">
<?if (isset($groups)):?>
<?foreach ($groups as $group)://groups?>
<?if ($group["group_name"] != "ungrouped"):?>
        <tr>
          <td colspan="2" class="little"><?php echo $group["group_name"]?></td>
        </tr>
<?endif?>
<?foreach ($group["fields"] as $field)://fields?>
<?if ($field["status"] == "hidden"):?>
<?php echo $field["field"]?>
<?elseif (($field["type"] == "container")||($field["type"] == "iframe")):?>
        <tr <?php echo $field["field_tr"]?>>
          <td colspan="2"><?php echo $field["field"]?></td>
        </tr>
<?else:?>
        <tr <?php echo $field["field_tr"]?>>
          <td style="width:120px;padding:1px;" class="littletableheader"><?php echo $field["label"]?></td>
          <td style="padding:1px;" class="littletablerow" <?php echo $field["field_td"]?>><?php echo $field["field"]?>&nbsp;</td>
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
          <td colspan="2" class="tablerow"><?php echo $message?></td>
        </tr>
<?endif;?>
      </table>
      <?php echo $form_scripts?>
      </div>
      <div class="mainfooter">
        <div>
          <div style="float:left"><?php echo $container_bl?></div>
          <div style="float:right"><?php echo $container_br?></div>
        </div><div style="clear:both;"></div>
      </div>
    </td>
  </tr>
</table>
<?php echo $form_end?>
