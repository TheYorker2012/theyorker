<?php

/// Pages admin controller.
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * This will allow management of the following database tables:
 *	- pages
 *	- page_properties
 *	- property_types
 *
 * And will have special editors for special property types:
 *	- plain text
 *	- wikitext (option to update or clear the cache)
 *	- images + photos
 */
class Pages extends Controller
{
	/// array Permissions array of bools indexed by permission name.
	protected $mPermissions;
	
	/// Default constructor
	function __construct()
	{
		parent::Controller();
		
		$this->load->model('pages_model');
		$this->load->library('frame_public');
		
		// Primitive permissions
		$this->mPermissions = array(
				'officer'       => TRUE,
				'administrator' => TRUE,
			);
		// Derived permissions
		$this->mPermissions['view']          = $this->mPermissions['officer'];
		
		$this->mPermissions['page_new']      = $this->mPermissions['administrator'];
		$this->mPermissions['page_edit']     = $this->mPermissions['officer'];
		$this->mPermissions['page_rename']   = $this->mPermissions['administrator'];
		$this->mPermissions['page_delete']   = $this->mPermissions['administrator'];
		
		$this->mPermissions['custom_new']    = $this->mPermissions['officer'];
		$this->mPermissions['custom_edit']   = $this->mPermissions['officer'];
		$this->mPermissions['custom_rename'] = $this->mPermissions['officer'];
		$this->mPermissions['custom_delete'] = $this->mPermissions['officer'];
	}
	
	/// Check if the user has permission to view these pages.
	/**
	 * @param $Permission string Permission name (key to $this->mPermissions).
	 * @param $Message string A message to display in the event of failure.
	 * @return bool Whether the user has permission.
	 */
	function _CheckViewPermissions($Permission = 'view', $Message = 'You do not have permission to access this page')
	{	
		if (!$this->mPermissions[$Permission]) {
			$this->frame_public->AddMessage(
					new PermissionDeniedMsg($Message)
				);
			return FALSE;
		} else {
			return TRUE;
		}
	}
	
	/// Default page
	function index()
	{
		$this->pages_model->SetPageCode('admin_pages');
		if ($this->_CheckViewPermissions('view')) {
			$all_pages = $this->pages_model->GetAllPages();
			
			$data = array();
			
			$data['pages'] = array(0 => FALSE);
			$data['custom'] = array();
			
			foreach ($all_pages as $key => $page) {
				if ('custom:' == substr($page['page_codename'],0,7)) {
					$page['codename'] = substr($page['page_codename'],7);
					$data['custom'][] = $page;
				} elseif ($page['page_id'] != 0) {
					$page['codename'] = $page['page_codename'];
					$data['pages'][] = $page;
				} else {
					$page['codename'] = $page['page_codename'];
					$data['pages'][0] = $page;
				}
			}
			if (FALSE === $data['pages'][0]) {
				unset($data['pages'][0]);
			}
			
			$data['permissions'] = $this->mPermissions;
			$this->frame_public->SetContentSimple('admin/pages_index.php', $data);
		}
		$this->frame_public->Load();
	}
	
