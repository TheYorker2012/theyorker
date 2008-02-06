<?php

/// Post processor for wikitext
class Xml_processor
{
	/// Dynamic variables.
	protected $dynamics;
	
	/// Default constructor.
	function __construct()
	{
		
	}
	
	/// Handle templates
	protected function handle_template($matches)
	{
		$result = $matches[0];
		$inside = $matches[1];
		if (preg_match('/^(\w+):(.*)$/i', $inside, $namespace_matches)) {
			switch (strtolower($namespace_matches[1])) {
				case 'prop':
					$label = $namespace_matches[2];
					// Protect against recursion
					static $anti_recurse = array();
					if (isset($anti_recurse[$label])) {
						break;
					}
					$antirecurse[$label] = true;
					$val = null;
					if (false) {
						// Transcluded wikitext causes problems with invalid nested p tags
						$val = get_instance()->pages_model->GetPropertyWikitext($label, false, true);
					}
					if (null !== $val) {
						$result = $val;
						break;
					}
					$val = get_instance()->pages_model->GetPropertyText($label, false, null);
					if (null !== $val) {
						$result = xml_escape($val);
						break;
					}
					$val = get_instance()->pages_model->GetPropertyXhtml($label, false, null);
					if (null !== $val) {
						$result = $val;
						break;
					}
					unset($anti_recurse[$label]);
					break;
			}
		}
		// If it doesn't make sense, don't replace it
		return $result;
	}
	
	/// Postprocess some xml.
	function Process($xhtml)
	{
		return preg_replace_callback('/\{\{(([^}])*)\}\}/i',array(&$this,'handle_template'),$xhtml);
	}
}

?>