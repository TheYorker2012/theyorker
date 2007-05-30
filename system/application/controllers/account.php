<?php

/**
 *	@file	account.php
 *	@brief	My Account controller
 *	@author	James Hogan (jh559@cs.york.ac.uk)
 *	@author	Chris Travis (cdt502 - ctravis@gmail.com)
 */

/// Account controller.
class Account extends controller
{
	/// Default constructor
	function __construct()
	{
		parent::controller();
		$this->load->model('prefs_model');
	}

	/// Set up the tabs on the main_frame.
	/**
	 * @param $SelectedPage string Selected Page.
	 * @pre CheckPermissions must have already been called.
	 */
	protected function _SetupTabs($SelectedPage)
	{
		$navbar = $this->main_frame->GetNavbar();
		$navbar->AddItem('subscriptions', 'Subscriptions',
				'/account');
		$navbar->AddItem('personal', 'Personal',
				'/account/personal');
		$navbar->AddItem('links', 'Links',
				'/account/links');
		$navbar->AddItem('password', 'Password',
				'/account/password/change');
		//$navbar->AddItem('bcards', 'VIP',
		//		'/account/bcards');

		$this->main_frame->SetPage($SelectedPage);
	}

	/**
	 *	@brief	Shows overview of a student's subscriptions and a menu for other account operations
	 */
	function index ()
	{
		/// Make sure users have necessary permissions to view this page
		if (!CheckPermissions('student')) return;

		$this->_SetupTabs('subscriptions');

		/// Get custom page content
		$this->pages_model->SetPageCode('account_home');

		/// Get subscriptions of the current user
		$data['Subscriptions']  = $this->prefs_model->getAllSubscriptions($this->user_auth->entityId);
		$data['vip_help_heading'] = $this->pages_model->GetPropertyText('vip_help_heading');
		$data['vip_help_text'] = $this->pages_model->GetPropertyWikitext('vip_help_text');

		/// Set up the main frame
		$this->main_frame->SetContentSimple('account/myaccount', $data);
		/// Set page title & load main frame with view
		$this->main_frame->Load();
	}

	/**
	 *	@brief	Allows a user to become a VIP for an organisation
	 */
	function vip($org_id = NULL)
	{
		if (!CheckPermissions('student')) return;

		$this->_SetupTabs('subscriptions');

		/// Get custom page content
		$this->pages_model->SetPageCode('account_home');
		$data['org_id'] = $org_id;
		$data['org_name'] = $this->prefs_model->getOrganisationDescription($org_id);
		$data['vip_help_heading'] = $this->pages_model->GetPropertyText('vip_help_heading');
		$data['vip_help_text'] = $this->pages_model->GetPropertyWikitext('vip_help_text');

		if (($org_id == NULL) || (!is_numeric($org_id))) {
			$this->messages->AddMessage('error', 'The organisation you tried to apply to be VIP for does not exist.');
			redirect('account/');
		} elseif (!$this->prefs_model->isSubscribed($this->user_auth->entityId, $org_id)) {
			$this->messages->AddMessage('error', 'You must be subscribed to the organisation before you can apply to become a VIP for it.');
			redirect('account/');
		} elseif ($this->input->post('v_apply') == 'Apply') {
			/// Process form submission
			$this->load->model('members_model');
			$position = htmlentities($this->input->post('v_position'), ENT_NOQUOTES, 'UTF-8');
			$phone = htmlentities($this->input->post('v_phone'), ENT_NOQUOTES, 'UTF-8');
			if ($position == '') {
				$this->messages->AddMessage('error', 'Please make sure you specify your position in the organisation before submitting the application.');
			} else {
				$this->members_model->UpdateVipStatus('requested',$this->user_auth->entityId,$org_id);
				$this->prefs_model->vipApplication ($this->user_auth->entityId,$org_id,$position,$phone);
				$this->messages->AddMessage('success', 'Your application to become VIP for ' . $data['org_name']['name'] . ' has been successfully recieved.');
				redirect('/account');
			}
		}

		/// Set up the main frame
		$this->main_frame->SetContentSimple('account/vip_application', $data);
		/// Set page title & load main frame with view
		$this->main_frame->Load();
	}

	/**
	 *	@brief	Allows setting of business card information
	 */
	function bcards()
	{
		/// Make sure users have necessary permissions to view this page
		if (!CheckPermissions('student')) return;

		$this->_SetupTabs('bcards');

		$data['test'] = 'test';

		/// Get custom page content
		$this->pages_model->SetPageCode('account_bcards');

		/// Set up the main frame
		$this->main_frame->SetContentSimple('account/business_cards', $data);
		/// Set page title & load main frame with view
		$this->main_frame->Load();
	}

