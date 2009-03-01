/**
 *	@brief		JavaScript News Ticker
 *	@requires	Prototype
 *	@requires	Scriptaculous
 *	@author		Chris Travis
 */

var tickerDisplaySpeed = 6000;
var tickerFadeInSpeed = 2.0;
var tickerFadeOutSpeed = 1.0;
var tickerArticles = new Array();
var tickerCurrentArticle = -1;
var tickerContainer = null;
var tickerTimer = null;

function tickerInit (object) {
	tickerContainer = object;
}

function tickerAdd (headline, url) {
	tickerArticles[tickerArticles.length] = new Array(headline, url);
}

function tickerStart () {
	if (tickerContainer == null || tickerContainer == undefined || tickerArticles.length == 0) return;
	tickerTimer = setTimeout('tickerHide()', tickerDisplaySpeed);
}

function tickerNext () {
	tickerCurrentArticle = (tickerCurrentArticle + 1) % tickerArticles.length;
	var ele_title = document.createElement('span');
	ele_title.innerHTML = tickerArticles[tickerCurrentArticle][0];
	if (tickerArticles[tickerCurrentArticle][1] != null && tickerArticles[tickerCurrentArticle][1] != undefined && tickerArticles[tickerCurrentArticle][1] != "") {
		var ele = document.createElement('a');
		ele.href = tickerArticles[tickerCurrentArticle][1];
		ele.appendChild(ele_title);
	} else {
		var ele = ele_title;
	}
	$(tickerContainer).innerHTML = '';
	$(tickerContainer).appendChild(ele);
//	$(tickerContainer).fadeIn(tickerFadeInSpeed);
	Effect.Appear(tickerContainer, {duration: tickerFadeInSpeed});
	this.timer = setTimeout('tickerHide()', tickerDisplaySpeed);
}

function tickerHide () {
//	$(tickerContainer).fadeOut(tickerFadeOutSpeed, tickerNext);
	Effect.Fade(tickerContainer, {duration: tickerFadeOutSpeed});
	this.timer = setTimeout('tickerNext()', tickerFadeOutSpeed * 1000);
}