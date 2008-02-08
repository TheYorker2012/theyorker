/*
 * Copied from clone.js
 * Modified by James Hogan
 */

var count = 1;

function NestedAppendIdsNames(parent, append)
{
	var newField = parent.childNodes;
	for (var i=0; i<newField.length; i++) {
		if (newField[i].name) {
// 			newField[i].name = newField[i].name + append;
			newField[i].setAttribute('name', newField[i].getAttribute('name') + append);
		}
		if (newField[i].id) {
			newField[i].id = newField[i].id + append;
		}
		if (newField[i].nodeType == '1' && newField[i].getAttribute('for')) {
			newField[i].setAttribute('for', newField[i].getAttribute('for') + append);
		}
		NestedAppendIdsNames(newField[i], append);
	}
}

function AddClone(source, destination) {
	count++;
	var newClone = document.getElementById(source).cloneNode(true);
	newClone.id = '';
	newClone.style.display = 'block';
	NestedAppendIdsNames(newClone, count);
	var Spawn = document.getElementById(destination);
	Spawn.value = count;
	Spawn.parentNode.insertBefore(newClone, Spawn);
}
