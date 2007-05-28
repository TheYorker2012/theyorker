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
			if (newField[i].nodeType == '1') if (newField[i].getAttribute('for')) //needs to be asked in order
				newField[i].setAttribute('for', newField[i].getAttribute('for') + count);
		}
		var Spawn = document.getElementById('destination');
		Spawn.value = count;
		Spawn.parentNode.insertBefore(newClone, Spawn);
		
	} else {
		alert('You may not upload more than 6 photos at once.');
	}
}

function ValidateClones() {
	var Spawn = document.getElementById('destination');
	var BrothersOfSpawn = Spawn.parentNode.childNodes;
	for (var j=0; j<BrothersOfSpawn.length; j++) {
		if(BrothersOfSpawn[j].nodeName == 'DIV') {
			var BrothersOfSpawnSons = BrothersOfSpawn[j].childNodes;
			for (var i=0; i<BrothersOfSpawnSons.length; i++) {
				if(BrothersOfSpawnSons[i].name && BrothersOfSpawnSons[i].name.substr(0,5) == 'title' && BrothersOfSpawnSons[i].value == '') {
					return confirm('You have some images without titles, these will be ignored by the uploader. Are you sure you want to continue?');
				}
			}
		}
	}
	return true;
}