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
	/// Primary constructor
	/**
	 * @param $PageCode string Page code.
	 * @param $Namespace string Namespace of page code.
	 * @note Actual page code is @a Namespace : @a PageCode.
	 */
	function __construct($PageCode, $Namespace = NULL)
	{
		// Set the view to use (and construct parent)
		parent::__construct('pages/custom_page');
		
		// Find and set page code using namespace
		if (NULL !== $Namespace) {
			$PageCode = $Namespace.':'.$PageCode;
		}
		$CI = &get_instance();
		$CI->pages_model->SetPageCode($PageCode);
		
		// Get the wikitext into data
		$content = $CI->pages_model->GetPropertyWikitext('main', FALSE);
		if (FALSE === $content) {
			$CI->messages->AddMessage('error','Internal error: custom page '.$PageCode.' invalid');
			$content = '';
		}
		$this->SetData('parsed_wikitext', $content);
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