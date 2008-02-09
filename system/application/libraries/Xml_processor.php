<?php

/// Post processor for wikitext
class Xml_processor
{
	/// Dynamic variables.
	protected $dynamics;
	
	/// array Temporary arguments array.
	protected $scope;
	
	/// bool Whether currently in paragraph.
	protected $in_paragraph = false;
	
	/// string Whether in quotes and if so the type of quote.
	protected $in_quotes = null;
	
	/// Default constructor.
	function __construct()
	{
		
	}
	
	/// Set whether in a paragraph tag
	public function in_paragraph($in_paragraph)
	{
		$this->in_paragraph = $in_paragraph;
	}
	
	/// Handle templates
	protected function handle_template($matches)
	{
		if ($matches[0][0] == '<') {
			$start_para = $matches[0][1] != '/';
			// Eliminate unecessary <p> and </p>
			if ($start_para == $this->in_paragraph) {
				return '';
			}
			$this->in_paragraph = $start_para;
			return $matches[0];
		}
		// An escaped quote
		if ($matches[0][0] == '\\') {
			// if same as current quote, ignore
			if ($this->in_quotes !== $matches[0][1]) {
				if (null !== $in_quotes) {
					$this->in_quotes = $matches[0][1];
				}
			}
			return $matches[0];
		}
		// An unescaped quote
		elseif ($matches[0] == '\'' || $matches[0] == '"') {
			if (null === $this->in_quotes) {
				$this->in_quotes = $matches[0];
			}
			elseif ($this->in_quotes == $matches[0]) {
				$this->in_quotes = null;
			}
			return $matches[0];
		}
		
		$result = $matches[0];
		$inside = $matches[1];
		
		$scope = $this->scope;
		// Find the arguments (separated by |)
		preg_match_all('/([^\\\|]|\\\\.)+/i', $inside, $arguments);
		$arguments = $arguments[0];
		foreach ($arguments as &$argument) {
			// trim and undo escaping
			$argument = str_replace(
				array('\{', '\|', '\}'),
				array( '{',  '|',  '}'),
				trim($argument)
			);
		}
		// Get the label and process it in the current scope.
		$label = array_shift($arguments);
		$label = $this->Process($label, $scope);
		$argc = 1;
		foreach ($arguments as &$argument) {
			$scope[$argc] = array($argument, true);
			if (false !== ($pos = strpos($argument, '='))) {
				$key = trim(substr($argument, 0, $pos));
				$argument = trim(substr($argument, $pos+1));
				$scope[$key] = array($argument, true);
			}
			++$argc;
		}
		unset($argument);
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
			$was_in_paragraph = $this->in_paragraph; $this->in_paragraph = false;
			$val = get_instance()->pages_model->GetPropertyWikitext($label, $page_code, true, $scope);
			$this->in_paragraph = true;
			if (null !== $val) {
				$result = $val;
				if ($was_in_paragraph) {
					$result = '</p>'.$result.'<p>';
				}
			}
			elseif (null !== ($val = get_instance()->pages_model->GetPropertyText($label, $page_code, null, $scope))) {
				$result = xml_escape($val);
				if (!$was_in_paragraph) {
					$result = '<p>'.$result.'</p>';
				}
			}
			else {
				$this->in_paragraph = $was_in_paragraph;
				if ($was_in_paragraph) {
					$val = get_instance()->pages_model->GetPropertyXhtmlInline($label, $page_code, null, $scope);
				}
				else {
					$val = get_instance()->pages_model->GetPropertyXhtmlBlock($label, $page_code, null, $scope);
				}
				if (null !== $val) {
					$result = $val;
				}
				// If its not a macro, it may be an argument
				elseif (isset($this->scope[$inside])) {
					$argument = $this->scope[$inside];
					$result = $this->Process($argument[0], $scope, $argument[1], $was_in_paragraph);
					if (null === $this->in_quotes && $was_in_paragraph != $argument[1]) {
						if ($was_in_paragraph) {
							$result = '</p>'.$result;
						}
						else {
							$result = '<p>'.$result;
						}
					}
				}
			}
			$this->in_paragraph = $was_in_paragraph;
			
			unset($anti_recurse[$label]);
		}
		
		// If it doesn't make sense, don't replace it
		return $result;
	}
	
	/// Postprocess some xml.
	function Process($xhtml, $scope = array(), $in_paragraph = null, $after_in_paragraph = null)
	{
		if ($in_paragraph !== null) {
			$this->in_paragraph = $in_paragraph;
		}
		$previous_scope = $this->scope;
		$this->scope = $scope;
		$result = preg_replace_callback('/\{\{(([^\\\}]|\\\\.)*)\}\}|<p>|<\/p>|\\\\?["\']/i',array(&$this,'handle_template'),$xhtml);
		$this->scope = $previous_scope;
		
		if ($after_in_paragraph !== null) {
			if (null === $this->in_quotes && $this->in_paragraph != $after_in_paragraph) {
				if ($this->in_paragraph) {
					$result .= '</p>';
				}
				else {
					$result .= '<p>';
				}
			}
			$this->in_paragraph = $after_in_paragraph;
		}
		$result = str_replace('<p></p>', '', $result);
		return $result;
	}
}

?>