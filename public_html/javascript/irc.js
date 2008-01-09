// Javascript for office ajax IRC client
// Author: James Hogan (james at albanarts dot com)
// Copyright (C) 2007 The Yorker

// History:
//  * initial commit 25th Nov 2007

// Manages:
//  * all irc related client-side scripting

// The URL for irc ajax
var irc_ajax_url = '/office/irc/ajax';
var defaultdata = {};

// List of screen names (Channel names etc)
var irc_screen_list = [];
// The next available screen id
var irc_next_screen_id = 0;
// The number of open screens
var irc_open_screens = 0;
// The id of the current screen (or null)
var irc_current_screen = null;

// Whether pinging is currently in place
var irc_pinging = false;
// Whether we should now be connected
var irc_connected = false;

/// Swap style c1 with c2 in o
function CssSwap(o,c1,c2)
{
	o.className = !CssCheck(o,c1)
		? o.className.replace(c2,c1)
		: o.className.replace(c1,c2);
}
/// Add style c1 to o
function CssAdd(o,c1)
{
	if(!CssCheck(o,c1)) {
		o.className+=o.className?' '+c1:c1;
	}
}
/// Remove style c1 from o
function CssRemove(o,c1)
{
	var rep=o.className.match(' '+c1)?' '+c1:c1;
	o.className=o.className.replace(rep,'');
}
/// Check if style c1 is in o
function CssCheck(o,c1)
{
	return new RegExp('\\b'+c1+'\\b').test(o.className);
}

// Create a new tab and return id
function irc_new_screen(name)
{
	var id = irc_next_screen_id++;
	irc_screen_list[id] = name;
	++irc_open_screens;
	
	// create tab
	var new_tab = document.createElement('li');
	new_tab.id = 'irc_channel_tab_'+id;
	new_tab.appendChild(document.createTextNode(name));
	new_tab.onclick = function () {
		irc_change_screen(id);
	}
	// add tab
	var tabs_ul = document.getElementById('irc_channel_tabs');
	tabs_ul.appendChild(new_tab);
	
	// create a main channel block
	var channel_div = document.createElement('div');
	channel_div.id = 'irc_channel_'+id;
	channel_div.style.display = 'none';
	channel_div.style.width = '100%';
	{
		// create table for main display and nick list
		var channel_table = document.createElement('table');
		channel_table.border = "0";
		channel_table.style.width = "100%";
		{
			var channel_table_row = document.createElement('tr');
			channel_table.appendChild(channel_table_row);
			
			var channel_table_messages_td = document.createElement('td');
			{
				var channel_table_messages_div = document.createElement('div');
				channel_table_messages_div.id = 'irc_channel_'+id+'_messages';
				channel_table_messages_div.className = 'irc_messages';
				channel_table_messages_td.appendChild(channel_table_messages_div);
			}
			channel_table_row.appendChild(channel_table_messages_td);
			
			var channel_table_peers_td = document.createElement('td');
			channel_table_peers_td.id = 'irc_channel_'+id+'_peers_td';
			channel_table_peers_td.style.width = "20%";
			channel_table_peers_td.style.display = 'none';
			{
				var channel_table_peers_div = document.createElement('div');
				channel_table_peers_div.id = 'irc_channel_'+id+'_peers';
				channel_table_peers_div.className = 'irc_peers';
				channel_table_peers_td.appendChild(channel_table_peers_div);
			}
			channel_table_row.appendChild(channel_table_peers_td);
			
		}
		// add table
		channel_div.appendChild(channel_table);
		
		// create form for message sending
		var channel_form = document.createElement('form');
		channel_form.className = 'form';
		channel_form.onsubmit = function() {
// 			irc_Send_msg(id);
			return false;
		}
		{
			var channel_fieldset = document.createElement('fieldset');
			channel_fieldset.className = 'inline';
			{
				var message_input = document.createElement('input');
				message_input.id='irc_'+id+'_message';
				message_input.type='text';
				message_input.value='';
				message_input.style.width='80%';
				channel_fieldset.appendChild(message_input);
				
				var send_input = document.createElement('input');
				send_input.id='irc_'+id+'_send';
				send_input.className='button';
				send_input.type='submit';
				send_input.value='Send';
				send_input.onclick = function () {
					irc_send_msg(id);
					return false;
				};
				channel_fieldset.appendChild(send_input);
			}
			
			// add fieldset
			channel_form.appendChild(channel_fieldset);
		}
		channel_div.appendChild(channel_form);
	}
	
	
	// add main channel block
	var channels_div = document.getElementById('irc_channels');
	channels_div.appendChild(channel_div);
	
	// select the screen if its the only one
	if (true || irc_open_screens == 1) {
		irc_change_screen(id);
	}
	
	return id;
}

