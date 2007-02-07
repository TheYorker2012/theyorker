function eventMenu () {

	// handles IE vs. Everyone else's event handling differences
	if (!e) e = window.event; // and we're in IE apparently
	
	
	// get the mouse position from the event object
	var posX = e.pageX;
	var posY = e.pageY;
	
	var evm = document.getElementById('calviewEventMenu');
	alert (evm.innerHTML);
	evm.style.backgroundColor = 'black';
	evm.style.top = parseInt (posX)+'px';


	
	// fade in the event context menu
	new Effect.Appear ('calviewEventMenu',{duration:0.2});
	
}
function hideEventMenu () {
	// fade out the menu. not an event handler
	new Effect.Fade ('calviewEventMenu',{duration:0.2});
	//$('calviewEventMenu').style.display = 'none';
}



function removeEvent (arrid) {
	// new Effect.Fade ('ev_'+arrid);

	$('ev_es_'+arrid).innterHTML = "removing from your calendar..."
	
	var params = 1;
	var url = 1;
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'get', 
		parameters: params, 
		onComplete: confirmRemoveEvent
	});

}

function confirmRemoveEvent () {

}

function expandEvent (arrid) {
	new Effect.Appear ('ev_es_'+arrid,{duration:0.2});
}
function collapseEvent (arrid) {
	new Effect.Fade ('ev_es_'+arrid,{duration:0.2});	
}