	/**
	 *	@brief	AJAX call
	 */
	function _links_update($links) {
		$objResponse = new xajaxResponse();
		$links = explode('+', $links);
		array_pop($links);
		foreach ($links as &$link) {
			$link = explode('_', $link);
			$link = $link[1];
		}
		$this->Links_Model->ChangeUserLinks($this->user_auth->entityId, $links);
		return $objResponse;
	}

	/**
	 *	@brief	Allows setting of links and other homepage related settings
	 */
	function links($action = 'none', $id = null) {

		/// Make sure users have necessary permissions to view this page
		if (!CheckPermissions('student')) return;

		$this->load->model('Links_Model');
		$this->load->library('xajax');
		$this->load->library('image');
		$this->xajax->registerFunction(array("links_update", &$this, "_links_update"));
		$this->xajax->processRequests();

		if ($action == 'add') {
			$this->Links_Model->AddUserLink($this->user_auth->entityId, $id);
			redirect('/account/links');
		}

		$this->_SetupTabs('links');

		$data['AllLinks'] = $this->Links_Model->GetAllOfficialLinks();
		$data['link'] = $this->Links_Model->GetUserLinks($this->user_auth->entityId);

		/// Get custom page content
		$this->pages_model->SetPageCode('account_links');

		$head = $this->xajax->getJavascript(null, '/javascript/xajax.js');
		$head.= '<script src="/javascript/prototype.js" type="text/javascript"></script><script src="/javascript/scriptaculous.js" type="text/javascript"></script>';
		$this->main_frame->SetExtraHead($head);

		/// Set up the main frame
		$this->main_frame->SetContentSimple('account/links', $data);
		/// Set page title & load main frame with view
		$this->main_frame->Load();
	}

	/**
	 *	@brief	Allows setting of links and other homepage related settings
	 */
	function customlink($action = '', $id = 0)
	{
		/// Make sure users have necessary permissions to view this page
		if (!CheckPermissions('student')) return;

		$this->load->model('Links_Model');

		if($action == 'store') {
				if ($this->input->post('title1') && $this->input->post('lurl') && $this->input->post('lurl') != 'http://') {

					$newId = $this->Links_Model->AddLink($this->input->post('title1'), $this->input->post('lurl'), $this->input->post('lnominate') == 'on');
					$this->Links_Model->AddUserLink($this->user_auth->entityId, $newId);

					if ($this->input->post('image_pick') == 'gallery') {
						//Take link image id and associate it with the link
						//$this->Links_Model->ReplaceImage($linkID, $this->user_auth->entityId, $imageID);
						$this->messages->AddMessage('success', 'Link added successfully.');
						redirect('/account/links', 'location');
					} elseif ($this->input->post('image_pick') == 'custom') {
						$this->load->library(array('xajax', 'image'));
						$this->load->library('image_upload');
						$this->xajax->processRequests();
						$_SESSION['img'] = array();
						$this->image_upload->recieveUpload('/account/customlink/addimage/'.$id, array('link'), false);
					} else {
						$this->messages->AddMessage('error', 'Please select either a custom or gallery image.');
						redirect('/account/customlink', 'location');
					}
				} else if($this->input->post('lurl')) {
					$this->messages->AddMessage('error', 'Please enter a name for your link.');
					redirect('/account/customlink', 'location');
				}
		} elseif ($action == 'addimage') {
				if (isset($_SESSION['img'])) {
					$image_count = 0;
					foreach ($_SESSION['img'] as $newImage) {
						if ($newImage['codename'] == 'link') {
							$this->Links_Model->ReplaceImage($linkID, $this->user_auth->entityId, $imageID);
							$this->messages->AddMessage('success', 'Link added successfully.');
							redirect('/account/links', 'location');
							break;
						}
					}
					$this->messages->AddMessage('error', 'The link image was not added.');
					redirect('/account/customlink', 'location');
				}
		} else {
			$data = array();
			$data['gallery_images'] = $this->Links_Model->GalleryLinks();

			$this->_SetupTabs('links');

			/// Get custom page content
			$this->pages_model->SetPageCode('account_customlinks');

			$this->load->library('image');

			/// Set up the main frame
			$this->main_frame->SetContentSimple('account/custom_link', $data);
			/// Set page title & load main frame with view
			$this->main_frame->Load();
		}
	}

	/**
	 *	@brief	Allows setting of personal information
	 */
	function personal()
	{
		// TODO: Check if this is the first time they've logged in or not
		if (!CheckPermissions('student')) return;

		/// Get custom page content
		$this->pages_model->SetPageCode('account_personal');

		$this->_SetupTabs('personal');

		$this->load->library('account_personal');


		$this->account_personal->Validate(false,'/account/personal');

		// Get page content
		$data['intro_heading'] = $this->pages_model->GetPropertyText('intro_heading');
		$data['intro'] = $this->pages_model->GetPropertyWikitext('intro');

		$data['bigcontent'] = $this->account_personal;
		$this->main_frame->SetContentSimple('account/preferences', $data);

		// Set up the main frame
		$this->main_frame->SetTitleParameters(
			array('section' => 'General')
		);
		// Load the main frame view (which will load the content view)
		$this->main_frame->Load();
	}


