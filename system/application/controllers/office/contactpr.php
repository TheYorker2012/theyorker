<?php

class ContactPR extends Controller
{
	/// Default constructor.
	function __construct()
	{
		parent::controller();
	}
	
	/// Default page.
	function index()
	{
		if (!CheckPermissions('vip')) return;
		
		//load the required models and libraries
		$this->load->model('pr_model','pr_model');
		$this->load->model('members_model','members_model');
		
		//setup the page properties
		$this->pages_model->SetPageCode('viparea_contactpr');
		
		//set the defaults for the email
		$subject = '';
		$content = '';
		
		//get the rep data from the pr model
		$rep_data = $this->pr_model->GetOrganisationRatings(VipOrganisation());
		if (isset($rep_data['rep'])) {
			$rep = $rep_data['rep'];
			$rep['email'] = $this->members_model->GetMemberEmail($rep['id']);
			$rep['has_rep'] = true;
		}
		else {
			$rep['name'] = $this->config->item('pr_officer_name');
			$rep['email'] = $this->config->item('pr_officer_email_address');
			$rep['has_rep'] = false;
		}
		
		//users data
		$user_name = $this->members_model->GetMemberName($this->user_auth->entityId);
		
		if (isset($_POST['submit_save_advert'])) {
			if ($_POST['a_subject'] == '') {
				$this->messages->AddMessage('error', 'You must enter a subject for the email.');
				$content = $_POST['a_content'];
			}
			else if ($_POST['a_content'] == '') {
				$this->messages->AddMessage('error', 'You must enter a message for the email.');
				$subject = $_POST['a_subject'];
			}
			else {
				//no errors so send the email
				$this->load->helper('yorkermail');
				$to = $rep['email'];
				$from = VipOrganisationName().' - '.$user_name.' <'.$this->members_model->GetMemberEmail($this->user_auth->entityId).'>';
				//try to send the email, report fail if error occurs
				try {
					yorkermail(
						$to,
						$_POST['a_subject'],
						$_POST['a_content'],
						$from);
					$this->messages->AddMessage('success', 'The email has been sent.');
				}
				catch (Exception $e) {
					$this->main_frame->AddMessage('error', $e->getMessage() );
				}
			}
		}
		
		$data = array(
			'main_text'			=> $this->pages_model->GetPropertyWikitext('main_text'),
			'message_pr_target'	=> vip_url('contactpr'),
			'rep'				=> $rep,
			'subject'			=> $subject,
			'content'			=> $content
		);
		$this->main_frame->SetContentSimple('viparea/contactpr', $data);
		
		$this->main_frame->Load();
	}
}

?>