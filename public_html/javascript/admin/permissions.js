/**
  @file   javascript/admin/permissions.js
  @brief  Javascript for permissions interface.
  @uthor  James Hogan <james_hogan@theyorker.co.uk>

  Depends on javascript/css_classes.js
 **/

/// hash Original permissions in each role (variable).
var originalRolePermissions = {};
/// hash Original roles played by each user (variable).
var originalUserRoles = {};

/// hash Permission descriptions (constant).
var permissions = {};
/// hash Roles containing each permission (derived).
var permissionRoles = {};
/// hash Permissions in each role (variable).
var rolePermissions = {};
/// hash Implicit roles (constant).
var implicitRoles = {};
/// hash Users playing each role (derived).
var roleUsers = {};
/// hash Roles played by each user (variable).
var userRoles = {};
/// hash User's fullnames (constant).
var users = {};

/// string Selected permission.
var selectedPermission = null;
/// string Selected role.
var selectedRole = null;
/// string Selected user.
var selectedUser = null;

/// Set the permission data.
/**
  @param newPermissions     hash  Hash of permissions to descriptions.
  @param newRolePermissions hash  Hash of roles to array of permissions.
  @param newImplicitRoles   hash  Hash of roles which are implicit.
  @param newUserRoles       hash  Hash of users to array of roles.
  @param newUsers           hash  Hash of users to full names.
 **/
function setPermissionData(newPermissions, newRolePermissions, newImplicitRoles, newUserRoles, newUsers)
{
	// Directly mapped data
	permissions     = newPermissions;
	implicitRoles   = newImplicitRoles
	users           = newUsers;
	
	// Initialise permissionRoles so all permissions are displayed
	for (var permission in newPermissions) {
		if (undefined == permissionRoles[permission]) {
			permissionRoles[permission] = {};
		}
	}
	
	// rolePermissions needs hashifying
	// permissionRoles is the inverse of rolePermissions
	for (var role in newRolePermissions) {
		if (undefined == rolePermissions[role]) {
			rolePermissions[role] = {};
			originalRolePermissions[role] = {}
		}
		if (undefined == roleUsers[role]) {
			roleUsers[role] = {};
		}
		for (var ind in newRolePermissions[role]) {
			permission = newRolePermissions[role][ind];
			if (undefined == permissionRoles[permission]) {
				permissionRoles[permission] = {};
			}
			permissionRoles[permission][role] = true;
			rolePermissions[role][permission] = true;
			originalRolePermissions[role][permission] = true;
		}
	}
	
	// userRoles needs hashifying
	// roleUsers is the inverse of userRoles
	for (var user in newUserRoles) {
		if (undefined == userRoles[user]) {
			userRoles[user] = {};
			originalUserRoles[user] = {};
		}
		for (var ind in newUserRoles[user]) {
			role = newUserRoles[user][ind];
			
			if (undefined == roleUsers[role]) {
				roleUsers[role] = {};
			}
			if (undefined == rolePermissions[role]) {
				rolePermissions[role] = {};
			}
			roleUsers[role][user] = true;
			userRoles[user][role] = true;
			originalUserRoles[user][role] = true;
		}
	}
}

/// Find the differences made to the permissions.
/**
 * @return [ 0(role), 1(users) :  [ [ add list ], [ remove list ] ] ]
 */
function diffPermissions()
{
	//	role,users => [
	//		[ /* add list    */ ],
	//		[ /* remove list */ ]
	//	]
	var roles = {};
	var users = {};
	
	// find created / modified role permissions
	for (var role in rolePermissions) {
		var addList = [];
		var removeList = [];
		if (undefined == originalRolePermissions[role]) {
			// this role is entirely new
			for (var permission in rolePermissions[role]) {
				addList.push(permission);
			}
		}
		else {
			for (var permission in rolePermissions[role]) {
				if (undefined == originalRolePermissions[role][permission]) {
					// this permission is new to this role
					addList.push(permission);
				}
			}
			for (var permission in originalRolePermissions[role]) {
				if (undefined == rolePermissions[role][permission]) {
					// this permission has been removed
					removeList.push(permission);
				}
			}
		}
		if (addList.length || removeList.length) {
			roles[role] = [ addList, removeList ];
		}
	}
	
	for (var user in userRoles) {
		var addList = [];
		var removeList = [];
		for (var role in userRoles[user]) {
			if (undefined == originalUserRoles[user][role]) {
				// this role is new to this user
				addList.push(role);
			}
		}
		for (var role in originalUserRoles[user]) {
			if (undefined == userRoles[user][role]) {
				// this role has been removed
				removeList.push(role);
			}
		}
		if (addList.length || removeList.length) {
			users[user] = [ addList, removeList ];
		}
	}
	
	return [ roles, users ];
}

