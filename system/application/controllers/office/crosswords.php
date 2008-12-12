<?php

/** Office controller for crosswords.
 * @author James Hogan <james_hogan@theyorker.co.uk>
 */
class Crosswords extends Controller
{
	function __construct()
	{
		parent::Controller();

		$this->load->model('crosswords_model');
	}

	/** Main index page.
	 * links to different sections
	 */
	function index()
	{
		if (!CheckPermissions('office')) return;
		if (!CheckRolePermissions('CROSSWORD_INDEX')) return;
		$this->pages_model->SetPageCode('crosswords_office_index');
		$data = array();
		$this->main_frame->SetContentSimple('crosswords/office/index', $data);
		$this->main_frame->Load();
	}

	/** Tips management.
	 */
	function tips($category = null, $argument = null)
	{
		if (!CheckPermissions('office')) return;
		if (null === $category) {
			if (!CheckRolePermissions('CROSSWORD_TIPS_INDEX')) return;
		}
		else {
			if ('add' === $category) {
				if (!CheckRolePermissions('CROSSWORD_TIP_CATEGORY_ADD')) return;
			}
			else {
				if (!CheckRolePermissions('CROSSWORD_TIP_CATEGORY_MODIFY')) return;
			}
		}
		$this->main_frame->Load();
	}

	/// Validate layout input from user.
	private function _validateLayoutPost(&$Layout, $MaxLengths)
	{
		$posted_layout = array(
			'name'        => $this->input->post('xword_layout_name'),
			'description' => $this->input->post('xword_layout_description'),
		);
		$valid_input = true;
		if (false !== $posted_layout['name']) {
			if (strlen($posted_layout['name']) > (int)$MaxLengths['name']) {
				$this->messages->AddMessage('error',
					'Layout name was too long. '.
					'It must be no longer than '.(int)$MaxLengths['name'].' characters long.');
				$valid_input = false;
			}
			else if (strlen($posted_layout['name']) <= 3) {
				$this->messages->AddMessage('error',
					'Layout name was too short. '.
					'It must be at least 3 characters long.');
				$valid_input = false;
			}
			$Layout['name'] = $posted_layout['name'];
		} else {
			$valid_input = false;
		}
		if (false !== $posted_layout['description']) {
			$Layout['description'] = $posted_layout['description'];
		} else {
			$valid_input = false;
		}
		return $valid_input;
	}

	/** Layout management.
	 */
	function layouts($layout = null)
	{
		if (!CheckPermissions('office')) return;
		if (null === $layout) {
			if (!CheckRolePermissions('CROSSWORD_LAYOUTS_INDEX')) return;
			$this->pages_model->SetPageCode('crosswords_office_layouts');
			$data = array(
				'Permissions' => array(
					'layout_add' => $this->permissions_model->hasUserPermission('CROSSWORD_LAYOUT_ADD'),
					'layout_edit' => $this->permissions_model->hasUserPermission('CROSSWORD_LAYOUT_MODIFY'),
				),
				'Layouts' => $this->crosswords_model->GetAllLayouts(),
			);
			$this->main_frame->SetContentSimple('crosswords/office/layouts', $data);
		}
		else {
			$ret = (isset($_GET['ret']) ? $_GET['ret'] : null);
			$effective_ret = (null === $ret ? 'office/crosswords/layouts' : $ret);
			$action = site_url($this->uri->uri_string());
			if ($ret !== null) {
				$action .= '?ret='.urlencode($ret);
			}
			$data = array(
				'MaxLengths' => array(
					'name' => 32,
				),
				'Layout'     => array(
					'name'        => '',
					'description' => '',
				),
				'Actions'    => array(),
				'PostAction' => $action,
			);
			if ('add' === $layout) {
				if (!CheckRolePermissions('CROSSWORD_LAYOUT_ADD')) return;
				// Check post input
				$cancelled = (false !== $this->input->post('xword_layout_cancel'));
				if ($cancelled) {
					redirect($effective_ret);
				}
				$valid_input = $this->_validateLayoutPost($data['Layout'], $data['MaxLengths']);
				// Do the adding if possible
				if ($valid_input) {
					$messages = $this->crosswords_model->AddLayout($data['Layout']);
					$this->messages->AddMessages($messages);
					if (!isset($messages['error']) || empty($messages['error'])) {
						redirect($effective_ret);
					}
				}
				// Setup output
				$this->pages_model->SetPageCode('crosswords_office_layout_add');
				$data['Actions']['add'] = 'Add Layout';
				$data['Actions']['cancel'] = 'Cancel';
				$this->main_frame->SetContentSimple('crosswords/office/layout_edit', $data);
			}
			elseif (is_numeric($layout)) {
				$layout = (int)$layout;

				if (!CheckRolePermissions('CROSSWORD_LAYOUT_MODIFY')) return;
				// Retreive current data about the layout.
				$layoutData = $this->crosswords_model->GetLayoutById($layout);
				if (null === $data['Layout']) {
					$this->messages->AddMessage('error', "No crossword layout with the id $layout exists.");
					redirect($effective_ret);
				}
				$data['Layout'] = $layoutData;
				// Check post input
				$cancelled = (false !== $this->input->post('xword_layout_cancel'));
				if ($cancelled) {
					redirect($effective_ret);
				}
				$valid_input = $this->_validateLayoutPost($data['Layout'], $data['MaxLengths']);
				// Do the saving if possible
				if ($valid_input) {
					// Don't bother if nothing has changed
					if ($data['Layout']['name'] != $layoutData['name'] ||
						$data['Layout']['description'] != $layoutData['description'])
					{
						$messages = $this->crosswords_model->ModifyLayout($layout, $data['Layout']);
					}
					else {
						$messages = array(
							'information' => array(xml_escape('You didn\'t make any changes.')),
						);
					}
					$this->messages->AddMessages($messages);
					if (!isset($messages['error']) || empty($messages['error'])) {
						redirect($effective_ret);
					}
				}
				// Setup output
				$this->pages_model->SetPageCode('crosswords_office_layout_edit');
				$data['Actions']['save'] = 'Save Layout';
				$data['Actions']['cancel'] = 'Cancel';
				$this->main_frame->SetContentSimple('crosswords/office/layout_edit', $data);
			}
			else {
				// not numeric
				show_404();
			}
		}
		$this->main_frame->Load();
	}

