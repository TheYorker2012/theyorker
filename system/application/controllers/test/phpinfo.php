<?php

class phpinfo extends Controller {

	/**
	 * @brief Default Constructor.
	 */
	function __construct() {
		parent::Controller();
	}

	function index() {
		echo phpinfo();
	}
}
?>
