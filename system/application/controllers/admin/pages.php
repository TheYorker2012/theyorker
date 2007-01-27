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
	 * @return bool Whether the user has permission.
	 */
	function _CheckViewPermissions($permission = 'view')
	{	
		if (!$this->mPermissions[$permission]) {
			$this->frame_public->AddMessage(
					new PermissionDeniedMsg('You do not have permission to access this page')
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
			
			$data['pages'] = array();
			$data['custom'] = array();
			
			foreach ($all_pages as $key => $page) {
				if ('custom:' == substr($page['page_codename'],0,7)) {
					$page['codename'] = substr($page['page_codename'],7);
					$data['custom'][] = $page;
				} else {
					$page['codename'] = $page['page_codename'];
					$data['pages'][] = $page;
				}
			}
			
			$data['permissions'] = $this->mPermissions;
			$this->frame_public->SetContentSimple('admin/pages_index.php', $data);
		}
		$this->frame_public->Load();
	}
	
	function page($Operation, $PageCode='')
	{
		$this->pages_model->SetPageCode('admin_pages_page');
		if ($this->_CheckViewPermissions('page_edit')) {
			$data = array();
			$data['permissions'] = $this->mPermissions;
			switch ($Operation) {
				case 'new':
					$data['target'] = '/admin/pages/page/'.$Operation;
					$data['codename'] = '';
					$data['title'] = '';
					$data['description'] = '';
					$data['keywords'] = '';
					$data['comments'] = 0;
					$data['ratings'] = 0;
					$data['properties'] = array();
					break;
				case 'edit':
					// Find the custom page code
					$page_info = $this->pages_model->GetSpecificPage($PageCode, TRUE);
					if (FALSE === $page_info) {
						show_404($Operation.'/'.$PageCode);
					} else {
						$data['target'] = '/admin/pages/page/'.$Operation.'/'.$PageCode;
						$data['codename'] = $page_info['codename'];
						$data['title'] = $page_info['title'];
						$data['description'] = $page_info['description'];
						$data['keywords'] = $page_info['keywords'];
						$data['comments'] = $page_info['comments'];
						$data['ratings'] = $page_info['ratings'];
						$data['properties'] = array();
						foreach ($page_info['properties'] as $property) {
							$data['properties'][] = array(
									'id'    => 'prop'.$property['id'],
									'label' => $property['label'],
									'text'  => $property['text'],
									'type'  => $property['type'],
								);
						}
					}
					break;
				default:
					show_404($Operation);
					return;
			}
			$this->frame_public->SetTitleParameters( array(
					'codename' => $PageCode,
				) );
			
			$this->frame_public->SetContentSimple('admin/pages_page.php', $data);
		}
		$this->frame_public->Load();
	}
	
	function custom($Operation, $CustomPageCode='')
	{
		$this->pages_model->SetPageCode('admin_pages_custom');
		if ($this->_CheckViewPermissions('custom_edit')) {
			$data = array();
			$data['permissions'] = $this->mPermissions;
			switch ($Operation) {
				case 'new':
					$data['target'] = '/admin/pages/custom/'.$Operation;
					$data['codename'] = '';
					$data['title'] = '';
					$data['description'] = '';
					$data['keywords'] = '';
					$data['comments'] = 0;
					$data['ratings'] = 0;
					$data['properties'] = array(
							array(
									'id'    => 'propnew',
									'label' => 'main',
									'text'  => '',
									'type'  => 'wikitext',
								),
						);
					break;
				case 'edit':
					$PageCode = 'custom:'.$CustomPageCode;
					// Find the custom page code
					$page_info = $this->pages_model->GetSpecificPage($PageCode, TRUE);
					if (FALSE === $page_info) {
						show_404($Operation.'/'.$PageCode);
					} else {
						$data['target'] = '/admin/pages/custom/'.$Operation.'/'.$CustomPageCode;
						$data['codename'] = $CustomPageCode;
						$data['title'] = $page_info['title'];
						$data['description'] = $page_info['description'];
						$data['keywords'] = $page_info['keywords'];
						$data['comments'] = $page_info['comments'];
						$data['ratings'] = $page_info['ratings'];
						$main_property = FALSE;
						foreach ($page_info['properties'] as $property) {
							if ($property['label'] === 'main' &&
									$property['type'] === 'wikitext') {
								$main_property = $property;
							}
						}
						$data['properties'] = array();
						if (FALSE === $main_property) {
							$data['properties'][] = array(
									'id'    => 'propnew',
									'label' => 'main',
									'text'  => '',
									'type'  => 'wikitext',
								);
						} else {
							$data['properties'][] = $main_property;
						}
					}
					break;
				default:
					show_404($Operation);
					return;
			}
			$this->frame_public->SetTitleParameters( array(
					'codename' => $CustomPageCode,
				) );
			
			$this->frame_public->SetContentSimple('admin/pages_page.php', $data);
		}
		$this->frame_public->Load();
	}
}

?>