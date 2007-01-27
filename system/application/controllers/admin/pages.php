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
	
	function page($PageCode)
	{
		$this->pages_model->SetPageCode('admin_pages_page');
		if ($this->_CheckViewPermissions('page_edit')) {
			$this->frame_public->SetTitleParameters( array(
					'codename' => $PageCode,
				) );
			
			$data = array();
			$data['permissions'] = $this->mPermissions;
			$this->frame_public->SetContentSimple('admin/pages_page_edit.php', $data);
		}
		$this->frame_public->Load();
		
	}
	
	function custom($CustomPageCode, $Operation='')
	{
		switch ($Operation) {
			case '':
				break;
			case 'save':
				$inpage = $this->input->post('main');
				var_dump($inpage);
				break;
			case 'preview':
				break;
			default:
				show_404($Operation);
		}
		$this->pages_model->SetPageCode('admin_pages_custom');
		if ($this->_CheckViewPermissions('custom_edit')) {
			$this->frame_public->SetTitleParameters( array(
					'codename' => $CustomPageCode,
				) );
			
			$data = array();
			$data['page'] = array(
					'codename'    => $CustomPageCode,
					'title'       => 'title',
					'description' => 'description',
					'keywords'    => 'description',
					'comments'    => '0',
					'ratings'     => '0',
					'main'        => 'main',
				);
			$data['permissions'] = $this->mPermissions;
			$this->frame_public->SetContentSimple('admin/pages_custom_edit.php', $data);
		}
		$this->frame_public->Load();
	}
	
	function newpage()
	{
		//echo 'create a new page';
		$this->pages_model->SetPageCode('admin_pages_new_page');
		if ($this->_CheckViewPermissions('page_new')) {
			
			$data = array();
			$data['permissions'] = $this->mPermissions;
			$this->frame_public->SetContentSimple('admin/pages_page_new.php', $data);
		}
		$this->frame_public->Load();
	}
	
	function newcustom()
	{
		//echo 'create a new custom page';
		$this->pages_model->SetPageCode('admin_pages_new_custom');
		if ($this->_CheckViewPermissions('custom_new')) {
			
			$data = array();
			$data['permissions'] = $this->mPermissions;
			$this->frame_public->SetContentSimple('admin/pages_custom_new.php', $data);
		}
		$this->frame_public->Load();
	}
	
	function deletepage($PageCode)
	{
		//echo 'are you sure you want to permanently delete the page '.$PageCode;
		$this->pages_model->SetPageCode('admin_pages_delete_page');
		if ($this->_CheckViewPermissions('page_delete')) {
			$this->frame_public->SetTitleParameters( array(
					'codename' => $PageCode,
				) );
			
			$data = array();
			$data['permissions'] = $this->mPermissions;
			$this->frame_public->SetContentSimple('admin/pages_delete.php', $data);
		}
		$this->frame_public->Load();
	}
	
	function deletecustom($CustomPageCode)
	{
		//echo 'are you sure you want to permanently delete the custom page '.$CustomPageCode;
		$this->pages_model->SetPageCode('admin_pages_delete_custom');
		if ($this->_CheckViewPermissions('custom_delete')) {
			$this->frame_public->SetTitleParameters( array(
					'codename' => $CustomPageCode,
				) );
			
			$data = array();
			$data['permissions'] = $this->mPermissions;
			$this->frame_public->SetContentSimple('admin/pages_delete.php', $data);
		}
		$this->frame_public->Load();
	}
}

?>