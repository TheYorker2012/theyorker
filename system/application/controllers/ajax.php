<?php

// Misc AJAX functions

class Ajax extends Controller
{
	function wikiparse()
	{
		$this->load->helper('input_wikitext');
		$_GET['input_wikitext_preview_field'] = 'parse';
		$wiki = new InputWikitextInterface('parse');
		$wiki->SetWikiparser();
		// should have exited by now
		show_404();
	}
}

?>
