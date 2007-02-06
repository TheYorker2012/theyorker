<?php

/**
 * @brief wikitext test controller.
 * @author James Hogan (jh559@cs.york.ac.uk)
 */
class Wikitext extends Controller {
	
	/**
	 * @brief Default constructor.
	 */
	function __construct()
	{
		parent::Controller();
		$this->load->helper('form');
		$this->load->library('wikiparser');
	}
	
	/**
	 * @brief Wikitest test page.
	 */
	function index()
	{
		if (!CheckPermissions('office')) return;
		
		// No POST data? just set wikitext to default string
		$wikitext = $this->input->post('wikitext');
		if ($wikitext === FALSE) {
			$wikitext  = '==This is the yorker wikitext parser==' . "\n";
			$wikitext .= '*This is an unordered list' . "\n";
			$wikitext .= '*#With an ordered list within' . "\n";
			$wikitext .= '*#And another item' . "\n";
			$wikitext .= "\n";
			$wikitext .= '#This is an ordered list' . "\n";
			$wikitext .= '#*With an unordered list within' . "\n";
			$wikitext .= '#*And another item' . "\n";
		} else if (get_magic_quotes_gpc()) {
			$wikitext = stripslashes($wikitext);
		}
		
		$data = array(
				'parsed_wikitext' => $this->wikiparser->parse($wikitext."\n",'wiki test'),
				'wikitext' => $wikitext,
			);
		
		// Set up the public frame
		$this->main_frame->SetTitle('Wikitext Preview');
		$this->main_frame->SetContentSimple('test/wikitext', $data);
		
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
}
?>
