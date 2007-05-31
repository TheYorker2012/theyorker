<?php
/*
 * Controller for links office pages
 * \author Nick Evans nse500
 */

class Links extends Controller
{
	/// Default constructor.
	function __construct()
	{
		parent::controller();
		$this->load->model('Links_Model');
	}

	/// Default page.
	function index()
	{
		if (!CheckPermissions('office')) return;

		$this->pages_model->SetPageCode('link_list');

		$data = array();

		$data['officiallinks'] = $this->Links_Model->GetAllOfficialLinks()->result_array();
		$data['nominatedlinks'] = $this->Links_Model->GetAllNominatedLinks()->result_array();

		$this->load->library('image');
		$this->main_frame->SetContentSimple('office/links/link_list', $data);

		$this->main_frame->Load();
	}

	//Edit link page
	function edit($link_id)
	{
		//has user got access to office
		if (!CheckPermissions('office')) return;

		$this->load->model('user_auth');

		if (!($this->user_auth->officeType == 'High' || $this->user_auth->officeType == 'Admin')) {
			$this->messages->AddMessage('error', 'Permission denied. You must be an editor to perform this operation.');
			redirect('/office/links');
		}

		$data['link'] = $this->Links_Model->GetLink($link_id);

		$this->load->library('image');
		$this->main_frame->SetContentSimple('office/links/link_edit', $data);

		$this->main_frame->Load();
	}

	//Promote link
	function promote($link_id)
	{
		//has user got access to office
		if (!CheckPermissions('office')) return;

		$this->load->model('user_auth');

		if (!($this->user_auth->officeType == 'High' || $this->user_auth->officeType == 'Admin')) {
			$this->messages->AddMessage('error', 'Permission denied. You must be an editor to perform this operation.');
			redirect('/office/links');
		}

		$this->load->model('Links_Model');
		$this->Links_Model->PromoteLink($this->user_auth->entityId, $link_id);
		$this->messages->AddMessage('success', 'Link promoted successfully');

		redirect('/office/links');
	}

	//Reject link
	function reject($link_id)
	{
		//has user got access to office
		if (!CheckPermissions('office')) return;

		$this->load->model('user_auth');

		if (!($this->user_auth->officeType == 'High' || $this->user_auth->officeType == 'Admin')) {
			$this->messages->AddMessage('error', 'Permission denied. You must be an editor to perform this operation.');
			redirect('/office/links');
		}

		$this->load->model('Links_Model');
		$this->Links_Model->RejectLink($this->user_auth->entityId, $link_id);
		$this->messages->AddMessage('success', 'Link rejected successfully');

		redirect('/office/links');
	}

	/**
	 *	@brief	Allows setting of links and other homepage related settings
	 */
	function customlink()
	{
		/// Make sure users have necessary permissions to view this page
		if (!CheckPermissions('office')) return;

		$this->load->model('Links_Model');

		if ($this->input->post('lurl') && $this->input->post('lname') && $this->input->post('lname') != 'http://' && $this->input->post('userfile')) {
			$this->load->library('upload');
			$config['upload_path'] = './tmp/uploads/';
			$config['allowed_types'] = 'gif|jpg|png';
			$config['max_size'] = '2048';
			$this->upload->initialize($config);

			if (!$this->upload->do_upload('userfile')) {
				$this->load->library('messages');
				$this->messages->AddMessage('error', $this->upload->display_errors());
				redirect('/office/links/customlink');
			} else {
				$uploadData = $this->upload->data();

				$this->db->insert('images', array('image_mime' => $uploadData['file_type'],
												   'image_data' => file_get_contents($uploadData['full_path']),
												   'image_title' => $this->input->post('lname'),
												   'image_image_type_id' => $this->Links_Model->GetLinkImageTypeId()
												   ));
				unlink($uploadData['full_path']);

				$image_id = $this->db->insert_id();

				$id = $this->Links_Model->AddLink($this->input->post('lname'), $this->input->post('lurl'), 1, $image_id);

				$this->messages->AddMessage('success', 'Image Uploaded Successfully');

				redirect('/office/links', 'location');
			}
		} elseif($this->input->post('lurl') && !$this->input->post('lname')) {
			$this->messages->AddMessage('error', 'Please enter a name for your link.');
			redirect('/office/links/customlink');
		} elseif ($this->input->post('lurl') && !$this->input->post('upload')) {
			$this->messages->AddMessage('error', 'Please choose an image to upload with the link.');
			redirect('/office/links/customlink');
		}

		$data = array();

		/// Get custom page content
		$this->pages_model->SetPageCode('account_customlinks');

		/// Set up the main frame
		$this->main_frame->SetContentSimple('office/links/link_custom', $data);
		/// Set page title & load main frame with view
		$this->main_frame->Load();
	}








