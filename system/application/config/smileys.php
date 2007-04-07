<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| SMILEYS
| -------------------------------------------------------------------
| This file contains an array of smileys for use with the emoticon helper.
| Individual images can be used to replace multiple simileys.  For example:
| :-) and :) use the same image replacement.
|
| Please see user guide for more info: 
| http://www.codeigniter.com/user_guide/helpers/smiley_helper.html
|
*/

// Default code ignitor smileys
$smileys = array(

//	smiley			image name						width	height	alt

	':lol:'			=>	array('lol.gif',			'19',	'19',	'LOL'),
	':cheese:'		=>	array('cheese.gif',			'19',	'19',	'cheese'),
	':-)'			=>	array('smile.gif',			'19',	'19',	'smile'),
	':)'			=>	array('smile.gif',			'19',	'19',	'smile'),
	';-)'			=>	array('wink.gif',			'19',	'19',	'wink'),
	';)'			=>	array('wink.gif',			'19',	'19',	'wink'),
	':smirk:'		=>	array('smirk.gif',			'19',	'19',	'smirk'),
	':roll:'		=>	array('rolleyes.gif',		'19',	'19',	'rolleyes'),
	':-S'			=>	array('confused.gif',		'19',	'19',	'confused'),
	':S'			=>	array('confused.gif',		'19',	'19',	'confused'),
	':wow:'			=>	array('surprise.gif',		'19',	'19',	'surprised'),
	':bug:'			=>	array('bigsurprise.gif',	'19',	'19',	'big surprise'),
	':-P'			=>	array('tongue_laugh.gif',	'19',	'19',	'tongue laugh'),
	'%-P'			=>	array('tongue_rolleye.gif',	'19',	'19',	'tongue rolleye'),
	';-P'			=>	array('tongue_wink.gif',	'19',	'19',	'tongue wink'),
	':P'			=>	array('rasberry.gif',		'19',	'19',	'rasberry'),
	':blank:'		=>	array('blank.gif',			'19',	'19',	'blank stare'),
	':long:'		=>	array('longface.gif',		'19',	'19',	'long face'),
	':ohh:'			=>	array('ohh.gif',			'19',	'19',	'ohh'),
	':grrr:'		=>	array('grrr.gif',			'19',	'19',	'grrr'),
	':gulp:'		=>	array('gulp.gif',			'19',	'19',	'gulp'),
	'8-/'			=>	array('ohoh.gif',			'19',	'19',	'oh oh'),
	':down:'		=>	array('downer.gif',			'19',	'19',	'downer'),
	':D'			=>	array('grin.gif',			'19',	'19',	'grin'),
	':d'			=>	array('grin.gif',			'19',	'19',	'grin'),
	':red:'			=>	array('embarrassed.gif',	'19',	'19',	'red face'),
	':sick:'		=>	array('sick.gif',			'19',	'19',	'sick'),
	':shut:'		=>	array('shuteye.gif',		'19',	'19',	'shut eye'),
	':-/'			=>	array('hmm.gif',			'19',	'19',	'hmmm'),
	'>:('			=>	array('mad.gif',			'19',	'19',	'mad'),
	':mad:'			=>	array('mad.gif',			'19',	'19',	'mad'),
	'>:-('			=>	array('angry.gif',			'19',	'19',	'angry'),
	':angry:'		=>	array('angry.gif',			'19',	'19',	'angry'),
	':zip:'			=>	array('zip.gif',			'19',	'19',	'zipper'),
	':kiss:'		=>	array('kiss.gif',			'19',	'19',	'kiss'),
	':ahhh:'		=>	array('shock.gif',			'19',	'19',	'shock'),
	':coolsmile:'	=>	array('shade_smile.gif',	'19',	'19',	'cool smile'),
	':coolsmirk:'	=>	array('shade_smirk.gif',	'19',	'19',	'cool smirk'),
	':coolgrin:'	=>	array('shade_grin.gif',		'19',	'19',	'cool grin'),
	':coolhmm:'		=>	array('shade_hmm.gif',		'19',	'19',	'cool hmm'),
	':coolmad:'		=>	array('shade_mad.gif',		'19',	'19',	'cool mad'),
	':coolcheese:'	=>	array('shade_cheese.gif',	'19',	'19',	'cool cheese'),
	':vampire:'		=>	array('vampire.gif',		'19',	'19',	'vampire'),
	':snake:'		=>	array('snake.gif',			'19',	'19',	'snake'),
	':exclaim:'		=>	array('exclaim.gif',		'19',	'19',	'excaim'),
	':question:'	=>	array('question.gif',		'19',	'19',	'question'),
	// From kopete MSN theme msn7_transparent
	'(Y)'			=>	array('thumbs_up.gif',		'19',	'19',	'thumbs up'),
	'(y)'			=>	array('thumbs_up.gif',		'19',	'19',	'thumbs up'),
	'(N)'			=>	array('thumbs_down.gif',	'19',	'19',	'thumbs down'),
	'(n)'			=>	array('thumbs_down.gif',	'19',	'19',	'thumbs down'),
	'(sn)'			=>	array('53_53.gif',			'19',	'19',	'snail'),
	'(pi)'			=>	array('57_57.gif',			'19',	'19',	'pizza'),
	'(au)'			=>	array('59_59.gif',			'19',	'19',	'car'),
	'(st)'			=>	array('66_66.gif',			'19',	'19',	'storm'),
	'(B)'			=>	array('beer_mug.gif',		'19',	'19',	'beer'),
	'(b)'			=>	array('beer_mug.gif',		'19',	'19',	'beer'),
	'(L)'			=>	array('heart.gif',			'19',	'19',	'heart'),
	'(l)'			=>	array('heart.gif',			'19',	'19',	'heart'),
	'(U)'			=>	array('heart.gif',			'19',	'19',	'broken heart'),
	'(u)'			=>	array('broken_heart.gif',	'19',	'19',	'broken heart'),
	'(^)'			=>	array('cake.gif',			'19',	'19',	'cake'),
	'(P)'			=>	array('camera.gif',			'19',	'19',	'camera'),
	'(p)'			=>	array('camera.gif',			'19',	'19',	'camera'),
	'(C)'			=>	array('coffee.gif',			'19',	'19',	'coffee'),
	'(c)'			=>	array('coffee.gif',			'19',	'19',	'coffee'),
	'(~)'			=>	array('film.gif',			'19',	'19',	'film'),
	'(I)'			=>	array('lightbulb.gif',		'19',	'19',	'lightbulb'),
	'(i)'			=>	array('lightbulb.gif',		'19',	'19',	'lightbulb'),
	'(D)'			=>	array('martini.gif',		'19',	'19',	'martini'),
	'(d)'			=>	array('martini.gif',		'19',	'19',	'martini'),
	'(8)'			=>	array('note.gif',			'19',	'19',	'note'),
	'(5)'			=>	array('moon.gif',			'19',	'19',	'moon'),
	'(*)'			=>	array('star.gif',			'19',	'19',	'star'),
	'(G)'			=>	array('present.gif',		'19',	'19',	'present'),
	'(g)'			=>	array('present.gif',		'19',	'19',	'present'),
	'(F)'			=>	array('rose.gif',			'19',	'19',	'rose'),
	'(f)'			=>	array('rose.gif',			'19',	'19',	'rose'),
	'(W)'			=>	array('wilted_rose.gif',	'19',	'19',	'wilted rose'),
	'(w)'			=>	array('wilted_rose.gif',	'19',	'19',	'wilted rose'),

);

?>