	/// Password related operations
	function password($option = '', $parameter = NULL)
	{
		static $handlers = array(
			'change' => '_password_change',
			'reset'  => '_password_reset',
			'register' => '_password_register',
		);

		if (array_key_exists($option, $handlers)) {
			if (NULL === $parameter) {
				$this->$handlers[$option]();
			} else {
				$this->$handlers[$option]($parameter);
			}
		} else {
			return show_404();
		}
	}

	/// Reset password
	protected function _password_reset($parameter = 'main')
	{
		$this->_password_reset_register('account_password_reset');
	}

	/// Register
	protected function _password_register($parameter = 'main')
	{
		$this->_password_reset_register('account_password_register');
	}

	protected function _password_reset_register($pagecode) {
		if (!CheckPermissions('public')) return;

		$username = $this->input->post('username');
		if (is_string($username)) {
			if($this->user_auth->resetpassword($username)) {
				get_instance()->messages->AddMessage(
					'success',
					'<p>An e-mail has been sent to '.$username.'@york.ac.uk. Please click on the link within it to activate your account.</p>'
				);
			} else {
				get_instance()->messages->AddMessage(
					'error',
					'<p>There was an error sending the e-mail.</p>'
				);
			}
		}

		$this->pages_model->SetPageCode($pagecode);

		$data = array();
		$data['intro'] = $this->pages_model->GetPropertyWikitext('intro');
		$data['submit'] = $this->pages_model->GetPropertyText('submit');

		// Set up the public frame
		$this->main_frame->SetContentSimple('login/resetpassword', $data);

		// Load the public frame view (which will load the content view)
		$this->main_frame->Load();
	}

	/// Change password
	protected function _password_change($parameter = 'main')
	{
		static $handlers = array(
			'main'    => array('student', 'checkPassword', 'setPassword'),
			'office'  => array('editor', 'checkOfficePassword', 'setOfficePassword'),
		);

		if (!array_key_exists($parameter, $handlers)) {
			return show_404();
		}
		$permission_level = $handlers[$parameter][0];
		$password_checker = $handlers[$parameter][1];
		$password_setter  = $handlers[$parameter][2];

		if (!CheckPermissions($permission_level)) return;

		$this->_SetupTabs('password');

		$this->pages_model->SetPageCode('account_password_change');

		// Check for post data for changing password
		$old_password = $this->input->post('oldpassword');
		if (is_string($old_password)) {
			$new_password = $this->input->post('newpassword');
			$confirm_password = $this->input->post('confirmpassword');

			$validation_errors = array();

			// Check existence of password fields.
			if (empty($old_password)) {
				$validation_errors[] = 'You must enter your current password. If you\'ve lost this you\'ll need to <a href="account/password/reset">reset</a> it first.';
			}
			if (empty($new_password)) {
				$validation_errors[] = 'You must enter your new password.';
			}
			if (empty($confirm_password)) {
				$validation_errors[] = 'You must confirm your new password by entering it again.';
			}

			if (empty($validation_errors)) {
				if ($new_password !== $confirm_password) {
					$validation_errors[] = 'The passwords entered do not match. Please try again, confirming your new password by entering it again.';
				} elseif ($old_password === $new_password) {
					$validation_errors[] = 'The new password is identical to the current password.';
				}

				if (empty($validation_errors)) {
					// Check old password
					if (!$this->user_auth->$password_checker($old_password)) {
						$validation_errors[] = 'Your current password was not entered correctly.';
					} else {
						try {
							$this->user_auth->$password_setter($new_password);
							$this->messages->AddMessage('success', 'Your password was successfully changed');
						} catch (Exception $e) {
							$validation_errors[] = $e->getMessage();
						}
					}
				}
			}


			if (!empty($validation_errors)) {
				$this->messages->AddMessage('error',
					'<p>There were problems with your passwords:'.
					'<ul><li>'.implode('</li><li>',$validation_errors).'</li></ul>'.
					'</p>');
			}
		}

		// Setup form
		$this->load->helper('form');
		$data = array(
			'main_text' => $this->pages_model->GetPropertyWikitext('main_text['.$parameter.']'),
			'change_password_target' => $this->uri->uri_string(),
		);

		// Load view
		$this->main_frame->SetContentSimple('account/password_change', $data);
		$this->main_frame->Load();
	}
}
