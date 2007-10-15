<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// The google maps API key will need to be changed whenever we change server
// There is a google account to do this:
//   username - theyorkermaps
//   password - same as the database

switch($_SERVER['SERVER_NAME']) {
	case 'theyorker2.gmghosting.com':
		$config['google']['api_key'] = 'ABQIAAAA4LuflJA4VPgM8D-gyba8yBTRyAb-KmMkdWctvtd_CKS_Gh2u2BQV2EX1b0qY4PM1eJgajR_yMSsENw';
		break;
	case 'www.theyorker.co.uk':
		$config['google']['api_key'] = 'ABQIAAAAG00OtuAD_rmvNO96T7IKKRSsupC0bCgwgBIcSr9y7Z9nV9qoOBRUsXmbgvycoXlZCvBZpQ0TqmIW0A';
		break;
	case 'theyorker.ado.is-a-geek.net':
		$config['google']['api_key'] = 'ABQIAAAA4LuflJA4VPgM8D-gyba8yBQqxqph5xmYOwiXPhDCFStDHdEmQxStGW_JuIRgEXmatGyMz88xSyDOow';
		break;
	case 'trunk.theyorker.gmghosting.com':
		$config['google']['api_key'] = 'ABQIAAAA4LuflJA4VPgM8D-gyba8yBR6L4oJGHgAGz3lHpOpEmhODgCApRRRjD3jh4l746FkEpP3mk8KX1qNQQ';
		break;
	case 'localhost':
		$config['google']['api_key'] = 'ABQIAAAA6vFF9HQVRyZ6pmMbEW2o8hT4dMPT2p45abcp05Afs400sGBlHhRGtu7daesOnj_9G28sgfkXgxTfxQ';
		break;
	default:
		$config['google']['api_key'] = 'unknown';
}

?>
