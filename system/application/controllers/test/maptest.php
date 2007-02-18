<?php
class Maptest extends Controller
{
	/// Default constructor.
	function __construct()
	{
		parent::__construct();
		$this->load->library('maps');
	}

	function index()
	{
		if (!CheckPermissions('public')) 
			return;

		$map = &$this->maps->CreateMap('Test Map', 'map');
		$map->AddLocation('Computer Science', 53.9495, -1.053);
		$map->AddLocation('Random Point', 54, -1.053);
		$map->WantLocation('Some new point');
		$map->WantLocation('Some other new point');
		$this->maps->SendMapData();

		$data = array();
		$this->main_frame->SetContentSimple('test/map', $data);

		// Load the public frame view
		$this->main_frame->Load();
	}

}
?>
