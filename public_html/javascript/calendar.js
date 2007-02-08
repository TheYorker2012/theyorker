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

function sortByStartTime(dayOfEvents) {
	var jev = '';
	for (i in dayOfEvents) {
		(jev.length > 0) ? (jev += ',') : 0;
		jev += i;
	}
	jev = eval ('['+jev+']');
	
	var i = 0;
	var tres = 0;
	var temp;
	while (i < jev.length) {
		if (i != (jev.length - 1)) {
			tres = compareDayStartTimes (dayOfEvents[jev[i]], dayOfEvents[jev[i+1]]);
			//alert('COMP:'+dayOfEvents[jev[i]].starttime+' with '+dayOfEvents[jev[i+1]].starttime+' = '+tres);
			if (tres == 1) {
				temp = dayOfEvents[jev[i+1]];
				dayOfEvents[jev[i+1]] = dayOfEvents[jev[i]];
				dayOfEvents[jev[i]] = temp;
				i = 0;
			}
		}
		i++;
	}

	return dayOfEvents;
}

function compareDayStartTimes (a,b) {
    var w = parseInt (a.starttime.split(':')[0]);
    var x = parseInt (b.starttime.split(':')[0]);
    var y = parseInt (a.starttime.split(':')[1]);
    var z = parseInt (b.starttime.split(':')[1]);
    return ((w < x) ? -1 : ((w > x) ? 1 : ((y < z) ? -1 : (y > z) ? 1 : 0) ));
}

function draw_calendar (events) {

	var day = 0;
	var event = new Array ();
	
	// loop through each day
	for (day in events) {
		
		// sort the events into chronological order
		events[day] = sortByStartTime(events[day]);
	
	}
	
	day = 0;
	var event_arrid = 0;
	var debug = '';
	var i = 0;
	
	for (day in events) {
		
		debug += '\nday='+day+'\n';
		
		for (event_index in events[day]) {
			
			debug += ' event_index='+event_index+'\n';
			debug += '  start time='+events[day][event_index]['starttime']+'\n';
			
			draw_calendar_event (events[day][event_index]);

		}
	}
	
	//alert (debug);
	
}

// takes the name of the day and an object with a single event in it
// appends the relevant code into the relevant day div
function draw_calendar_event (indEvent) {
	
	var daysDiv = ['calviewMonday','calviewTuesday','calviewWednesday','calviewThursday','calviewFriday','calviewSaturday'];
	var outHtml = '\n';
	
	outHtml += '					<div id=\"ev_%%refid%%\" class=\"calviewIndEventBox\">\n';
	outHtml += '					<div>\n';
	outHtml += '						<div id=\"calviewIECtrlButtonBLBound\">\n';
	outHtml += '						\n';
	outHtml += '							<div class=\"calviewCloseButton\" onclick=\"removeEvent(%%refid%%);hideEventMenu()\">\n';
	outHtml += '								<a href=\"#\" onclick=\"return false\"\n';
	outHtml += '								style=\"text-decoration: none; color: #ffffff;\">X</a>\n';
	outHtml += '							</div>\n';
	outHtml += '							\n'; 
	outHtml += '							<div class=\"calviewExpandButton\"\n';
	outHtml += '								onmouseover=\"expandEvent(%%refid%%)\"\n';
	outHtml += '								onmouseout=\"collapseEvent(%%refid%%)\"\n';
	outHtml += '								style=\"cursor: pointer\">\n';
	outHtml += '								V\n';
	outHtml += '							</div>\n';
	outHtml += '							\n';
	outHtml += '						</div>\n';
	outHtml += '						\n';
	outHtml += '						<strong>%%name%%</strong>\n';
	outHtml += '						\n';
	outHtml += '						<div class=\"calviewExpandedSmall\" id=\"ev_es_%%refid%%\" style=\"display: none\">\n';
	outHtml += '						<div>\n';
	outHtml += '							%%starttime%% to %%endtime%%<br />At: %%shortloc%%<br /><i>%%blurb%%</i>\n';
	outHtml += '						</div>\n';
	outHtml += '						</div>\n';
	outHtml += '						\n';
	outHtml += '					</div>\n';
	outHtml += '					</div>\n\n';
	
	// replace the strings as would be done server side
	
	outHtml = outHtml.replace (/%%refid%%/g,indEvent[0]);
	outHtml = outHtml.replace (/%%name%%/g,indEvent.name);
	outHtml = outHtml.replace (/%%starttime%%/g,indEvent.starttime);
	outHtml = outHtml.replace (/%%endtime%%/g,indEvent.endtime);
	outHtml = outHtml.replace (/%%shortloc%%/g,indEvent.shortloc);
	outHtml = outHtml.replace (/%%blurb%%/g,indEvent.blurb);

	$(daysDiv[parseInt (indEvent.day)]).innerHTML += outHtml;	
	draw_calendar_timeSpacer (daysDiv[parseInt (indEvent.day)],'123124','R');

}

function draw_calendar_timeSpacer (dayDivName,text,goodbad) {

	var outHtml = '\n';

	outHtml += '					<div class=\"calviewTimeSpacer%%tsc%%\">\n';
	outHtml += '					<div>\n';
	outHtml += '						%%tsText%%\n';
	outHtml += '					</div>\n';
	outHtml += '					</div>\n';
	
	outHtml = outHtml.replace (/%%tsc%%/g,goodbad);
	outHtml = outHtml.replace (/%%tsText%%/g,text);

	$(dayDivName).innerHTML += outHtml;	

}



