<?php
/*
 * Controller for contact us pages
 * \author Alex Fargus (agf501)
 */

class ContactUs extends Controller
{
	/// Default constructor.
	function __construct()
	{
		parent::controller();
		$this->load->model('Contact_Model');
	}
	
	/// Default page.
	function index()
	{
		if (!CheckPermissions('office')) return;
		
		$this->pages_model->SetPageCode('contact_us');
		
		$data = array();
		$this->main_frame->SetContentSimple('office/general/contact_us', $data);
		
		$this->main_frame->Load();
	}

	//Add contact page.
	function addcontact()
	{
		//has user got access to office
		if (!CheckPermissions('office')) return;
		$name = $this->input->post('name');
		$email = $this->input->post('email');
		$description = $this->input->post('description');
		if ($name && $email && $description){
			$this->load->model('Contact_Model');
			$this->Contact_Model->InsertContact($name,$email,$description);
			redirect('/office/contactus');
		} else {
			redirect('');
		}
	}
}

?>
