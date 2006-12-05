function eventMenu (e) {

	// handles IE vs. Everyone else event handling differences
	if (!e) e = window.event; // and we're in IE apparently
	
	
	// get the mouse position from the event object
	var posX = e.clientX;
	var posY = e.clientY;
	
	// set the position of the <div> containing the event context menu
	$('calviewEventMenu').style.top = posY;
	$('calviewEventMenu').style.left = posX;
	
	// fade in the event context menu
	new Effect.Appear ('calviewEventMenu',{duration:0.2});
	
}
function hideEventMenu () {
	// fade out the menu. not an event handler
	new Effect.Fade ('calviewEventMenu',{duration:0.2});
}

function removeEvent (arrid) {

}

function expandEvent (arrid) {
	new Effect.Appear ('ev_es_'+arrid,{duration:0.2});
}
function collapseEvent (arrid) {
	new Effect.Fade ('ev_es_'+arrid,{duration:0.2});	
}
