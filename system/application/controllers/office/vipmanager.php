<?php
/*
 * Controller for vip manager
 * \author Nick Evans nse500
 */

class Vipmanager extends Controller
{
	/// Default constructor.
	function __construct()
	{
		parent::controller();
		$this->load->model('members_model');
	}

	/// Default page.
	function index()
	{
		if (!CheckPermissions('office')) return;

		$this->load->model('user_auth');

		if (!($this->user_auth->officeType == 'High' || $this->user_auth->officeType == 'Admin')) {
			$this->messages->AddMessage('error', 'Permission denied. You must be an editor to view this page.');
			redirect('/office/');
		}

		$this->pages_model->SetPageCode('vip manager');

		$data = array(
			'main_text'    => $this->pages_model->GetPropertyWikitext('main_text'),
			'target'       => $this->uri->uri_string(),
			'members'      => $this->members_model->GetMemberDetails(false)
		);

		$this->main_frame->SetContentSimple('office/vipmanager/vip_list', $data);

		$this->main_frame->Load();
	}

	function promote($EntityId, $OrgId)
	{
		//has user got access to office
		if (!CheckPermissions('office')) return;

		$this->load->model('user_auth');

		if (!($this->user_auth->officeType == 'High' || $this->user_auth->officeType == 'Admin')) {
			$this->messages->AddMessage('error', 'Permission denied. You must be an editor to perform this operation.');
			redirect('/office/');
		}

		if ( $this->members_model->UpdateVipStatus('approved', $EntityId, $OrgId) ) {
			$user = $this->members_model->GetUsername($EntityId);
			$org = $this->members_model->GetOrganisationFromId($OrgId);

			$to = $user->entity_username.$this->config->Item('username_email_postfix');
			$from = $this->pages_model->GetPropertyText('system_email', true);
			$subject = $this->pages_model->GetPropertyText('vip_promotion_email_subject', true);
			$message = str_replace('%%nickname%%',$user->nickname,str_replace('%%organisation%%',$org->organisation_name,$this->pages_model->GetPropertyText('vip_promotion_email_body', true)));

			$this->load->helper('yorkermail');
			try {
			    yorkermail($to,$subject,$message,$from);
			    $this->main_frame->AddMessage('success',
			    	'Member promoted successfully. A notification e-mail has also been sent.' );
			} catch (Exception $e) {
			    $this->main_frame->AddMessage('error',
			    	'Member promoted successfully, but e-mail sending <b>failed</b>. '.$e->getMessage() );
			}
		} else {
			$this->messages->AddMessage('error','No changes were made to the membership.');
		}
		return redirect('/office/vipmanager');
	}

	function demote($EntityId, $OrgId)
	{
		//has user got access to office
		if (!CheckPermissions('office')) return;

		$this->load->model('user_auth');

		if (!($this->user_auth->officeType == 'High' || $this->user_auth->officeType == 'Admin')) {
			$this->messages->AddMessage('error', 'Permission denied. You must be an editor to perform this operation.');
			redirect('/office/');
		}

		if ( $this->members_model->UpdateVipStatus('none', $EntityId, $OrgId) ) {
			$this->main_frame->AddMessage('success',
			    'The member was demoted.' );
		} else {
			$this->messages->AddMessage('error','No changes were made to the membership.');
		}
		return redirect('/office/vipmanager');
	}


}

?>