/// Save changes that have occurred.
function saveAllPermissions()
{
	var diff = diffPermissions();
	var roles = diff[0];
	var users = diff[1];
	
	// roles[role]['+'] = [ @permissions ]
	// roles[role]['-'] = [ @permissions ]
	// users[user]['+'] = [ @roles ]
	// users[user]['-'] = [ @roles ]
	
	var changes = false;
	var post = {};
	var adds = 0;
	var removes = 0;
	for (var role in roles) {
		for (var i = 0; i < roles[role][0].length; ++i) {
			post['roles[0]['+adds+'][r]'] = role;
			post['roles[0]['+adds+'][p]'] = roles[role][0][i];
			++adds;
			changes = true;
		}
		for (var i = 0; i < roles[role][1].length; ++i) {
			post['roles[1]['+removes+'][r]'] = role;
			post['roles[1]['+removes+'][p]'] = roles[role][1][i];
			++removes;
			changes = true;
		}
	}
	adds = 0;
	removes = 0;
	for (var user in users) {
		for (var i = 0; i < users[user][0].length; ++i) {
			post['users[0]['+adds+'][u]'] = user;
			post['users[0]['+adds+'][r]'] = users[user][0][i];
			++adds;
			changes = true;
		}
		for (var i = 0; i < users[user][1].length; ++i) {
			post['users[1]['+removes+'][u]'] = user;
			post['users[1]['+removes+'][r]'] = users[user][1][i];
			++removes;
			changes = true;
		}
	}
	if (changes) {
		var ajax = new AJAXInteraction('/admin/permissions/update', post,
			function (responseXml) {
				alert('Permissions saved.');
				location.reload(true);
			},
			function(status, statusText) {
				alert(statusText);
			}
		);
		ajax.doPost();
	}
	else {
		alert('No changes have been made to the permissions.');
	}
}

/// Display the changes that have been made.
function viewPermissionChanges()
{
	var diff = diffPermissions();
	var roles = diff[0];
	var users = diff[1];
	
	var message = '';
	message += "Role permissions:\n";
	for (var role in roles) {
		message += "\t"+role + ':';
		for (var i = 0; i < roles[role][0].length; ++i) {
			message += "\t+"+roles[role][0][i];
		}
		for (var i = 0; i < roles[role][1].length; ++i) {
			message += "\t-"+roles[role][1][i];
		}
		message += "\n";
	}
	message += "User roles:\n"
	for (var user in users) {
		message += "\t"+user + ':';
		for (var i = 0; i < users[user][0].length; ++i) {
			message += "\t+"+users[user][0][i];
		}
		for (var i = 0; i < users[user][1].length; ++i) {
			message += "\t-"+users[user][1][i];
		}
		message += "\n";
	}
	alert(message);
}

