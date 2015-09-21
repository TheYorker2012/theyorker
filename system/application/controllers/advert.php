<?php

/**
 * @file controllers/advert.php
 * @brief Used to handle adverts
 * @author Richard Simpson (rs581@cs.york.ac.uk)
 */

/// Comments public controller.
class Advert extends Controller
{
	// Get the advert to display and pass it on to the FlexiBox
	/**
	 * @param none
	 * 
	 * First module made on the site by RS. Designed to get the next advert and
	 *	then pass onto the FlexiBox
	 *
	 */
	function GetNextAdvert()
	{
		//load in the advert library
		$this->load->library('Adverts');
		
		$new_advert = $this->adverts->SelectNextAdvert();

		return $new_advert;
	}
	
	
	function Advert()
	{
		
		
		/*
		 	advert_id as id,
			advert_image_id as image_id,
			advert_image_alt as alt,
			advert_image_url as url,
			advert_views_current as current_views,
			advert_live as live
		 */
		
		
		$data = $this->GetNextAdvert();
		
		while($data['live'] != '1')
		{
			$data = $this->GetNextAdvert();
		}
		
		$this->load->view('flexibox/adsense_third', $data);
	}
}

?>
