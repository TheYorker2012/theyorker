<?php
/**
 *	@brief		Management of bylines for Yorker Staff
 *	@author		Chris Travis (cdt502 - ctravis@gmail.com)
 */

class Bylines extends Controller
{

	// Variable declarations
	var $access;
	var $navbar;

	/// Default constructor
	function __construct()
	{
		parent::Controller();
		// Load models
		$this->load->model('businesscards_model');

		// All functionality in this section requires office access or above
		if (!CheckPermissions('office')) return;
		// Retrieve access level
		$this->access = GetUserLevel();
		// Make it so that we only need to worry about two levels of access (admin == editor)
		if ($this->access == 'admin') $this->access = 'editor';

		// Create navigation menu
		$this->navbar = $this->main_frame->GetNavbar();
		$this->navbar->AddItem('user', 'User Bylines', '/office/bylines/user/');
		$this->navbar->AddItem('new', 'New Byline', '/office/bylines/new_byline');
		if ($this->access == 'editor') {
			$this->navbar->AddItem('teams', 'Teams', '/office/bylines/teams');
			$this->navbar->AddItem('pending', 'Pending', '/office/bylines/pending');
		}
	}

	function index()
	{
		$this->user();
	}

	function user ($user_id = NULL)
	{
		if (!is_numeric($user_id))
			$user_id = $this->user_auth->entityId;
		// Special case so that editors can view all global bylines
		if ($user_id == -1)
			$user_id = NULL;

		if (($user_id == $this->user_auth->entityId) || ($this->access == 'editor')) {
			$data = array();
			// Get user info
			$this->load->model('prefs_model');
			if ($user_id !== NULL) {
				$data['user_info'] = $this->prefs_model->getUserInfo($user_id);
				$data['default_byline'] = $this->businesscards_model->GetDefaultByline($this->user_auth->entityId);
			} else {
				$data['user_info'] = array(
					'user_firstname'	=>	NULL,
					'user_surname'		=>	NULL
				);
			}
			if (count($data['user_info']) == 0) {
				$this->main_frame->AddMessage('error', 'The requested user does not exist, please try again.');
				redirect('/office/bylines/user/' . $this->user_auth->entityId . '/');
			} else {
				$data['bylines'] = $this->businesscards_model->GetUserBylines($user_id);
				$data['user_id'] = $user_id;
				$this->load->library('image');
				foreach ($data['bylines'] as &$byline) {
					if ($byline['business_card_image_id'] === NULL) {
						$byline['business_card_image_href'] = '';
					} else {
						$byline['business_card_image_href'] = $this->image->getPhotoURL($byline['business_card_image_id'], 'userimage');
					}
				}

				// Get page properties information
				$this->pages_model->SetPageCode('office_bylines_index');
				$data['whats_this_heading'] = $this->pages_model->GetPropertyText('whats_this_heading');
				$data['whats_this_text'] = $this->pages_model->GetPropertyWikiText('whats_this_text');

				// Load the page
				$this->navbar->SetSelected('user');
				$this->main_frame->SetContentSimple('office/bylines/index', $data);
				$this->main_frame->Load();
			 }
		} else {
			$this->main_frame->AddMessage('error', 'You do not have access to view the bylines owned by the requested user, please try again.');
			redirect('/office/bylines/user/' . $this->user_auth->entityId . '/');
		}
	}

	function setdefault ($byline_id = NULL)
	{
		if (!is_numeric($byline_id)) {
			$this->main_frame->AddMessage('error', 'You must specify the byline you wish to set as your default, please try again.');
		} else {
			$data['byline_info'] = $this->businesscards_model->GetBylineInfo($byline_id);
			if (count($data['byline_info']) == 0) {
				$this->main_frame->AddMessage('error', 'The byline you are trying to set as your default does not exist, please try again.');
			} elseif ($data['byline_info']['business_card_user_entity_id'] != $this->user_auth->entityId) {
				$this->main_frame->AddMessage('error', 'The byline you are trying to set as your default does not belong to you, please try again.');
			} else {
				$set = $this->businesscards_model->SetDefaultByline($this->user_auth->entityId, $byline_id);
				if ($set) {
					$this->main_frame->AddMessage('success', 'Your default byline was successfully set.');
				} else {
					$this->main_frame->AddMessage('error', 'There was an error setting your default byline, please try again.');
				}
			}
		}
		redirect('/office/bylines/');
	}