// Close an open screen
function irc_close_screen(id)
{
	if (irc_screen_list[id]) {
		// remove bits from dom
		var tab = document.getElementById('irc_channel_tab_'+id);
		if (tab) {
			tab.parentNode.removeChild(tab);
		}
		var channel_div = document.getElementById('irc_channel_'+id);
		if (channel_div) {
			channel_div.parentNode.removeChild(channel_div);
		}
		// remove from js data
		irc_screen_list[id] = null;
		--irc_open_screens;
	}
}

// Change to a different screen
function irc_change_screen(id)
{
	if (null != irc_current_screen) {
		var prev_screen_tab = document.getElementById('irc_channel_tab_'+irc_current_screen);
		if (prev_screen_tab) {
			CssRemove(prev_screen_tab, 'selected');
		}
		
		var prev_screen = document.getElementById('irc_channel_'+irc_current_screen);
		if (prev_screen) {
			prev_screen.style.display = 'none';
		}
	}
	if (null != id) {
		var next_screen_tab = document.getElementById('irc_channel_tab_'+id);
		if (next_screen_tab) {
			CssRemove(next_screen_tab, 'updated');
			CssRemove(next_screen_tab, 'highlighted');
			CssAdd(next_screen_tab, 'selected');
		}
		
		var next_screen = document.getElementById('irc_channel_'+id);
		if (next_screen) {
			next_screen.style.display = 'block';
		}
		
		var next_screen_messages = document.getElementById('irc_channel_'+id+'_messages');
		if (next_screen_messages) {
			next_screen_messages.scrollTop = next_screen_messages.scrollHeight;
		}
	}
	irc_current_screen = id;
}

// Get the id of a screen by name
function irc_get_screen(name)
{
	for (var i = 0; i < irc_screen_list.length; ++i) {
		if (irc_screen_list[i] == name) {
			return i;
		}
	}
	return null;
}

function irc_get_new_screen(name)
{
	var screen_id = irc_get_screen(name);
	if (null != screen_id) {
		return screen_id;
	} else {
		return irc_new_screen(name);
	}
}

// Send a message via the web server
function irc_send_msg(id)
{
	var message_box = document.getElementById('irc_'+id+'_message');
	var message = message_box.value;
	if (message != '') {
		message_box.value = '';
		var post = {};
		for (var key in defaultdata) {
			post[key] = defaultdata[key];
		}
		post['cmd'] = 'msg';
		post['msg'] = message;
		post['channel'] = irc_screen_list[id];
		var ajax = new AJAXInteraction(irc_ajax_url, post, irc_ajax_callback);
		ajax.doGet();
	}
}

// Join an IRC channel
function irc_join_channel(channel)
{
	var post = {};
	for (var key in defaultdata) {
		post[key] = defaultdata[key];
	}
	post['cmd'] = 'join';
	post['channel'] = channel;
	var ajax = new AJAXInteraction(irc_ajax_url, post, irc_ajax_callback);
	ajax.doGet();
}

// Ping the web server so it doesn't close our connection
function irc_ping()
{
	var post = {};
	for (var key in defaultdata) {
		post[key] = defaultdata[key];
	}
	post['cmd'] = 'ping';
	var ajax = new AJAXInteraction(irc_ajax_url, post, irc_ping_ajax_callback);
	ajax.doGet();
}

// Handle a ping response from web server, start up another ping
function irc_ping_ajax_callback(responseXML)
{
	irc_pinging = false;
	irc_ajax_callback(responseXML);
	if (irc_connected && !irc_pinging) {
		irc_pinging = true;
		irc_ping();
	} else {
		irc_pinging = false;
	}
}

// Get the first child element string
function irc_xml_get_item(container, name)
{
	var all = container.getElementsByTagName(name);
	if (all.length > 0 && all[0].firstChild) {
		return all[0].firstChild.nodeValue;
	} else {
		return null;
	}
}