	/// Setup the data array for the view for creating a new page.
	/**
	 * @param $Data array Data used for setting up the page.
	 * @param $Target string Page to send the updated data to.
	 * @param $Redirect string Page to direct to after successful save.
	 * @param $Prefix string Prefix to add to codenames.
	 * @return array Modified @a $Data array ready for view.
	 */
	private function _NewPage($Data, $Target, $Redirect, $Prefix = '')
	{
		$Data['target'] = $Target;
		$Data['codename'] = '';
		$Data['title'] = '';
		$Data['description'] = '';
		$Data['keywords'] = '';
		$Data['comments'] = 0;
		$Data['ratings'] = 0;
		$Data['properties'] = array();
		
		
		$input['codename']    = $this->input->post('codename',    FALSE);
		if (FALSE !== $input['codename']) {
			$input['title']       = $this->input->post('title',       FALSE);
			$input['description'] = $this->input->post('description', FALSE);
			$input['keywords']    = $this->input->post('keywords',    FALSE);
			$input['comments']    = $this->input->post('comments',    FALSE);
			$input['ratings']     = $this->input->post('ratings',     FALSE);
			$save_failed = FALSE;
			
			// Validate and check permissions
			if ($this->_CheckViewPermissions('custom_new','You do not have permission to create a new page')) {
				// Check if new codename is in use
				$Data['codename'] = $input['codename'];
				if (FALSE !== $this->pages_model->PageCodeInUse($Prefix.$input['codename'])) {
					$this->frame_public->AddMessage(new InformationMsg('Codename in use','A page with the codename "'.$input['codename'].'" already exists. Please choose another.'));
					$save_failed = TRUE;
				}
			} else {
				$save_failed = TRUE;
			}
			if (FALSE !== $input['title'])
				$Data['title'] = $input['title'];
			if (FALSE !== $input['description'])
				$Data['description'] = $input['description'];
			if (FALSE !== $input['keywords'])
				$Data['keywords'] = $input['keywords'];
			$Data['comments'] = $input['comments'] = (($input['comments'] !== FALSE)?1:0);
			$Data['ratings']  = $input['ratings']  = (($input['ratings']  !== FALSE)?1:0);
			
			if (FALSE === $save_failed) {
				// Try and save to db
				$input['codename'] = $Prefix.$input['codename'];
				if ($this->pages_model->CreatePage($input)) {
					$this->frame_public->AddMessage(new InformationMsg('Saved', 'The page was successfully saved'));
					if ($Data['codename'] != $page_code) {
						redirect($Redirect.$Data['codename']);
					}
				} else {
					$this->frame_public->AddMessage(new ErrorMsg('Internal Error', 'The page could not be saved as an internal error occurred'));
					$save_failed = TRUE;
				}
			}
		}
		
		return $Data;
	}
	
