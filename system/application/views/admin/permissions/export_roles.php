<?php

/**
 * @file views/admin/permissions/export_roles.php
 * @brief Export role permissions.
 * @author James Hogan <james_hogan@theyorker.co.uk>
 *
 * @param $textRoles
 */

?>

<div class="BlueBox">
	<h2>Import/Export</h2>
	<p>	WARNING: using this form will override all role permissions.
		Please make sure you know what you are doing before using this.
		</p>
	<form method="post">
		<fieldset>
			<textarea cols="75" rows="20" name="role_permissions"><?php echo(xml_escape($textRoles)); ?></textarea>
			<input class="button" type="submit" value="Save" />
		</fieldset>
	</form>
</div>