var myEvents=new Array();
var currentMenuSubject;
function eventMenu(event) {
	//currentMenuSubject = event.srcElement.id;
	if (1) { //($(Event.element(event)) == 'ev_1') {
		var xPos = Event.pointerX(event);
		var yPos = Event.pointerY(event);
		$('calviewEventMenu').style.top=yPos;
		$('calviewEventMenu').style.left=xPos;
		new Effect.Appear ('calviewEventMenu', {duration:0.2});
	}	
}
function eventSetHighlight (e) {
	//$('ev_1').class="indEventBoxHL";
	$(currentMenuSubject).style.color="#ff0000";
}
function hideEventMenu (e) {
	new Effect.Fade ('calviewEventMenu',{duration:0.2});
}
function eventCreate(date,time,sid,title,loc,blurb) {
	
}
function hideEvent (idn) {
	new Effect.Puff ('ev_'+idn,{duration:0.5});
}
function expandEvent (idn) {
	new Effect.Appear ('ev_es_'+idn,{duration:0.2});
}
function collapseEvent (idn) {
	new Effect.Fade ('ev_es_'+idn,{duration:0.2});
}
if (!e) var e = window.event