	function pending()
	{
		$data = array();
		$data['bylines'] = $this->businesscards_model->GetPendingBylines();
		$this->load->library('image');
		foreach ($data['bylines'] as &$byline) {
			if ($byline['business_card_image_id'] === NULL) {
				$byline['business_card_image_href'] = '';
			} else {
				$byline['business_card_image_href'] = $this->image->getPhotoURL($byline['business_card_image_id'], 'userimage');
			}
		}

		// Get page properties information
		$this->pages_model->SetPageCode('office_bylines_pending');
		$data['whats_this_heading'] = $this->pages_model->GetPropertyText('whats_this_heading');
		$data['whats_this_text'] = $this->pages_model->GetPropertyWikiText('whats_this_text');

		// Load the page
		$this->navbar->SetSelected('pending');
		$this->main_frame->SetContentSimple('office/bylines/pending', $data);
		$this->main_frame->Load();
	}

	function teams ()
	{
		// Process add new byline team request
		if ((isset($_POST['team_name'])) && ($_POST['team_name'] != '')) {
			$add_op = $this->businesscards_model->AddOrganisationCardGroup(array(
				'group_name'		=>	$_POST['team_name'],
				'organisation_id'	=>	NULL,
				'group_order'		=>	($this->businesscards_model->SelectMaxGroupOrderById(NULL) + 1)
			));
			if ($add_op) {
				$this->main_frame->AddMessage('success', 'The new byline team was successfully created.');
			} else {
				$this->main_frame->AddMessage('error', 'There was an error adding the new byline team, please try again.');
			}
			redirect('/office/bylines/teams/');
		}

		$data = array();
		$data['teams'] = $this->businesscards_model->GetBylineTeams();

		// Get page properties information
		$this->pages_model->SetPageCode('office_bylines_teams');
		$data['whats_this_heading'] = $this->pages_model->GetPropertyText('whats_this_heading');
		$data['whats_this_text'] = $this->pages_model->GetPropertyWikiText('whats_this_text');

		// Load the page
		$this->navbar->SetSelected('teams');
		$this->main_frame->SetContentSimple('office/bylines/teams', $data);
		$this->main_frame->Load();
	}

	function delete_team ($team_id = NULL)
	{
		if ($team_id === NULL) {
			redirect('/office/bylines/teams/');
		} else {
			$data = array();
			$data['team_info'] = $this->businesscards_model->BylineTeamInfo($team_id);
			if (count($data['team_info']) == 0) {
				$this->main_frame->AddMessage('error', 'The byline team you were trying to delete does not exist, please try again.');
				redirect('/office/bylines/teams/');
			} elseif ($this->access != 'editor') {
				$this->main_frame->AddMessage('error', 'You must have editor access to delete a byline team.');
				redirect('/office/bylines/teams/');
			} else {
				$data['bylines'] = $this->businesscards_model->GetTeamBylines($team_id);
				if (count($data['bylines']) > 0) {
					$this->main_frame->AddMessage('error', 'The byline team you are trying to delete is not empty, ensure the team is empty and then please try again.');
					redirect('/office/bylines/teams/');
				} else {
					/// Process delete request
					if ($this->input->post('delete_yes') == 'Delete') {
						$delete = $this->businesscards_model->RemoveOrganisationCardGroupById($team_id);
						if ($delete) {
							$this->main_frame->AddMessage('success', 'The requested byline team has successfully been deleted.');
							redirect('/office/bylines/teams/');
						} else {
							$this->main_frame->AddMessage('error', 'There was an error deleting the requested byline team, please try again.');
						}
					} elseif ($this->input->post('delete_no') == 'Cancel') {
						redirect('/office/bylines/teams/');
					}

					// Get page properties information
					$this->pages_model->SetPageCode('office_bylines_team_delete');
					$data['whats_this_heading'] = $this->pages_model->GetPropertyText('whats_this_heading');
					$data['whats_this_text'] = $this->pages_model->GetPropertyWikiText('whats_this_text');

					// Load the page
					$this->main_frame->SetContentSimple('office/bylines/team_delete', $data);
					$this->main_frame->Load();
				}
			 }
		 }
	}