/// Deselect all selections.
function deselect()
{
	permissionsBox = document.getElementById('permissionsBox');
	rolesBox       = document.getElementById('rolesBox');
	usersBox       = document.getElementById('usersBox');
	
	CssRemove(permissionsBox, 'secondary');
	CssRemove(rolesBox,       'secondary');
	CssRemove(rolesBox,       'implicit');
	CssRemove(usersBox,       'secondary');
	
	if (selectedPermission != null) {
		CssRemove(permissionsBox, 'primary');
		CssSwap(permissionsBox, 'blue_box', 'grey_box');
		
		permissionDiv = document.getElementById('permission-'+selectedPermission);
		CssRemove(permissionDiv, 'primary');
	
		// Secondary deselect
		if (undefined != permissionRoles[selectedPermission]) {
			for (var role in permissionRoles[selectedPermission]) {
				roleDiv = document.getElementById('role-'+role);
				if (roleDiv) {
					CssRemove(roleDiv, 'secondary');
				}
				if (undefined != roleUsers[role]) {
					for (var user in roleUsers[role]) {
						userDiv = document.getElementById('user-'+user);
						if (userDiv) {
							CssRemove(userDiv, 'secondary');
						}
					}
				}
			}
		}
		
		selectedPermission = null;
	}
	else if (selectedRole != null) {
		CssRemove(rolesBox, 'primary');
		CssSwap(rolesBox, 'blue_box', 'grey_box');
		
		roleDiv = document.getElementById('role-'+selectedRole);
		CssRemove(roleDiv, 'primary');
	
		// Secondary deselect
		if (undefined != rolePermissions[selectedRole]) {
			for (var permission in rolePermissions[selectedRole]) {
				permissionDiv = document.getElementById('permission-'+permission);
				if (permissionDiv) {
					CssRemove(permissionDiv, 'secondary');
				}
			}
		}
		if (undefined != roleUsers[selectedRole]) {
			for (var user in roleUsers[selectedRole]) {
				userDiv = document.getElementById('user-'+user);
				if (userDiv) {
					CssRemove(userDiv, 'secondary');
				}
			}
		}
		
		selectedRole = null;
	}
	else if (selectedUser != null) {
		CssRemove(usersBox, 'primary');
		CssSwap(usersBox, 'blue_box', 'grey_box');
		
		userDiv = document.getElementById('user-'+selectedUser);
		CssRemove(userDiv, 'primary');
	
		// Secondary deselect
		if (undefined != userRoles[selectedUser]) {
			for (var role in userRoles[selectedUser]) {
				roleDiv = document.getElementById('role-'+role);
				if (roleDiv) {
					CssRemove(roleDiv, 'secondary');
				}
				if (undefined != rolePermissions[role]) {
					for (var permission in rolePermissions[role]) {
						permissionDiv = document.getElementById('permission-'+permission);
						if (permissionDiv) {
							CssRemove(permissionDiv, 'secondary');
						}
					}
				}
			}
		}
		
		selectedUser = null;
	}
}

/// Primary select a permission.
function permissionPrimary(permission)
{
	deselect();
	selectedPermission = permission;
	permissionsBox = document.getElementById('permissionsBox');
	rolesBox       = document.getElementById('rolesBox');
	usersBox       = document.getElementById('usersBox');
	CssSwap(permissionsBox, 'grey_box', 'blue_box');
	CssAdd(permissionsBox, 'primary');
	CssAdd(rolesBox,       'secondary');
	CssAdd(rolesBox,       'implicit');
	
	permissionDiv = document.getElementById('permission-'+permission);
	CssAdd(permissionDiv, 'primary');
	
	// Secondary select
	if (undefined != permissionRoles[permission]) {
		for (var role in permissionRoles[permission]) {
			roleDiv = document.getElementById('role-'+role);
			if (roleDiv) {
				CssAdd(roleDiv, 'secondary');
			}
			if (undefined != roleUsers[role]) {
				for (var user in roleUsers[role]) {
					userDiv = document.getElementById('user-'+user);
					if (userDiv) {
						CssAdd(userDiv, 'secondary');
					}
				}
			}
		}
	}
}

/// Primary select a role.
function rolePrimary(role)
{
	deselect();
	selectedRole = role;
	permissionsBox = document.getElementById('permissionsBox');
	rolesBox       = document.getElementById('rolesBox');
	usersBox       = document.getElementById('usersBox');
	CssSwap(rolesBox, 'grey_box', 'blue_box');
	CssAdd(permissionsBox, 'secondary');
	CssAdd(rolesBox,       'primary');
	if (undefined == implicitRoles[role]) {
		CssAdd(usersBox,       'secondary');
	}
	
	roleDiv = document.getElementById('role-'+role);
	CssAdd(roleDiv, 'primary');
	
	// Secondary select
	if (undefined != rolePermissions[role]) {
		for (var permission in rolePermissions[role]) {
			permissionDiv = document.getElementById('permission-'+permission);
			if (permissionDiv) {
				CssAdd(permissionDiv, 'secondary');
			}
		}
	}
	if (undefined != roleUsers[role]) {
		for (var user in roleUsers[role]) {
			userDiv = document.getElementById('user-'+user);
			if (userDiv) {
				CssAdd(userDiv, 'secondary');
			}
		}
	}
}

