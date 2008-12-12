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

	/** Layout management.
	 */
	function layouts($layout = null)
	{
		if (!CheckPermissions('office')) return;
		if (null === $layout) {
			if (!CheckRolePermissions('CROSSWORD_LAYOUTS_INDEX')) return;
		}
		else {
			if ('add' === $layout) {
				if (!CheckRolePermissions('CROSSWORD_LAYOUT_ADD')) return;
			}
			else {
				if (!CheckRolePermissions('CROSSWORD_LAYOUT_MODIFY')) return;
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
						'Please <a href="'.site_url('office/crosswords/layouts/add').'">add a layout</a> before adding categories.');
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
