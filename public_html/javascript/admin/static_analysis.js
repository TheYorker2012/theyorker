// Javascript for static analyser
// Author: James Hogan (james at albanarts dot com)
// Copyright (C) 2007 The Yorker

// Manages:
//  * starting of static analysis tests
//  * displaying of static analysis test results

var tests = {};

// Start the selected tests.
function start_tests()
{
	var tests_string = '';
	var comma = '';
	for (var name in tests) {
		var input = document.getElementById('test_'+name);
		if (input && input.checked) {
			tests_string += comma + name;
			comma = ',';
		}
	}
	
	var post = {};
	post['tests'] = tests_string;
	var ajax = new AJAXInteraction('/admin/tools/test/static/ajax', post, ajax_test_callback);
	ajax.doPost();
}

// Callback after xml received.
function ajax_test_callback(responseXML)
{
	if (responseXML) {
		var results = document.getElementById('results');
		if (results) {
			var texts = responseXML.getElementsByTagName('line');
			var new_pre = document.createElement('pre');
			for (var i in texts) {
				if (texts[i].firstChild) {
					new_pre.appendChild(document.createTextNode(texts[i].firstChild.nodeValue));
					new_pre.appendChild(document.createElement('br'));
				}
			}
			results.appendChild(new_pre);
		}
	}
}
