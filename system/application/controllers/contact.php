<?php
/**
 * This is the controller for contact us page
 *
 * \author Alex Fargus	
 */
class Contact extends Controller {


	/**
	 * @brief Default Constructor.
	 */
	function __construct()
	{
		parent::Controller();
		$this->load->model('Contact_Model');
	}
	
	function index()
	{
		if (!CheckPermissions('public')) return;
		
		$this->pages_model->SetPageCode('contact_us');
		
		//Various arrays defined
		$data = array();	//Stores all data to be passed to view

		$data['welcome_title'] = $this->pages_model->GetPropertyText('welcome_title');
		$data['welcome_text']  = $this->pages_model->GetPropertyWikitext('welcome_text');
		//Get the contacts to be displayed
		$data['contacts'] = $this->Contact_Model->GetAllContacts();
		// Set up the public frame
		$this->main_frame->SetContentSimple('/contact/contact', $data);

		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}

	function sendmail(){
		//Still need to add captcha
		if (!CheckPermissions('public')) return;
		
		$to = $this->input->post('recipient');
		$from = 'From: '.$this->input->post('contact_email')."\r\n";
		$from = "Bob";
		$subject = $this->input->post('contact_subject'); 
		$message = $this->input->post('contact_message');
		if ($to && $subject && $message && $this->input->post('contact_email')){
			if (mail($to,$subject,$message,$from)) {
				redirect('/about');
			} else {
				redirect('/contact');
			}
		} else {
			redirect('/failed');
		}
	}

}
?>
