<?php

class CrosswordsMiniView
{
	private $latest;
	private $next;

	function __construct($count)
	{
		$ci = &get_instance();
		$ci->load->model('crosswords_model');

		// Published crosswords
		$this->latest = $ci->crosswords_model->GetCrosswords(null,null, null,null,true,null, $count,'DESC');

		// Look into future as well so we can say when next crossword is expected
		$this->next   = $ci->crosswords_model->GetCrosswords(null,null, null,true,null,null, 1,'ASC');
	}

	function Load()
	{
		$data = array(
			'Latest' => &$this->latest,
			'Next'   => &$this->next,
		);
		get_instance()->load->view('crosswords/miniview', $data);
	}
}

?>