	function view_team ($team_id = NULL)
	{
		if ($team_id === NULL) {
			redirect('/office/bylines/teams/');
		} else {
			$data = array();
			$data['team_info'] = $this->businesscards_model->BylineTeamInfo($team_id);
			if (count($data['team_info']) == 0) {
				$this->main_frame->AddMessage('error', 'The byline team you were trying to access does not exist, please try again.');
				redirect('/office/bylines/teams/');
			} else {
				// Process edit byline team name
				if ((isset($_POST['team_name'])) && ($_POST['team_name'] != '')) {
					$edit_op = $this->businesscards_model->RenameOrganisationCardGroup($team_id, $_POST['team_name']);
					if ($edit_op) {
						$this->main_frame->AddMessage('success', 'The byline team\'s name was successfully renamed.');
					} else {
						$this->main_frame->AddMessage('error', 'There was an error renaming the byline team\'s name, please try again.');
					}
					redirect('/office/bylines/view_team/' . $team_id . '/');
				}

				$data['bylines'] = $this->businesscards_model->GetTeamBylines($team_id);

				$this->load->library('image');
				foreach ($data['bylines'] as &$byline) {
					if ($byline['business_card_image_id'] === NULL) {
						$byline['business_card_image_href'] = '';
					} else {
						$byline['business_card_image_href'] = $this->image->getPhotoURL($byline['business_card_image_id'], 'userimage');
					}
				}

				// Get page properties information
				$this->pages_model->SetPageCode('office_bylines_team_view');
				$data['whats_this_heading'] = $this->pages_model->GetPropertyText('whats_this_heading');
				$data['whats_this_text'] = $this->pages_model->GetPropertyWikiText('whats_this_text');

				// Load the page
				$this->navbar->SetSelected('teams');
				$this->main_frame->SetContentSimple('office/bylines/team_view', $data);
				$this->main_frame->Load();
			}
		}
	}

	function approve ($byline_id = NULL)
	{
		if (($byline_id !== NULL) && (is_numeric($byline_id))) {
			if ($this->access == 'editor') {
				$approve = $this->businesscards_model->ApproveBusinessCard($byline_id);
				if ($approve) {
					$this->main_frame->AddMessage('success', 'The byline was approved.');
				} else {
					$this->main_frame->AddMessage('error', 'There was an error trying to approve this byline, please try again.');
				}
				redirect('/office/bylines/view_byline/' . $byline_id . '/');
			} else {
				$this->main_frame->AddMessage('error', 'You must have editor access to be able to approve changes to bylines.');
  			}
		} else {
			$this->main_frame->AddMessage('error', 'Unknown byline, please try again.');
		}
		redirect('/office/bylines/');
	}

