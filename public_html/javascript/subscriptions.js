/**
 *	Provides the subscriptions interface
 *
 *	@author Chris Travis (cdt502 - ctravis@gmail.com)
 */

	var lastViewed = -1;

	function get_info (socid) {
		document.getElementById('subscription_info').style.display = 'block';
		document.getElementById('subscription_desc').innerHTML = "<div class='ajax_loading'><img src='/images/prototype/prefs/loading.gif' alt='Loading' title='Loading' /> Retrieving Description</div>";
		document.getElementById('subscription_name').innerHTML = societies[socid]['name'];
		document.getElementById('subscription_subscribe').className = 'button hide';
		if ((lastViewed >= 0) && (document.getElementById('soc' + lastViewed).className != 'selected')) {
			document.getElementById('soc' + lastViewed).className = 'unselected';
		}
		lastViewed = socid;
		if (document.getElementById('soc' + socid).className != 'selected') {
			document.getElementById('soc' + socid).className = 'viewing';
		}
		xajax__getInfo(socid);
		return false;
	}

	function socSubscribe () {
		document.getElementById('soc_subscribe').className = 'hide';
		document.getElementById('subscription_loading').style.display = 'block';
		document.getElementById('soc' + lastViewed).className = 'loading';
		xajax__changeSubscription(lastViewed);
		return false;
	}

	// pre-load ajax image's
	imageObj = new Image();
	imageObj.src = '/images/prototype/prefs/loading.gif';
	imageObj.src = '/images/prototype/prefs/success.gif';
	imageObj.src = '/images/prototype/prefs/arrow.gif';
	imageObj.src = '/images/prototype/prefs/yorker-bg.png';