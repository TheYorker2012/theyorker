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
	 * 
	 * @todo Fix that it escapes single quotes.
	 */
	function index()
	{
		// No POST data? just set wikitext to default string
		$wikitext = $this->input->post('wikitext');
		if ($wikitext === FALSE) {
			$wikitext  = '==This is the yorker wikitext parser==' . "\n";
			$wikitext .= 'Enter wikitext here:';
		}
		
		$data = array(
				'parsed_wikitext' => $this->wikiparser->parse($wikitext."\n",'wiki test'),
				'wikitext' => $wikitext,
			);
		
		$this->load->view('test/wikitext.php',$data);
	}
}
?>