	function new_byline ()
	{
		$data = array();
		/// Get byline teams
		$data['groups'] = $this->businesscards_model->GetBylineTeams();

		/// Process new byline creation request
		$this->load->library('validation');
		$this->validation->set_error_delimiters('<li>','</li>');
		/// Validation rules
		$rules['card_name'] = 'trim|required|xss_clean';
		$rules['card_title'] = 'trim|required|xss_clean';
		$rules['group_id'] = 'trim|required|numeric';
		$fields['card_name'] = 'name';
		$fields['card_title'] = 'title';
		$fields['card_course'] = 'course';
		$fields['card_email'] = 'e-mail';
		$fields['card_about'] = 'about';
		$fields['postal_address'] = 'postal address';
		$fields['phone_internal'] = 'phone (internal)';
		$fields['phone_external'] = 'phone (external)';
		$fields['phone_mobile'] = 'phone (mobile)';
		$fields['group_id'] = 'byline team';
		$fields['date_from_day'] = 'display from date (day)';
		$fields['date_from_month'] = 'display from date (month)';
		$fields['date_from_year'] = 'display from date (year)';
		$fields['date_to_day'] = 'display to date (day)';
		$fields['date_to_month'] = 'display to date (month)';
		$fields['date_to_year'] = 'display to date (year)';
		$fields['global_setting'] = 'global setting';
		$fields['aboutus'] = 'about us only';
		/// Set rules on field inputs
		$this->validation->set_rules($rules);
		$this->validation->set_fields($fields);
		/// Run validation checks
		$errors = array();
		if ($this->validation->run()) {
			$group_check = false;
			foreach ($data['groups'] as $group) {
				if ($group['business_card_group_id'] == $this->input->post('group_id')) {
					$group_check = true;
					break;
				}
			}
			if (!$group_check)
				$errors[] = 'Please assign the byline to a team that exists.';
			if (!checkdate($this->input->post('date_from_month'), $this->input->post('date_from_day'), $this->input->post('date_from_year')))
				$errors[] = 'The display from date you have specified is not a valid date, please correct it and try again.';
			if (!checkdate($this->input->post('date_to_month'), $this->input->post('date_to_day'), $this->input->post('date_to_year')))
				$errors[] = 'The display to date you have specified is not a valid date, please correct it and try again.';
			$from_timestamp = mktime(0, 0, 0, $this->input->post('date_from_month'), $this->input->post('date_from_day'), $this->input->post('date_from_year'));
			$to_timestamp = mktime(0, 0, 0, $this->input->post('date_to_month'), $this->input->post('date_to_day'), $this->input->post('date_to_year'));
			if ($from_timestamp > $to_timestamp)
				$errors[] = 'Please ensure that the display to date is after the display from date.';

			/// If no errors, update byline
			if (count($errors) == 0) {
				$from_timestamp = date('Y-m-d', $from_timestamp);
				$to_timestamp = date('Y-m-d', $to_timestamp);
				if (($this->access == 'editor') && ($this->input->post('global_setting') == 'yes')) {
					$insert_user_id = NULL;
				} else {
					$insert_user_id = $this->user_auth->entityId;
				}
				if ($this->input->post('aboutus') == 'yes') {
					$aboutus = 1;
				} else {
					$aboutus = 0;
				}

				$addop = $this->businesscards_model->NewBusinessCard(
							$insert_user_id,
							$this->input->post('group_id'),
							NULL,
							$this->input->post('card_name'),
							$this->input->post('card_title'),
							($this->input->post('card_about') == '') ? NULL : $this->input->post('card_about'),
							($this->input->post('card_course') == '') ? NULL : $this->input->post('card_course'),
							($this->input->post('card_email') == '') ? NULL : $this->input->post('card_email'),
							($this->input->post('phone_mobile') == '') ? NULL : $this->input->post('phone_mobile'),
							($this->input->post('phone_internal') == '') ? NULL : $this->input->post('phone_internal'),
							($this->input->post('phone_external') == '') ? NULL : $this->input->post('phone_external'),
							($this->input->post('postal_address') == '') ? NULL : $this->input->post('postal_address'),
							($this->businesscards_model->MaxBusinessCardOrder($this->input->post('group_id')) + 1),
							$from_timestamp,
							$to_timestamp,
							0,
							$aboutus);
				if ($addop) {
					$this->main_frame->AddMessage('success', 'Your request for a new byline to be created is now pending editor approval.');
					redirect('/office/bylines/view_byline/' . $addop . '/');
				} else {
					$this->main_frame->AddMessage('error', 'There was an error submitting your request for the creation of a new byline, please try again.');
				}
			}
		}

		/// Validation errors occured
		if ($this->validation->error_string != "") {
			$this->main_frame->AddMessage('error', 'We were unable to process the information you submitted for the following reasons:<ul>' . $this->validation->error_string . '</ul>');
		} elseif (count($errors) > 0) {
			$temp_msg = '<li>' . implode('</li><li>', $errors) . '</li>';
			$this->main_frame->AddMessage('error', 'We were unable to process the information you submitted for the following reasons:<ul>' . $temp_msg . '</ul>');
		} else {
			// First time form has been loaded so populate fields
			$this->validation->date_from_day = date('j');
			$this->validation->date_from_month = date('n');
			$this->validation->date_from_year = date('Y');
			$this->validation->date_to_day = date('j', mktime() + (60*60*24*120));
			$this->validation->date_to_month = date('n', mktime() + (60*60*24*120));
			$this->validation->date_to_year = date('Y', mktime() + (60*60*24*120));
		}

		// Get page properties information
		$this->pages_model->SetPageCode('office_bylines_new');
		$data['whats_this_heading'] = $this->pages_model->GetPropertyText('whats_this_heading');
		$data['whats_this_text'] = $this->pages_model->GetPropertyWikiText('whats_this_text');

		// Load the page
		$this->navbar->SetSelected('new');
		$this->main_frame->SetContentSimple('office/bylines/new', $data);
		$this->main_frame->Load();
	}

