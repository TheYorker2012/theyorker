// Javascript for progress input
// Author: James Hogan (james_hogan at theyorker dot co dot uk)
// Copyright (C) 2009 The Yorker

function findPos(obj) {
	var curleft = curtop = 0;
	if (obj.offsetParent) {
		curleft = obj.offsetLeft
		curtop = obj.offsetTop
		while (obj = obj.offsetParent) {
			curleft += obj.offsetLeft
			curtop += obj.offsetTop
		}
	}
	return [curleft,curtop];
}

function findMouse(e) {
	var posx = 0;
	var posy = 0;
	if (!e) var e = window.event;
	if (e.pageX || e.pageY) 	{
		posx = e.pageX;
		posy = e.pageY;
	} else if (e.clientX || e.clientY) 	{
		posx = e.clientX + document.body.scrollLeft
			+ document.documentElement.scrollLeft;
		posy = e.clientY + document.body.scrollTop
			+ document.documentElement.scrollTop;
	}
	return [posx,posy];
}

var input_progress_mouse_down = null;

function input_progress_mousedown(name, event)
{
	input_progress_mouse_down = name;
	input_progress_mousemove(name, event);
}

function input_progress_rgb(progress)
{
	var r = 0;
	var g = 0;
	if (progress <= 0.5) {
		r = 255;
		g = 510 * progress;
	}
	else {
		r = 510*(1-progress);
		g = 255;
	}
	r = Math.round(r);
	g = Math.round(g);
	return "rgb("+r+","+g+",0)";
}

function input_progress_mousemove(name, event)
{
	if (name == input_progress_mouse_down) {
		var input = document.getElementById(name+'__val');
		var progress = document.getElementById(name+'__progress');
		var mouse = findMouse(event);
		var progress_pos = findPos(progress);
		var progress = (mouse[0]-progress_pos[0]-2) / (progress.clientWidth-4);
		if (progress > 1) {
			progress = 1;
		}
		else if (progress < 0) {
			progress = 0;
		}
		var percent = progress*100;
		var rounded1 = Math.round(percent);
		input.value = rounded1;
		input_progress_changed(name);
	}
}

function input_progress_mouseup(name, event)
{
	if (input_progress_mouse_down == name) {
		input_progress_mouse_down = null;
	}
}

function input_progress_changed(name)
{
	var input = document.getElementById(name+'__val');
	var bar = document.getElementById(name+'__bar');
	var percent = parseInt(input.value);
	if (isNaN(percent) || percent != input.value) {
		bar.style.width="";
		bar.style.backgroundColor="";
	}
	else {
		if (percent > 100) {
			percent = 100;
		}
		else if (percent < 0) {
			percent = 0;
		}
		bar.style.width=percent+"%";
		bar.style.backgroundColor = input_progress_rgb(percent/100);
	}
}