	/** Category management.
	 */
	function cats($category = null)
	{
		if (!CheckPermissions('office')) return;
		if (null === $category) {
			if (!CheckRolePermissions('CROSSWORD_CATEGORIES_INDEX')) return;
			$this->pages_model->SetPageCode('crosswords_office_cats');
			$data = array(
				'Permissions' => array(
					'category_add' => $this->permissions_model->hasUserPermission('CROSSWORD_CATEGORY_ADD'),
				),
				'Categories' => array(
					1 => array(
						'name' => 'Quick Crosswords',
						'short_name' => 'quick',
					),
					2 => array(
						'name' => 'Cryptic Crosswords',
						'short_name' => 'cryptic',
					),
				),
			);
			$this->main_frame->SetContentSimple('crosswords/office/categories', $data);
		}
		else {
			$layouts = $this->crosswords_model->GetAllLayouts();
			$action = $this->uri->uri_string();
			$data = array(
				'MaxLengths' => array(
					'name'       => 255,
					'short_name' => 32,
				),
				'Layouts' => $layouts,
				'Category' => array(
					'name'          => '',
					'short_name'    => '',
					'default_width' => 13,
					'default_height' => 13,
				),
			);
			if ('add' === $category) {
				if (!CheckRolePermissions('CROSSWORD_CATEGORY_ADD')) return;
				$this->pages_model->SetPageCode('crosswords_office_cat_add');
				if (empty($layouts)) {
					$this->messages->AddMessage('error',
						'No crossword layouts have been set up. '.
						'Please <a href="'.site_url('office/crosswords/layouts/add').'?ret='.xml_escape(urlencode($action)).'">add a layout</a> before adding categories.');
				}
				else {
					$this->main_frame->SetContentSimple('crosswords/office/category_edit', $data);
				}
			}
			else {
				if (!CheckRolePermissions('CROSSWORD_CATEGORY_MODIFY')) return;
				$this->pages_model->SetPageCode('crosswords_office_cat_edit');
				$this->main_frame->SetContentSimple('crosswords/office/category_edit', $data);
			}
		}
		$this->main_frame->Load();
	}

	/** Crosswords management.
	 */
	function crossword($crossword = null, $operation = null)
	{
		if (!CheckPermissions('office')) return;
		if (null !== $crossword && is_numeric($crossword)) {
			$crossword = (int)$crossword;
			if (null == $operation) {
				if (!CheckRolePermissions('CROSSWORD_VIEW')) return;
			}
			else if ('edit' === $operation) {
				if (!CheckRolePermissions('CROSSWORD_VIEW', 'CROSSWORD_MODIFY')) return;
			}
			else if ('stats' === $operation) {
				if (!CheckRolePermissions('CROSSWORD_STATS_BASIC')) return;
			}
			else {
				show_404();
			}
		}
		$this->main_frame->Load();
	}

}

?>