	function delete_byline ($byline_id = NULL)
	{
		if ($byline_id === NULL) {
			$this->main_frame->AddMessage('error', 'You must specify the byline you want to delete, please try again.');
			redirect('/office/bylines/');
		} else {
			$data = array();
			$data['byline_info'] = $this->businesscards_model->GetBylineInfo($byline_id);
			if (count($data['byline_info']) == 0) {
				$this->main_frame->AddMessage('error', 'The byline you were trying to delete does not exist, please try again.');
				redirect('/office/bylines/');
			} elseif (($this->access != 'editor') && ($data['byline_info']['business_card_user_entity_id'] != $this->user_auth->entityId)) {
				$this->main_frame->AddMessage('error', 'You do not have access to delete the requested byline, please try again.');
				redirect('/office/bylines/');
			} else {
				/// Process delete request
				if ($this->input->post('delete_yes') == 'Delete') {
					$delete = $this->businesscards_model->DeleteBusinessCard($byline_id);
					if ($delete) {
						$this->main_frame->AddMessage('success', 'The requested byline has successfully been deleted.');
						redirect('/office/bylines/');
					} else {
						$this->main_frame->AddMessage('error', 'There was an error deleting the requested byline, please try again.');
					}
				} elseif ($this->input->post('delete_no') == 'Cancel') {
					redirect('/office/bylines/view_byline/' . $byline_id . '/');
				}

				/// Process byline image
				$this->load->library('image');
				if ($data['byline_info']['business_card_image_id'] === NULL) {
					$data['byline_info']['business_card_image_href'] = '';
				} else {
					$data['byline_info']['business_card_image_href'] = $this->image->getPhotoURL($data['byline_info']['business_card_image_id'], 'userimage');
				}

				// Get page properties information
				$this->pages_model->SetPageCode('office_bylines_delete');
				$data['whats_this_heading'] = $this->pages_model->GetPropertyText('whats_this_heading');
				$data['whats_this_text'] = $this->pages_model->GetPropertyWikiText('whats_this_text');

				// Load the page
				$this->main_frame->SetContentSimple('office/bylines/byline_delete', $data);
				$this->main_frame->Load();
			}
		}
	}

