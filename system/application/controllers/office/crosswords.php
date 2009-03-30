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
	private function _validateLayoutPost(&$Layout, &$MaxLengths)
	{
		$posted_layout = array(
			'name'        => $this->input->post('xword_layout_name'),
			'description' => $this->input->post('xword_layout_description'),
		);
		$valid_input = true;
		// Name
		if (false !== $posted_layout['name']) {
			if (strlen($posted_layout['name']) > (int)$MaxLengths['name']) {
				$this->messages->AddMessage('error',
					'Layout name was too long. '.
					'It must be no longer than '.(int)$MaxLengths['name'].' characters long.');
				$valid_input = false;
			}
			else if (strlen($posted_layout['name']) < 3) {
				$this->messages->AddMessage('error',
					'Layout name was too short. '.
					'It must be at least 3 characters long.');
				$valid_input = false;
			}
			$Layout['name'] = $posted_layout['name'];
		} else {
			$valid_input = false;
		}
		// Description
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
					'index' => $this->permissions_model->hasUserPermission('CROSSWORD_INDEX'),
					'layout_add' => $this->permissions_model->hasUserPermission('CROSSWORD_LAYOUT_ADD'),
					'layout_edit' => $this->permissions_model->hasUserPermission('CROSSWORD_LAYOUT_MODIFY'),
				),
				'Layouts' => $this->crosswords_model->GetAllLayouts(),
			);
			$this->main_frame->SetContentSimple('crosswords/office/layouts', $data);
		}
		else {
			// URL return path
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
				if (null === $layoutData) {
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

	/// Validate category input from user.
	private function _validateCategoryPost(&$Category, &$MaxLengths, &$Layouts)
	{
		$posted_category = array(
			'name'                      =>  $this->input->post('xword_cat_name'),
			'short_name'                =>  $this->input->post('xword_cat_short_name'),
			'default_width'             =>  $this->input->post('xword_cat_default_width'),
			'default_height'            =>  $this->input->post('xword_cat_default_height'),
			'default_layout_id'         =>  $this->input->post('xword_cat_default_layout'),
			'default_has_normal_clues'  => ($this->input->post('xword_cat_default_has_normal_clues') !== false),
			'default_has_cryptic_clues' => ($this->input->post('xword_cat_default_has_cryptic_clues') !== false),
			'default_winners'           =>  $this->input->post('xword_cat_default_winners'),
		);
		$valid_input = true;
		// Name
		if (false !== $posted_category['name']) {
			if (strlen($posted_category['name']) > (int)$MaxLengths['name']) {
				$this->messages->AddMessage('error',
					'Category name was too long. '.
					'It must be no longer than '.(int)$MaxLengths['name'].' characters long.');
				$valid_input = false;
			}
			else if (strlen($posted_category['name']) < 3) {
				$this->messages->AddMessage('error',
					'Category name was too short. '.
					'It must be at least 3 characters long.');
				$valid_input = false;
			}
			$Category['name'] = $posted_category['name'];
			$Category['default_has_normal_clues'] = $posted_category['default_has_normal_clues'];
			$Category['default_has_cryptic_clues'] = $posted_category['default_has_cryptic_clues'];
		} else {
			$valid_input = false;
		}
		// Short name
		if (false !== $posted_category['short_name']) {
			if (strlen($posted_category['short_name']) > (int)$MaxLengths['short_name']) {
				$this->messages->AddMessage('error',
					'Category short name was too long. '.
					'It must be no longer than '.(int)$MaxLengths['short_name'].' characters long.');
				$valid_input = false;
			}
			else if (strlen($posted_category['short_name']) < 3) {
				$this->messages->AddMessage('error',
					'Category short name was too short. '.
					'It must be at least 3 characters long.');
				$valid_input = false;
			}
			else if (!preg_match('/^[a-z0-9_-]*$/', $posted_category['short_name'])) {
				$this->messages->AddMessage('error',
					xml_escape('Category short name was not URI compatible. '.
								'It must consist of only lower case alphanumeric characters, numeric digits, underscores and dashes (PCRE: /^[a-z0-9_-]*$/).'));
				$valid_input = false;
			}
			$Category['short_name'] = $posted_category['short_name'];
		} else {
			$valid_input = false;
		}
		// Default width
		if (false !== $posted_category['default_width']) {
			$Category['default_width'] = $posted_category['default_width'];
			if (!is_numeric($posted_category['default_width'])) {
				$this->messages->AddMessage('error',
					xml_escape('Default crossword width is not numeric.'));
				$valid_input = false;
			}
			else {
				$Category['default_width'] = (int)$posted_category['default_width'];
				if ($Category['default_width'] < 5 || $Category['default_width'] > 50) {
					$this->messages->AddMessage('error',
						xml_escape('Default crossword width is out of range.'));
					$valid_input = false;
				}
			}
		} else {
			$valid_input = false;
		}
		// Default height
		if (false !== $posted_category['default_height']) {
			$Category['default_height'] = $posted_category['default_height'];
			if (!is_numeric($posted_category['default_height'])) {
				$this->messages->AddMessage('error',
					xml_escape('Default crossword height is not numeric.'));
				$valid_input = false;
			}
			else {
				$Category['default_height'] = (int)$posted_category['default_height'];
				if ($Category['default_height'] < 5 || $Category['default_height'] > 50) {
					$this->messages->AddMessage('error',
						xml_escape('Default crossword height is out of range.'));
					$valid_input = false;
				}
			}
		} else {
			$valid_input = false;
		}
		/// Default layout
		if (false !== $posted_category['default_layout_id'] && is_numeric($posted_category['default_layout_id'])) {
			$Category['default_layout_id'] = $posted_category['default_layout_id'] = (int)$posted_category['default_layout_id'];
			if (!isset($Layouts[$posted_category['default_layout_id']])) {
				$this->messages->AddMessage('error',
					xml_escape('Unrecognised layout identifier.'));
				$valid_input = false;
			}
		}
		else {
			$valid_input = false;
		}
		// Default winners
		if (false !== $posted_category['default_winners']) {
			$Category['default_winners'] = $posted_category['default_winners'];
			if (!is_numeric($posted_category['default_winners'])) {
				$this->messages->AddMessage('error',
					xml_escape('Default crossword winners is not numeric.'));
				$valid_input = false;
			}
			else {
				$Category['default_winners'] = (int)$posted_category['default_winners'];
				if ($Category['default_winners'] < 0 || $Category['default_winners'] > 20) {
					$this->messages->AddMessage('error',
						xml_escape('Default crossword winners is out of range.'));
					$valid_input = false;
				}
			}
		} else {
			$valid_input = false;
		}

		return $valid_input;
	}

	/** Category management.
	 */
	function cats($category = null, $op = null)
	{
		if (!CheckPermissions('office')) return;
		$this->load->model('permissions_model');
		$permissions = array(
			'index'            => $this->permissions_model->hasUserPermission('CROSSWORD_INDEX'),
			'categories_index' => $this->permissions_model->hasUserPermission('CROSSWORD_CATEGORIES_INDEX'),
			'category_add'     => $this->permissions_model->hasUserPermission('CROSSWORD_CATEGORY_ADD'),
			'category_view'    => $this->permissions_model->hasUserPermission('CROSSWORD_CATEGORY_VIEW'),
			'category_edit'    => $this->permissions_model->hasUserPermission('CROSSWORD_CATEGORY_MODIFY'),
			'crossword_add'    => $this->permissions_model->hasUserPermission('CROSSWORD_ADD'),
			'crossword_view'   => $this->permissions_model->hasUserPermission('CROSSWORD_VIEW'),
			'crossword_edit'   => $this->permissions_model->hasUserPermission('CROSSWORD_MODIFY'),
		);
		if (null === $category) {
			if (!CheckRolePermissions('CROSSWORD_CATEGORIES_INDEX')) return;
			$this->pages_model->SetPageCode('crosswords_office_cats');
			$data = array(
				'Permissions' => &$permissions,
				'Categories' => $this->crosswords_model->GetAllCategories(),
			);
			$this->main_frame->SetContentSimple('crosswords/office/categories', $data);
		}
		else {
			// URL return path
			$ret = (isset($_GET['ret']) ? $_GET['ret'] : null);
			$effective_ret = (null === $ret ? 'office/crosswords/cats' : $ret);
			$action = site_url($this->uri->uri_string());
			if ($ret !== null) {
				$action .= '?ret='.urlencode($ret);
			}
			$layouts = $this->crosswords_model->GetAllLayouts();
			$data = array(
				'Permissions' => &$permissions,
				'MaxLengths' => array(
					'name'       => 255,
					'short_name' => 32,
				),
				'Layouts' => $layouts,
				'Category' => array(
					'name'                      => '',
					'short_name'                => '',
					'default_width'             => 13,
					'default_height'            => 13,
					'default_layout_id'         => -1,
					'default_has_normal_clues'  => true,
					'default_has_cryptic_clues' => false,
					'default_winners'           => 3,
				),
				'Actions'    => array(),
				'PostAction' => $action,
			);
			if ('add' === $category) {
				if (!CheckRolePermissions('CROSSWORD_CATEGORY_ADD')) return;
				$this->pages_model->SetPageCode('crosswords_office_cat_add');
				// Check post input
				$cancelled = (false !== $this->input->post('xword_cat_cancel'));
				if ($cancelled) {
					redirect($effective_ret);
				}
				if (empty($layouts)) {
					$this->messages->AddMessage('error',
						'No crossword layouts have been set up. '.
						'Please <a href="'.site_url('office/crosswords/layouts/add').'?ret='.xml_escape(urlencode($action)).'">add a layout</a> before adding categories.');
				}
				else {
					// Do the adding if possible
					$valid_input = $this->_validateCategoryPost($data['Category'], $data['MaxLengths'], $layouts);
					if ($valid_input) {
						$messages = $this->crosswords_model->AddCategory($data['Category']);
						$this->messages->AddMessages($messages);
						if (!isset($messages['error']) || empty($messages['error'])) {
							redirect($effective_ret);
						}
					}
					$data['Actions']['add'] = 'Add Category';
					$data['Actions']['cancel'] = 'Cancel';
					$this->main_frame->SetContentSimple('crosswords/office/category_edit', $data);
				}
			}
			elseif (is_numeric($category)) {
				$category = (int)$category;

				if ($op == 'edit') {
					if (!CheckRolePermissions('CROSSWORD_CATEGORY_MODIFY')) return;
					$this->pages_model->SetPageCode('crosswords_office_cat_edit');
				}
				elseif ($op === null) {
					if (!CheckRolePermissions('CROSSWORD_CATEGORY_VIEW')) return;
					$this->pages_model->SetPageCode('crosswords_office_cat_view');
				}
				else {
					show_404();
				}

				// Retreive current data about the category.
				$categoryData = $this->crosswords_model->GetCategoryById($category);
				if (null === $categoryData) {
					$this->messages->AddMessage('error', "No crossword category with the id $category exists.");
					redirect($effective_ret);
				}
				$data['Category'] = $categoryData;
				// Provide some info for the title
				$this->main_frame->SetTitleParameters(array(
					'CATEGORY' => $categoryData['name'],
				));

				if ($op == 'edit') {
					// Check post input
					$cancelled = (false !== $this->input->post('xword_cat_cancel'));
					if ($cancelled) {
						redirect($effective_ret);
					}
					$valid_input = $this->_validateCategoryPost($data['Category'], $data['MaxLengths'], $layouts);
					// Do the saving if possible
					if ($valid_input) {
						// Don't bother if nothing has changed
						if ($data['Category']['name'] != $categoryData['name'] ||
							$data['Category']['short_name'] != $categoryData['short_name'] ||
							$data['Category']['default_width'] != $categoryData['default_width'] ||
							$data['Category']['default_height'] != $categoryData['default_height'] ||
							$data['Category']['default_layout_id'] != $categoryData['default_layout_id'] ||
							$data['Category']['default_has_normal_clues'] != $categoryData['default_has_normal_clues'] ||
							$data['Category']['default_has_cryptic_clues'] != $categoryData['default_has_cryptic_clues'] ||
							$data['Category']['default_winners'] != $categoryData['default_winners'])
						{
							$messages = $this->crosswords_model->ModifyCategory($category, $data['Category']);
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
					$data['Actions']['save'] = 'Save Category';
					$data['Actions']['cancel'] = 'Cancel';
					$this->main_frame->SetContentSimple('crosswords/office/category_edit', $data);
				}
				else {
					if (false !== $this->input->post('xword_cat_view_add_crossword')) {
						if (!CheckRolePermissions('CROSSWORD_ADD')) return;
						$new_category_id = $this->crosswords_model->AddCrossword($category);
						if (null !== $new_category_id) {
							redirect("office/crosswords/crossword/$new_category_id");
						}
						else {
							$this->messages->AddMessage('error', 'Could not add a new crossword to this category.');
						}
					}

					// Get the crosswords in this category
					$crosswords = $this->crosswords_model->GetCrosswords(null, $category);
					$data['Crosswords'] = $crosswords;
					$this->main_frame->SetContentSimple('crosswords/office/category_view', $data);
				}
			}
			else {
				show_404();
			}
		}
		$this->main_frame->includeCss('stylesheets/crosswords_office.css');
		$this->main_frame->Load();
	}

	/** Crosswords management.
	 */
	function crossword($crossword = null, $operation = null)
	{
		if ('save' === $operation) {
			OutputModes('ajax');
		}
		if (!CheckPermissions('office')) return;
		if (null !== $crossword && is_numeric($crossword)) {
			$crossword = (int)$crossword;

			$crosswords = $this->crosswords_model->GetCrosswords($crossword);
			if (count($crosswords) === 0) {
				show_404();
			}

			$this->load->model('permissions_model');
			$data = array(
				'Permissions' => array(
					'modify' => $this->permissions_model->hasUserPermission('CROSSWORD_MODIFY'),
					'stats_basic' => $this->permissions_model->hasUserPermission('CROSSWORD_STATS_BASIC'),
				),
				'Crossword' => $crosswords[0],
			);

			if (null === $operation) {
				if (!CheckRolePermissions('CROSSWORD_VIEW')) return;
				$this->pages_model->SetPageCode('crosswords_office_xword_view');
				$this->main_frame->SetContentSimple('crosswords/office/crossword_view', $data);
			}
			else if ('save' === $operation) {
				if (!CheckRolePermissions('CROSSWORD_VIEW', 'CROSSWORD_MODIFY')) return;
				$this->pages_model->SetPageCode('crosswords_office_xword_edit');

				if (isset($_GET['xw']['save'])) {
					$puzzle = new CrosswordPuzzle();
					$worked = $puzzle->importData($_GET['xw']);
					if ($worked) {
						$this->crosswords_model->SaveCrossword($crossword, $puzzle);
						$status = 'success';
					}
					else {
						$this->main_frame->Error(array(
							'class' => 'error',
							'text' => 'Invalid crossword data.',
						));
						$status = 'fail';
					}
				}
				else {
					$this->main_frame->Error(array(
						'class' => 'error',
						'text' => 'Unable to edit crossword.',
					));
					$status = 'fail';
				}

				$root = array(
					'_tag' => 'crossword',
					'status' => $status,
				);

				$this->main_frame->SetXml($root);
				$this->main_frame->Load();
				return;
			}
			else if ('edit' === $operation) {
				if (!CheckRolePermissions('CROSSWORD_VIEW', 'CROSSWORD_MODIFY')) return;
				$this->pages_model->SetPageCode('crosswords_office_xword_edit');
				$this->load->helper('input_date');

				$puzzle = 0;
				$worked = $this->crosswords_model->LoadCrossword($crossword, $puzzle);
				if (!$worked) {
					$puzzle = new CrosswordPuzzle(13, 13);
				}

				$crosswordView = new CrosswordView($puzzle, true);

				$data = array();
				$config = new InputInterfaces;
				$config->Add('Quick clues', new InputCheckboxInterface('quickclues', true));
				$config->Add('Cryptic Clues', new InputCheckboxInterface('crypticclues', true));
				$config->Add('Deadline', new InputDateInterface('deadline', time(), false));
				$config->Add('Publication', new InputDateInterface('publication', time(), false));
				$config->Add('Expiry', new InputDateInterface('expiry', time(), false));
				$num_winners = new InputIntInterface('winners', 3, true);
				$num_winners->SetRange(1,100);
				$config->Add('Winners', $num_winners);
				$config->Validate();
				$data['Configuration'] = &$config;
				$data['Grid'] = $crosswordView;
				$data['Paths'] = array(
					'view' => "/office/crosswords/crossword/$crossword",
					'save' => "/office/crosswords/crossword/$crossword/save",
				);

				$this->main_frame->includeCss('stylesheets/crosswords.css');
				$this->main_frame->includeJs('javascript/simple_ajax.js');
				$this->main_frame->includeJs('javascript/crosswords.js');
				$this->main_frame->includeJs('javascript/crosswords_edit.js');
				$this->main_frame->SetContentSimple('crosswords/office/crossword_edit', $data);
			}
			else if ('stats' === $operation) {
				if (!CheckRolePermissions('CROSSWORD_STATS_BASIC')) return;
			}
			else {
				show_404();
			}
		}
		else {
			show_404();
		}
		$this->main_frame->Load();
	}

}

?>
