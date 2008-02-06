<?php

/// Post processor for wikitext
class Xml_processor
{
	/// Dynamic variables.
	protected $dynamics;
	
	/// array Temporary arguments array.
	protected $arguments;
	
	/// Default constructor.
	function __construct()
	{
		
	}
	
	/// Handle templates
	protected function handle_template($matches)
	{
		$result = $matches[0];
		$inside = $matches[1];
		if (preg_match('/^(\w+):([^|]*)(\|(.*))?$/i', $inside, $namespace_matches)) {
			switch (strtolower($namespace_matches[1])) {
				case 'prop':
					$label = $namespace_matches[2];
					$arguments = $this->arguments;
					if (isset($namespace_matches[4])) {
						$args_str = $namespace_matches[4];
						$argc = 1;
						foreach (explode('|',$args_str) as $arg) {
							$arguments[$argc] = $arg;
							if (false !== ($pos = strpos($arg, '='))) {
								$key = substr($arg, 0, $pos);
								$arg = substr($arg, $pos+1);
								$arguments[$key] = $arg;
							}
							++$argc;
						}
					}
					
					// Protect against recursion
					static $anti_recurse = array();
					if (isset($anti_recurse[$label])) {
						break;
					}
					$antirecurse[$label] = true;
					$val = null;
					if (false) {
						// Transcluded wikitext causes problems with invalid nested p tags
						$val = get_instance()->pages_model->GetPropertyWikitext($label, false, true, $arguments);
					}
					if (null !== $val) {
						$result = $val;
						break;
					}
					$val = get_instance()->pages_model->GetPropertyText($label, false, null, $arguments);
					if (null !== $val) {
						$result = xml_escape($val);
						break;
					}
					$val = get_instance()->pages_model->GetPropertyXhtml($label, false, null, $arguments);
					if (null !== $val) {
						$result = $val;
						break;
					}
					unset($anti_recurse[$label]);
					break;
			}
		}
		if (isset($this->arguments[$inside])) {
			return $this->arguments[$inside];
		}
		// If it doesn't make sense, don't replace it
		return $result;
	}
	
	/// Postprocess some xml.
	function Process($xhtml, $arguments = array())
	{
		$this->arguments = $arguments;
		return preg_replace_callback('/\{\{(([^}])*)\}\}/i',array(&$this,'handle_template'),$xhtml);
		unset($this->arguments);
	}
}

?>