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
	<p>Users can have a number of roles, each with a group of associated permissions.</p>
	<ul>
		<li>Click on a permission to adjust which roles contain it.</li>
		<li>Click on a role to adjust which permissions it contains and which users have the role.</li>
		<li>Click on a user to adjust which roles they have.</li>
	</ul>
	<p>	Remember that the purpose of roles is to represent groups of users with common sets of permissions.
		You can create new roles if appropriate using the "New Role" box.</p>
	<p>Once a permission, role or user is selected you can toggle associated permissions, roles or users by clicking the boxes on the left.</p>
	
	<h4>Save</h4>
	<form class="form">
		<fieldset>
			<input	class="button" type="button" value="Save"
					onclick="saveAllPermissions();" />
			<input	class="button" type="button" value="View Changes"
					onclick="viewPermissionChanges();" />
		</fieldset>
	</form>
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
	<p>	Permissions represent actions that can be performed.
		A permission can belong to a role, so that anybody who has the role has the permission.</p>
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
	<p>Each role represents a group of related permissions common to all users with that role.</p>
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
	<p>	Users can have a number of roles which determine which permissions they have.</p>
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
