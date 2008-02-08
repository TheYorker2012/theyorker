<?php

/// Post processor for wikitext
class Xml_processor
{
	/// Dynamic variables.
	protected $dynamics;
	
	/// array Temporary arguments array.
	protected $arguments;
	
	/// bool Whether currently in paragraph.
	protected $in_paragraph = false;
	
	/// Default constructor.
	function __construct()
	{
		
	}
	
	/// Handle templates
	protected function handle_template($matches)
	{
		if (substr($matches[0], 0, 1) == '<') {
			$this->in_paragraph = substr($matches[0], 1, 1) != '/';
			return $matches[0];
		}
		$result = $matches[0];
		$inside = $matches[1];
		// Get the label and arguments
		if (preg_match('/^\s*([^|]*)\s*(\|((\s|.)*))?/', $inside, $namespace_matches)) {
			$label = trim($namespace_matches[1]);
			$arguments = $this->arguments;
			if (isset($namespace_matches[2])) {
				$args_str = $namespace_matches[3];
				$argc = 1;
				foreach (explode('|',$args_str) as $arg) {
					$arg = trim($arg);
					$arguments[$argc] = $arg;
					if (false !== ($pos = strpos($arg, '='))) {
						$key = trim(substr($arg, 0, $pos));
						$arg = trim(substr($arg, $pos+1));
						$arguments[$key] = $arg;
					}
					++$argc;
				}
			}
			// Protect against recursion
			static $anti_recurse = array();
			if (!isset($anti_recurse[$label])) {
				$antirecurse[$label] = true;
				// Find the page code
				$page_code = '_transclude';
				if (false !== ($pos=strpos($label, ':'))) {
					$namespace = substr($label, 0, $pos);
					$label     = substr($label, $pos+1);
					
					// special namespaces:
					switch ($namespace) {
						case 'prop':
							$page_code = false;
							break;
						default:
							$page_code = $namespace;
							break;
					}
				}
				if (false !== $page_code) {
					$label = 't:'.$label;
				}
				
				$val = null;
				// Transcluded wikitext causes problems with invalid nested p tags
				$val = get_instance()->pages_model->GetPropertyWikitext($label, $page_code, true, $arguments);
				if (null !== $val) {
					$result = $val;
					if ($this->in_paragraph) {
						$result = '</p>'.$result.'<p>';
					}
				}
				elseif (null !== ($val = get_instance()->pages_model->GetPropertyText($label, $page_code, null, $arguments))) {
					$result = xml_escape($val);
					if (!$this->in_paragraph) {
						$result = '<p>'.$result.'</p>';
					}
				}
				else {
					if ($this->in_paragraph) {
						$val = get_instance()->pages_model->GetPropertyXhtmlInline($label, $page_code, null, $arguments);
					}
					else {
						$val = get_instance()->pages_model->GetPropertyXhtmlBlock($label, $page_code, null, $arguments);
					}
					if (null !== $val) {
						$result = $val;
					}
					// If its not a macro, it may be an argument
					elseif (isset($this->arguments[$inside])) {
						$result = $this->arguments[$inside];
					}
				}
				
				unset($anti_recurse[$label]);
			}
		}
		
		// If it doesn't make sense, don't replace it
		return $result;
	}
	
	/// Postprocess some xml.
	function Process($xhtml, $arguments = array())
	{
		$this->arguments = $arguments;
		$this->in_paragraph = false;
		return preg_replace_callback('/\{\{(([^}])*)\}\}|<p\W|<\/p\W/i',array(&$this,'handle_template'),$xhtml);
		unset($this->arguments);
	}
}

?>