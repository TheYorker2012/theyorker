/* AddClones will take a element of name 'source' and copy it to a
 * 'destination' appending a counter to the names of the child
 * elements. It limits to 5
 */

var count = 1;

function AddClones() {
	if (count <= 5) {
		count++;
		var newClone = document.getElementById('source').cloneNode(true);
		newClone.id = '';
		newClone.style.display = 'block';
		var newField = newClone.childNodes;
		for (var i=0; i<newField.length; i++) {
			if (newField[i].name)
				newField[i].name = newField[i].name + count;
			if (newField[i].getAttribute('for'))
				newField[i].setAttribute('for', newField[i].getAttribute('for') + count);
		}
		var Spawn = document.getElementById('destination');
		Spawn.value = count;
		Spawn.parentNode.insertBefore(newClone, Spawn);
		
	}
}
