<?php
/**
 *	Displays the journalist style guide for writing articles
 *
 *	@author Chris Travis (cdt502 - ctravis@gmail.com)
 */

class Guide extends Controller
{

	/**
	 *	@brief	Default constructor
	 */
	function __construct()
	{
		parent::Controller();
		$this->load->library('image');
	}


	/**
	 *	@brief	Display style guide
	 */
	function index()
	{
		if (!CheckPermissions('office')) return;

		/// Style guide stored in page properties
		$this->pages_model->SetPageCode('office_style_guide');

		/// Get the blocks array from page properties
		$blocks = $this->pages_model->GetPropertyArray('blocks', array(
			/// First index is [int]
			array('pre' => '[', 'post' => ']', 'type' => 'int'),
			/// Second index is .string
			array('pre' => '.', 'type' => 'enum',
				'enum' => array(
					array('title',	'text'),
					array('blurb',	'wikitext'),
					array('image',	'text'),
				),
			),
		));
		if (FALSE === $blocks) {
			$blocks = array();
		}

		/// Create data array.
		$data = array();
		$data['textblocks'] = array();

		/// Process page properties
		foreach ($blocks as $key => $block) {
			$curdata = array();
			$curdata['title'] = $block['title'];
			$curdata['shorttitle'] = str_replace(' ','_',$block['title']);
			$curdata['blurb'] = $block['blurb'];
			if (array_key_exists('image', $block)) {
				$curdata['image'] = $this->image->getThumb($block['image'], 'medium');
			} else {
				$curdata['image'] = null;
			}
			$data['textblocks'][] = $curdata;
		}

		/// Load the main frame
		$this->main_frame->SetContentSimple('office/guide/guide', $data);
		$this->main_frame->Load();
	}
}

?>
