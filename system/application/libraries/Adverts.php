<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 *	@file	Byline.php
 *	@author	Richard Ingle (ri504)
 *	@brief	Library for getting the latest advert to display
 */

class Adverts extends FramesView
{
	/// array of advert data
	protected $mAdvert;
	
	/// Primary constructor.
	function __construct()
	{
	
	}

	/**
	 * @brief returns the image if the next advert to be shown
	 */
	function SelectNextAdvert()
	{
		$CI = &get_instance();
		
		//load the advert model
		$CI->load->model('advert_model');
		
		//get the least recently used advert
		$mAdvert = $CI->advert_model->SelectLatestAdvert();
		
		//return the advert
		return $mAdvert;
	}
}
?>