	/// Setup the data array for the view for editing a page.
	/**
	 * @param $Data array Data used for setting up the page.
	 * @param $InputPageCode string Page code from uri.
	 * @param $Target string Page to send the updated data to.
	 * @param $Redirect string Page to direct to after successful save.
	 * @param $DefaultProperties array Set of properties to create automatically
	 *	if they don't exist (and to display first). Must have:
	 *	- 'label'
	 *	- 'type'
	 *	- 'text'
	 * @param $Prefix string Prefix to add to codenames.
	 * @return array Modified @a $Data array ready for view.
	 */
	private function _EditPage(
			$Data,
			$InputPageCode,
			$Target,
			$Redirect,
			$DefaultProperties = array(),
			$Prefix = '')
	{
		$data = $Data;
		$page_code = $Prefix.$InputPageCode;
		// Find the custom page code
		$page_info = $this->pages_model->GetSpecificPage($page_code, TRUE);
		if (FALSE === $page_info) {
			show_404($InputPageCode);
		} else {
			$data['target']      = $Target.$InputPageCode;
			$data['codename']    = $InputPageCode;
			$data['title']       = $page_info['title'];
			$data['description'] = $page_info['description'];
			$data['keywords']    = $page_info['keywords'];
			$data['comments']    = $page_info['comments'];
			$data['ratings']     = $page_info['ratings'];
			
			// DefaultProperties has default properties (without id's)
			// page_info['properties'] has current properties (with id's)
			$data['properties']  = array();
			$new_id = 0;
			$properties = array();
			foreach ($page_info['properties'] as $property) {
				if ($property['type'] !== 'wikitext_cache') {
					$key = $property['label'].'|'.$property['type'];
					$properties[$key] = array(
							'status'=> 'prop',
							'id'    => $property['id'],
							'label' => $property['label'],
							'type'  => $property['type'],
							'text'  => $property['text'],
						);
				}
			}
			foreach ($DefaultProperties as $property) {
				$key = $property['label'].'|'.$property['type'];
				if (!array_key_exists($key, $properties)) {
					$properties[$key] = array(
							'status'=> 'newprop',
							'id'    => (--$new_id),
							'label' => $property['label'],
							'type'  => $property['type'],
							'text'  => $property['text'],
						);
				}
			}
			$data['properties'] = array();
			foreach ($properties as $property) {
				$data['properties'][$property['id']] = array(
						'id'    => $property['status'].$property['id'],
						'label' => $property['label'],
						'type'  => $property['type'],
						'text'  => $property['text'],
					);
			}
			
			$input['codename']    = $this->input->post('codename',    FALSE);
			if (FALSE !== $input['codename']) {
				$input['title']       = $this->input->post('title',       FALSE);
				$input['description'] = $this->input->post('description', FALSE);
				$input['keywords']    = $this->input->post('keywords',    FALSE);
				$input['comments']    = $this->input->post('comments',    FALSE);
				$input['ratings']     = $this->input->post('ratings',     FALSE);
				$save_failed = FALSE;
				
				// Validate and check permissions
				if ($input['codename'] != $data['codename']) {
					if ($this->_CheckViewPermissions('custom_rename','You do not have permission to rename custom pages')) {
						// Check if new codename is in use
						$data['codename'] = $input['codename'];
						if (FALSE !== $this->pages_model->PageCodeInUse($Prefix.$input['codename'])) {
							$this->frame_public->AddMessage(new InformationMsg('Codename in use','A page with the codename "'.$input['codename'].'" already exists. Please choose another.'));
							$save_failed = TRUE;
						}
					} else {
						$save_failed = TRUE;
					}
				}
				if (FALSE !== $input['title'])
					$data['title'] = $input['title'];
				if (FALSE !== $input['description'])
					$data['description'] = $input['description'];
				if (FALSE !== $input['keywords'])
					$data['keywords'] = $input['keywords'];
				$data['comments'] = $input['comments'] = (($input['comments'] !== FALSE)?1:0);
				$data['ratings']  = $input['ratings']  = (($input['ratings']  !== FALSE)?1:0);
				
				if (FALSE === $save_failed) {
					// Try and save to db
					$input['codename'] = $Prefix.$input['codename'];
					if ($this->pages_model->SaveSpecificPage($page_code, $input)) {
						$this->frame_public->AddMessage(new InformationMsg('Saved', 'The page was successfully saved'));
						if ($data['codename'] != $page_code) {
							redirect($Redirect.$data['codename']);
						}
					} else {
						$this->frame_public->AddMessage(new ErrorMsg('Internal Error', 'The page could not be saved as an internal error occurred'));
					}
				}
			}
			if ($this->input->post('property_edit_button', FALSE) !== FALSE) {
				$input = array();
				$input['properties'] = array();
				$input['property_add'] = array();
				$input['property_remove'] = array('wikitext_cache' => array());
				foreach ($_POST as $key => $value) {
					if (preg_match('/^prop(\d+)$/',$key,$matches)) {
						$property_id = (int)$matches[1];
						if (array_key_exists($property_id, $data['properties'])) {
							if ($data['properties'][$property_id]['text'] != $value) {
								// property has been changed
								$input['properties'][] = array(
										'id' => $property_id,
										'text' => $value,
									);
								$data['properties'][$property_id]['text'] = $value;
								if ($data['properties'][$property_id]['type'] === 'wikitext') {
									$input['property_remove']['wikitext_cache'][] = $data['properties'][$property_id]['label'];
								}
							}
						}
					}
					if (preg_match('/^newprop(\-?\d+)$/',$key,$matches)) {
						$label_key = 'label-'.$key;
						$type_key = 'type-' .$key;
						if (	array_key_exists($label_key, $_POST) &&
								array_key_exists($type_key, $_POST)) {
							// New property
							$input['property_add'][] = array(
									'label'	=> $_POST[$label_key],
									'type'	=> $_POST[$type_key],
									'text'	=> $value,
								);
						}
					}
				}
				
				if (count($input['properties'])+count($input['property_add'])> 0) {
					// Try and save to db
					if ($this->pages_model->SaveSpecificPage($page_code, $input)) {
						$this->frame_public->AddMessage(new InformationMsg('Saved', 'The page was successfully saved'));
					} else {
						$this->frame_public->AddMessage(new ErrorMsg('Internal Error', 'The page could not be saved as an internal error occurred'));
					}
				}
				
				$_POST = array();
				return $this->_EditPage($Data, $InputPageCode, $Target, $Redirect, $DefaultProperties, $Prefix);
			}
		}
		return $data;
	}
	
	/// Delete a page.
	private function _DeletePage(
			$Data,
			$InputPageCode,
			$Target,
			$Prefix = '')
	{
		$data = $Data;
		$data['target'] = $Target.$InputPageCode;
		if (FALSE === $this->input->post('confirm_delete',FALSE)) {
			$data['complete'] = FALSE;
			// Get information about the page so user is informed before confirming.
		} else {
			$data['complete'] = TRUE;
			// user confirmed, delete the page and its properties.
		}
		return $data;
	}
	
