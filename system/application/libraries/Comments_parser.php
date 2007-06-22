<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file Comments_parser.php
 * @author James Hogan (jh559@cs.york.ac.uk)
 * @brief Extension of basic wikiparser for comments.
 */

// Ensure that the wikiparser is loaded.
$CI = & get_instance();
$CI->load->library('wikiparser');

/// A special wikitext parser for comments.
class Comments_parser extends Wikiparser
{
	/// Default constructor
	function __construct()
	{
		parent::__construct();
		// Set the newline mode to line break.
		$this->newline_mode = 'br';
		$this->enable_headings = false;
		$this->enable_youtube = false;
		$this->enable_quickquotes = false;
		unset($this->templates['pull_quote']);
	}
}

?>