/// Primary select a user.
function userPrimary(user)
{
	deselect();
	selectedUser = user;
	permissionsBox = document.getElementById('permissionsBox');
	rolesBox       = document.getElementById('rolesBox');
	usersBox       = document.getElementById('usersBox');
	CssSwap(usersBox, 'grey_box', 'blue_box');
	CssAdd(rolesBox,       'secondary');
	CssAdd(usersBox,       'primary');
	
	userDiv = document.getElementById('user-'+user);
	CssAdd(userDiv, 'primary');
	
	// Secondary select
	if (undefined != userRoles[user]) {
		for (var role in userRoles[user]) {
			roleDiv = document.getElementById('role-'+role);
			if (roleDiv) {
				CssAdd(roleDiv, 'secondary');
			}
			if (undefined != rolePermissions[role]) {
				for (var permission in rolePermissions[role]) {
					permissionDiv = document.getElementById('permission-'+permission);
					if (permissionDiv) {
						CssAdd(permissionDiv, 'secondary');
					}
				}
			}
		}
	}
}

/// Toggle a role and permission link.
function toggleRolePermission(role, permission)
{
	var rolePermissionsDiv = document.getElementById('role-'+role+'-permissions');
	var name = 'permission-'+permission;
	
	if (undefined != rolePermissions[role][permission]) {
		delete rolePermissions[role][permission];
		delete permissionRoles[permission][role];
		
		if (rolePermissionsDiv) {
			var children = rolePermissionsDiv.childNodes;
			for (var i = 0; i < children.length; ++i) {
				if (children[i].nodeType == Node.ELEMENT_NODE && children[i].getAttribute('name') == name) {
					children[i].parentNode.removeChild(children[i]);
				}
			}
		}
	}
	else {
		rolePermissions[role][permission] = true;
		permissionRoles[permission][role] = true;
		
		if (rolePermissionsDiv) {
			var newDiv = document.createElement('div');
			newDiv.setAttribute('name', name);
			newDiv.className = 'permission';
			newDiv.onmouseover = function() { permissionMouseOver(permission); }
			newDiv.onmouseout  = function() { permissionMouseOut(permission);  }
			newDiv.appendChild(document.createTextNode(permission));
			rolePermissionsDiv.appendChild(newDiv);
			rolePermissionsDiv.appendChild(document.createTextNode(' '));
		}
	}
}

/// Toggle a user and role link.
function toggleUserRole(user, role)
{
	var userRolesDiv = document.getElementById('user-'+user+'-roles');
	var name = 'role-'+role;
	
	if (undefined != userRoles[user][role]) {
		delete userRoles[user][role];
		delete roleUsers[role][user];
		
		if (userRolesDiv) {
			var children = userRolesDiv.childNodes;
			for (var i = 0; i < children.length; ++i) {
				if (children[i].nodeType == Node.ELEMENT_NODE && children[i].getAttribute('name') == name) {
					children[i].parentNode.removeChild(children[i]);
				}
			}
		}
	}
	else {
		userRoles[user][role] = true;
		roleUsers[role][user] = true;
		
		if (userRolesDiv) {
			var newDiv = document.createElement('div');
			newDiv.setAttribute('name', name);
			newDiv.className = 'role';
			newDiv.onmouseover = function() { roleMouseOver(role); }
			newDiv.onmouseout  = function() { roleMouseOut(role);  }
			newDiv.appendChild(document.createTextNode(role));
			userRolesDiv.appendChild(newDiv);
			userRolesDiv.appendChild(document.createTextNode(' '));
		}
	}
}

