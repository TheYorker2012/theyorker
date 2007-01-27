var jumperPrefix = "Jump";
var anchorPrefix = "";

var Letters = "0ABCDEFGHIJKLMNOPQRSTUVWXYZ";

var timerID = 0;

var SectionsShown = 0;

var currentThread = Letters.length - 1;


function searchPage(searchTextElement,prefix,filterPrefix) {

	eval("var eLoading = document.getElementById('loadingdiv');");
	eval("var eNotFoundElement = document.getElementById('NotFound');");

	eLoading.style.display = "block";
	eNotFoundElement.style.display = "none";

	SectionsShown = 0;
	
	currentThread = Letters.length - 1;

	if(timerID) {
		clearTimeout(timerID);
	}
	timerID = setTimeout("searchPageThread('" + searchTextElement + "','" + prefix + "','" + filterPrefix + "'," + currentThread + ");",50);
}
	
function searchPageThread(searchTextElement,prefix,filterPrefix,threadNo) {
	if (currentThread > threadNo) return;


	eval("var eSearchTextElement = document.getElementById('" + searchTextElement + "');");
	
	if (eSearchTextElement == null) {
		alert("Please specify a valid search element.");
	}
	
	var SearchText = eSearchTextElement.value;
	
	
	var filterString = "";
	
	var i = 1;
	while (true) {
		var ItemElementId = filterPrefix + i;
		eval("var eItemElement = document.getElementById('" + ItemElementId + "');");
		if (eItemElement == null) {
			break;
		} else if (eItemElement.checked) {
			filterString += '(' + eItemElement.getAttribute("name").toUpperCase() + ')';
		}
		i++;
	}

	var j = threadNo;
	if (j>=0) {
	
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
					if ((filterString.indexOf('(' + eItemElement.getAttribute("name").toUpperCase() + ')') >= 0) && (eItemElement.innerHTML.toUpperCase().indexOf(SearchText.toUpperCase()) >= 0)) {
						eItemElement.style.display = "block";
						NumberShown++;
					} else {
						eItemElement.style.display = "none";
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
	
	if (j<=0) {
		eval("var eLoading = document.getElementById('loadingdiv');");
		eval("var eNotFoundElement = document.getElementById('NotFound');");
		if (eNotFoundElement != null) {
			if (SectionsShown == 0) {
				eNotFoundElement.style.display = "block";
				eSearchTextElement.style.backgroundColor="#FF9933";
			} else {
				eNotFoundElement.style.display = "none";
				eSearchTextElement.style.backgroundColor="#ffffff";
			}
		}
		eLoading.style.display = "none";
	}
	
	currentThread--;
	timerID = setTimeout("searchPageThread('" + searchTextElement + "','" + prefix + "','" + filterPrefix + "'," + currentThread + ");",50);
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

function showJumpAsLoad(CurrentLetter) {
		var JumperElementId = jumperPrefix + CurrentLetter;
		eval("var eJumperElement = document.getElementById('" + JumperElementId + "');");
		showJump(eJumperElement, true);
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




