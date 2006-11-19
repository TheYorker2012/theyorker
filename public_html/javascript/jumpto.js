var jumperPrefix = "";
var anchorPrefix = "";

function searchPage(searchTextElement,prefix) {
	eval("var eSearchTextElement = document.getElementById('" + searchTextElement + "');");
	
	if (eSearchTextElement == null) {
		alert("Please specify a valid search element.");
	}
	
	var SearchText = eSearchTextElement.value;
	
	var Letters = "0ABCDEFGHIJKLMNOPQRSTUVWXYZ"
	
	var SectionsShown = 0;
	
	for (var j=0; j<Letters.length ; j++) {
		var CurrentLetter = Letters.charAt(j);
		var ContainerElementId = prefix + CurrentLetter;
		var JumperElementId = jumperPrefix + CurrentLetter;
		eval("var eContainerElement = document.getElementById('" + ContainerElementId + "');");
		eval("var eJumperElement = document.getElementById('" + JumperElementId + "');");
		if (eContainerElement != null) {
			var i = 1;
			var NumberShown = 0;
			while (true) {
				var ItemElementId = prefix + CurrentLetter + i;
				eval("var eItemElement = document.getElementById('" + ItemElementId + "');");
				if (eItemElement == null) {
					break;
				} else {
					if (eItemElement.innerHTML.toUpperCase().indexOf(SearchText.toUpperCase()) < 0) {
						eItemElement.style.display = "none";
						//Effect.BlindUp(ItemElementId);
					} else {
						eItemElement.style.display = "block";
						//Effect.BlindDown(ItemElementId);
						
						NumberShown++;
					}
				}
				i++;
			}
			if (NumberShown == 0) {
				eContainerElement.style.display = "none";
				//Effect.BlindUp(ContainerElementId);
				
				showJump(eJumperElement,false);
			} else {
				eContainerElement.style.display = "block";
				//Effect.BlindDown(ContainerElementId);
				
				SectionsShown++;
				showJump(eJumperElement,true);
			}
		} else {
			showJump(eJumperElement,false);
		}
	}
	
	eval("var eNotFoundElement = document.getElementById('NotFound');");
	if (eNotFoundElement != null) {
		if (SectionsShown == 0) {
			eNotFoundElement.style.display = "block";	
		} else {
			eNotFoundElement.style.display = "none";
		}
	}
	
}

function showEventMore(prefix, eventnumber) {
	var i = 1;
	while (true) {
		var ItemElementId = prefix + i;
		eval("var eItemElement = document.getElementById('" + ItemElementId + "');");
		if (eItemElement == null) {
			break;
		} else {
			if (i != eventnumber) {
				eItemElement.style.display = "none";
				//Effect.BlindUp(ItemElementId);
			} else {
				if (eItemElement.style.display == "block") {
					eItemElement.style.display = "none";
				} else {
					eItemElement.style.display = "block";
				}
				//Effect.BlindDown(ItemElementId);
			}
		}
		i++;
	}
}

function showJump(eJumperElement, show) {
	if (eJumperElement != null) {
		if (show) {
			eJumperElement.style.color = "coral";
			eJumperElement.style.fontWeight = "bold";
			eJumperElement.style.cursor = "pointer";
			eJumperElement.style.textDecoration = "underline";
		} else {
			eJumperElement.style.color = "#cccccc";
			eJumperElement.style.fontWeight = "normal";
			eJumperElement.style.cursor = "default";
			eJumperElement.style.textDecoration = "none";
		}
	}
}

function insertJumpers(prefix,anchorprefix) {
	jumperPrefix = prefix;
	anchorPrefix = anchorprefix;
	
	var Letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ"
	
	for (var j=0; j<Letters.length ; j++) {
		var CurrentLetter = Letters.charAt(j);
		document.write( "<span class=\"AZJump\" onClick=\"jumpTo('" + CurrentLetter + "');\" id=\"" + jumperPrefix + CurrentLetter + "\">" + CurrentLetter + "</span> "  ) ;
	}
	
}

function jumpTo(letter) {
	eval("var eJumperElement = document.getElementById('" + jumperPrefix + letter + "');");
	if (eJumperElement.style.fontWeight == "bold") {
		location.hash = anchorPrefix + letter;
	}
}




