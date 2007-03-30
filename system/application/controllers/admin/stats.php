<?php
/// Statistics controller.
/**
 * @author Mark Goodall (mark.goodall@gmail.com)
 * 
 * Records and displays statistics collected on the site such as hits and errors
 */
class Stats extends Controller
{

	function __construct()
	{
		parent::Controller();
	}

	/// Main page
	function index() {
		//images
		//photos
		//users
		//organisations
		//comments
		//articles
		//errors
		//hits
		//browserstats
		echo "Under Consideration";
	}
	
	function clickheat() {
		$this->load->library('xajax');
		$this->xajax->registerFunction(array("process_click", &$this, "process_click"));
		$this->xajax->processRequests();
		
		if (!CheckPermissions('admin')) return;
		
		if ($this->uri->segment(3) == 'show') {
			$this->main_frame->SetTitle('ClickHeat');
			$this->main_frame->SetContentSimple('stats/clickheat_show');
			$this->main_frame->Load();
		} else {
			$this->main_frame->SetTitle('ClickHeat');
			$this->main_frame->SetContentSimple('stats/clickheat');
			$this->main_frame->Load();
		}
		
	}
	
	function process_click($clickData) {
		$this->load->model('clickheat');
		$this->clickheat->store($clickData);
		$clickData['p']; //page
		$clickData['x'];
		$clickData['y']; //y & x co-ords
		$clickData['w']; //widthofscreen
		$clickData['b']; //browser
		$clickData['random']; //time
	}
	
	function generateHeatmap(){
		if (!CheckPermissions('admin')) return;
		//create and ouput
	}
}
?>