	function view_byline ($byline_id = NULL)
	{
		if ($byline_id === NULL) {
			redirect('/office/bylines/');
		} else {
			$data = array();
			$data['byline_info'] = $this->businesscards_model->GetBylineInfo($byline_id);
			if (count($data['byline_info']) == 0) {
				$this->main_frame->AddMessage('error', 'The byline you were trying to access does not exist, please try again.');
				redirect('/office/bylines/');
			} elseif (($this->access != 'editor') && ($data['byline_info']['business_card_user_entity_id'] != $this->user_auth->entityId)) {
				$this->main_frame->AddMessage('error', 'You do not have access to view or edit the requested byline, please try again.');
				redirect('/office/bylines/');
			} else {
				/// Get byline teams
				$data['groups'] = $this->businesscards_model->GetBylineTeams();

				/// Process edit byline request
				$this->load->library('validation');
				$this->validation->set_error_delimiters('<li>','</li>');
				/// Validation rules
				$rules['card_name'] = 'trim|required|xss_clean';
				$rules['card_title'] = 'trim|required|xss_clean';
				$rules['group_id'] = 'trim|required|numeric';
				$fields['card_name'] = 'name';
				$fields['card_title'] = 'title';
				$fields['card_course'] = 'course';
				$fields['card_email'] = 'e-mail';
				$fields['card_about'] = 'about';
				$fields['postal_address'] = 'postal address';
				$fields['phone_internal'] = 'phone (internal)';
				$fields['phone_external'] = 'phone (external)';
				$fields['phone_mobile'] = 'phone (mobile)';
				$fields['group_id'] = 'byline team';
				$fields['date_from_day'] = 'display from date (day)';
				$fields['date_from_month'] = 'display from date (month)';
				$fields['date_from_year'] = 'display from date (year)';
				$fields['date_to_day'] = 'display to date (day)';
				$fields['date_to_month'] = 'display to date (month)';
				$fields['date_to_year'] = 'display to date (year)';
				$fields['aboutus'] = 'about us only';
				/// Set rules on field inputs
				$this->validation->set_rules($rules);
				$this->validation->set_fields($fields);
				/// Run validation checks
				$errors = array();
				if ($this->validation->run()) {
					$group_check = false;
					foreach ($data['groups'] as $group) {
						if ($group['business_card_group_id'] == $this->input->post('group_id')) {
							$group_check = true;
							break;
						}
					}
					if (!$group_check)
						$errors[] = 'Please assign the byline to a team that exists.';
					if (!checkdate($this->input->post('date_from_month'), $this->input->post('date_from_day'), $this->input->post('date_from_year')))
						$errors[] = 'The display from date you have specified is not a valid date, please correct it and try again.';
					if (!checkdate($this->input->post('date_to_month'), $this->input->post('date_to_day'), $this->input->post('date_to_year')))
						$errors[] = 'The display to date you have specified is not a valid date, please correct it and try again.';
					$from_timestamp = mktime(0, 0, 0, $this->input->post('date_from_month'), $this->input->post('date_from_day'), $this->input->post('date_from_year'));
					$to_timestamp = mktime(0, 0, 0, $this->input->post('date_to_month'), $this->input->post('date_to_day'), $this->input->post('date_to_year'));
					if ($from_timestamp > $to_timestamp)
						$errors[] = 'Please ensure that the display to date is after the display from date.';

					/// If no errors, update byline
					if (count($errors) == 0) {
						$from_timestamp = date('Y-m-d', $from_timestamp);
						$to_timestamp = date('Y-m-d', $to_timestamp);
						if ($this->input->post('aboutus') == 'yes') {
							$aboutus = 1;
						} else {
							$aboutus = 0;
						}

						$update = $this->businesscards_model->UpdateBuisnessCard(
									$data['byline_info']['business_card_user_entity_id'],
									$this->input->post('group_id'),
									$data['byline_info']['business_card_image_id'],
									$this->input->post('card_name'),
									$this->input->post('card_title'),
									($this->input->post('card_about') == '') ? NULL : $this->input->post('card_about'),
									($this->input->post('card_course') == '') ? NULL : $this->input->post('card_course'),
									($this->input->post('card_email') == '') ? NULL : $this->input->post('card_email'),
									($this->input->post('phone_mobile') == '') ? NULL : $this->input->post('phone_mobile'),
									($this->input->post('phone_internal') == '') ? NULL : $this->input->post('phone_internal'),
									($this->input->post('phone_external') == '') ? NULL : $this->input->post('phone_external'),
									($this->input->post('postal_address') == '') ? NULL : $this->input->post('postal_address'),
									$data['byline_info']['business_card_order'],
									$from_timestamp,
									$to_timestamp,
									$byline_id,
									0,
									$aboutus);
						if ($update) {
							$this->main_frame->AddMessage('success', 'The changes you have requested to the below byline have been sent to an editor for approval.');
						} else {
							$this->main_frame->AddMessage('error', 'There was an error updating the byline\'s information, please try again.');
						}
						redirect('/office/bylines/view_byline/' . $byline_id . '/');
					}
				}

				/// Validation errors occured
				if ($this->validation->error_string != "") {
					$this->main_frame->AddMessage('error', 'We were unable to process the information you submitted for the following reasons:<ul>' . $this->validation->error_string . '</ul>');
				} elseif (count($errors) > 0) {
					$temp_msg = '<li>' . implode('</li><li>', $errors) . '</li>';
					$this->main_frame->AddMessage('error', 'We were unable to process the information you submitted for the following reasons:<ul>' . $temp_msg . '</ul>');
				} else {
					// First time form has been loaded so populate fields
					$this->validation->card_name = $data['byline_info']['business_card_name'];
					$this->validation->card_title = $data['byline_info']['business_card_title'];
					$this->validation->card_course = $data['byline_info']['business_card_course'];
					$this->validation->card_email = $data['byline_info']['business_card_email'];
					$this->validation->card_about = $data['byline_info']['business_card_blurb'];
					$this->validation->postal_address = $data['byline_info']['business_card_postal_address'];
					$this->validation->phone_internal = $data['byline_info']['business_card_phone_internal'];
					$this->validation->phone_external = $data['byline_info']['business_card_phone_external'];
					$this->validation->phone_mobile = $data['byline_info']['business_card_mobile'];
					$this->validation->group_id = $data['byline_info']['business_card_business_card_group_id'];
					$this->validation->date_from_day = date('j', $data['byline_info']['business_card_start_date']);
					$this->validation->date_from_month = date('n', $data['byline_info']['business_card_start_date']);
					$this->validation->date_from_year = date('Y', $data['byline_info']['business_card_start_date']);
					$this->validation->date_to_day = date('j', $data['byline_info']['business_card_end_date']);
					$this->validation->date_to_month = date('n', $data['byline_info']['business_card_end_date']);
					$this->validation->date_to_year = date('Y', $data['byline_info']['business_card_end_date']);
					$this->validation->aboutus = $data['byline_info']['business_card_about_us'];
				}

				/// Process byline image
				$this->load->library('image');
				if ($data['byline_info']['business_card_image_id'] === NULL) {
					$data['byline_info']['business_card_image_href'] = '';
				} else {
					$data['byline_info']['business_card_image_href'] = $this->image->getPhotoURL($data['byline_info']['business_card_image_id'], 'userimage');
				}

				// Get page properties information
				$this->pages_model->SetPageCode('office_bylines_view');
				$data['whats_this_heading'] = $this->pages_model->GetPropertyText('whats_this_heading');
				$data['whats_this_text'] = $this->pages_model->GetPropertyWikiText('whats_this_text');

				// Load the page
				$this->navbar->SetSelected('user');
				$this->main_frame->SetContentSimple('office/bylines/byline_view', $data);
				$this->main_frame->Load();
			}
		}
	}

