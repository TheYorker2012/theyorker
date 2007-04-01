<?php
class Directorywizard extends Controller {

	function __construct()
	{
		parent::Controller();
		
		$this->load->library('organisations');
		$this->load->model('directory_model');
	}
	
	private function CreateDirectoryEntryName ($long_name){
		//strip non alpha-numerical symbols
		$new_string = preg_replace("/[^a-zA-Z0-9s]/", "", $long_name);
		//replace spaces with an underscore
		return str_replace(" ", "_", $new_string);
	}
	function index()
	{
		if (!CheckPermissions('public')) return;
		$this->pages_model->SetPageCode('directorywizard_pages');
		
		$data = array();
		if(!empty($_POST)){
			$data['information'] = $_POST;
			if(empty($_POST['organisations_name']) || empty($_POST['organisations_description']) || empty($_POST['suggestors_name']) || empty($_POST['suggestors_position']))
			{
				$this->messages->AddMessage('error', 'Please fill in all of the your details section and the general information section.');
			} else {
				//Store post data, so other varibles can be added to the array.
				$post_data = array(
					'type_id' => $_POST['organisation_type'],
					'name' => $_POST['organisations_name'],
					'suggestors_name' => $_POST['suggestors_name'],
					'suggestors_position' => $_POST['suggestors_position'],
					'description' => $_POST['organisations_description'],
					'postal_address' => $_POST['organisation_address'],
					'postcode' => $_POST['organisation_postcode'],
					'phone_external' => $_POST['phone_external'],
					'phone_internal' => $_POST['phone_internal'],
					'fax_number' => $_POST['fax_number'],
					'email_address' => $_POST['contact_email'],
					'url' => $_POST['organisation_website'],
					'opening_hours' => $_POST['opening_times'],
				);
				
				//create a useable directory entry name and add the directory entry name to the post data
				$post_data['directory_entry_name'] = $this->CreateDirectoryEntryName($post_data['name']);
				$exists_already = $this->directory_model->GetDirectoryOrganisationByEntryName($post_data['directory_entry_name']);
				if(empty($exists_already)){
					//create directory entry
					$result = $this->directory_model->AddDirectoryEntry($post_data);
					if($result == 1)
					{
					//create directory entry revision
					$this->directory_model->AddDirectoryEntryRevision($post_data['directory_entry_name'], $post_data);
					$this->messages->AddMessage('success', 'Thank you for your suggestion.');
					} else {
					//Something went wrong so don't make a revision
					$this->messages->AddMessage('error', 'An error occurred when your details were submitted, please try again.');
					}
				}else{
				//Name has been taken already!
				$this->messages->AddMessage('error', 'The name of  your suggestion already exists in the directory. If you still wish to submit your suggestion please change the name.');
				}
			}
		}
		$data['main_text'] = $this->pages_model->GetPropertyWikiText('maintext_general');
		$data['submit_text'] = $this->pages_model->GetPropertyText('maintext_submit');
		$data['org_types'] = $this->directory_model->GetOrganisationTypes();
		$this->main_frame->SetContentSimple('directory/directory_wizard', $data);
				
		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}
}
?>