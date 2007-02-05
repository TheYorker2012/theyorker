/**
 *	Provides the Society/AU selection and preview controls for subscriptions
 *	Pages used: /register/societies/	/register/au/
 *
 *	@author Chris Travis (cdt502 - ctravis@gmail.com)
 */

	var lastViewed = -1;

	function get_info (socid) {
		document.getElementById('socdesc').innerHTML = "<div class='ajax_loading'><img src='/images/prototype/prefs/loading.gif' alt='Loading' title='Loading' /> Retrieving Description</div>";
		document.getElementById('socname').innerHTML = societies[socid]['name'];
		document.getElementById('socinfo').innerHTML = "<a href='/directory/" + societies[socid]['directory'] + "/' target='_blank'><b>The Yorker Directory Entry</b></a><br /><b>Website:</b> <a href='" + societies[socid]['url'] + "' target='_blank'>" + societies[socid]['url'] + "</a>";
		document.getElementById('soc_subscribe').className = 'hide';
		if ((lastViewed >= 0) && (document.getElementById('soc' + lastViewed).className != 'selected')) {
			document.getElementById('soc' + lastViewed).className = 'unselected';
		}
		lastViewed = socid;
		if (document.getElementById('soc' + socid).className != 'selected') {
			document.getElementById('soc' + socid).className = 'viewing';
		}
		xajax_getInfo(socid);
		return false;
	}

	function socSubscribe () {
		document.getElementById('soc_subscribe').className = 'hide';
		document.getElementById('sub_loading').className = 'show';
		document.getElementById('soc' + lastViewed).className = 'loading';
		xajax_societySubscription(lastViewed);
		return false;
	}

	// pre-load ajax image's
	imageObj = new Image();
	imageObj.src = '/images/prototype/prefs/loading.gif';
	imageObj.src = '/images/prototype/prefs/success.gif';
	imageObj.src = '/images/prototype/prefs/arrow.gif';
	imageObj.src = '/images/prototype/prefs/yorker-bg.png';