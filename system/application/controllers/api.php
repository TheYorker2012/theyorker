<?php
/**
 * This controller provides an API to the yorker, post in, JSON out.
 *
 * @author Mark Goodall (mg512)
 * 
 */
class Api extends Controller {

	function Api() {
		parent::Controller();
	}
	//TODO migrate to JSON
	//TODO limit requests via API key, yorker API Key = unlimited, referer must be yorker.
	function index() {
		if (!CheckPermissions('public')) return;
		
		$this->pages_model->SetPageCode('api');
		
		$data = array();
		
		// Set up the public frame
		$this->main_frame->SetContentSimple('', $data);
		
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}

	//recieves the ajax query
	// in future, throttle the number of queries from host
	function sitesearch() {
		$this->output->cache();
		$this->load->model('orwell');
		$data = $this->orwell->ajax($this->input->post('site-search'));
		
		$this->load->view('search/ajax-results', $data);
	}

	function usersearch() {
		$this->output->cache(1, 3);
		$this->load->helper('entity');
		if (is_username($this->input->post('username'))) {
			$this->output->set_output('{"'.$this->input->post('username').'":{"valid":true}}');
		}
		$this->output->set_output('{"'.$this->input->post('username').'":{"valid":false}}');
	}

}
?>