	function add_photo ($byline_id = NULL)
	{
		if ($byline_id === NULL) {
			$this->main_frame->AddMessage('error', 'You must specify the byline you want to add a photo to, please try again.');
			redirect('/office/bylines/');
		} else {
			$data = array();
			$data['byline_info'] = $this->businesscards_model->GetBylineInfo($byline_id);
			if (count($data['byline_info']) == 0) {
				$this->main_frame->AddMessage('error', 'The byline you were trying to add a photo to does not exist, please try again.');
				redirect('/office/bylines/');
			} elseif (($this->access != 'editor') && ($data['byline_info']['business_card_user_entity_id'] != $this->user_auth->entityId)) {
				$this->main_frame->AddMessage('error', 'You do not have access to add a photo to the requested byline, please try again.');
				redirect('/office/bylines/');
			} else {
	        	$this->load->library('image_upload');
				$this->image_upload->automatic('/office/bylines/uploaded_photo/' . $byline_id . '/', array('userimage'), false, true);
			}
		}
	}

	function uploaded_photo ($byline_id = NULL)
	{
		if ($byline_id === NULL) {
			$this->main_frame->AddMessage('error', 'You must specify the byline you want to add a photo to, please try again.');
			redirect('/office/bylines/');
		} else {
			$data = array();
			$data['byline_info'] = $this->businesscards_model->GetBylineInfo($byline_id);
			if (count($data['byline_info']) == 0) {
				$this->main_frame->AddMessage('error', 'The byline you were trying to add a photo to does not exist, please try again.');
				redirect('/office/bylines/');
			} elseif (($this->access != 'editor') && ($data['byline_info']['business_card_user_entity_id'] != $this->user_auth->entityId)) {
				$this->main_frame->AddMessage('error', 'You do not have access to add a photo to the requested byline, please try again.');
				redirect('/office/bylines/');
			} elseif (!isset($_SESSION['img'])) {
				$this->main_frame->AddMessage('error', 'No photos were added to the requested byline, please try again.');
				redirect('/office/bylines/view_byline/' . $byline_id . '/');
			} else {
				$photo = array();
				foreach ($_SESSION['img'] as $image) {
					$photo[] = $image['list'];
				}
				$photo = array_unique($photo);
				/// Reset list of uploaded photos
				unset($_SESSION['img']);
				if (count($photo) != 1) {
					$this->main_frame->AddMessage('error', 'You must only add one photo to the requested byline, please try again.');
				} else {
					$photo = $this->businesscards_model->AddPhotoToByline($byline_id, $photo[0]);
					if ($photo) {
						$this->main_frame->AddMessage('success', 'The photo was successfully added to the requested byline.');
					} else {
						$this->main_frame->AddMessage('error', 'There was an error trying to add the photo to the requested byline, please try again.');
					}
				}
				redirect('/office/bylines/view_byline/' . $byline_id . '/');
			}
		}
	}

