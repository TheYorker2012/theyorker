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



function confirmRemoveEvent (confResponse) {
	var opo = confResponse.responseText.split('|');
	if (opo[2] == 'OK') {
		// hide the event as it's confirmed in the db
		new Effect.Fade ('ev_'+opo[1]);
		revokeRefids.push(opo[1]);
		draw_calendar (calevents);
	}
	else {
		alert ("Couldn't remove event, sorry!\nError: "+opo[3]);
		draw_calendar (calevents);
	}
	
}
function removeEvent (arrid) {
	// new Effect.Fade ('ev_'+arrid);

	$('ev_'+arrid).innerHTML = "removing from your calendar...<br />\n<img src=\"/images/waitcounter.gif\" title=\"Please wait\" alt=\"Billy\" />"
	
	var url = '/calendar/ajaxCalUpdate/-1/'+arrid+'/HIDE';
	var myAjax = new Ajax.Request(
	url, 
	{
		method: 'get', 
		onComplete: confirmRemoveEvent
	});
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

	clear_calendar ();

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
	var cpd = [0,0,0,0,0,0,0];
	var lastime = '';
	var evc = [];
	var evl = [];
	var hdif = 0;
	var mdif = 0;
	var clash = false;
	var cdifstr = '';
	var colcho = 'G';
	
	for (day in events) {
		
		debug += '\nday='+day+'\n';
		
		for (event_index in events[day]) {
			
			debug += ' event_index='+event_index+'\n';
			debug += '  start time='+events[day][event_index]['starttime']+'\n';
			if (revokeRefids.indexOf(events[day][event_index]['ref_id']) == -1) {
				 if (cpd[day] == 0) {
				 	// first event of the day
				 	draw_calendar_timeSpacer (daysDiv[parseInt (day)],"Day starts: "+events[day][event_index]['starttime'],'G');
				 }
				 else {
				 	// subsequent event in the day
				 	evc = events[day][event_index]['starttime'].split(':');
				 	evl = lastime.split(':');
				 	hdif = evc[0] - evl[0]; // difference in hours
				 	mdif = evc[1] - evl[1]; // difference in minutes
				 	clash = false; // for now >:)
				 	if (hdif <= 0) {
				 		if (mdif > 0) {
				 			cdifstr = minuteslamatron(mdif)+' mins free';
				 		}
				 		else {
				 			clash = true;
				 		}
				 	}
				 	else {
				 		if (mdif < 0) {
				 			hdif--;
				 			mdif = 60 - (mdif*-1);
				 		}
				 		
				 		if (hdif > 0) {
				 			cdifstr = hdif+':'+minuteslamatron(mdif)+' free';
				 		}
				 		else {
				 			cdifstr = minuteslamatron(mdif)+' mins free';
				 		}
				 	}
				 	if (clash) {
					 	hdif = evc[0] - evl[0]; // difference in hours
					 	mdif = evc[1] - evl[1]; // difference in minutes
					 	mdif = (mdif < 0) ? (60-mdif*-1) : (mdif);
					 	cdifstr = 'CLASH!<br />\n'+hdif+':'+minuteslamatron(mdif)+' overlap!';
					 	colcho = 'R';
				 	}
				 	else {
				 		colcho = 'G';
				 	}
				 	draw_calendar_timeSpacer (daysDiv[parseInt (day)],cdifstr,colcho);
				 }
//				 if (!clash) {
				 	// this clashes up like... the whole day \o/
				 	lastime = events[day][event_index]['endtime'];
//				 }
				 	
				 draw_calendar_event (events[day][event_index]);
				 cpd[day]++
			}
			


		}
		if (cpd[day] > 0) {
		 	draw_calendar_timeSpacer (daysDiv[parseInt (day)],"Day ends: "+lastime,'G');
		}

	}
	
	for (i = 0;i<=7;i++) {
	
		if (cpd[i] == 0) {
		 	draw_calendar_timeSpacer (daysDiv[parseInt (i)],"FREE DAY!",'Grey');
		}
	
	}
	
	//alert (debug);
	
}

function minuteslamatron (mins) {
	mins = ''+mins+'';
	while (mins.length < 2) {
		mins = '0'+mins;
	}
	return mins;
}

// takes the name of the day and an object with a single event in it
// appends the relevant code into the relevant day div
function draw_calendar_event (indEvent) {

	var outHtml = '\n';
	
	outHtml += '					<div id=\"ev_%%refid%%\" class=\"calviewIndEventBox\">\n';
	outHtml += '					<div>\n';
	outHtml += '						<div id=\"calviewIECtrlButtonBLBound\">\n';
	outHtml += '						\n';
	outHtml += '							<div class=\"calviewCloseButton\" onclick=\"removeEvent(%%refid%%)\">\n';
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
	outHtml = outHtml.replace (/%%day%%/g,indEvent.day);
	outHtml = outHtml.replace (/%%starttime%%/g,indEvent.starttime);
	outHtml = outHtml.replace (/%%endtime%%/g,indEvent.endtime);
	outHtml = outHtml.replace (/%%shortloc%%/g,indEvent.shortloc);
	outHtml = outHtml.replace (/%%blurb%%/g,indEvent.blurb);



	$(daysDiv[parseInt (indEvent.day)]).innerHTML += outHtml;	


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

function clear_calendar () {

	for (i in daysDiv) {
		$(daysDiv[i]).innerHTML = '&nbsp;';
	}
	
}

