function AJAXInteraction(url, post, callback)
{
	var req = init();
	req.onreadystatechange = processRequest;
		
	function init() {
		if (window.XMLHttpRequest) {
		return new XMLHttpRequest();
		} else if (window.ActiveXObject) {
		return new ActiveXObject("Microsoft.XMLHTTP");
		}
	}
	
	function processRequest () {
		// readyState of 4 signifies request is complete
		if (req.readyState == 4) {
			// status of 200 signifies sucessful HTTP call
			if (req.status == 200) {
				if (callback) callback(req.responseXML);
			}
		}
	}
	
	this.doGet = function() {
		// make a HTTP GET request to the URL asynchronously
		var post_string = url+"?";
		var first = 1;
		for (var key in post) {
			if (!first) {
				post_string += "&";
			} else {
				first = 0;
			}
			post_string += key+"="+encodeURIComponent(post[key]);
		}
		req.open("GET", post_string, true);
		req.send(null);
	}
}
