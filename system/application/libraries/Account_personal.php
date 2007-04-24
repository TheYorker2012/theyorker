<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 *	@file	Account_personal.php
 *	@author	Chris Travis (ctravis@gmail.com - cdt502)
 *	@brief	Library for rendering reporter bylines
 */

// Load the Frames library
$CI = &get_instance();
$CI->load->library('frames');

class Account_personal extends FramesView
{
	/// Primary constructor.
	function __construct()
	{
		/// Set view to use for layout & design of byline
		parent::__construct('account/user_settings');
	}
	
	/**
	 * @brief Echo's out the form and does validation
	 */
	function Load()
	{
	
		$data['colleges'] = $this->prefs_model->GetColleges();
		$data['years'] = $this->prefs_model->GetYears();

		// Perform validation checks on submitted data
		$this->load->library('validation');
		$this->validation->set_error_delimiters('<li>','</li>');
		// Validation rules
		$rules['fname'] = 'trim|required|alpha';
		$rules['sname'] = 'trim|required|alpha';
		$rules['email'] = 'trim|required|valid_email';
		$rules['nick'] = 'trim|required|alpha_numeric';
		$rules['gender'] = 'trim|required';
		$rules['college'] = 'trim|required|numeric';
		$rules['year'] = 'trim|required|numeric';
		$rules['time'] = 'trim|required|numeric';
		$this->validation->set_rules($rules);
		// names of fields for error msgs
		$fields['fname'] = 'first name';
		$fields['sname'] = 'surname';
		$fields['email'] = 'e-mail address';
		$fields['nick'] = 'nickname';
		$fields['gender'] = 'gender';
		$fields['college'] = 'college';
		$fields['year'] = 'year of study';
		$fields['time'] = 'time format';
		$this->validation->set_fields($fields);
		// Run validation checks, if they pass proceed to conduct db integrity checks
		$errors = array();
		if ($this->validation->run()) {
			if (!$this->prefs_model->genderCheck($_POST['gender'])) {
				array_push($errors, 'You must select your gender');
			}
			if (!$this->prefs_model->collegeExists($_POST['college'])) {
				array_push($errors, 'Please select the college you are a member of');
			}
			if (!$this->prefs_model->yearValid($_POST['year'])) {
				array_push($errors, 'You didn\'t choose the year you enrolled at university');
			}
			if (!$this->prefs_model->timeValid($_POST['time'])) {
				array_push($errors, 'Please decide whether you wish to view 12hr or 24hr time');
			}

			// If no db integrity errors then save and move onto next section
			if (count($errors) == 0) {
				$info = array(
					$this->validation->college,
					$this->validation->fname,
					$this->validation->sname,
					$this->validation->email,
					$this->validation->nick,
					$this->validation->gender,
					$this->validation->year,
					$this->validation->time
					);
				$this->prefs_model->updateUserInfo($this->user_auth->entityId,$info);
				redirect('/register/academic');
			}
		}

		// Validation errors occured
		if ($this->validation->error_string != "") {
			$this->main_frame->AddMessage('error','We were unable to process the information you submitted for the following reasons:<ul>' . $this->validation->error_string . '</ul>');
		} elseif (count($errors) > 0) {
			$temp_msg = '';
			foreach ($errors as $error) {
				$temp_msg .= '<li>' . $error . '</li>';
			}
			$this->main_frame->AddMessage('error','We were unable to process the information you submitted for the following reasons:<ul>' . $temp_msg . '</ul>');
		} else {
			// If there were no errors then this is the first time the form has been loaded
			// so load default/preset values from db
			$userInfo = $this->prefs_model->getUserInfo($this->user_auth->entityId);
			$this->validation->fname = $userInfo['user_firstname'];
			$this->validation->sname = $userInfo['user_surname'];
			$this->validation->nick = $userInfo['user_nickname'];
			$this->validation->gender = $userInfo['user_gender'];
			$this->validation->college = $userInfo['user_college'];
			$this->validation->year = $userInfo['user_enrolled_year'];
			$this->validation->time = $userInfo['user_time_format'];
			if ($userInfo['user_email'] != '') {
				$this->validation->email = $userInfo['user_email'];
			} else {
				$this->validation->email = $this->user_auth->username . '@york.ac.uk';
			}
		}
		$this->main_frame->SetContentSimple('account/preferences', $data);
		
	}
}