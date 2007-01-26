/* AddClones will take a element of name 'source' and copy it to a
 * 'destination' appending a counter to the names of the child
 * elements
 */

var count = 0;

function init() {
	document.getElementById('AddClone').onclick = AddClones;
	AddClones();
}

function AddClones() {
	count++;
	var newClone = document.getElementById('source').cloneNode(true);
	newClone.id = '';
	newClone.style.display = 'block';
	var newField = newClone.childNodes;
	for (var i=0; i<newField.length; i++) {
		if (newField[i].name)
			newField[i].name = newField[i].name + count;
	}
	var Spawn = document.getElementById('destination');
	Spawn.value = Spawn.value++;
	Spawn.parentNode.insertBefore(newClone, Spawn);
}

window.onload = init;