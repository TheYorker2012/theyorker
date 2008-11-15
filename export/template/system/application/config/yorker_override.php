<?php

/**
 * @file config/yorker_override.php
 * @brief Yorker configuration overrides.
 *
 * This config file is guaranteed to be loaded after config/yorker.php so that
 * it can override those config options.
 */

// Enable google adsense
$config['enable_adsense'] = true;
$config['enable_analytics'] = true;

$config['temp_local_path'] = '/srv/www/public_html/tmp/';
$config['temp_web_address'] = '/tmp/';
$config['static_local_path'] = '/srv/static';

?>