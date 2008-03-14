<?php

/**
 * @file views/admin/permissions/index.php
 * @brief Permissions interface index.
 * @author James Hogan <james_hogan@theyorker.co.uk>
 */

$permissionDescriptions = $this->config->Item('permissions');

$rolePermissions = array(
	'LEVEL_OFFICER' => array(
		'IRC_CHAT',
		'SOMETHING_ELSE',
	),
	'LEVEL_EDITOR' => array(
	),
	'LEVEL_ADMIN' => array(
	),
	'MODERATOR' => array(
		'COMMENT_MODIFY',
		'COMMENT_DELETE',
	),
);

$roleExplicit = array(
	'LEVEL_OFFICER' => false,
	'LEVEL_EDITOR'  => false,
	'LEVEL_ADMIN'   => false,
);

$userRoles = array(
	'jh559' => array(
		'LEVEL_OFFICER',
		'LEVEL_EDITOR',
		'LEVEL_ADMIN',
		'MODERATOR',
	),
	'cdt502' => array(
		'LEVEL_OFFICER',
		'LEVEL_EDITOR',
		'LEVEL_ADMIN',
	),
	'dta501' => array(
		'LEVEL_OFFICER',
		'LEVEL_EDITOR',
	),
	'rm500' => array(
		'LEVEL_OFFICER',
	),
);

$userNames = array(
	'jh559'  => 'James Hogan',
	'cdt502' => 'Chris Travis',
	'dta501' => 'Dan Ashby',
	'rm500'  => 'Richard Mitchell',
);

?>

<div class="RightToolbar">
	<h4>What's this?</h4>
	<p>nothing</p>
</div>

<script type="text/javascript">
// <![CDATA[
	// Initialise the permissions data
	setPermissionData(
		<?php echo(js_literalise($permissionDescriptions)); ?>,
		<?php echo(js_literalise($rolePermissions)); ?>,
		<?php echo(js_literalise($roleExplicit)); ?>,
		<?php echo(js_literalise($userRoles)); ?>,
		<?php echo(js_literalise($userNames)); ?>
	);
// ]]>
</script>

<div id="permissionsBox" class="grey_box">
	<h2>Permissions</h2>
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
		<?php foreach ($rolePermissions as $role => $permissions) { ?>
		<div	id="role-<?php echo(xml_escape($role)); ?>"
				class="role<?php if (!isset($roleExplicit[$role]) || $roleExplicit[$role]) { ?> explicit<?php }?>"
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



