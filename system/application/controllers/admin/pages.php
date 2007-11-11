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
	
	function __construct()
	{
		parent::Controller();
		
		$this->load->library('messages');
	}
	
	function _SetPermissions()
	{
		// Primitive permissions
		$this->mPermissions = array(
				'officer'       => FALSE,
				'administrator' => FALSE,
			);
		
		$this->mPermissions['administrator'] = ($this->user_auth->officeType==='Admin');
		$this->mPermissions['officer'] = $this->mPermissions['administrator'] || ($this->user_auth->officeType==='High');
		
		// Derived permissions
		$this->mPermissions['view']          = $this->mPermissions['officer'];
		
		$this->mPermissions['page_new']         = $this->mPermissions['administrator'];
		$this->mPermissions['page_edit']        = $this->mPermissions['officer'];
		$this->mPermissions['page_rename']      = $this->mPermissions['administrator'];
		$this->mPermissions['page_delete']      = $this->mPermissions['administrator'];
		$this->mPermissions['page_prop_add']    = $this->mPermissions['administrator'];
		$this->mPermissions['page_prop_edit']   = $this->mPermissions['officer'];
		$this->mPermissions['page_prop_delete'] = $this->mPermissions['administrator'];
		
		$this->mPermissions['custom_new']         = $this->mPermissions['officer'];
		$this->mPermissions['custom_edit']        = $this->mPermissions['officer'];
		$this->mPermissions['custom_rename']      = $this->mPermissions['officer'];
		$this->mPermissions['custom_delete']      = $this->mPermissions['officer'];
		$this->mPermissions['custom_prop_add']    = $this->mPermissions['administrator'];
		$this->mPermissions['custom_prop_edit']   = $this->mPermissions['officer'];
		$this->mPermissions['custom_prop_delete'] = $this->mPermissions['administrator'];
		
		$this->mPermissions['common_add']    = $this->mPermissions['administrator'];
		$this->mPermissions['common_edit']   = $this->mPermissions['officer'];
		$this->mPermissions['common_delete'] = $this->mPermissions['administrator'];
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
			$this->messages->AddMessage('error',$Message);
			return FALSE;
		} else {
			return TRUE;
		}
	}
	
	/// Default page
	function index()
	{
		if (!CheckPermissions('editor')) return;
		
		$this->pages_model->SetPageCode('admin_pages');
		
		$this->_SetPermissions();
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
			
			$main_text = $this->pages_model->GetPropertyWikitext('main_text',FALSE, NULL);
			if (NULL !== $main_text) {
				$data['main_text'] = $main_text;
			}
			
			$data['permissions'] = $this->mPermissions;
			$this->main_frame->SetContentSimple('admin/pages_index.php', $data);
		}
		$this->main_frame->Load();
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
		$Data['permissions']['prop_add'] = FALSE;
		$Data['permissions']['rename']   = TRUE;
		
		$Data['show_details']   = TRUE;
		
		$Data['target'] = $Target;
		$Data['codename'] = '';
		$Data['head_title'] = '';
		$Data['body_title'] = '';
		$Data['title_separate'] = FALSE;
		$Data['description'] = '';
		$Data['keywords'] = '';
		$Data['properties'] = array();
		
		$input['codename']    = $this->input->post('codename',    FALSE);
		if (FALSE !== $input['codename']) {
			$input['head_title']  = $this->input->post('head_title',  FALSE);
			$input['title_separate']  = ($this->input->post('title_separate',  FALSE) !== FALSE);
			if (!$input['title_separate']) {
				$input['body_title'] = $this->input->post('head_title', FALSE);
			} else {
				$input['body_title'] = $this->input->post('body_title', FALSE);
			}
			$input['description'] = $this->input->post('description', FALSE);
			$input['keywords']    = $this->input->post('keywords',    FALSE);
			$save_failed = FALSE;
			
			// Validate and check permissions
			if ($this->_CheckViewPermissions('custom_new','You do not have permission to create a new page')) {
				// Check if new codename is in use
				$Data['codename'] = $input['codename'];
				if (FALSE !== $this->pages_model->PageCodeInUse($Prefix.$input['codename'])) {
					$this->messages->AddMessage('error','A page with the codename "'.$input['codename'].'" already exists. Please choose another.');
					$save_failed = TRUE;
				}
			} else {
				$save_failed = TRUE;
			}
			if (FALSE !== $input['head_title'])
				$Data['head_title'] = $input['head_title'];
			if (FALSE !== $input['body_title'])
				$Data['body_title'] = $input['body_title'];
			$Data['title_separate'] = $input['title_separate'];
			if (FALSE !== $input['description'])
				$Data['description'] = $input['description'];
			if (FALSE !== $input['keywords'])
				$Data['keywords'] = $input['keywords'];
				
			// If don't separate titles, ignore body title
			if (!$input['title_separate']) {
				$input['body_title'] = NULL;
			}
			
			if (FALSE === $save_failed) {
				// Try and save to db
				$input['codename'] = $Prefix.$input['codename'];
				if ($this->pages_model->CreatePage($input)) {
					$this->messages->AddMessage('success', 'The page was successfully saved');
					redirect($Redirect.$Data['codename']);
				} else {
					$this->messages->AddMessage('error', 'The page could not be saved as an internal error occurred');
					$save_failed = TRUE;
				}
			}
		}
		
		$main_text = $this->pages_model->GetPropertyWikitext('main_text',FALSE, NULL);
		if (NULL !== $main_text) {
			$Data['main_text'] = $main_text;
		}
		return $Data;
	}
	
	/// Setup the data array for the view for editing a page.
	/**
	 * @param $Data array Data used for setting up the page.
	 * @param $InputPageCode string/FALSE Page code from uri or FALSE for common.
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
		$global_scope = (FALSE === $InputPageCode);
		if (!$global_scope)
			$page_code = $Prefix.$InputPageCode;
		else
			$page_code = $InputPageCode;
		// Find the custom page code
		$page_info = $this->pages_model->GetSpecificPage($page_code, TRUE);
		if (FALSE === $page_info) {
			$this->messages->AddMessage('error', 'The page named "'.$page_code.'" doesn\'t exist');
			redirect('admin/pages');
		} else {
			$data['show_details'] = !$global_scope;
			if (!$global_scope) {
				$data['target']      = $Target.$InputPageCode;
				$data['codename']    = $InputPageCode;
				$data['head_title']  = $page_info['head_title'];
				$data['title_separate'] = (NULL !== $page_info['body_title']);
				if (!$data['title_separate']) {
					$data['body_title'] = $page_info['head_title'];
				} else {
					$data['body_title'] = $page_info['body_title'];
				}
				$data['description'] = $page_info['description'];
				$data['keywords']    = $page_info['keywords'];
			} else {
				$data['target']      = $Target;
			}
			
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
			
			if (!$global_scope) {
				$input['codename']    = $this->input->post('codename',    FALSE);
				if (FALSE !== $input['codename']) {
					$input['head_title']  = $this->input->post('head_title',  FALSE);
					$input['body_title']  = $this->input->post('body_title',  FALSE);
					$input['title_separate']  = ($this->input->post('title_separate',  FALSE) !== FALSE);
					if (!$input['title_separate']) {
						$input['body_title'] = $this->input->post('head_title', FALSE);
					} else {
						$input['body_title'] = $this->input->post('body_title', FALSE);
					}
					$input['description'] = $this->input->post('description', FALSE);
					$input['keywords']    = $this->input->post('keywords',    FALSE);
					$save_failed = FALSE;
					
					// Validate and check permissions
					if ($input['codename'] != $data['codename']) {
						if ($this->_CheckViewPermissions('custom_rename','You do not have permission to rename custom pages')) {
							// Check if new codename is in use
							$data['codename'] = $input['codename'];
							if (FALSE !== $this->pages_model->PageCodeInUse($Prefix.$input['codename'])) {
								$this->messages->AddMessage('error','A page with the codename "'.$input['codename'].'" already exists. Please choose another.');
								$save_failed = TRUE;
							}
						} else {
							$save_failed = TRUE;
						}
					}
					if (FALSE !== $input['head_title'])
						$data['head_title'] = $input['head_title'];
					if (FALSE !== $input['body_title'])
						$data['body_title'] = $input['body_title'];
					$data['title_separate'] = $input['title_separate'];
					if (FALSE !== $input['description'])
						$data['description'] = $input['description'];
					if (FALSE !== $input['keywords'])
						$data['keywords'] = $input['keywords'];
				
					// If don't separate titles, ignore body title
					if (!$input['title_separate']) {
						$input['body_title'] = NULL;
					}
					
					if (FALSE === $save_failed) {
						// Try and save to db
						$input['codename'] = $Prefix.$input['codename'];
						if ($this->pages_model->SaveSpecificPage($page_code, $input)) {
							$this->messages->AddMessage('success', 'The page was successfully saved');
							if ($data['codename'] != $page_code) {
								redirect($Redirect.$data['codename']);
							}
						} else {
							$this->messages->AddMessage('error','The page could not be saved as an internal error occurred');
						}
					}
				}
			}
			if ($Data['permissions']['prop_edit']) {
				if ($this->input->post('save_properties', FALSE) !== FALSE) {
					$changes = 0;
					$input = array();
					$input['properties'] = array();
					$input['property_add'] = array();
					$input['property_remove'] = array('wikitext_cache' => array());
					$ignored_new_props = 0;
					foreach ($_POST as $key => $value) {
						if (preg_match('/^prop(\d+)$/',$key,$matches)) {
							$property_id = (int)$matches[1];
							if (array_key_exists($property_id, $data['properties'])) {
								if (array_key_exists('delete-'.$key, $_POST)) {
									// Property needs deleting
									$input['property_remove'][$data['properties'][$property_id]['type']][] = $data['properties'][$property_id]['label'];
									if ($data['properties'][$property_id]['type'] === 'wikitext') {
										$input['property_remove']['wikitext_cache'][] = $data['properties'][$property_id]['label'];
										++$changes;
									}
									++$changes;
								} elseif ($data['properties'][$property_id]['text'] != $value) {
									// property has been changed
									$input['properties'][] = array(
											'id' => $property_id,
											'text' => $value,
										);
									++$changes;
									$data['properties'][$property_id]['text'] = $value;
									if ($data['properties'][$property_id]['type'] === 'wikitext') {
										$input['property_remove']['wikitext_cache'][] = $data['properties'][$property_id]['label'];
										++$changes;
									}
								}
							}
						}
						if (preg_match('/^newprop(\-?\d+)$/',$key,$matches)) {
							$label_key = 'label-'.$key;
							$type_key = 'type-' .$key;
							if (	array_key_exists($label_key, $_POST) &&
									array_key_exists($type_key, $_POST)) {
								$label = $_POST[$label_key];
								$type  = $_POST[$type_key];
								if (empty($label) && empty($value)) {
									++$ignored_new_props;
								} else {
									// New property
									$input['property_add'][] = array(
											'label'	=> $label,
											'type'	=> $type,
											'text'	=> $value,
										);
									++$changes;
								}
							}
						}
					}
					
					if ($ignored_new_props == 1) {
						$this->messages->AddMessage('information',$ignored_new_props.' new property was ignored as it was blank');
					} elseif ($ignored_new_props > 1) {
						$this->messages->AddMessage('information',$ignored_new_props.' new properties were ignored as they were blank');
					}
					
					if ($changes > 0) {
						// Try and save to db
						if ($this->pages_model->SaveSpecificPage($page_code, $input)) {
							$this->messages->AddMessage('success', 'Properties were successfully saved.');
						} else {
							$this->messages->AddMessage('error','Properties weren\'t save. An internal error occurred.');
						}
					} else {
						$this->messages->AddMessage('information', 'No properties have changed');
					}
					
					$_POST = array();
					return $this->_EditPage($Data, $InputPageCode, $Target, $Redirect, $DefaultProperties, $Prefix);
				}
			}
		}
		$main_text = $this->pages_model->GetPropertyWikitext('main_text',FALSE, NULL);
		if (NULL !== $main_text) {
			$data['main_text'] = $main_text;
		}
		$page_help = $this->pages_model->GetPropertyWikitext('special:help',$page_code, NULL);
		if (NULL !== $page_help) {
			$data['page_help'] = $page_help;
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
			// Get information about the page so user is informed before confirming.
			$information = $this->pages_model->GetSpecificPage($Prefix.$InputPageCode, TRUE);
			if (FALSE === $information) {
				$this->messages->AddMessage('error','Page \'' . $InputPageCode . '\' not found');
				$data['confirm'] = FALSE;
			} else {
				$data['confirm'] = TRUE;
			}
			$data['information'] = $information;
			
		} else {
			$data['confirm'] = FALSE;
			// user confirmed, delete the page and its properties.
			$result = $this->pages_model->DeletePage($Prefix.$InputPageCode);
			if ($result) {
				// Success
				$this->messages->AddMessage('success','The page was successfully deleted.');
				redirect('admin/pages');
			} else {
				// Failure
				$this->messages->AddMessage('error','The page could not be deleted.');
				redirect('admin/pages');
			}
		}
		$main_text = $this->pages_model->GetPropertyWikitext('main_text',FALSE, NULL);
		if (NULL !== $main_text) {
			$data['main_text'] = $main_text;
		}
		return $data;
	}
	
	/// Function for administrating common properties (global scope)
	/**
	 */
	function common()
	{
		if (!CheckPermissions('editor')) return;
		
		$this->_SetPermissions();
		// Tweak the permissions
		$this->mPermissions['new']    = FALSE;
		$this->mPermissions['edit']   = FALSE;
		$this->mPermissions['rename'] = FALSE;
		$this->mPermissions['delete'] = FALSE;
		$this->mPermissions['prop_add'] = $this->mPermissions['common_add'];
		$this->mPermissions['prop_edit'] = $this->mPermissions['common_edit'];
		$this->mPermissions['prop_delete'] = $this->mPermissions['common_delete'];
		
		$this->main_frame->SetExtraHead('<script src="/javascript/clone.js" type="text/javascript"></script>');
		if ($this->_CheckViewPermissions()) {
			$data = array();
			$data['permissions'] = $this->mPermissions;
			
			$this->pages_model->SetPageCode('admin_pages_common');
			$this_uri = '/admin/pages/common';
			$data = $this->_EditPage($data, FALSE, $this_uri, $this_uri);
			$this->main_frame->SetContentSimple('admin/pages_page.php', $data);
		}
		$this->main_frame->Load();
		
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
		if (!CheckPermissions('editor')) return;
		
		$this->_SetPermissions();
		// Tweak the permissions
		$this->mPermissions['new']    = $this->mPermissions['page_new'];
		$this->mPermissions['edit']   = $this->mPermissions['page_edit'];
		$this->mPermissions['rename'] = $this->mPermissions['page_rename'];
		$this->mPermissions['delete'] = $this->mPermissions['page_delete'];
		$this->mPermissions['prop_add'] = $this->mPermissions['page_prop_add'];
		$this->mPermissions['prop_edit'] = $this->mPermissions['page_prop_edit'];
		$this->mPermissions['prop_delete'] = $this->mPermissions['page_prop_delete'];
		
		// Get url segments after the first (controller).
		$num_segments = $this->uri->total_segments();
		$segments = array();
		for ($counter = 5; $counter <= $num_segments; ++$counter) {
			$segments[] = $this->uri->segment($counter);
		}
		$PageCode = implode('/',$segments);
		// We now have the page code so we can continue.
		
		$this->main_frame->SetExtraHead('<script src="/javascript/clone.js" type="text/javascript"></script>');
		if ($this->_CheckViewPermissions()) {
			$data = array();
			$data['permissions'] = $this->mPermissions;
			switch ($Operation) {
				case 'new':
					$this->pages_model->SetPageCode('admin_pages_page_new');
					if ($this->_CheckViewPermissions('new','You don\'t have permission to create a new page')) {
						$data = $this->_NewPage($data,
										'/admin/pages/page/new',
										'/admin/pages/page/edit/');
						$this->main_frame->SetContentSimple('admin/pages_page.php', $data);
					}
					break;
					
				case 'edit':
					$this->pages_model->SetPageCode('admin_pages_page_edit');
					$this_uri = '/admin/pages/page/edit/';
					$data = $this->_EditPage($data, $PageCode, $this_uri, $this_uri);
					$this->main_frame->SetContentSimple('admin/pages_page.php', $data);
					break;
					
				case 'delete':
					$this->pages_model->SetPageCode('admin_pages_page_delete');
					if ($this->_CheckViewPermissions('delete','You don\'t have permission to delete this page')) {
						$this_uri = '/admin/pages/page/delete/';
						$data = $this->_DeletePage($data, $PageCode, $this_uri);
						$this->main_frame->SetContentSimple('admin/pages_delete.php', $data);
					}
					break;
					
				default:
					$this->messages->AddMessage('error', 'Unknown operation "'.$Operation.'"');
					redirect('admin/pages');
					return;
			}
			$this->main_frame->SetTitleParameters( array(
					'codename' => $PageCode,
				) );
		}
		$this->main_frame->Load();
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
		if (!CheckPermissions('editor')) return;
		
		$this->_SetPermissions();
		// Tweak the permissions
		$this->mPermissions['new']    = $this->mPermissions['custom_new'];
		$this->mPermissions['edit']   = $this->mPermissions['custom_edit'];
		$this->mPermissions['rename'] = $this->mPermissions['custom_rename'];
		$this->mPermissions['delete'] = $this->mPermissions['custom_delete'];
		$this->mPermissions['prop_add'] = $this->mPermissions['custom_prop_add'];
		$this->mPermissions['prop_edit'] = $this->mPermissions['custom_prop_edit'];
		$this->mPermissions['prop_delete'] = $this->mPermissions['custom_prop_delete'];
		
		// Get url segments after the first (controller).
		$num_segments = $this->uri->total_segments();
		$segments = array();
		for ($counter = 5; $counter <= $num_segments; ++$counter) {
			$segments[] = $this->uri->segment($counter);
		}
		$CustomPageCode = implode('/',$segments);
		// We now have the page code so we can continue.
		
		$this->main_frame->SetExtraHead('<script src="/javascript/clone.js" type="text/javascript"></script>');
		if ($this->_CheckViewPermissions()) {
			$data = array();
			$data['permissions'] = $this->mPermissions;
			switch ($Operation) {
				case 'new':
					$this->pages_model->SetPageCode('admin_pages_custom_new');
					if ($this->_CheckViewPermissions('new','You don\'t have permission to create a new custom page')) {
						$data = $this->_NewPage($data,
										'/admin/pages/custom/new',
										'/admin/pages/custom/edit/',
										'custom:');
						$this->main_frame->SetContentSimple('admin/pages_page.php', $data);
					}
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
					$this->main_frame->SetContentSimple('admin/pages_page.php', $data);
					break;
					
				case 'delete':
					$this->pages_model->SetPageCode('admin_pages_custom_delete');
					if ($this->_CheckViewPermissions('delete','You don\'t have permission to delete this custom page')) {
						$this_uri = '/admin/pages/custom/delete/';
						$data = $this->_DeletePage($data, $CustomPageCode, $this_uri, 'custom:');
						$this->main_frame->SetContentSimple('admin/pages_delete.php', $data);
					}
					break;
					
				default:
					$this->messages->AddMessage('error', 'Unknown operation "'.$Operation.'"');
					redirect('admin/pages');
					return;
			}
			$this->main_frame->SetTitleParameters( array(
					'codename' => $CustomPageCode,
				) );
		}
		$this->main_frame->Load();
	}
	
}

?>