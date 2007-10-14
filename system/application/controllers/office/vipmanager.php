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
		if (!CheckPermissions('editor')) return;

		$this->load->model('user_auth');

		/* obsolete ? */
		if (!($this->user_auth->officeType == 'High' || $this->user_auth->officeType == 'Admin')) {
			$this->messages->AddMessage('error', 'Permission denied. You must be an editor to view this page.');
			redirect('/office/');
		}

		$this->pages_model->SetPageCode('vip_manager');

		$data = array(
			'main_text'    => $this->pages_model->GetPropertyWikitext('main_text'),
			'target'       => $this->uri->uri_string(),
			'members'      => $this->members_model->GetMemberDetails(false)
		);
		
		// Include the javascript
		$this->main_frame->SetExtraHead('<script src="/javascript/viplist.js" type="text/javascript"></script>');

		// Set up the content
		$this->main_frame->SetContentSimple('office/vipmanager/vip_list', $data);

		// Load the main frame
		$this->main_frame->Load();
	}

	/// Default page.
	function info($organisation_id = NULL, $entity_id = NULL)
	{
		if (!CheckPermissions('editor')) return;
		
		// If no entity id was provided, redirect back to members list.
		if (NULL === $organisation_id || NULL === $entity_id) {
			return redirect('office/vipmanager');
		}
		
		//get the members data for the organisation
		$member_details = $this->members_model->GetMemberDetails($organisation_id, $entity_id, 'TRUE', array(), FALSE);
		
		if (!isset($member_details[0])) {
			return redirect('office/vipmanager');
		}
		
		$member_details = $member_details[0];
		
		// Stringify gender
		$member_details['gender'] = (($member_details['gender']=='m')?('Male')
									:(($member_details['gender']=='f')?('Female')
									:('unknown')));
		
		$data = array(
			'membership' => $member_details,
		);

		// Set the title parameters
		$this->main_frame->SetTitleParameters(array(
			'organisation'	=> $member_details['organisation_name'],
			'name'		=> $member_details['firstname'].' '.$member_details['surname'],
		));
			
		$this->pages_model->SetPageCode('vip_manager_info');
		$this->main_frame->SetContentSimple('office/vipmanager/info', $data);
		$this->main_frame->Load();
	}

	function promote($EntityId, $OrgId)
	{
		//has user got access to office
		if (!CheckPermissions('editor')) return;

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
		if (!CheckPermissions('editor')) return;

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