	/// Function for administrating pages of the website.
	/**
	 * @param $Operation string An operation to perform:
	 *	- new
	 *	- edit (@a $PageCode required)
	 * @param $PageCode string Page code of page to edit.
	 */
	function page($Operation, $PageCode='')
	{
		// Get url segments after the first (controller).
		$num_segments = $this->uri->total_segments();
		$segments = array();
		for ($counter = 5; $counter <= $num_segments; ++$counter) {
			$segments[] = $this->uri->segment($counter);
		}
		$PageCode = implode('/',$segments);
		// We now have the page code so we can continue.
		
		$this->frame_public->SetExtraHead('<script src="/javascript/clone.js" type="text/javascript"></script>');
		if ($this->_CheckViewPermissions()) {
			$data = array();
			$data['permissions'] = $this->mPermissions;
			switch ($Operation) {
				case 'new':
					$this->pages_model->SetPageCode('admin_pages_page_new');
					$data = $this->_NewPage($data,
									'/admin/pages/page/new',
									'/admin/pages/page/edit/');
					$this->frame_public->SetContentSimple('admin/pages_page.php', $data);
					break;
					
				case 'edit':
					$this->pages_model->SetPageCode('admin_pages_page_edit');
					$this_uri = '/admin/pages/page/edit/';
					$data = $this->_EditPage($data, $PageCode, $this_uri, $this_uri);
					$this->frame_public->SetContentSimple('admin/pages_page.php', $data);
					break;
					
				case 'delete':
					$this->pages_model->SetPageCode('admin_pages_page_delete');
					$this_uri = '/admin/pages/page/delete/';
					$data = $this->_DeletePage($data, $PageCode, $this_uri);
					$this->frame_public->SetContentSimple('admin/pages_delete.php', $data);
					break;
					
				default:
					show_404($Operation);
					return;
			}
			$this->frame_public->SetTitleParameters( array(
					'codename' => $PageCode,
				) );
		}
		$this->frame_public->Load();
	}
	
	/// Function for administrating custom pages.
	/**
	 * @param $Operation string An operation to perform:
	 *	- new
	 *	- edit (@a $PageCode required)
	 * @param $PageCode string Page code of custom page to edit
	 *	(without preceeding 'custom:').
	 */
	function custom($Operation, $CustomPageCode='')
	{
		// Get url segments after the first (controller).
		$num_segments = $this->uri->total_segments();
		$segments = array();
		for ($counter = 5; $counter <= $num_segments; ++$counter) {
			$segments[] = $this->uri->segment($counter);
		}
		$CustomPageCode = implode('/',$segments);
		// We now have the page code so we can continue.
		
		$this->frame_public->SetExtraHead('<script src="/javascript/clone.js" type="text/javascript"></script>');
		if ($this->_CheckViewPermissions()) {
			$data = array();
			$data['permissions'] = $this->mPermissions;
			switch ($Operation) {
				case 'new':
					$this->pages_model->SetPageCode('admin_pages_custom_new');
					$data = $this->_NewPage($data,
									'/admin/pages/custom/new',
									'/admin/pages/custom/edit/',
									'custom:');
					$this->frame_public->SetContentSimple('admin/pages_page.php', $data);
					break;
					
				case 'edit':
					$this->pages_model->SetPageCode('admin_pages_custom_edit');
					$this_uri = '/admin/pages/custom/edit/';
					$data = $this->_EditPage($data, $CustomPageCode, $this_uri, $this_uri,
							array(
								array(
									'label' => 'main',
									'type' => 'wikitext',
									'text' => 'Your page content goes here.',
								),
							),
							'custom:'
						);
					$this->frame_public->SetContentSimple('admin/pages_page.php', $data);
					break;
					
				case 'delete':
					$this->pages_model->SetPageCode('admin_pages_custom_delete');
					$this_uri = '/admin/pages/custom/delete/';
					$data = $this->_DeletePage($data, $CustomPageCode, $this_uri, 'custom:');
					$this->frame_public->SetContentSimple('admin/pages_delete.php', $data);
					break;
					
				default:
					show_404($Operation);
					return;
			}
			$this->frame_public->SetTitleParameters( array(
					'codename' => $CustomPageCode,
				) );
		}
		$this->frame_public->Load();
	}
	
}

?>