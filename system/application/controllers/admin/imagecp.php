<?php


class Imagecp extends Controller {

	function Imagecp() {
		parent::Controller();
		if (!CheckPermissions('office')) return;
		$this->load->helper(array('url', 'form', 'entity'));
		$this->load->library('image');
	}

	function index() {

		if ($this->input->post('image_type_name') &&
		    $this->input->post('image_type_width') &&
		    $this->input->post('image_type_height') &&
		    $this->input->post('image_type_codename')) {
			$insert = array('image_type_name'				=> $this->input->post('image_type_name'),
			                'image_type_width'				=> $this->input->post('image_type_width'),
			                'image_type_height'				=> $this->input->post('image_type_height'),
			                'image_type_codename'			=> $this->input->post('image_type_codename'),
			                'image_type_photo_thumbnail'	=> $this->input->post('image_type_photo_thumbnail'));
			$this->db->insert('image_types', $insert);
		}

		$data['imageType'] = $this->db->select('image_type_id, image_type_name, image_type_codename, image_type_photo_thumbnail')->get('image_types');
		$data['extra'] = $this->load->view('admin/image/add', '', true);

		$this->main_frame->SetTitle('Image Control Panel');
		$this->main_frame->SetContentSimple('admin/image/index', $data);

		$this->main_frame->Load();
	}

	function edit($codename) {

		if ($this->input->post('image_type_id') &&
		    $this->input->post('image_type_name') &&
		    $this->input->post('image_type_width') &&
		    $this->input->post('image_type_height') &&
		    $this->input->post('image_type_codename')) {
			$insert = array('image_type_name'				=> $this->input->post('image_type_name'),
			                'image_type_width'				=> $this->input->post('image_type_width'),
			                'image_type_height'				=> $this->input->post('image_type_height'),
			                'image_type_codename'			=> $this->input->post('image_type_codename'),
			                'image_type_photo_thumbnail'	=> $this->input->post('image_type_photo_thumbnail'));
			$this->db->where('image_type_id', $this->input->post('image_type_id'))->update('image_types', $insert);
		} elseif ($this->input->post('image_type_id')) {
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
					$this->db->where('image_type_id', $this->input->post('image_type_id'))
					         ->update('image_types', array('image_type_error_mime' => $uploadData['file_type'],
					                                       'image_type_error_data' => file_get_contents($uploadData['full_path'])));
					unlink($uploadData['full_path']);
				}
			}

		$data['imageType'] = $this->db->select('image_type_name, image_type_codename, image_type_photo_thumbnail')->get('image_types');
		$typeData = $this->db->select('image_type_id, image_type_name, image_type_width, image_type_height, image_type_photo_thumbnail, image_type_codename')
		                     ->getwhere('image_types', array('image_type_codename' => $codename))->first_row('array');
		$data['extra'] = $this->load->view('admin/image/edit', $typeData, true);

		$this->main_frame->SetTitle('Image Control Panel - Editing '.$typeData['image_type_name']);
		$this->main_frame->SetContentSimple('admin/image/index', $data);

		$this->main_frame->Load();
	}

	function view($codename, $action = 'view', $id = 0) {
		if ($action == 'delete') {
			$sql = 'SELECT image_type_photo_thumbnail FROM image_types WHERE image_type_codename = ? LIMIT 1';
			$typeDetails = $this->db->query($sql, array($codename));
			if ($typeDetails->num_rows() == 1 && $typeDetails->first_row()->image_type_photo_thumbnail == 0) {
				$this->image->delete('image', $id);
			}
		}
		//TODO paginate using pageination lib
		$sql = 'SELECT image_id, image_title, image_image_type_id, image_type_photo_thumbnail FROM images, image_types WHERE image_image_type_id = image_type_id AND image_type_codename = ?';
		$data['images'] = $this->db->query($sql, array($codename));
		$data['codename'] = $codename;

		$this->main_frame->SetTitle('Image Control Panel - Viewing Images');
		$this->main_frame->SetContentSimple('admin/image/view', $data);

		$this->main_frame->Load();
	}

	function add($codename) {
		$this->load->library('image_upload');
		$this->image_upload->automatic('admin/imagecp', array($codename), true, false);
	}

}
?>
