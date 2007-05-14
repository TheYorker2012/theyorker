<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 *	@file	Byline.php
 *	@author	Chris Travis (ctravis@gmail.com - cdt502)
 *	@brief	Library for rendering reporter bylines
 */

// Load the Frames library
$CI = &get_instance();
$CI->load->library('frames');

class Byline extends FramesView
{
	/// array Reporter's info
	protected $mReporters;
	/// string Article publication date
	protected $mDate;

	/// Primary constructor.
	function __construct()
	{
		/// Set view to use for layout & design of byline
		parent::__construct('general/byline');
		/// Set default values for variables
		$this->mReporters = array();
		$this->mDate = '';
	}

	/**
	 * @brief Adds a reporter to the byline
	 * @param $EntityId integer/array Entity ID's for reporters to be added
	 */
	function AddReporter($Reporters)
	{
		if (!is_array($Reporters)) {
			$Reporters = array($Reporters);
		}
		$CI = &get_instance();
		$CI->load->model('article_model');
		$CI->load->helper('images');
		foreach ($Reporters as $Entity) {
			$reporter = $CI->article_model->GetReporterByline($Entity['id']);
			if (count($reporter) > 0) {
				if ($reporter['photo'] == null) {
					$this->mReporters[$Entity['id']] = array(
						'name' => $reporter['name'],
						'photo' => 'images/prototype/directory/members/no_image.png'
					);
				} else {
					$this->mReporters[$Entity['id']] = array(
						'name' => $reporter['name'],
						'photo' => imageLocation($reporter['photo'], 'userimage')
					);
				}
			}
		}
	}

	/**
	 * @brief Sets the date to display in the byline
	 * @param $Date string Publication date of the article
	 */
	function SetDate($Date)
	{
		$this->mDate = $Date;
	}

	/**
	 * @brief Echo's out the byline
	 */
	function Load()
	{
		$this->SetData('reporters',$this->mReporters);
		$this->SetData('article_date',$this->mDate);
		parent::Load();
	}

	/**
	 * @brief Reset the authors and date to none
	 */
	function Reset()
	{
		$this->mReporters = array();
		$this->mDate = '';
	}

}

?>
