<?php
/**
 *	This provides the wizard for setting up a user's
 *	preferences when they first login to the site.
 *
 *	@author Chris Travis (cdt502 - ctravis@gmail.com)
 */
class Register extends Controller {

	/**
	 * @brief Default Constructor.
	 */
	function __construct()
	{
		parent::Controller();
		// Load data model
		$this->load->model('prefs_model','model');
		// Load the public frame
		$this->load->library('frame_public');
	}

	function index()
	{
		$data['test'] = "test";

		// Set up the public frame
		$this->frame_public->SetTitle('Preferences');
		$this->frame_public->SetContentSimple('account/wizard', $data);
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}

	function general()
	{
		// Retrieve data required for view
		$data['colleges'] = $this->model->GetColleges();
		$data['years'] = $this->model->GetYears();

		// Perform validation checks on submitted data
		$this->load->library('validation');

		$rules['name'] = 'trim|required';
		$rules['email'] = 'trim|required|valid_email';
		$rules['nick'] = 'trim|required';
		$rules['college'] = 'trim|required';
		$rules['year'] = 'trim|required';
		$rules['time'] = 'trim|required';

		$this->validation->set_error_delimiters('<div class=\'error\'>', '</div>');
		$this->validation->set_rules($rules);

		$fields['name'] = 'name';
		$fields['email'] = 'e-mail address';
		$fields['nick'] = 'nickname';
		$fields['college'] = 'college';
		$fields['Year'] = 'year of study';
		$fields['Time'] = 'time format';

		$this->validation->set_fields($fields);

		if ($this->validation->run()) {
			// If data validates then move onto next section of wizard
			redirect('/register/academic/', 'location');
		} else {
			if (!$this->validation->name) { $this->validation->name = 'Name from LDAP'; }
			if (!$this->validation->email) { $this->validation->email = 'Username from LDAP/Login @york.ac.uk'; }
			if (!$this->validation->nick) { $this->validation->nick = 'First word retrieved from name from LDAP'; }

			// Set up the public frame
			$this->frame_public->SetTitle('Preferences : General');
			$this->frame_public->SetContentSimple('account/preferences', $data);
			// Load the public frame view (which will load the content view)
			$this->frame_public->Load();
		}
	}