		function view($codename, $action = 'view', $id = 0) {
			if ($this->input->post('image_type_id')) {
				$this->load->library('upload');
				$config['upload_path'] = './tmp/uploads/';
				$config['allowed_types'] = 'gif|jpg|png';
				$config['max_size'] = '2048';
				$this->upload->initialize($config);

				if (!$this->upload->do_upload('upload')) {
					$this->load->library('messages');
					$this->messages->AddMessage('error', $this->upload->display_errors());
				} else {
					$uploadData = $this->upload->data();
					$image_id = $this->input->post('image_id');
					if (isset($image_id) && strlen(trim($image_id)) > 0) {
						$this->db->where('image_id', $image_id)
							 ->update('images', array('image_mime' => $uploadData['file_type'],
														   'image_data' => file_get_contents($uploadData['full_path']),
														   'image_title' => $this->input->post('image_title')
														   ));
					} else {
						$this->db->insert('images', array('image_mime' => $uploadData['file_type'],
														   'image_data' => file_get_contents($uploadData['full_path']),
														   'image_title' => $this->input->post('image_title'),
														   'image_image_type_id' => $this->input->post('image_type_id')
														   ));
					}
					unlink($uploadData['full_path']);

					$this->messages->AddMessage('success', 'Image Uploaded Successfully');
				}
				redirect('admin/imagecp/view/'.$codename.'/');
			}

			if ($action == 'delete') {
				$sql = 'SELECT image_type_photo_thumbnail FROM image_types WHERE image_type_codename = ? LIMIT 1';
				$typeDetails = $this->db->query($sql, array($codename));
				if ($typeDetails->num_rows() == 1 && $typeDetails->first_row()->image_type_photo_thumbnail == 0) {
					$this->image->delete('image', $id);
				}
				redirect('admin/imagecp/view/'.$codename.'/');
			}
			//TODO paginate using pageination lib
			$sql = 'SELECT image_type_id, image_type_name, image_type_width , image_type_height , image_type_photo_thumbnail, image_type_codename FROM image_types WHERE image_type_codename = ?';
			$result = $this->db->query($sql, array($codename));
			$data = $result->row_array();

			$sql = 'SELECT image_id, image_title, image_image_type_id, image_type_photo_thumbnail FROM images, image_types WHERE image_image_type_id = image_type_id AND image_type_codename = ?';
			$data['images'] = $this->db->query($sql, array($codename));
			$data['codename'] = $codename;

			$this->main_frame->SetTitle('Image Control Panel - Viewing Images');
			$this->main_frame->SetContentSimple('admin/image/view', $data);

			$this->main_frame->Load();
	}














	//Update link
	function update($link_id)
	{
		//has user got access to office
		if (!CheckPermissions('office')) return;

		$this->load->model('user_auth');

		if (!($this->user_auth->officeType == 'High' || $this->user_auth->officeType == 'Admin')) {
			$this->messages->AddMessage('error', 'Permission denied. You must be an editor to perform this operation.');
			redirect('/office/links');
		}

		$link_name = htmlentities($this->input->post('link_name'), ENT_NOQUOTES, 'UTF-8');
		$link_url = $this->input->post('link_url');
		$delete = ($this->input->post('name_delete_button') == 'Delete');

		$this->load->model('Links_Model');
		if ($delete) {
			$this->Links_Model->DeleteOfficialLink($link_id);
			$this->messages->AddMessage('success', 'Link deleted successfully');
		} else {
			$this->Links_Model->UpdateLink($link_id, $link_name, $link_url);
			$this->messages->AddMessage('success', 'Link updated successfully');
		}

		redirect('/office/links');
	}

	function upload() {
		$this->load->library('image_upload');
		$this->image_upload->automatic('/office/links', array('link'), true, false);
	}
}

?>