function createNewRole(roleInput)
{
	var inputElem = document.getElementById(roleInput);
	if (inputElem) {
		var roleName = inputElem.value;
		if (roleName != '') {
			var name = 'role-'+roleName;
			var roleElem = document.getElementById(name);
			if (roleElem) {
				alert('A role called '+roleName+' already exists');
			}
			else {
				// Set up div
				var newRoleDiv = document.createElement('div');
				newRoleDiv.id = name;
				newRoleDiv.className = 'role';
				if (undefined == implicitRoles[roleName]) {
					newRoleDiv.className += ' explicit';
				}
				{
					var actionDiv = document.createElement('div');
					actionDiv.className = 'action';
					actionDiv.onclick = function() { roleSecondary(roleName); };
					newRoleDiv.appendChild(actionDiv);
					
					var groupDiv = document.createElement('div');
					groupDiv.onclick = function() { roleClick(roleName); };
					{
						var nameDiv = document.createElement('div');
						nameDiv.setAttribute('name', name);
						nameDiv.className = 'name';
						nameDiv.onmouseover = function() { roleMouseOver(roleName); };
						nameDiv.onmouseout  = function() { roleMouseOut(roleName); };
						nameDiv.appendChild(document.createTextNode(roleName));
						groupDiv.appendChild(nameDiv);
						
						var permissionsDiv = document.createElement('div');
						permissionsDiv.id = name+'-permissions';
						permissionsDiv.className = 'permissions';
						groupDiv.appendChild(permissionsDiv);
					}
					newRoleDiv.appendChild(groupDiv);
				}
				var rolesList = document.getElementById('rolesList');
				rolesList.appendChild(newRoleDiv);
				rolesList.appendChild(document.createElement('hr'));
				
				// Set up arrays
				rolePermissions[roleName] = {};
				roleUsers[roleName] = {};
				
				inputElem.value = '';
			}
		}
	}
}

/// Secondary select a permission.
function permissionSecondary(permission)
{
	if (selectedRole != null) {
		// toggle link between permission and role
		role = selectedRole;
		deselect();
		toggleRolePermission(role, permission);
		// refresh styles
		rolePrimary(role);
	}
}

/// Secondary select a role.
function roleSecondary(role)
{
	if (selectedPermission != null) {
		// toggle link between permission and role
		permission = selectedPermission;
		deselect();
		toggleRolePermission(role, permission);
		// refresh styles
		permissionPrimary(permission);
	}
	else if (selectedUser != null) {
		// toggle link between permission and role
		user = selectedUser;
		deselect();
		toggleUserRole(user, role);
		// refresh styles
		userPrimary(user);
	}
}

/// Secondary select a user.
function userSecondary(user)
{
	if (selectedRole != null) {
		// toggle link between permission and role
		role = selectedRole;
		deselect();
		toggleUserRole(user, role);
		// refresh styles
		rolePrimary(role);
	}
}

/// Event for when a permission category is clicked.
function permissionCategoryClick(permissionCategory)
{
	permissionCat = document.getElementById('permissions-'+permissionCategory);
	if (CssCheck(permissionCat, 'hidden')) {
		CssRemove(permissionCat, 'hidden');
	}
	else {
		CssAdd(permissionCat, 'hidden');
	}
}

/// Event for when a permission is clicked.
function permissionClick(permission)
{
	if (permission == selectedPermission) {
		deselect();
	}
	else {
		permissionPrimary(permission);
	}
}

/// Event for when a role is clicked.
function roleClick(role)
{
	if (role == selectedRole) {
		deselect();
	}
	else {
		rolePrimary(role);
	}
}

/// Event for when a user is clicked.
function userClick(user)
{
	if (user == selectedUser) {
		deselect();
	}
	else {
		userPrimary(user);
	}
}

function permissionMouseOver(permission)
{
	elements = document.getElementsByName('permission-'+permission);
	for (var i = 0; i < elements.length; ++i) {
		CssAdd(elements[i], 'highlight');
	}
}
function permissionMouseOut(permission)
{
	elements = document.getElementsByName('permission-'+permission);
	for (var i = 0; i < elements.length; ++i) {
		CssRemove(elements[i], 'highlight');
	}
}
function roleMouseOver(role)
{
	elements = document.getElementsByName('role-'+role);
	for (var i = 0; i < elements.length; ++i) {
		CssAdd(elements[i], 'highlight');
	}
}
function roleMouseOut(role)
{
	elements = document.getElementsByName('role-'+role);
	for (var i = 0; i < elements.length; ++i) {
		CssRemove(elements[i], 'highlight');
	}
}