	function academic ()
	{
		$this->load->library('xajax');

		function getModules ($module_id)
		{
			///TODO: Protection on $module_id for SQL injection
			$xajax_response = new xajaxResponse();
			$modules = '<div class=\'table-box\'>
					<table>
					<thead>
						<tr>
						<th>&nbsp;</th>
						<th>Module Title</th>
						</tr>
					</thead>
					<tbody>';

			$module_style = FALSE;
			$get_modules = mysql_query('SELECT organisation_entity_id AS module_id, organisation_name AS module_name FROM organisations WHERE organisation_organisation_type_id = 8 AND organisation_parent_organisation_entity_id = ' . $module_id . ' ORDER BY module_name ASC');
			if (mysql_num_rows($get_modules) == 0) {
				$modules .= '<tr>
					<td align=\'center\' colspan=\'2\'>No Modules Found</td>
					</tr>';
			} else {
				while ($module = mysql_fetch_array($get_modules)) {
					$modules .= '<tr';
					if ($module_style) {
						$modules .= ' class=\'tr2\'';
					}
					$module_style = $module_style ? FALSE : TRUE;
					$modules .= '>
						<td><input type=\'checkbox\' name=\'\' id=\'\' onClick=\'update_subscriptions(' . $module['module_id'] . ');\' /></td>
						<td>' . $module['module_name'] . '</td>
						</tr>';
				}
			}
			$modules .= '</tbody></table></div>';
			$xajax_response->addAssign('module_list','innerHTML', $modules);
			return $xajax_response;
		}

		function updateModules ($module_id)
		{
			///TODO: Use database rather than static data
			///TODO: SQL injection protection on $module_id
			$xajax_response = new xajaxResponse();
			$modules = '<div class=\'table-box\'>
					<table>
					<thead>
						<tr>
						<th>&nbsp;</th>
						<th>Course</th>
						<th>Module Title</th>
						<th>&nbsp;</th>
						</tr>
					</thead>
					<tbody>';

			$module_style = FALSE;
			$get_module = mysql_query('SELECT organisation_name AS module_name, organisation_parent_organisation_entity_id AS dept_id FROM organisations WHERE organisation_organisation_type_id = 8 AND organisation_entity_id = ' . $module_id);
			if (mysql_num_rows($get_module) == 0) {
				$modules .= '<tr>
					<td align=\'center\' colspan=\'4\'>No Modules Found</td>
					</tr>';
			} else {
				$module = mysql_fetch_array($get_module);
				$dept = mysql_fetch_array(mysql_query('SELECT organisation_name AS dept_name FROM organisations WHERE organisation_organisation_type_id = 7 AND organisation_entity_id = ' . $module['dept_id']));
				$modules .= '<tr';
				if ($module_style) {
					$modules .= ' class=\'tr2\'';
				}
				$module_style = $module_style ? FALSE : TRUE;
				$modules .= '>
					<td>&nbsp;</td>
					<td>' . $dept['dept_name'] . '</td>
					<td>' . $module['module_name'] . '</td>
					<td><a href=\'\'>[X]</a></td>
					</tr>';
			}
			$modules .= '<tr';
			if ($module_style) {
				$modules .= ' class=\'tr2\'';
			}
			$module_style = $module_style ? FALSE : TRUE;
			$modules .= '>
				<td>&nbsp;</td>
				<td>CompSci</td>
				<td>Principles of Programming</td>
				<td><a href=\'\'>[X]</a></td>
				</tr>';
			$modules .= '</tbody></table></div>';
			$xajax_response->addAssign('current_modules','innerHTML', $modules);
			return $xajax_response;
		}

		$this->xajax->registerFunction('getModules');
		$this->xajax->registerFunction('updateModules');
		$this->xajax->processRequests();

		$data['courses'] = $this->model->GetDepartments();

		// Set up the public frame
		$this->frame_public->SetTitle('Preferences : Academic Modules');
		$this->frame_public->SetExtraHead($this->xajax->getJavascript(null, '/javascript/xajax.js'));
		$this->frame_public->SetContentSimple('account/academic', $data);
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}

	function societies ()
	{
		$this->load->library('xajax');

		function getInfo ($soc_id)
		{
			///TODO: Same as above really, SQL injection protection etc.
			$xajax_response = new xajaxResponse();
			$dbquery = mysql_query('SELECT organisation_description AS description FROM organisations WHERE organisation_organisation_type_id = 2 AND organisation_entity_id = ' . $soc_id . ' ORDER BY organisation_name ASC');
			$dbres = mysql_fetch_array($dbquery);
			$info = $dbres['description'];
			$xajax_response->addAssign('socdesc','innerHTML', $info);

			$get_slideshow = mysql_query('SELECT photos.photo_title, photos.photo_id FROM photos, organisation_slideshows AS slideshow WHERE slideshow.organisation_slideshow_organisation_entity_id = ' . $soc_id . ' AND slideshow.organisation_slideshow_photo_id = photos.photo_id ORDER BY slideshow.organisation_slideshow_order ASC');
			$xajax_response->addScriptCall('ss_reset');
			while ($dbres = mysql_fetch_array($get_slideshow)) {
				$xajax_response->addScriptCall('ss_add', '/images/photos/' . $dbres['photo_id'] . '.jpg');
			}
			$xajax_response->addScriptCall('ss_load');
			return $xajax_response;
		}

		$this->xajax->registerFunction('getInfo');
		$this->xajax->processRequests();

		$data['societies'] = array();
		$dbquery = mysql_query("SELECT organisation_entity_id AS id, organisation_name AS name FROM organisations WHERE organisation_organisation_type_id = 2 ORDER BY name");
		while ($dbres = mysql_fetch_array($dbquery)) {
			array_push($data['societies'], $dbres);
		}

		// Set up the public frame
		$this->frame_public->SetTitle('Preferences : Societies');
		$this->frame_public->SetExtraHead($this->xajax->getJavascript(null, '/javascript/xajax.js'));
		$this->frame_public->SetContentSimple('account/societies', $data);
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}

}
?>
