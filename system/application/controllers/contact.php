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

		$contact = $this->Contact_Model->GetContact($this->input->post('recipient'));

		$to = $contact->contact_us_email;
		$from = $this->input->post('contact_email');
		$subject = $this->input->post('contact_subject');
		$message = $this->input->post('contact_message');

		if (!$subject) $subject = 'No subject';

		if ($to && $subject && $message && $from){
			$this->load->helper('yorkermail');
			try {
				yorkermail($to,$subject,$message,$from);
				$this->main_frame->AddMessage('success',
					'Thank you for contacting us.' );
				redirect('/about');
			} catch (Exception $e) {
				$this->main_frame->AddMessage('error',
					'E-mail sending failed: '.$e->getMessage() );
				redirect('/contact');
			}
		} elseif (!$to) {
			$this->main_frame->AddMessage('error', 'E-mail sending failed. Please enter your e-mail address.');
			redirect('/contact');
		} else {
			$this->main_frame->AddMessage('error', 'E-mail sending failed. Please enter a message to send.');
			redirect('/contact');
		}
	}
}
?>
