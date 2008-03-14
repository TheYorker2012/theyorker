<?php

/**
 * @file views/admin/permissions/index.php
 * @brief Permissions interface index.
 * @author James Hogan <james_hogan@theyorker.co.uk>
 */

$permissionDescriptions = $this->permissions_model->getAllPermissions();
$rolePermissions = $this->permissions_model->getAllRolePermissions();
$implicitRoles = $this->permissions_model->getImplicitRoles();
list($userRoles, $userNames) = $this->permissions_model->getAllUserRoles();

// Roles might not have permissions
foreach ($userRoles as $roles) {
	foreach ($roles as $role) {
		if (!isset($rolePermissions[$role])) {
			$rolePermissions[$role] = array();
		}
	}
}


?>

<div class="RightToolbar">
	<h4>What's this?</h4>
	<p>Options for saving</p>
	<p>ajax as you edit</p>
	<p>storing changes + running through</p>
	<p>!send the lot and diff with database!</p>
</div>

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
	<div class="blue_box">
		<h2>Javascript Disabled</h2>
		<p>Please enable Javascript in your browser to be able to use this interface.</p>
	</div>
</noscript>

<div id="permissionsBox" class="grey_box">
	<h2>Permissions</h2>
	<hr />
	<?php foreach ($permissionDescriptions as $permission => $description) { ?>
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
	<hr />
	<?php } ?>
</div>

<div id="rolesBox" class="grey_box">
	<h2>Roles</h2>
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

<div id="usersBox" class="grey_box">
	<h2>Users</h2>
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



