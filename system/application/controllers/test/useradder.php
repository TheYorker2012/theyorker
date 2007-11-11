<?php

/// Quick bodge to add admin users so we can test stuff
class Useradder extends Controller
{

	function index()
	{
		if (!CheckPermissions()) return;

		$this->main_frame->SetContentSimple('test/useradder');

		$this->main_frame->Load();
	}

	function add()
	{
		if (!CheckPermissions()) return;

		$this->load->helper('string');

		$safety = $this->input->post('safety');
		$username = $this->input->post('username');
		$password = $this->input->post('password');

		$salt = random_string('alnum', 32);
		$hash = sha1($salt.$password);

		if ('grannysmith' === $safety) {
			$sql_add_entity = '
				INSERT INTO entities (
					entity_username,
					entity_password,
					entity_salt
				) VALUES (?,?,?)';
			$this->db->query($sql_add_entity,array($username,$hash,$salt));
			if (1||$this->db->affected_rows() > 0) {
				$entity_id = $this->db->insert_id();

				$firstname = $this->input->post('firstname');
				$surname = $this->input->post('surname');
				$email = $this->input->post('email');
				$nickname = $this->input->post('nickname');
				$officepassword = $this->input->post('officepassword');
				$hashoffice = sha1($salt.$officepassword);
				$sql_add_entity = '
					INSERT INTO users (
						user_entity_id,
						user_firstname,
						user_surname,
						user_nickname,
						user_gender,
						user_office_password,
						user_office_access,
						user_admin
					) VALUES (?,?,?,?,\'m\',?,1,1)';
				$this->db->query($sql_add_entity,array(
						$entity_id,
						$firstname,
						$surname,
						$nickname,
						$hashoffice,
					));
				if ($this->db->affected_rows() > 0) {
					$this->main_frame->AddMessage('success','Inserted user successfully');
				} else {
					$this->main_frame->AddMessage('error','Entity created but user could not be. This needs cleaning up the the db (entity_id='.$entity_id.')!');
				}
			} else {
				$this->main_frame->AddMessage('error','Could not create entity');
			}
		} else {
			$this->main_frame->AddMessage('error','Incorrect key');
		}
		redirect('test/useradder');
	}

}

?>