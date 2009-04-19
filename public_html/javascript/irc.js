// Javascript for office ajax IRC client
// Author: James Hogan (james at albanarts dot com)
// Copyright (C) 2007 The Yorker
// Depends on javascript/css_classes.js

// History:
//  * 25th Nov 2007: initial commit 
//  * 10th Jan 2008: keeps nick list up to date.

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

// Set the error message.
function irc_error(msg)
{
	var err = document.getElementById('irc_error_msg');
	
	while (err.childNodes.length > 0) {
		err.removeChild(err.firstChild);
	}
	err.appendChild(document.createTextNode(msg));
	err.style.display = 'block';
}

// Clear the error message
function irc_clear_error()
{
	var err = document.getElementById('irc_error_msg');
	
	err.style.display = 'none';
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
	// tab close button
	if (true) {
		var tab_close = document.createElement('img');
		tab_close.src = "/images/icons/delete8.png";
		tab_close.alt = "close channel";
		tab_close.onclick = function () {
			var screen_id = irc_get_screen(name);
			if (name.substr(0,1) == '#') {
				// /part the channel
				irc_query(screen_id, '/part');
			} else {
				// otherwise just close the query
				irc_close_screen(screen_id);
			}
		}
		new_tab.appendChild(document.createTextNode(" "));
		new_tab.appendChild(tab_close);
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
		var channel_table_tbody = document.createElement('tbody');
		channel_table.appendChild(channel_table_tbody);
		{
			var channel_table_row = document.createElement('tr');
			channel_table_tbody.appendChild(channel_table_row);
			
			var channel_table_topic_td = document.createElement('td');
			channel_table_topic_td.colSpan = 2;
			{
				var channel_table_topic_div = document.createElement('div');
				channel_table_topic_div.id = 'irc_channel_'+id+'_topic';
				channel_table_topic_div.className = 'irc_topic';
				channel_table_topic_div.style.display = 'none';
				channel_table_topic_td.appendChild(channel_table_topic_div);
			}
			channel_table_row.appendChild(channel_table_topic_td);
		}
		{
			var channel_table_row = document.createElement('tr');
			channel_table_tbody.appendChild(channel_table_row);
			
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
 			irc_send_msg(id);
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

function irc_query(id,query)
{
	var post = {};
	for (var key in defaultdata) {
		post[key] = defaultdata[key];
	}
	post['cmd'] = 'msg';
	post['msg'] = query;
	post['channel'] = irc_screen_list[id];
	var ajax = new AJAXInteraction(irc_ajax_url, post, irc_ajax_callback);
	ajax.doPost();
}

// Send a message via the web server
function irc_send_msg(id)
{
	var message_box = document.getElementById('irc_'+id+'_message');
	var message = message_box.value;
	if (message != '') {
		message_box.value = '';
		irc_query(id, message);
	}
}

// Join an IRC channel
function irc_join_channel(channel)
{
	var screen_id = irc_get_screen(channel);
	if (screen_id != null) {
		irc_change_screen(screen_id);
	}
	
	var post = {};
	for (var key in defaultdata) {
		post[key] = defaultdata[key];
	}
	post['cmd'] = 'join';
	post['channel'] = channel;
	var ajax = new AJAXInteraction(irc_ajax_url, post, irc_ajax_callback);
	ajax.doPost();
}

// Clear a channel's peers
function irc_peers_clear(id)
{
	var nicklist = document.getElementById('irc_channel_'+id+'_peers');
	var nicklist_td = document.getElementById('irc_channel_'+id+'_peers_td');
	nicklist_td.style.display = '';
	while (nicklist.childNodes.length > 0) {
		nicklist.removeChild(nicklist.firstChild);
	}
}

// Add a peer to the list of peers for a channel
function irc_peers_add(id, nick)
{
	// find in list
	var nicklist = document.getElementById('irc_channel_'+id+'_peers');
	var peer_id = 'irc_channel_'+id+'_peer_'+nick;
	var old_peer = document.getElementById(peer_id);
	// add if doesn't exist
	if (!old_peer) {
		var new_peer = document.createElement('div');
		new_peer.className='irc_peer';
		new_peer.id=peer_id;
		// Click on the user to open up a query
		var new_peer_link = document.createElement('a');
		new_peer_link.onclick = function () {
			irc_change_screen(irc_get_new_screen(nick));
		}
		new_peer_link.appendChild(document.createTextNode(nick));
		new_peer.appendChild(new_peer_link);
		nicklist.appendChild(new_peer);
	}
}

// Remove a peer from the list of peers for a channel
function irc_peers_remove(id, nick)
{
	// find in list
	var nicklist = document.getElementById('irc_channel_'+id+'_peers');
	var peer_id = 'irc_channel_'+id+'_peer_'+nick;
	var old_peer = document.getElementById(peer_id);
	
	// remove if exists
	if (old_peer) {
		nicklist.removeChild(old_peer);
		return true;
	} else {
		return false;
	}
}

// A given nick has quit, part from all channels
function irc_peers_remove_quit(nick)
{
	var chans = [];
	for (var i = 0; i < irc_screen_list.length; ++i) {
		if (irc_screen_list[i] != null) {
			if (irc_peers_remove(i, nick)) {
				chans[chans.length] = i;
			}
		}
	}
	return chans;
}

// Set the local topic of a screen
function irc_topic_set(id, topic)
{
	var topic_div = document.getElementById('irc_channel_'+id+'_topic');
	while (topic_div.childNodes.length > 0) {
		topic_div.removeChild(topic_div.firstChild);
	}
	if (topic != '') {
		topic_div.appendChild(document.createTextNode("Topic: " + topic));
		topic_div.style.display = 'block';
	} else {
		topic_div.style.display = 'none';
	}
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
	ajax.doPost();
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
		irc_handle_ajax_errors(responseXML, false);
		var messages = responseXML.getElementsByTagName('msg');
		if (messages.length > 0) {
			for (var msgid = 0; msgid < messages.length; ++msgid) {
				var type = messages[msgid].attributes.getNamedItem('type').value;
				if (type != 'RPL_NAMREPLY' &&
					type != 'RPL_ENDOFNAMES' &&
					type != 'MODE' &&
					type != 'RPL_TOPIC' &&
					type != 'RPL_NOTOPIC')
				{
					var highlight = messages[msgid].attributes.getNamedItem('highlight');
					
					var new_message = document.createElement('div');
					new_message.className='irc_message';
					var sender_prefix = '<';
					var sender_postfix = '>';
					var show_type = false;
					
					var time = irc_xml_get_item(messages[msgid], 'time');
					var sender = irc_xml_get_item(messages[msgid], 'sender');
					var channels = [];
					
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
						if (sender) {
							channels = irc_peers_remove_quit(sender);
						}
					}
					else if (type == 'TOPIC') {
						new_message.className += ' channel';
						sender_prefix = '*** ';
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
					
					if (time) {
						var new_time = document.createElement('div');
						new_time.className='irc_message_time';
						new_time.appendChild(document.createTextNode('['+time+']'));
						new_message.appendChild(new_time);
						new_message.appendChild(document.createTextNode(' '));
					}
					
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
					
					if (!channels.length) {
						var channel = irc_xml_get_item(messages[msgid], 'channel');
						var id = irc_get_new_screen('server');
						if (channel) {
							id = irc_get_new_screen(channel);
						}
						channels[channels.length] = id;
					}
					
					var firstChan = true;
					for (var i = 0; i < channels.length; ++i) {
						var id = channels[i];
						
						var messages_div = document.getElementById('irc_channel_'+id+'_messages');
						if (firstChan) {
							messages_div.appendChild(new_message);
							firstChan = false;
						} else {
							messages_div.appendChild(new_message.cloneNode(true));
						}
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
					}
				}
				
				var channel = irc_xml_get_item(messages[msgid], 'channel');
				var channel_id = irc_get_screen(channel);
				
				// Names list provided?
				var names_element = messages[msgid].getElementsByTagName('names');
				if (names_element.length > 0) {
					var replace = names_element[0].attributes.getNamedItem('replace');
					var nicklist = document.getElementById('irc_channel_'+channel_id+'_peers');
					if (replace && replace.value == 'yes') {
						irc_peers_clear(channel_id);
					}
					var names = names_element[0].getElementsByTagName('name');
					if (names.length > 0) {
						if (channel) {
							for (var nameid = 0; nameid < names.length; ++nameid) {
								var nick = irc_xml_get_item(names[nameid], 'nick');
								if (nick) {
									var mode = names[nameid].attributes.getNamedItem('mode');
									if (mode && mode.value == 'part') {
										irc_peers_remove(channel_id,nick);
									} else {
										irc_peers_add(channel_id,nick);
									}
								}
							}
						}
					}
				}
				
				// Topic changed?
				var topic = irc_xml_get_item(messages[msgid], 'topic');
				if (topic) {
					irc_topic_set(channel_id, topic);
				}
				
				// Scroll to bottom
				var messages_div = document.getElementById('irc_channel_'+channel_id+'_messages');
				if (messages_div) {
					messages_div.scrollTop = messages_div.scrollHeight;
				}
			}
		}
	}
}

// Handle errors returned by ajax
function irc_handle_ajax_errors(responseXML, clear)
{
	var errors = responseXML.getElementsByTagName('error');
	var errormsg = '';
	for (var i = 0; i < errors.length; ++i) {
		var code = errors[i].attributes.getNamedItem('code');
		var retry = errors[i].attributes.getNamedItem('code');
		// Show error message
		errormsg = errors[i].firstChild.nodeValue;
	}
	if (errormsg != '') {
		irc_error(errormsg);
		if (irc_connected) {
			irc_disconnect();
		}
		return true;
	} else if (clear) {
		irc_clear_error();
		return false;
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
		function (responseXML) {
			if (responseXML) {
				if (!irc_handle_ajax_errors(responseXML, true)) {
					irc_connected = true;
					// Start pinging after connection
					if (!irc_pinging) {
						irc_ping();
						irc_pinging = true;
					}
				}
			}
		} );
	irc_clear_error();
	ajax.doPost();
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
		} );
	ajax.doPost();
	irc_connected = false;
}

/// Main onLoad function
function irc_onLoad()
{
	// Doesn't actually load anything. this may be handy in future
}

// Register onLoad function
// onLoadFunctions.push(irc_onLoad);
