<?php
/**
 * This controller is used to display nested views.
 * It contains a number of functions that frame the chosen view with a particular
 *
 * \author Nick Evans
 */
class Prototype extends Controller {

	/**
	* This function displays the specified view inside the student frame
	* \param selected_directory Is the directory where the specifed view is stored
	* \param selected_view Is the name of the specified view
	* \param selected_subdirectory Is the directory where the specifed subview is stored
	* \param selected_subview The subview is the name of a view which is passed to the specified selected_view
	*/
	function sframe($selected_directory = 'general',$selected_view = 'home',$selected_subdirectory = '',$selected_subview = '')
	{
		$data = array(
			'content_view' => $selected_directory . '/' . $selected_view,
			'subcontent_view' => ($selected_subview == '' ? '' : $selected_directory . '/' . $selected_subdirectory . '/' . $selected_subview),
		);
		$this->load->view('frames/student_frame',$data);
	}

	/**
	* This function displays the specified view inside the specified frame
	* \param selected_frame Is the frame to be displayed
	* \param selected_directory Is the directory where the specifed view is stored
	* \param selected_view Is the name of the specified view
	* \param selected_subdirectory Is the directory where the specifed subview is stored
	* \param selected_subview The subview is the name of a view which is passed to the specified selected_view
	*/
	function frame($selected_frame = 'student_frame',  $selected_directory = 'general', $selected_view = 'home',$selected_subdirectory = '',$selected_subview = '')
	{
		$data = array(
			'content_view' => $selected_directory . '/' . $selected_view,
			'subcontent_view' => ($selected_subview == '' ? '' : $selected_directory . '/' . $selected_subdirectory . '/' . $selected_subview),
		);
		$this->load->view('frames/'.$selected_frame,$data);
	}

	/**
	* This function displays the prototype homepage in the prototype student frame, if no function is specified
	*/
	function index()
	{
		$data = array(
			'content_view' => 'home',
			'subcontent_view' => ''
		);
		$this->load->view('frames/student_frame',$data);
	}
}
?>