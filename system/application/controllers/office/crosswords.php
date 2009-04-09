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

		$permissions = array(
			'index' => $this->permissions_model->hasUserPermission('CROSSWORD_INDEX'),
			'tips_index' => $this->permissions_model->hasUserPermission('CROSSWORD_TIPS_INDEX'),
			'tip_cat_add' => $this->permissions_model->hasUserPermission('CROSSWORD_TIP_CATEGORY_ADD'),
			'tip_cat_view' => $this->permissions_model->hasUserPermission('CROSSWORD_TIP_CATEGORY_VIEW'),
			'tip_cat_edit' => $this->permissions_model->hasUserPermission('CROSSWORD_TIP_CATEGORY_MODIFY'),
		);
		if (null === $category) {
			// Main tip index page showing tip categories
			if (!CheckRolePermissions('CROSSWORD_TIPS_INDEX')) return;

			$data = array(
				'Permissions' => &$permissions,
				'Categories' => $this->crosswords_model->GetTipCategories(),
				'SelfUri' => $this->uri->uri_string(),
			);
			$this->main_frame->setContentSimple('crosswords/office/tips', $data);
		}
		else {
			$category_info = null;
			if (is_numeric($category)) {
				$category_info = $this->crosswords_model->GetTipCategories((int)$category);
				if (empty($category_info)) {
					$category_info = null;
				}
				else {
					$category_info = $category_info[0];
				}
			}
			$edit = ('edit' === $argument);
			if ('add' === $category || $edit) {
				// Page to add or edit a category
				if ($edit) {
					if (!CheckRolePermissions('CROSSWORD_TIP_CATEGORY_MODIFY')) return;
					if (null === $category_info) {
						show_404();
					}
				}
				else {
					if (!CheckRolePermissions('CROSSWORD_TIP_CATEGORY_ADD')) return;
				}

				$this->load->helper('input');
				$form = new InputInterfaces;

				if (!$edit) {
					$category_info = array(
						'id' => null,
						'name' => '',
						'description' => '',
					);
				}

				$name_interface = new InputTextInterface('name', $category_info['name']);
				$name_interface->SetMaxLength(255);
				$name_interface->SetRequired(true);
				$form->Add('Name', $name_interface);

				$description_interface = new InputTextInterface('description', $category_info['description']);
				$description_interface->SetMultiline(true);
				$form->Add('Description', $description_interface);

				$num_errors = $form->Validate();
				if (0 == $num_errors && $form->Updated()) {
					$values = $form->ChangedValues();
					$error = false;
					if (count($values) == 0) {
						$this->messages->AddMessage('information', "You did not make any changes");
						$error = true;
					}
					if (!$error) {
						if (!$edit) {
							$id = $this->crosswords_model->AddTipCategory($values);
							if ($id === null) {
								$this->messages->AddMessage('error', 'Tip category could not be added');
							}
							else {
								$this->messages->AddMessage('success', 'Tip category added');
								if (isset($_GET['ret'])) {
									redirect($_GET['ret']);
								}
								redirect('office/crosswords/tips/'.$id);
							}
						}
						else {
							$values['id'] = $category_info['id'];
							if (!$this->crosswords_model->UpdateTipCategory($values)) {
								$this->messages->AddMessage('error', 'Changes could not be saved');
							}
							else {
								$this->messages->AddMessage('success', 'Changes have been saved successfully');
								if (isset($_GET['ret'])) {
									redirect($_GET['ret']);
								}
								foreach ($values as $id => $value) {
									$category_info[$id] = $value;
								}
							}
						}
					}
				}

				$data = array(
					'Permissions' => &$permissions,
					'Form' => &$form,
					'Actions' => array(
						'add' => ($edit ? 'Save tip category' : 'Add tip category'),
					),
					'PostAction' => $this->uri->uri_string().(isset($_GET['ret']) ? ('?ret='.urlencode($_GET['ret'])) : ''),
				);
				$this->main_frame->setContentSimple('crosswords/office/tip_cat_edit', $data);
			}
			else {
				if (!CheckRolePermissions('CROSSWORD_TIP_CATEGORY_VIEW')) return;
				if (null === $category_info) {
					show_404();
				}

				$data = array(
					'Permissions' => &$permissions,
					'Category' => $category_info,
					'Tips' => new CrosswordTipsList($category_info['id'], null, true),
					'PostAction' => $this->uri->uri_string(),
				);

				$this->main_frame->setContentSimple('crosswords/office/tip_cat_view', $data);
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
		$this->main_frame->IncludeCss('stylesheets/crosswords_office.css');
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
			$crossword_info = $crosswords[0];

			$this->load->model('permissions_model');
			$data = array(
				'Permissions' => array(
					'modify' => $this->permissions_model->hasUserPermission('CROSSWORD_MODIFY'),
					'stats_basic' => $this->permissions_model->hasUserPermission('CROSSWORD_STATS_BASIC'),
				),
				'Crossword' => &$crossword_info,
			);

			if (null === $operation) {
				if (!CheckRolePermissions('CROSSWORD_VIEW')) return;

				$puzzle = 0;
				$worked = $this->crosswords_model->LoadCrossword($crossword_info['id'], $puzzle);
				if (!$worked) {
					show_404();
				}
				$crosswordView = new CrosswordView($puzzle);
				$crosswordView->setClueTypes($crossword_info['has_quick_clues'], $crossword_info['has_cryptic_clues']);
				$crosswordView->setReadOnly(true, true);
				$data['Grid'] = &$crosswordView;
				$data['Tips'] = new CrosswordTipsList(null, $crossword_info['id'], true, false);

				$this->pages_model->SetPageCode('crosswords_office_xword_view');
				$this->main_frame->SetContentSimple('crosswords/office/crossword_view', $data);
			}
			else if ('save' === $operation) {
				if (!CheckRolePermissions('CROSSWORD_VIEW', 'CROSSWORD_MODIFY')) return;

				if (isset($_POST['xw']['save'])) {
					$puzzle = new CrosswordPuzzle();
					$worked = $puzzle->importData($_POST['xw']);
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
				$this->load->helper('input_progress');

				$puzzle = 0;
				$worked = $this->crosswords_model->LoadCrossword($crossword, $puzzle);
				if (!$worked) {
					$puzzle = new CrosswordPuzzle(13, 13);
				}

				$crosswordView = new CrosswordView($puzzle, true);

				$data = array();

				// MAIN CONFIGURATION
				$config = new InputInterfaces;

				$quick_clues_interface = new InputCheckboxInterface('has_quick_clues', $crossword_info['has_quick_clues']);
				$config->Add('Quick clues', $quick_clues_interface);

				$cryptic_clues_interface = new InputCheckboxInterface('has_cryptic_clues', $crossword_info['has_cryptic_clues']);
				$config->Add('Cryptic Clues', $cryptic_clues_interface);

				$categories = $this->crosswords_model->GetAllCategories();
				$category_names = array();
				foreach ($categories as $id => $category) {
					$category_names[$id] = $category['name'];
				}
				$category_interface = new InputSelectInterface('category_id', $crossword_info['category_id']);
				$category_interface->SetOptions($category_names);
				$config->Add('Category', $category_interface);

				$layouts = $this->crosswords_model->GetAllLayouts();
				$layout_names = array();
				foreach ($layouts as $id => $layout) {
					$layout_names[$id] = $layout['name'];
				}
				$layout_interface = new InputSelectInterface('layout_id', $crossword_info['layout_id']);
				$layout_interface->SetOptions($layout_names);
				$config->Add('Layout', $layout_interface);

				$deadline_interface = new InputDateInterface('deadline', $crossword_info['deadline'], true);
				$config->Add('Deadline', $deadline_interface);

				$publication_interface = new InputDateInterface('publication', $crossword_info['publication'], true);
				$config->Add('Publication', $publication_interface);

				$expiry_interface = new InputDateInterface('expiry', $crossword_info['expiry'], true);
				$config->Add('Expiry', $expiry_interface);

				$winners_value = $crossword_info['winners'];
				$winners_interface = new InputIntInterface('winners', $winners_value, $winners_value > 0);
				$winners_interface->SetRange(1,100);
				$config->Add('Winners', $winners_interface);

				$completeness_interface = new InputProgressInterface('completeness', $crossword_info['completeness']);
				$config->Add('Progress', $completeness_interface);

				$authors_interface = new InputSelectInterface('authors', $crossword_info['author_ids']);
				$authors = $this->crosswords_model->GetAllAuthors();
				$author_options = array();
				foreach ($authors as $author) {
					$author_options[(int)$author['id']] = $author['fullname'];
				}
				foreach ($crossword_info['authors'] as $author) {
					if (!isset($author_options[$author['id']])) {
						$author_options[$author['id']] = $author['fullname'];
					}
				}
				$authors_interface->SetOptions($author_options);
				$config->Add('Authors', $authors_interface);

				// VALIDATION
				$num_errors = $config->Validate();
				if (0 == $num_errors && $config->Updated()) {
					$values = $config->ChangedValues();
					$error = false;
					if (count($values) == 0) {
						$this->messages->AddMessage('information', "You did not make any changes");
						$error = true;
					}
					// Apply rules to changes here
					$integrated_values = $crossword_info;
					foreach ($values as $id => $value) {
						$integrated_values[$id] = $value;
					}
					// can't have deadline after publishing
					if ($integrated_values['deadline'] !== null && $integrated_values['publication'] !== null
							&& $integrated_values['deadline'] > $integrated_values['publication']) {
						$this->messages->AddMessage('error', 'Deadline should not be set after publication');
						$error = true;
					}
					// can't have expiry before publishing
					if ($integrated_values['publication'] !== null && $integrated_values['expiry'] !== null
							&& $integrated_values['publication'] > $integrated_values['expiry']) {
						$this->messages->AddMessage('error', 'Expiry should not be set before publication');
						$error = true;
					}

					if (!$error) {
						if (isset($values['authors'])) {
							$authors = $values['authors'];
							$values['authors'] = array();
							foreach ($authors as $author_id) {
								$values['authors'][(int)$author_id] = array(
									'id' => (int)$author_id,
									'fullname' => $author_options[(int)$author_id],
								);
							}
						}
						$values['id'] = $crossword_info['id'];
						if (!$this->crosswords_model->UpdateCrossword($values)) {
							$this->messages->AddMessage('error', 'Changes could not be saved');
						}
						else {
							$this->messages->AddMessage('success', 'Changes have been saved successfully');
							foreach ($values as $id => $value) {
								$crossword_info[$id] = $value;
							}
						}
					}
				}

				// Which clues are enabled may have just changed
				$crosswordView->setClueTypes($crossword_info['has_quick_clues'], $crossword_info['has_cryptic_clues']);

				$data['Configuration'] = &$config;
				$data['Tips'] = new CrosswordTipsList(null, $crossword_info['id'], true);
				$data['Grid'] = &$crosswordView;
				$data['Paths'] = array(
					'view' => site_url("office/crosswords/crossword/$crossword"),
					'save' => site_url("office/crosswords/crossword/$crossword/save"),
				);

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
