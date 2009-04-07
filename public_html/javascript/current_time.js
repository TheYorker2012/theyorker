/**
 *	Gives a preview of the current time in 12/24hr time formats
 *	Pages used: /register/
 *	Requires:	- select :id = 'time'
 *					- option(0) :12hr
 *					- option(1) :24hr
 *				- div :id = 'current_time'
 *
 *	@author Chris Travis (cdt502 - ctravis@gmail.com)
 */

function updateTime () {
	var currentTime = new Date();
	var hours = currentTime.getHours();
	var minutes = currentTime.getMinutes();
	var seconds = currentTime.getSeconds();
	var temp = "";
	if (minutes < 10) { minutes = "0" + minutes; }
	if (seconds < 10) { seconds = "0" + seconds; }
	if (document.getElementById('time').selectedIndex == 1) {
		if (hours < 10) { temp = "0"; }
		temp = temp + hours + ":" + minutes + ":" + seconds;
		document.getElementById('current_time').innerHTML = temp;
	} else {
		if (hours > 12) { hours = hours - 12; }
		temp = hours + ":" + minutes + ":" + seconds;
		if (hours > 11){
			temp = temp + " PM";
		} else {
			temp = temp + " AM";
		}
		document.getElementById('current_time').innerHTML = temp;
	}
	setTimeout('updateTime()',1000);
}
setTimeout('updateTime()',0);