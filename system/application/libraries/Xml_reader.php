<?php

/// XML Reader.
class Xml_reader
{
	protected function _xmlerror($parser, $error)
	{
		die(sprintf("XML error: %s at line %d",
					$error,
					xml_get_current_line_number($parser)));
	}
	
	protected function _startElement($parser, $name, $attrs)
	{
		// Check this tag is valid in this context
		if (isset($this->tagStack[0])) {
			$in = ' in '.$this->tagStack[0];
		}
		else {
			$in = '';
		}
		if (!isset($this->childstack[0][$name])) {
			$this->_xmlerror($parser, 'Illegal '.$name.$in.'. Valid tags are '.implode(', ', array_keys($this->childstack[0])).'.');
		}
		// Quantity of tags
		$tagName = $this->childstack[0][$name];
		$multiple = is_array($tagName);
		if ($multiple) {
			$tagName = $tagName[0];
		}
		if (isset($this->info[$tagName]['children']) || !empty($attrs)) {
			$tagConstruct = array();
			if (!empty($attrs)) {
				foreach ($attrs as $attr => $val) {
					$tagConstruct[$attr] = $val;
				}
			}
		}
		else {
			$tagConstruct = '';
		}
		if ($multiple) {
			$this->construct[0][$name][] = &$tagConstruct;
		}
		elseif (!isset($this->childcounts[0][$name])) {
			$this->childcounts[0][$name] = xml_get_current_line_number($parser);
			$this->construct[0][$name] = &$tagConstruct;
		}
		else {
			$this->_xmlerror($parser, 'Duplicate '.$name.$in.' disallowed (previous definition at line '.$this->childcounts[0][$name].')');
		}
		array_unshift($this->construct, 0);
		$this->construct[0] = &$tagConstruct;
		
		// Validate the attributes
		if (isset($this->info[$tagName]['attr']) &&
			!empty($this->info[$tagName]['attr']))
		{
			foreach ($attrs as $attr => $value) {
				if (!isset($this->info[$tagName]['attr'][$attr])) {
					$this->_xmlerror($parser, 'Illegal '.$attr.' attribute in '.$name.'. Valid attributes are '.implode(', ', array_keys($this->info[$tagName]['attr'])).'.');
				}
			}
			foreach ($this->info[$tagName]['attr'] as $attr => $default) {
				if (false === $default) {
					if (!isset($attrs[$attr])) {
						$this->_xmlerror($parser, 'Missing '.$attr.' attribute in '.$name.'.');
					}
				}
			}
		}
		else {
			if (!empty($attrs)) {
				$this->_xmlerror($parser, 'Illegal attributes in '.$name.'. Attributes are disallowed in this tag.');
			}
		}
		
		// put a new level on stacks
		array_unshift($this->tagStack, $name);
		if (isset($this->info[$tagName]['children'])) {
			array_unshift($this->childstack, $this->info[$tagName]['children']);
		}
		else {
			array_unshift($this->childstack, array());
		}
		array_unshift($this->childcounts, array());
	}
	
	protected function _endElement($parser, $name)
	{
		array_shift($this->tagStack);
		array_shift($this->childstack);
		array_shift($this->childcounts);
		array_shift($this->construct);
	}
	
	protected function _chararcterData($parser, $data)
	{
		if (!empty($this->childstack[0])) return;
		
		// If start \n, remove it
		if (substr($data, 0, 1) == "\n") {
			$data = substr($data, 1);
		}
		// Remove last line if nothing on it
		if (preg_match('/^(.*)(\n\s*)$/s', $data, $matches)) {
			$data = $matches[1];
		}
		
		if (is_array($this->construct[0])) {
			if (!isset($this->construct[0][0])) {
				$this->construct[0][0] = '';
			}
			$this->construct[0][0] .= $data;
		}
		else {
			$this->construct[0] .= $data;
		}
	}
	
	/// Gets an array from a file.
	function fromXml(&$structure, $xml)
	{
		$this->tagStack = array();
		
		$this->childstack = array($structure['']);
		$this->childcounts = array(
			array(),
		);
		$this->construct = array(
			array(),
		);
		
		$this->info = &$structure;
		

		$xml_parser = xml_parser_create();
		xml_set_element_handler($xml_parser,
			array(&$this, '_startElement'),
			array(&$this, '_endElement')
		);
		xml_set_character_data_handler($xml_parser,
			array(&$this, '_chararcterData')
		);
		if (!xml_parse($xml_parser, $xml, true)) {
			$this->_xmlerror($xml_parser, xml_error_string(xml_get_error_code($xml_parser)));
		}
		xml_parser_free($xml_parser);
		
		unset($this->childstack);
		unset($this->childcounts);
		unset($this->info);
		
		return $this->construct[0];
	}
	
	/// Turns a tag from data back into XML.
	protected function _xmlizeTag(&$structure, &$tagStructure, &$data, $tagName, $indentation)
	{
		$tagName = strtolower($tagName);
		$result = '';
		$result .= $indentation.'<'.$tagName;
		if (is_string($data)) {
			if (empty($data)) {
				$result .= ' />';
			}
			else {
				$result .= '>'.xml_escape($data).'</'.$tagName.'>';
			}
		}
		else {
			if (is_array($tagStructure) && isset($tagStructure['attr'])) {
				foreach ($tagStructure['attr'] as $attr => $default) {
					if (isset($data[$attr])) {
						$result .= ' '.strtolower($attr).'="'.xml_escape($data[$attr]).'"';
					}
				}
			}
			$anySub = false;
			if (isset($tagStructure['children'])) {
				foreach ($tagStructure['children'] as $tag => $name) {
					if (isset($data[$tag])) {
						if (is_array($name)) {
							$name = $name[0];
							$subDatum = $data[$tag];
						}
						else {
							$subDatum = array($data[$tag]);
						}
						$newResult = '';
						if (isset($structure[$name])) {
							foreach ($subDatum as $subData) {
								$newResult .= $this->_xmlizeTag($structure, $structure[$name], $subData, $tag, $indentation."\t");
							}
						}
						else {
							$noStructure = null;
							foreach ($subDatum as $subData) {
								$newResult .= $this->_xmlizeTag($structure, $noStructure, $subData, $tag, $indentation."\t");
							}
						}
						if (!empty($newResult)) {
							if (!$anySub) {
								$result .= '>'."\n";
								$anySub = true;
							}
							$result .= $newResult;
						}
					}
				}
			}
			elseif (isset($data[0])) {
				if (!empty($data[0])) {
					$result .= '>'.xml_escape($data[0]).'</'.$tagName.'>';
					$anySub = null;
				}
			}
			if (!$anySub) {
				$result .= ' />';
			}
			elseif (null !== $anySub) {
				$result .= $indentation.'</'.$tagName.'>';
			}
		}
		$result .= "\n";
		return $result;
	}
	
	/// Turns data back into XML.
	function toXml(&$structure, &$data)
	{
		$result = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		$nextStack = $structure[''];
		foreach ($nextStack as $tag => $name) {
			if (isset($data[$tag])) {
				if (isset($structure[$name])) {
					$result .= $this->_xmlizeTag($structure, $structure[$name], $data[$tag], $tag, '');
				}
				else {
					$result .= $this->_xmlizeTag($structure, null, $data[$tag], $tag, '');
				}
			}
		}
		return $result;
	}
}

?>