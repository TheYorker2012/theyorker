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
		$navbar->AddItem('bcards', 'VIP',
				'/account/bcards');

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
			$position = htmlentities($this->input->post('v_position'), ENT_QUOTES, 'UTF-8');
			$phone = htmlentities($this->input->post('v_phone'), ENT_QUOTES, 'UTF-8');
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
		$this->load->model('directory_model');
	
		$this->_SetupTabs('bcards');
		
		/// Get custom page content
		$this->pages_model->SetPageCode('account_bcards');
		
		$data['bcards_about'] = $this->pages_model->GetPropertyWikitext('bcards_about');
		$cards = $this->directory_model->GetDirectoryOrganisationCardsByUserId($this->user_auth->entityId);
		// translate into nice names for view
		$data['cards'] = array();
		foreach ($cards as $card) {
			$data['cards'][] = array(
				'organisation' => $card['organisation_name'],
				'user_id' => $card['business_card_user_entity_id'],
				'id' => $card['business_card_id'],
				'name' => $card['business_card_name'],
				'title' => $card['business_card_title'],
				'course' => $card['business_card_course'],
				'blurb' => $card['business_card_blurb'],
				'email' => $card['business_card_email'],
				'image_id' => $card['business_card_image_id'],
				'phone_mobile' => $card['business_card_mobile'],
				'phone_internal' => $card['business_card_phone_internal'],
				'phone_external' => $card['business_card_phone_external'],
				'postal_address' => $card['business_card_postal_address'],
				'approved' => $card['business_card_approved']
			);
		}

		/// Set up the main frame
		$this->main_frame->SetContentSimple('account/business_cards', $data);
		/// Set page title & load main frame with view
		$this->main_frame->Load();
	}
	function bcardsedit($CardId)
	{
		/// Make sure users have necessary permissions to view this page
		if (!CheckPermissions('student')) return;
		$this->load->model('directory_model');
		$this->load->model('businesscards_model');
	
		$this->_SetupTabs('bcards');
		
		/// Get custom page content
		$this->pages_model->SetPageCode('account_bcards');
		
		$data['bcards_about'] = $this->pages_model->GetPropertyWikitext('bcards_about');
			
			//Send data to form from db, if there is a fail the post will overwrite this.
			$data['users_card'] = true; // Prevent view from editing the username and group
			$data['url'] = '/account/bcardsedit/'.$CardId;
			$data['cancel_url'] = '/account/bcards';
			$cards_data = $this->directory_model->GetDirectoryOrganisationCardsById($CardId);//only gets one card
			foreach($cards_data as $card_data){
				$data['card_form'] = array(
					'userid' => $card_data['business_card_user_entity_id'],
					'organisation' => $card_data['organisation_directory_entry_name'],
					'card_name' => $card_data['business_card_name'],
					'card_title' => $card_data['business_card_title'],
					'group_id' => $card_data['business_card_business_card_group_id'],
					'card_course' => $card_data['business_card_course'],
					'email' => $card_data['business_card_email'],
					'card_about' => $card_data['business_card_blurb'],
					'postal_address' => $card_data['business_card_postal_address'],
					'phone_mobile' => $card_data['business_card_mobile'],
					'phone_internal' => $card_data['business_card_phone_internal'],
					'phone_external' => $card_data['business_card_phone_external'],
				);
			}
			//User ID and group ID are not provided so use from db.
			$group_id = $data['card_form']['group_id'];
			$user_id = $this->user_auth->entityId;
			//Get post data
			if(!empty($_POST["card_editbutton"])){
				if(empty($_POST["card_name"]) || empty($_POST["card_title"]))
				{
					$this->main_frame->AddMessage('error','Please include a name and a title for your contact card');
					//add failed send the data back into the form
					$data['card_form']=$_POST;
				}else{
					if($data['card_form']['userid'] != $user_id){//User ID has to be that of the logged in user
						$this->main_frame->AddMessage('error','This contact card does not belong to you!');
					}else{
						//update contact card
						//@note start time, end time, order, and image id are all currently null and not in use.
						$this->businesscards_model->UpdateBuisnessCard($user_id, $group_id, null, $_POST["card_name"],
					$_POST["card_title"], $_POST["card_about"], $_POST["card_course"], $_POST["email"], $_POST["phone_mobile"],
					$_POST["phone_internal"], $_POST["phone_external"], $_POST["postal_address"],
					0, null, null, $CardId, 1); //@note The last param 1 forces immediate publishing
						$this->main_frame->AddMessage('success','The contact card was successfully updated.');
						
						redirect("/account/bcards");
					}
				}
			} else {
			}
			$this->main_frame->SetContentSimple('directory/viparea_directory_contacts', $data);
			if($data['card_form']['userid'] != $user_id) {
			redirect("/account/bcards");
			}//Do not load the page if the student is accessing the editor for a card other than his.
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
				$this->load->library('image_upload');
				$this->xajax->processRequests();
				if ($this->input->post('title1') && $this->input->post('lurl') && $this->input->post('lurl') != 'http://') {

					$newId = $this->Links_Model->AddLink($this->input->post('title1'), $this->input->post('lurl'), $this->input->post('lnominate') == 'on');
					$this->Links_Model->AddUserLink($this->user_auth->entityId, $newId);
					$chosenImageID = $this->input->post('chosen_image');
					$_SESSION['link_id'] = $newId;

					if ($this->input->post('image_pick') == 'gallery' && $chosenImageID) {
						//Take link image id and associate it with the link
						$this->Links_Model->ReplaceImage($newId, $this->user_auth->entityId, $chosenImageID);
						$this->messages->AddMessage('success', 'Link added successfully.');
						redirect('/account/links', 'location');
					} elseif ($this->input->post('image_pick') == 'custom') {
						$_SESSION['img'] = array();
						$this->image_upload->recieveUpload('/account/customlink/addimage/'.$newId, array('link'), false);
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
					foreach ($_SESSION['img'] as $newImage) {
						if ($newImage['codename'] == 'link') {
							$this->Links_Model->ReplaceImage($id, $this->user_auth->entityId, $newImage['list']);
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

		$valid = false;
		$email_postfix = $this->config->Item('username_email_postfix');

		if (is_string($username)) {
			if (preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,6}$/i', $username) == 1) {
				// We have something that is probably an e-mail address
				
				if (substr($username, - strlen($email_postfix)) == $email_postfix) {
					// This is a york e-mail address, trim the @york.ac.uk from the end
					$username = substr($username, 0, strlen($username) - strlen($email_postfix));
				} else {
					$email = $username;
					$valid = true;
				}
			}
			if (!$valid) {
				// Not an e-mail address
				if (preg_match('/^[a-z]{2,4}[0-9]{3}$/i', $username) == 1) {
					// This is a university login
					$dnslookuptest = $username . '.imap.york.ac.uk';
					$valid = (count(dns_get_record($dnslookuptest)) != 0);
					if (!$valid)
						$this->messages->AddMessage('error', 'The username does not exist. Please enter a valid YorkWeb username.');
					$email = $username.$this->config->Item('username_email_postfix');
				} else {
					$this->messages->AddMessage('error', 'The username does not appear to be of the correct form. Please enter a username, e.g. abc456, or an e-mail address.');
				}
			}
		}

		if ($valid) {
			if($this->user_auth->resetpassword($username, $email)) {
				$this->messages->AddMessage('success', 'An e-mail has been sent to '.$email.'. Please click on the link within it to activate your account.');
			} else {
				$this->messages->AddMessage('error', 'There was an error sending the e-mail.');
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
				$validation_errors[] = 'You must enter your current password. If you\'ve lost this you\'ll need to <a href="'.site_url('account/password/reset').'">reset</a> it first.';
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
