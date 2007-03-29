<?php

/**
 * @file organisation.php
 * @brief Wizard for organisation suggestion.
 */

/**
 */
class Organisation extends controller
{
	/// Default constructor
	function __construct()
	{
		parent::controller();
	}
	
	function index()
	{
		if (!CheckPermissions('public')) return;

		$this->pages_model->SetPageCode('wizard_organisation');

		$stage_count = 6; //total number of stages
		$skip_stages = array('3', '4'); //these stages are skipped when the user is not connected to the organisation
		$headings = array('1'=>'Start', '2'=>'Basic Details', '3'=>'More Details', '4'=>'Photos', '5'=>'Map', '6'=>'Finish');
		$data['session_var'] = 'org_wizard'; //variable in the session to store the data

		if (isset($_POST['r_stage']))
		{
			//a post has occured but there is no session data, get the serialised data out of the form and put it back into the session
			/*
			echo '<pre>';
			echo print_r($_POST);
			echo '<pre>';
			*/
			if (isset($_SESSION[$data['session_var']]['a_connected']) == false)
			{
				$unserialized = stripslashes($_POST['r_dump']);
				$unserialized = preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $unserialized );
				$_SESSION[$data['session_var']] = unserialize($unserialized);
			}
			//dump the post data into the session
			foreach ($_POST as $key => $postitem)
			{
				$_SESSION['org_wizard'][$key] = $postitem;
			}
			$data['is_connected'] = $_SESSION['org_wizard']['a_connected'];
			//$data['post'][$_POST['r_stage']] = $_POST;
			//$data['post'][$_POST['r_stage']]['prev'] = htmlentities(serialize($_POST), ENT_QUOTES);
			if (isset($_POST['r_submit_finish']))
				if ($_POST['r_stage'] == $stage_count)
				{
					//finished
					//##TODO: actually process the form data
					//all fields in session with a_ in front are submitted data
					//the maps and photos pages aren't working atm just the standard form submit pages
					$this->main_frame->AddMessage('success','Your suggestion has been submitted.');
					$data['stage'] = 1;
				}
				else
				{
					//send them to the final stage
					$data['stage'] = $stage_count;
				}
			else if (isset($_POST['r_submit_next']))
			{
				$data['stage'] = $_POST['r_stage'] + 1;
				while ($data['is_connected'] == 'No' && in_array($data['stage'], $skip_stages))
					$data['stage'] = $data['stage'] + 1;
			}
			else if (isset($_POST['r_submit_back']))
			{
				$data['stage'] = $_POST['r_stage'] - 1;
				while ($data['is_connected'] == 'No' && in_array($data['stage'], $skip_stages))
					$data['stage'] = $data['stage'] - 1;
			}
		}
		else
		{
			$data['stage'] = 1;
			$data['is_connected'] = 'yes';
			$data['prev'] = array();
		}

		$data['stage_list']['count'] = $stage_count;
		$data['stage_list']['skip'] = $skip_stages;
		$data['stage_list']['headings'] = $headings;
		
		//if (isset($_SESSION[$data['session_var']]) == true)
			$_SESSION[$data['session_var']]['r_dump'] = NULL;

		// Set up the public frame
		$the_view = $this->frames->view('wizard/organisation', $data);
		$this->main_frame->SetContent($the_view);

		// Load the public frame view (which will load the content view)
		$this->main_frame->load();
	}
}
?>