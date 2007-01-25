<?php

class Dev extends Controller {

	function index()
	{
		echo 'Devr\'s Status page\n';
		echo 'If you think this is wrong then email mg512\n';
		echo 'Info dumps follow:\n';
		echo system('svn info');
	}
	
	function phpinfo()
	{
		phpinfo();
	}
}
?>
