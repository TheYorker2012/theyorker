<?php

$config['saved_login_duration'] = 60*60*24*30;
$config['username_email_postfix'] = '@york.ac.uk';

$config['company_organisation_id'] = 'theyorker';
$config['company_entity_id'] = 378;

$config['temp_local_path'] = '/home/yorker/www.theyorker/www/tmp/uploads/';
$config['temp_web_address'] = '/tmp/uploads/';

$config['static_local_path'] = '/home/yorker/static.theyorker';
$config['static_web_address'] = 'http://static.theyorker.co.uk';
$config['static_ftp_address'] = 'theyorker.co.uk';
$config['static_ftp_username'] = 'staticftp';
$config['static_ftp_password'] = 'yrkrsttc';
$config['podcast_rss_feed'] = 'www.theyorker.co.uk/feeds/podcasts';

$config['no_reply_email_address'] = 'no-reply@theyorker.co.uk';
$config['editor_email_address'] = 'editor@theyorker.co.uk';
$config['webmaster_email_address'] = 'webmaster@theyorker.co.uk';
$config['pr_officer_email_address'] = 'publicrelations@theyorker.co.uk';
$config['pr_officer_name'] = 'Anna Greenleaves and James Koziaryn';

$config['rss_feed_stats'] = false;
$config['enable_adsense'] = false;
$config['enable_analytics'] = false;

$config['twitter_admin_userid'] = 'ykr';
$config['twitter_admin_passwd'] = 'naG88fEn@laTh46ButT';

$config['comments'] = array(
	// Who has permission to edit a comment
	'edit'  => array(
		'author'    => true,
		'moderator' => true,
	),
	// Number of comments displayed on a page before a new page is started
	'max_per_page' => 20,
);

?>