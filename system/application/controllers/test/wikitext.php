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
		
		// Load the public frame
		$this->load->library('frame_public');
	}
	
	/**
	 * @brief Wikitest test page.
	 */
	function index()
	{
		// No POST data? just set wikitext to default string
		$wikitext = $this->input->post('wikitext');
		if ($wikitext === FALSE) {
			$wikitext  = '==This is the yorker wikitext parser==' . "\n";
			$wikitext .= 'Enter wikitext here:';
		} else if (get_magic_quotes_gpc()) {
			$wikitext = stripslashes($wikitext);
		}
		
		$data = array(
				'parsed_wikitext' => $this->wikiparser->parse($wikitext."\n",'wiki test'),
				'wikitext' => $wikitext,
			);
		
		 // Set up the subview
		$wikitext_test_view = $this->frames->view('test/wikitext.php', $data);
		
		// Set up the public frame
		$this->frame_public->SetContent($wikitext_test_view);
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}
}
?>
