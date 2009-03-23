<?php

/**
 * @file views/admin/permissions/index.php
 * @brief Permissions interface index.
 * @author James Hogan <james_hogan@theyorker.co.uk>
 *
 * @param $permissionDescriptions
 * @param $rolePermissions
 * @param $implicitRoles
 * @param $userRoles
 * @param $userNames
 * @param $xml_whats_this
 * @param $xml_info_permissions
 * @param $xml_info_roles
 * @param $xml_info_users
 */

?>

<div id="RightColumn">
	<?php
	echo($xml_whats_this);
	?>
	
	<h2>Save</h2>
	<form class="form">
		<fieldset>
			<input	class="button" type="button" value="Save"
					onclick="saveAllPermissions();" />
			<input	class="button" type="button" value="View Changes"
					onclick="viewPermissionChanges();" />
		</fieldset>
	</form>
</div>

<div id="MainColumn">

<script type="text/javascript">
// <![CDATA[
	// Initialise the permissions data
	setPermissionData(
		<?php echo(js_literalise($permissionDescriptions)); ?>,
		<?php echo(js_literalise($rolePermissions)); ?>,
		<?php echo(js_literalise($implicitRoles)); ?>,
		<?php echo(js_literalise($userRoles)); ?>,
		<?php echo(js_literalise($userNames)); ?>
	);
// ]]>
</script>

<noscript>
	<div class="BlueBox">
		<h2>Javascript Disabled</h2>
		<p>Please enable Javascript in your browser to be able to use this interface.</p>
	</div>
</noscript>

<div id="permissionsBox" class="BlueBox">
<div>
	<?php
	echo($xml_info_permissions);
	?>
	<?php
	$last_prefix = '';
	foreach ($permissionDescriptions as $permission => $description) {
		$new_prefix = substr($permission, 0, strpos($permission.'_', '_'));
		if ($last_prefix != $new_prefix) {
			if ($last_prefix !== '') {
				?></div><?php
			}
			?><hr /><?php
			// Section title, which allows showing / hiding of permissions
			?><div	id="permissionCategory-<?php echo(xml_escape($new_prefix)); ?>"
					class="permissionCategoryHead"
					onclick="permissionCategoryClick(<?php echo(xml_escape(js_literalise($new_prefix))); ?>)"
				>
				<div class="name"><?php echo(xml_escape($new_prefix)); ?></div>
				<div class="description">click to expand or hide</div>
			</div>
			<div	id="permissions-<?php echo(xml_escape($new_prefix)); ?>"
					class="permissionCategory hidden"><?php
			$last_prefix = $new_prefix;
		}
	?>
	<div	id="permission-<?php echo(xml_escape($permission)); ?>"
			class="permission"
		>
		<div	class="action"
				onclick="permissionSecondary(<?php echo(xml_escape(js_literalise($permission))) ?>)"
			></div>
		<div onclick="permissionClick(<?php echo(xml_escape(js_literalise($permission))) ?>)" >
			<div	name="permission-<?php echo(xml_escape($permission)); ?>"
					class="name"
					onmouseover="permissionMouseOver(<?php echo(xml_escape(js_literalise($permission))) ?>)"
					onmouseout="permissionMouseOut(<?php echo(xml_escape(js_literalise($permission))) ?>)"
					>
				<?php echo(xml_escape($permission)); ?>
			</div>
			<div class="description">
				<?php echo(xml_escape($description)); ?>
			</div>
		</div>
	</div>
	<?php }
	if ($last_prefix !== '') {
		?></div><?php
	}
	?>
	<hr />
</div>
</div>

<div id="rolesBox" class="BlueBox">
<div>
	<?php
	echo($xml_info_roles);
	?>
	<div id="rolesList">
		<hr />
		<?php foreach ($rolePermissions as $role => $permissions) { ?>
		<div	id="role-<?php echo(xml_escape($role)); ?>"
				class="role<?php if (!isset($implicitRoles[$role])) { ?> explicit<?php }?>"
			>
			<div	class="action"
					onclick="roleSecondary(<?php echo(xml_escape(js_literalise($role))) ?>)"
				></div>
			<div onclick="roleClick(<?php echo(xml_escape(js_literalise($role))) ?>)" >
				<div	name="role-<?php echo(xml_escape($role)); ?>"
						class="name"
						onmouseover="roleMouseOver(<?php echo(xml_escape(js_literalise($role))) ?>)"
						onmouseout="roleMouseOut(<?php echo(xml_escape(js_literalise($role))) ?>)"
						>
					<?php echo(xml_escape($role)); ?>
				</div>
				<div id="role-<?php echo(xml_escape($role)); ?>-permissions" class="permissions">
					<?php foreach ($permissions as $permission) { ?>
					<div	name="permission-<?php echo(xml_escape($permission)); ?>"
							class="permission"
							onmouseover="permissionMouseOver(<?php echo(xml_escape(js_literalise($permission))) ?>)"
							onmouseout="permissionMouseOut(<?php echo(xml_escape(js_literalise($permission))) ?>)"
							>
						<?php echo(xml_escape($permission)); ?>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
		<hr />
		<?php } ?>
	</div>
	<form class="form">
		<fieldset>
			<label for="newRoleName">New Role:</label>
			<input type="text" id="newRoleName" name="newRoleName" value="" />
			<input	class="button" type="button" value="Create"
					onclick="createNewRole('newRoleName');"	/>
		</fieldset>
	</form>
</div>
</div>

<div id="usersBox" class="BlueBox">
<div>
	<?php
	echo($xml_info_users);
	?>
	<hr />
	<?php foreach ($userRoles as $user => $roles) { ?>
	<div	id="user-<?php echo(xml_escape($user)); ?>"
			class="user"
		>
		<div	class="action"
				onclick="userSecondary(<?php echo(xml_escape(js_literalise($user))) ?>)"
			></div>
		<div onclick="userClick(<?php echo(xml_escape(js_literalise($user))) ?>)" >
			<div class="name">
				<?php echo(xml_escape($user.' - '.$userNames[$user])); ?>
			</div>
			<div id="user-<?php echo(xml_escape($user)); ?>-roles" class="roles">
				<?php foreach ($roles as $role) { ?>
				<div	name="role-<?php echo(xml_escape($role)); ?>"
						class="role"
						onmouseover="roleMouseOver(<?php echo(xml_escape(js_literalise($role))) ?>)"
						onmouseout="roleMouseOut(<?php echo(xml_escape(js_literalise($role))) ?>)"
						>
					<?php echo(xml_escape($role)); ?>
				</div>
				<?php } ?>
			</div>
		</div>
	</div>
	<hr />
	<?php } ?>
</div>
</div>

</div>