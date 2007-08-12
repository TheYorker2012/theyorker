<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file Custom_pages.php
 * @brief Library of custom page abstraction classes.
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * @pre frames library must be loaded.
 * @pre messages library must be loaded.
 * @pre pages_model model must be loaded
 */

/// Main custom page library class.
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 */
class CustomPageView extends FramesView
{
	/// Content from page properties.
	protected $mProperties = NULL;
	
	/// Primary constructor
	/**
	 * @param $PageCode string Page code.
	 * @param $Namespace string Namespace of page code.
	 * @note Actual page code is @a Namespace : @a PageCode.
	 */
	function __construct($PageCode, $Namespace = NULL)
	{
		parent::__construct(NULL);
		
		// Find and set page code using namespace
		if (NULL !== $Namespace) {
			$PageCode = $Namespace.':'.$PageCode;
		}
		$CI = &get_instance();
		$CI->pages_model->SetPageCode($PageCode);
		
		$this->mProperties = $CI->pages_model->GetPropertyArrayNew('main');
	}

	function Load()
	{
		$CI = & get_instance();
		static $valid_templates = array(
			'default' => 'pages/custom_page',
			'error' => 'pages/error',
		);
		foreach ($this->mProperties as $content) {
			$template = @$content['template']['_text'];
			if (NULL === $template) {
				$template = 'default';
			}
			if (array_key_exists($template, $valid_templates)) {
				$CI->load->view(
					$valid_templates[$template],
					array_merge($this->mDataArray, $content)
				);
			}
		}
	}
}

/// Main custom page library class.
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 * @note This class really doesn't do anything!
 */
class Custom_pages
{
	
}

?>