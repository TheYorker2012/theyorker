<?php

/**
 * @file views/admin/permissions/export_roles.php
 * @brief Export role permissions.
 * @author James Hogan <james_hogan@theyorker.co.uk>
 *
 * @param $textRoles
 * @param $xml_info
 */

?>

<div class="BlueBox">
	<?php
	echo($xml_info);
	?>
	<form method="post">
		<fieldset>
			<textarea cols="75" rows="20" name="role_permissions"><?php echo(xml_escape($textRoles)); ?></textarea>
			<input class="button" type="submit" value="Save" />
		</fieldset>
	</form>
</div>