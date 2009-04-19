function AJAXInteraction(url, post, callback, failcallback)
{
	var req = init();
	req.onreadystatechange = processRequest;
		
	function init() {
		try {
			// Firefox, Opera 8.0+, Safari
			return new XMLHttpRequest();
		}
		catch (e) {
			// Internet Explorer
			try {
				return new ActiveXObject("Msxml2.XMLHTTP");
			}
			catch (e) {
				try {
					return new ActiveXObject("Microsoft.XMLHTTP");
				}
				catch (e) {
					alert("Your browser does not support AJAX!");
					return null;
				}
			}
		}
	}
	
	function processRequest () {
		// readyState of 4 signifies request is complete
		if (req.readyState == 4) {
			// status of 200 signifies sucessful HTTP call
			if (req.status == 200) {
				if (callback) {
					callback(req.responseXML);
				}
			}
			else {
				if (undefined != failcallback) {
					failcallback(req.status, req.statusText);
				}
			}
		}
	}

	this.getPostString = function()
	{
		var post_string = "";
		var first = 1;
		for (var key in post) {
			if (!first) {
				post_string += "&";
			} else {
				first = 0;
			}
			post_string += key+"="+encodeURIComponent(post[key]);
		}
		return post_string;
	}
	
	this.doGet = function() {
		// make a HTTP GET request to the URL asynchronously
		var post_string = this.getPostString();
		req.open("GET", url+'?'+post_string, true);
		// send request
		req.send(null);
	}
	
	this.doPost = function() {
		// make a HTTP POST request to the URL asynchronously
		var post_string = this.getPostString();
		req.open("POST", url, true);
		// proper header information
		req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		req.setRequestHeader("Content-length", post_string.length);
		req.setRequestHeader("Connection", "close");
		// send request
		req.send(post_string);
	}
}
