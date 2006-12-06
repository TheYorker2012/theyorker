<?php

/**
 * @brief Yorker directory.
 * @author Owen Jones (oj502@york.ac.uk)
 * @author James Hogan (jh559@cs.york.ac.uk)
 * 
 * The URI /directory maps to this controller (see config/routes.php).
 *
 * Any 2nd URI segment is sent to Yorkerdirectory::view (see config/routes.php).
 */
class Yorkerdirectory extends Controller {
	
	/**
	 * @brief Directory index page.
	 */
	function index()
	{
		$data = array(
			'content_view' => 'directory/directory',
			'organisations' => array(
				array(
					'shortname'   => 'fragsoc',
					'name'        => 'FragSoc',
					'description' => 'A computer gaming society',
					'type'        => 'Society',
				),
				array(
					'shortname'   => 'theyorker',
					'name'        => 'The Yorker',
					'description' => 'The people who run this website',
					'type'        => 'Organisation',
				),
				array(
					'shortname'   => 'toffs',
					'name'        => 'Toffs',
					'description' => 'A nightclub in york',
					'type'        => 'Venue',
				),
				array(
					'shortname'   => 'poledancing',
					'name'        => 'Pole Dancing',
					'description' => 'A fitness club',
					'type'        => 'Athletics Union',
				),
				array(
					'shortname'   => 'cookiesoc',
					'name'        => 'Cookie Soc',
					'description' => 'Eat cookies',
					'type'        => 'Society',
				),
				array(
					'shortname'   => 'costcutter',
					'name'        => 'Costcutter',
					'description' => 'Campus shop',
					'type'        => 'College & Campus',
				),
			),
		);
		$this->load->view('frames/student_frame',$data);
	}
	
	/**
	 * @brief Directory organisation page.
	 */
	function view($organisation,$subpage='index')
	{
		if($subpage=='events'||$subpage=='reviews'||$subpage=='members')
		{
			$subpageview='directory/directory_view_'.$subpage;
		}
		else
		{
			$subpageview='directory/directory_view';
		}
		$data = array(
			'content_view' => $subpageview,
			'organisation' => array(
				'shortname'   => 'theyorker',
				'name'        => 'The Yorker',
				'description' => 'The people who run this website',
				'type'        => 'Organisation',
				'cards'       => array(
					array(
						'name' => 'Daniel Ashby',
						'title' => 'Editor',
						'course' => 'Politics and Philosophy',
						'blurb' => 'The guy in charge',
						'email' => 'editor@theyorker.co.uk',
						'phone_mobile' => '07777 777777',
						'phone_internal' => '01904 444444',
						'phone_external' => '01904 555555',
						'postal_address' => '',
					),
					array(
						'name' => 'Nick Evans',
						'title' => 'Technical Director',
						'course' => 'Computer Science',
						'blurb' => 'The other guy',
						'email' => 'webmaster@theyorker.co.uk',
						'phone_internal' => '07788 888888',
						'phone_external' => '01904 333333',
						'postal_address' => '01904 666666',
					),
				),
			),
		);
		$this->load->view('frames/student_frame',$data);
	}
}
?>
