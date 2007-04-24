<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 *	@file	Account_personal.php
 *	@author	Chris Travis (ctravis@gmail.com - cdt502)
 *	@brief	Library for rendering reporter bylines
 */

class Account_personal extends FramesFrame
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
	function Validate()
	{
		// Load the Frames library
		$CI = &get_instance();
		$CI->load->library('frames');
	
		$CI->load->model('prefs_model');
	
		// Perform validation checks on submitted data
		$CI->load->library('validation');
		$CI->validation->set_error_delimiters('<li>','</li>');
		// Validation rules
		$rules['fname'] = 'trim|required|alpha';
		$rules['sname'] = 'trim|required|alpha';
		$rules['email'] = 'trim|required|valid_email';
		$rules['nick'] = 'trim|required|alpha_numeric';
		$rules['gender'] = 'trim|required';
		$rules['college'] = 'trim|required|numeric';
		$rules['year'] = 'trim|required|numeric';
		$rules['time'] = 'trim|required|numeric';
		$CI->validation->set_rules($rules);
		// names of fields for error msgs
		$fields['fname'] = 'first name';
		$fields['sname'] = 'surname';
		$fields['email'] = 'e-mail address';
		$fields['nick'] = 'nickname';
		$fields['gender'] = 'gender';
		$fields['college'] = 'college';
		$fields['year'] = 'year of study';
		$fields['time'] = 'time format';
		$CI->validation->set_fields($fields);
		// Run validation checks, if they pass proceed to conduct db integrity checks
		$errors = array();
		if ($CI->validation->run()) {
			if (!$CI->prefs_model->genderCheck($_POST['gender'])) {
				array_push($errors, 'You must select your gender');
			}
			if (!$CI->prefs_model->collegeExists($_POST['college'])) {
				array_push($errors, 'Please select the college you are a member of');
			}
			if (!$CI->prefs_model->yearValid($_POST['year'])) {
				array_push($errors, 'You didn\'t choose the year you enrolled at university');
			}
			if (!$CI->prefs_model->timeValid($_POST['time'])) {
				array_push($errors, 'Please decide whether you wish to view 12hr or 24hr time');
			}

			// If no db integrity errors then save and move onto next section
			if (count($errors) == 0) {
				$info = array(
					$CI->validation->college,
					$CI->validation->fname,
					$CI->validation->sname,
					$CI->validation->email,
					$CI->validation->nick,
					$CI->validation->gender,
					$CI->validation->year,
					$CI->validation->time
					);
				$CI->prefs_model->updateUserInfo($CI->user_auth->entityId,$info);
				redirect('/register/academic');
			}
		}

		// Validation errors occured
		if ($CI->validation->error_string != "") {
			$CI->main_frame->AddMessage('error','We were unable to process the information you submitted for the following reasons:<ul>' . $CI->validation->error_string . '</ul>');
		} elseif (count($errors) > 0) {
			$temp_msg = '';
			foreach ($errors as $error) {
				$temp_msg .= '<li>' . $error . '</li>';
			}
			$CI->main_frame->AddMessage('error','We were unable to process the information you submitted for the following reasons:<ul>' . $temp_msg . '</ul>');
		} else {
			// If there were no errors then this is the first time the form has been loaded
			// so load default/preset values from db
			$userInfo = $CI->prefs_model->getUserInfo($CI->user_auth->entityId);
			$CI->validation->fname = $userInfo['user_firstname'];
			$CI->validation->sname = $userInfo['user_surname'];
			$CI->validation->nick = $userInfo['user_nickname'];
			$CI->validation->gender = $userInfo['user_gender'];
			$CI->validation->college = $userInfo['user_college'];
			$CI->validation->year = $userInfo['user_enrolled_year'];
			$CI->validation->time = $userInfo['user_time_format'];
			if ($userInfo['user_email'] != '') {
				$CI->validation->email = $userInfo['user_email'];
			} else {
				$CI->validation->email = $CI->user_auth->username . '@york.ac.uk';
			}
		}

		$this->SetData('colleges', $CI->prefs_model->GetColleges());
		$this->SetData('years', $CI->prefs_model->GetYears());
	}
}