// Handle a response from the web server
function irc_ajax_callback(responseXML)
{
	if (responseXML) {
		var messages = responseXML.getElementsByTagName('msg');
		if (messages.length > 0) {
			for (var msgid = 0; msgid < messages.length; ++msgid) {
				var type = messages[msgid].attributes.getNamedItem('type').value;
				var highlight = messages[msgid].attributes.getNamedItem('highlight');
				
				var new_message = document.createElement('div');
				new_message.className='irc_message';
				var sender_prefix = '<';
				var sender_postfix = '>';
				var show_type = false;
				if (type == 'NOTICE') {
					new_message.className += ' notice';
				}
				else if (type == 'JOIN') {
					new_message.className += ' channel';
					sender_prefix = '---> ';
					sender_postfix = '';
				}
				else if (type == 'PART') {
					new_message.className += ' channel';
					sender_prefix = '<--- ';
					sender_postfix = '';
				}
				else if (type == 'QUIT') {
					new_message.className += ' channel';
					sender_prefix = '<--- ';
					sender_postfix = '';
				}
				else {
					if (type == 'ACTION') {
						new_message.className += ' action';
						new_message.className += ' privmsg';
						sender_prefix = '* ';
						sender_postfix = '';
					} else if (type == 'PRIVMSG') {
						new_message.className += ' privmsg';
					} else {
						show_type = true;
						new_message.className += ' notice';
					}
					if (highlight) {
						new_message.className += ' highlighted';
					}
				}
				
				var time = irc_xml_get_item(messages[msgid], 'time');
				if (time) {
					var new_time = document.createElement('div');
					new_time.className='irc_message_time';
					new_time.appendChild(document.createTextNode('['+time+']'));
					new_message.appendChild(new_time);
					new_message.appendChild(document.createTextNode(' '));
				}
				
				var sender = irc_xml_get_item(messages[msgid], 'sender');
				if (sender) {
					var new_sender = document.createElement('div');
					new_sender.className='irc_message_sender';
					new_sender.appendChild(document.createTextNode(sender_prefix+sender+sender_postfix));
					new_message.appendChild(new_sender);
					new_message.appendChild(document.createTextNode(' '));
				}
				
				var content = irc_xml_get_item(messages[msgid], 'content');
				if (content) {
					var new_content = document.createElement('div');
					new_content.className='irc_message_content';
					// The value from XML is html code
					var html = '';
					if (show_type) {
						html += '<b>'+type + '</b> ';
					}
					html += content;
					new_content.innerHTML = html;
					
					new_message.appendChild(new_content);
					new_message.appendChild(document.createTextNode(' '));
				}
				
				var channel = irc_xml_get_item(messages[msgid], 'channel');
				var id = irc_get_new_screen('server');
				if (channel) {
					id = irc_get_new_screen(channel);
				}
				var messages_div = document.getElementById('irc_channel_'+id+'_messages');
				messages_div.appendChild(new_message);
				
				// Highlight / update channel
				if (id != irc_current_screen) {
					var screen_tab = document.getElementById('irc_channel_tab_'+id);
					if (screen_tab) {
						if (CssCheck(new_message, 'highlighted')) {
							CssAdd(screen_tab, 'highlighted');
						}
						if (CssCheck(new_message, 'privmsg')) {
							CssAdd(screen_tab, 'updated');
						}
					}
				}
				
				// Names list provided?
				var names = messages[msgid].getElementsByTagName('name');
				if (names.length > 0) {
					var channel = irc_xml_get_item(messages[msgid], 'channel');
					if (channel) {
						var nicklist_td = document.getElementById('irc_channel_'+id+'_peers_td');
						nicklist_td.style.display = '';
						var nicklist = document.getElementById('irc_channel_'+id+'_peers');
						while (nicklist.childNodes.length > 0) {
							nicklist.removeChild(nicklist.firstChild);
						}
						for (var nameid = 0; nameid < names.length; ++nameid) {
							var nick = irc_xml_get_item(names[nameid], 'nick');
							if (nick) {
								var new_peer = document.createElement('div');
								new_peer.className='irc_peer';
								new_peer.appendChild(document.createTextNode(nick));
								nicklist.appendChild(new_peer);
							}
						}
					}
				}
				messages_div.scrollTop = messages_div.scrollHeight;
			}
		}
	}
}

// Send a connection request
function irc_connect()
{
	// Start the irc client manager
	var post = {};
	for (var key in defaultdata) {
		post[key] = defaultdata[key];
	}
	post['cmd'] = 'connect';
	// This query will remain open as long as the IRC client is connected.
	var ajax = new AJAXInteraction(irc_ajax_url, post,
		function () {
			if (irc_connected) {
				irc_disconnect();
			}
		} );
	ajax.doGet();
	
	irc_connected = true;
	if (!irc_pinging) {
		irc_ping();
		irc_pinging = true;
	}
}

// Send disconnection request and remove all screens
function irc_disconnect()
{
	var post = {};
	for (var key in defaultdata) {
		post[key] = defaultdata[key];
	}
	post['cmd'] = 'disconnect';
	var ajax = new AJAXInteraction(irc_ajax_url, post,
		function () {
			for (var i = 0; i < irc_screen_list.length; ++i) {
				if (irc_screen_list[i]) {
					irc_close_screen(i);
				}
			}
			irc_screen_list = [];
			irc_next_screen_id = 0;
			irc_open_screens = 0;
			irc_current_screen = null;
			irc_connected = false;
		} );
	ajax.doGet();
}

/// Main onLoad function
function irc_onLoad()
{
	// Doesn't actually load anything. this may be handy in future
}

// Register onLoad function
// onLoadFunctions.push(irc_onLoad);