	function order_team ($team_id = NULL, $direction = 'up')
	{
		if (($team_id === NULL) || (!is_numeric($team_id))) {
			$this->main_frame->AddMessage('error', 'You must specify the byline team you wish to reorder, please try again.');
		} elseif ($this->access != 'editor') {
			$this->main_frame->AddMessage('error', 'You do not have the required access to reorder byline teams, please try again.');
		} else {
			$data = array();
			$data['byline_team'] = $this->businesscards_model->BylineTeamInfo($team_id);
			if (count($data['byline_team']) == 0) {
				$this->main_frame->AddMessage('error', 'The byline team you were trying to re-order does not exist, please try again.');
			} else {
				switch ($direction) {
					case 'up':
					case 'down':
						$order = $this->businesscards_model->ReorderOrganisationCardGroup($team_id, $direction);
						if ($order) {
							$this->main_frame->AddMessage('success', 'The byline team was successfully re-ordered.');
						} else {
							$this->main_frame->AddMessage('error', 'There was an error re-ordering the byline team, please try again.');
						}
						break;
					default:
						$this->main_frame->AddMessage('error', 'You must specify the direction by which you wish to reorder the byline team by, please try again.');
				}
			}
		}
		redirect('/office/bylines/teams/');
	}

	function order_byline ($byline_id = NULL, $direction = 'up')
	{
		if (($byline_id === NULL) || (!is_numeric($byline_id))) {
			$this->main_frame->AddMessage('error', 'You must specify the byline you wish to reorder, please try again.');
			redirect('/office/bylines/teams/');
		} elseif ($this->access != 'editor') {
			$this->main_frame->AddMessage('error', 'You do not have the required access to reorder bylines, please try again.');
			redirect('/office/bylines/teams/');
		} else {
			$data = array();
			$data['byline_info'] = $this->businesscards_model->GetBylineInfo($byline_id);
			if (count($data['byline_info']) == 0) {
				$this->main_frame->AddMessage('error', 'The byline you were trying to re-order does not exist, please try again.');
			} else {
				switch ($direction) {
					case 'up':
					case 'down':
						$order = $this->businesscards_model->ReorderByline($byline_id, $direction);
						if ($order) {
							$this->main_frame->AddMessage('success', 'The byline was successfully re-ordered.');
						} else {
							$this->main_frame->AddMessage('error', 'There was an error re-ordering the byline, please try again.');
						}
						break;
					default:
						$this->main_frame->AddMessage('error', 'You must specify the direction by which you wish to reorder the byline by, please try again.');
				}
			}
			redirect('/office/bylines/view_team/' . $data['byline_info']['business_card_business_card_group_id'] . '#Byline' . $byline_id);
		}
	}

}
?>