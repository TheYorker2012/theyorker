<?php

/// This model retrieves data about pages.
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 */
class Pages_model extends Model
{
	/// string The code string identifying the page.
	protected $mPageCode;
	
	/// array Information about the page indexed by page code.
	protected $mPageInfo;
	
	/// array[string=>PageProperty] Array of page property's
	///	indexed by page code (where global scope is FALSE)
	///	then label
	///	then type
	protected $mProperties;
	
	/// bool Whether to allow inline edits of page properties.
	/**
	 * @invariant $mInlineEditAllowed => user.type==editor && user.level==office
	 */
	protected $mInlineEditAllowed = false;
	
	/// bool Whether inline edit mode is currently enabled.
	protected $mInlineEditMode = false;
	
	/// Primary constructor.
	function __construct()
	{
		parent::Model();
		$this->mPageCode = FALSE;
		$this->mPageInfo = array();
		$this->mProperties = array();
		$inline_edit_allowed_in_office_types = array('High'=>true,'Admin'=>true);
		$this->mInlineEditAllowed = $this->user_auth->isLoggedIn &&
			isset($inline_edit_allowed_in_office_types[$this->user_auth->officeType]);
		if (isset($_SESSION['inline_edit'])) {
			if ($this->mInlineEditAllowed) {
				$this->mInlineEditMode = true;
			} else {
				unset($_SESSION['inline_edit']);
			}
		}
	}
	
	/// Get whether inline edit mode is on.
	function GetInlineEditMode()
	{
		return $this->mInlineEditMode;
	}
	
	/// Try to enable/disable inline edit mode.
	/**
	 * @param $Enable bool Inline edit mode on.
	 */
	function SetInlineEditMode($Enable)
	{
		if ($this->mInlineEditMode != $Enable) {
			if ($Enable) {
				if ($this->mInlineEditAllowed) {
					$this->mInlineEditMode = true;
					$_SESSION['inline_edit'] = true;
				}
			} else {
				$this->mInlineEditMode = false;
				if (isset($_SESSION['inline_edit'])) {
					unset($_SESSION['inline_edit']);
				}
			}
		}
		return $this->mInlineEditMode;
	}
	
	/// Set the page code.
	/**
	 * @param $PageCode string Code identifying the page.
	 */
	function SetPageCode($PageCode)
	{
		$this->mPageCode = $PageCode;
	}
	
	/// Has the page code been set?
	/**
	 * @return Whether SetPageCode has been called yet.
	 */
	function PageCodeSet()
	{
		return is_string($this->mPageCode);
	}
	
	
	/// Get a specific text property associated with the page.
	/**
	 * @param $PropertyLabel string Label of desired property.
	 * @param $PageCode
	 *	- string Page code of page to get property from.
	 *	- TRUE Get page property from global properties.
	 *	- FALSE Current page code specified using SetPageCode.
	 * @param $Default string Default string.
	 * @return string Property value or $Default if it doesn't exist.
	 * @pre (@a $PageCode === FALSE) => (PageCodeSet() === TRUE))
	 */
	function GetPropertyText($PropertyLabel, $PageCode = FALSE, $Default = '')
	{
		$value = $this->GetRawProperty($PageCode, $PropertyLabel, 'text');
		if (FALSE === $value) {
			return $Default;
		} else {
			return $value['text'];
		}
	}
	
	/// Get a specific xhtml property associated with the page.
	/**
	 * @param $PropertyLabel string Label of desired property.
	 * @param $PageCode
	 *	- string Page code of page to get property from.
	 *	- TRUE Get page property from global properties.
	 *	- FALSE Current page code specified using SetPageCode.
	 * @param $Default string Default string.
	 * @param $Arguments array[replace => with] $Arguments to make in the wikitext.
	 * @return string Property value or $Default if it doesn't exist.
	 * @pre (@a $PageCode === FALSE) => (PageCodeSet() === TRUE))
	 */
	function GetPropertyXhtmlInline($PropertyLabel, $PageCode = FALSE, $Default = '', $Arguments = array())
	{
		$inline = true;
		$value = $this->GetRawProperty($PageCode, $PropertyLabel, 'xhtml_inline');
		if (FALSE === $value) {
			$value = $this->GetRawProperty($PageCode, $PropertyLabel, 'xhtml');
			if (FALSE !== $value) {
				$inline = false;
				$value['text'] = '</p>'.$value['text'].'<p>';
			}
		}
		if (FALSE === $value) {
			return $Default;
		} else {
			// Postprocess the xhtml
			$this->load->library('xml_processor');
			return $this->xml_processor->Process($value['text'], $Arguments);
		}
	}
	
	/// Get a specific xhtml property associated with the page.
	/**
	 * @param $PropertyLabel string Label of desired property.
	 * @param $PageCode
	 *	- string Page code of page to get property from.
	 *	- TRUE Get page property from global properties.
	 *	- FALSE Current page code specified using SetPageCode.
	 * @param $Default string Default string.
	 * @param $Arguments array[replace => with] $Arguments to make in the wikitext.
	 * @return string Property value or $Default if it doesn't exist.
	 * @pre (@a $PageCode === FALSE) => (PageCodeSet() === TRUE))
	 */
	function GetPropertyXhtmlBlock($PropertyLabel, $PageCode = FALSE, $Default = '', $Arguments = array())
	{
		$inline = false;
		$value = $this->GetRawProperty($PageCode, $PropertyLabel, 'xhtml');
		if (FALSE === $value) {
			$value = $this->GetRawProperty($PageCode, $PropertyLabel, 'xhtml_inline');
			if (FALSE !== $value) {
				$inline = true;
				$value['text'] = '<p>'.$value['text'].'</p>';
			}
		}
		if (FALSE === $value) {
			return $Default;
		} else {
			// Postprocess the xhtml
			$this->load->library('xml_processor');
			return $this->xml_processor->Process($value['text'], $Arguments);
		}
	}
	
	/// Get a specific integer property associated with the page.
	/**
	 * @param $PropertyLabel string Label of desired property.
	 * @param $PageCode
	 *	- string Page code of page to get property from.
	 *	- TRUE Get page property from global properties.
	 *	- FALSE Current page code specified using SetPageCode.
	 * @param $Default string Default number.
	 * @return string Property value or $Default if it doesn't exist.
	 * @pre (@a $PageCode === FALSE) => (PageCodeSet() === TRUE))
	 */
	function GetPropertyInteger($PropertyLabel, $PageCode = FALSE, $Default = -1)
	{
		$value = $this->GetRawProperty($PageCode, $PropertyLabel, 'integer');
		if (FALSE === $value) {
			return $Default;
		} else {
			if (is_numeric($value['text'])) {
				return (int)$value['text'];
			} else {
				return $Default;
			}
		}
	}
	
	/// Get a specific text property associated with the page.
	/**
	 * @param $PropertyLabel string Label of desired property.
	 * @param $PageCode
	 *	- string Page code of page to get property from.
	 *	- TRUE Get page property from global properties.
	 *	- FALSE Current page code specified using SetPageCode.
	 * @param $DefaultToNull bool Whether to default to NULL if there is no property.
	 * @param $Arguments array[replace => with] $Arguments to make in the wikitext.
	 * @return string Property value or $Default if it doesn't exist.
	 * @pre (@a $PageCode === FALSE) => (PageCodeSet() === TRUE))
	 */
	function GetPropertyWikitext($PropertyLabel, $PageCode = FALSE, $DefaultToNull = false, $Scope = array())
	{
		if ($this->mInlineEditMode || FALSE === ($cache = $this->GetRawProperty($PageCode, $PropertyLabel, 'wikitext_cache'))) {
			// No cache, see if the wikitext is there
			$wikitext = $this->GetRawProperty($PageCode, $PropertyLabel, 'wikitext');
			if (FALSE === $wikitext) {
				if ($DefaultToNull) {
					return NULL;
				} else {
					$wikitext_cached = '';
				}
			} else {
				// Build the cache
				$this->load->library('wikiparser');
				
				$cached_wikitext = $this->wikiparser->parse($wikitext['text']);
				if (get_magic_quotes_gpc()) {
					// If magic quotes are on, code igniter doesn't escape
					$cached_wikitext = addslashes($cached_wikitext);
				}
				// Save the cache back to the database
				$cache = array('text' => $cached_wikitext);
				if (!$this->mInlineEditMode) {
					$this->InsertProperty($PageCode,
							$PropertyLabel, 'wikitext_cache', $cache);
				}
				$wikitext_cached = $cached_wikitext;
			}
		} else {
			// Use the cache
			$wikitext_cached = $cache['text'];
		}
		// Postprocess the cache
		$this->load->library('xml_processor');
		$wikitext_cached = $this->xml_processor->Process($wikitext_cached, $Scope);
		
		if (!$this->mInlineEditMode) {
			return $wikitext_cached;
		} else {
			if (FALSE === $PageCode) {
				$PageCode = $this->mPageCode;
			}
			if (TRUE === $PageCode) {
				$page_link = site_url('admin/pages/common');
				$PageCode = '_common';
			} else {
				$page_link = site_url("admin/pages/page/edit/$PageCode");
			}
			$this->main_frame->IncludeJs('javascript/simple_ajax.js');
			$this->main_frame->IncludeJs('javascript/ppedit_inline.js');
			static $edit_counter = 0;
			++$edit_counter;
			$output = '';
			$output .= '<div style="display: block;margin:0px;">';
			$output .= " <div style=\"background-color:lightblue;border:1px solid blue;\">";
			$output .= "  <div style=\"background-color:#8080FF;color:white;\" onclick=\"return PPEditToggle($edit_counter);\">$PageCode::<strong>$PropertyLabel</strong></div>";
			$output .= "  <div id=\"ppedit_wikitext_$edit_counter\" style=\"display:none;\">";
			$output .= '   <form>';
			$output .= '    <ul><li><a href="'.site_url('admin/pages/inline/off').$this->uri->uri_string().'">Disable inline edit mode</a></li></ul>';
			$output .= '    <p>';
			$output .= "     <strong>warning</strong>: <em>This property belongs to the page type <strong><a href=\"$page_link\">$PageCode</a></strong>. Other parts of the site other than this page may use this page type. Changes will take place immediately after saving.</em>";
			$output .= '    </p>';
			$output .= '    <fieldset>';
			$output .= "     <textarea id=\"ppedit_wikitext_value_$edit_counter\" cols=\"40\" rows=\"10\">".xml_escape($wikitext['text']).'</textarea>';
			$output .= '    </fieldset>';
			$output .= '    <fieldset>';
			$output .= "     <input class=\"button\" type=\"button\" value=\"save\" onclick=\"return PPEditSubmitWikitext($edit_counter,'$PageCode','$PropertyLabel','wikitext','save');\"/>";
			$output .= "     <input class=\"button\" type=\"button\" value=\"preview\" onclick=\"return PPEditSubmitWikitext($edit_counter,'$PageCode','$PropertyLabel','wikitext','preview');\"/>";
			$output .= '     <input class="button" type="reset" value="reset" />';
			$output .= '    </fieldset>';
			$output .= '   </form>';
			$output .= '   Preview:';
			$output .= "   <div id=\"pp_wikitext_preview_$edit_counter\" style=\"border:1px dashed blue; margin: 3px; background-color:white;\">$wikitext_cached</div>";
			$output .= '  </div>';
			$output .= ' </div>';
			$output .= " <div id=\"pp_wikitext_$edit_counter\" style=\"border:1px dashed blue;\">$wikitext_cached</div>";
			$output .= '</div>';
			return $output;
		}
	}
	
	/// Get a specific message property associated with the page.
	/**
	 * @param $PropertyLabel string Label of desired property.
	 * @param $PageCode
	 *	- string Page code of page to get property from.
	 *	- TRUE Get page property from global properties.
	 *	- FALSE Current page code specified using SetPageCode.
	 * @param $Default Default value if message doesn't exist.
	 * @return array Which can be input into constructor of Message.
	 * @pre (@a $PageCode === FALSE) => (PageCodeSet() === TRUE))
	 */
	function GetPropertyMessage($PropertyLabel, $PageCode = FALSE, $DefaultClass = 'information')
	{
		$message_text = $this->GetPropertyWikitext($PropertyLabel, $PageCode);
		return array(
			'class' => $this->GetPropertyText($PropertyLabel, $PageCode, $DefaultClass),
			'text' => $message_text,
		);
	}
	
	/// Get a specific array property associated with the page.
	/**
	 * @param $PropertyLabel string Label of desired property.
	 * @param $PageCode
	 *	- string Page code of page to get property from.
	 *	- TRUE Get page property from global properties.
	 *	- FALSE Current page code specified using SetPageCode.
	 * @param $Scope array Scope variables.
	 * @return array Array page property as specified by @a $ArraySpec.
	 * @pre (@a $PageCode === FALSE) => (PageCodeSet() === TRUE))
	 */
	function GetPropertyArrayNew($Name, $PageCode = FALSE, $Scope = array())
	{
		// property type retrieval functions
		$property_types = array(
			'wikitext' => 'GetPropertyWikitext',
			'text'     => 'GetPropertyText',
		);
		
		// get the matching properties
		$Regex = '/\.([^\.\[]*)|\[([^\]]*)\]|()]/';
		$properties = $this->MatchRawProperties($PageCode, (NULL !== $Name) ? $Name : '', $Regex, (NULL !== $Name) ? '' : '.');
		// convert into array format
		$result = array();
		foreach ($properties as $label => $property) {
			// reformat the match results into an array of indicies
			$verbose_match = $property[1];
			unset($verbose_match[0]);
			$indicies = array();
			foreach ($verbose_match as $minor_match) {
				foreach ($minor_match as $k => $atom_match) {
					if ('' !== $atom_match) {
						$indicies[$k] = $atom_match;
					}
				}
			}
			ksort($indicies);
			
			// use indicies to add to the array
			$cur = & $result;
			foreach ($indicies as $index) {
				if (!array_key_exists($index, $cur)) {
					$cur[$index] = array();
				}
				$cur = & $cur[$index];
			}
			foreach ($property[0] as $type => $info) {
				if (array_key_exists($type, $property_types)) {
					$cur['_'.$type] = $this->$property_types[$type]($label, $PageCode, false, $Scope);
				}
			}
		
		}
		
		return $result;
	}
	
	/// Get a specific array property associated with the page.
	/**
	 * @param $PropertyLabel string Label of desired property.
	 * @param $ArraySpec array Index descriptors, eath with:
	 *	- ['pre'] - prefix to index
	 *	- ['post'] - postfix to index (default='')
	 *	- ['type'] - key type ('int': natural numbers starting at 0,
	 *				'enum': fields specified in ['enum']
	 *	- ['enum'] - array of fields: array(name,type)
	 * @param $PageCode
	 *	- string Page code of page to get property from.
	 *	- TRUE Get page property from global properties.
	 *	- FALSE Current page code specified using SetPageCode.
	 * @return array Array page property as specified by @a $ArraySpec.
	 * @pre (@a $PageCode === FALSE) => (PageCodeSet() === TRUE))
	 */
	function GetPropertyArray($PropertyLabel, $ArraySpec, $PageCode = FALSE)
	{
		$result = array();
		foreach($ArraySpec as $key => $indexing) {
			unset($ArraySpec[$key]);
			if (!array_key_exists('post',$indexing)) {
				$indexing['post'] = '';
			}
			if ($indexing['type'] === 'enum') {
				foreach ($indexing['enum'] as $field_info) {
					$field_name =	$PropertyLabel .
									$indexing['pre'] .
									$field_info[0] .
									$indexing['post'];
					$value = FALSE;
					if ($field_info[1] === 'wikitext') {
						$value = $this->GetPropertyWikitext($field_name, $PageCode, TRUE);
						if (NULL === $value) {
							$value = FALSE;
						}
					}
					if ($field_info[1] === 'text') {
						$value = $this->GetPropertyText($field_name, $PageCode, FALSE);
					}
					if (FALSE !== $value) {
						$result[$field_info[0]] = $value;
					}
				}
			} elseif ($indexing['type'] === 'int') {
				$index_counter = 0;
				while (TRUE) {
					$field_name =	$PropertyLabel .
									$indexing['pre'] .
									$index_counter .
									$indexing['post'];
					$value = $this->GetPropertyArray($field_name, $ArraySpec, $PageCode);
					if (!empty($value)) {
						$result[$index_counter] = $value;
					} else {
						break;
					}
					
					++$index_counter;
				}
			}
			
			break;
		}
		
		return $result;
	}
	
	/// Get a specific property associated with the page.
	/**
	 * @param $PageCode
	 *	- string Page code of page to get property from.
	 *	- TRUE Get page property from global properties.
	 *	- FALSE Current page code specified using SetPageCode.
	 * @param $PropertyLabel string Label of desired property.
	 * @param $PropertyTypeName string Property type name.
	 * @return
	 *	- array Property information (if property exists)
	 *	- FALSE (if property doesn't exist)
	 */
	function GetRawProperty($PageCode, $PropertyLabel, $PropertyTypeName = FALSE)
	{
		// Higher level function, doesn't care about what extra gets fetched
		
		// Interpret special page code
		/// @pre is_bool(@a $PageCode) OR (@a $PageCode)
		Assert('is_bool($PageCode) || is_string($PageCode)');
		/// @pre (@a $PageCode === FALSE) => (PageCodeSet() === TRUE))
		Assert('$PageCode !== FALSE || $this->PageCodeSet()');
		if (FALSE === $PageCode) {
			$PageCode = $this->mPageCode;
		} elseif (TRUE === $PageCode) {
			$PageCode = -1;
		}
		
		// Get the properties associated with the page
		if (!array_key_exists($PageCode, $this->mProperties)) {
			// Page hasn't got any properties
			// get them now into $this->mProperties[$PageCode]
			$this->GetProperties($PageCode, $PropertyLabel);
		}
		
		if (	array_key_exists($PageCode, $this->mProperties) &&
				array_key_exists($PropertyLabel, $this->mProperties[$PageCode])) {
			$property = $this->mProperties[$PageCode][$PropertyLabel];
			if (FALSE !== $PropertyTypeName && array_key_exists($PropertyTypeName, $property)) {
				return $property[$PropertyTypeName];
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
	
	function MatchRawProperties($PageCode, $Prefix, $Regex, $ForcePrefix = '')
	{
		// Higher level function, doesn't care about what extra gets fetched
		
		// Interpret special page code
		/// @pre is_bool(@a $PageCode) OR (@a $PageCode)
		Assert('is_bool($PageCode) || is_string($PageCode)');
		/// @pre (@a $PageCode === FALSE) => (PageCodeSet() === TRUE))
		Assert('$PageCode !== FALSE || $this->PageCodeSet()');
		if (FALSE === $PageCode) {
			$PageCode = $this->mPageCode;
		} elseif (TRUE === $PageCode) {
			$PageCode = -1;
		}
		
		// Get the properties associated with the page
		if (!array_key_exists($PageCode, $this->mProperties)) {
			// Page hasn't got any properties
			// get them now into $this->mProperties[$PageCode]
			$this->GetProperties($PageCode);
		}
		if (array_key_exists($PageCode, $this->mProperties)) {
			$results = array();
			foreach ($this->mProperties[$PageCode] as $label => $properties) {
				if (substr($label,0,strlen($Prefix)) == $Prefix &&
					preg_match_all($Regex, $ForcePrefix.substr($label,strlen($Prefix)), $matches))
				{
					$results[$label] = array($properties, $matches);
				}
			}
			return $results;
		} else {
			return array();
		}
	}
	
	/// Get the properties associated with the page.
	/**
	 * @param $PageCode
	 *	- string Page code of page to get property from.
	 *	- -1 Get page property from global properties.
	 * @param $LabelPattern
	 *	string label or MySQL string pattern (using '%' and '_' as wildcards)
	 * @param $MatchLike
	 *	bool Whether to use SQL LIKE instead of =
	 */
	protected function GetProperties($PageCode, $LabelPattern = FALSE, $MatchLike = FALSE)
	{
		/// @pre (@a $PageCode === -1) OR (is_string(@a $PageCode)))
		Assert('$PageCode === -1 || is_string($PageCode)');
		$sql_where = array();
		if (is_string($PageCode)) {
			// Specific page
			$sql_where[] = 'pages.page_codename=\''.$PageCode.'\'';
			
		} else {
			// Global
			$sql_where[] = 'page_properties.page_property_page_id IS NULL';
			
			/*if ($MatchLike) {
				$sql_where[] = 'page_properties.page_property_label LIKE \''.$LabelPattern.'\'';
			} else {
				$sql_where[] = 'page_properties.page_property_label=\''.$LabelPattern.'\'';
			}*/
		}
		$sql =
			'SELECT
				page_properties.page_property_page_id	AS page_id,
				pages.page_codename						AS page_code,
				page_properties.page_property_label		AS label,
				page_properties.page_property_text		AS text,
				property_types.property_type_name		AS type
			FROM page_properties
			LEFT JOIN pages
				ON pages.page_id = page_properties.page_property_page_id
			INNER JOIN property_types
				ON property_types.property_type_id
						= page_properties.page_property_property_type_id
			WHERE	'.implode(' AND ',$sql_where);
		
		$query = $this->db->query($sql);
		
		$results = $query->result_array();
		
		// Go through properties, sorting into $this->mProperties by label
		foreach ($results as $property) {
			$property_label = $property['label'];
			if (NULL === $property['page_id']) {
				$page_code = -1;
			} else {
				$page_code = $property['page_code'];
			}
			if (!array_key_exists($page_code, $this->mProperties)) {
				$this->mProperties[$page_code] = array();
			}
			if (!array_key_exists($property_label, $this->mProperties[$page_code])) {
				$this->mProperties[$page_code][$property_label] = array();
			}
			$this->mProperties[$page_code][$property_label][$property['type']] = array(
					'text' => $property['text'],
				);
		}
	}
	
	/// Insert a property
	/**
	 * @param $PageCode
	 *	- string Page code of page to get property from.
	 *	- TRUE Get page property from global properties.
	 *	- FALSE Current page code specified using SetPageCode.
	 * @param $PropertyLabel string Label of property.
	 * @param $PropertyType string Name of the property type.
	 * @param $Property array Property object.
	 * @return bool Whether any properties were created.
	 */
	function InsertProperty($PageCode, $PropertyLabel, $PropertyType, $Property)
	{
		// Interpret special page code
		/// @pre (@a $PageCode === FALSE) => (PageCodeSet() === TRUE))
		Assert('$PageCode !== FALSE || $this->PageCodeSet()');
		if (FALSE === $PageCode) {
			$PageCode = $this->mPageCode;
		}
		
		$sql =
			'INSERT INTO page_properties (
				page_property_property_type_id,
				page_property_page_id,
				page_property_label,
				page_property_text)
			SELECT
				property_types.property_type_id,';
		if (TRUE === $PageCode) {
			$sql .= 'NULL,';
		} else {
			$sql .= 'pages.page_id,';
		}
		$sql .= '?, ? FROM ';
		$bind_data = array($PropertyLabel, $Property['text']);
		if (TRUE !== $PageCode) {
			$sql .= 'pages,';
		}
		$sql .=
			'	property_types
			WHERE ';
		if (TRUE !== $PageCode) {
			$sql .= 'pages.page_codename=? AND ';
			$bind_data[] = $PageCode;
		}
		$sql .= 'property_types.property_type_name=?
			ON DUPLICATE KEY UPDATE page_property_text=?';
		$bind_data[] = $PropertyType;
		$bind_data[] = $Property['text'];
		
		$query = $this->db->query($sql, $bind_data);
		return ($this->db->affected_rows() > 0);
	}
	
	/// Check if a page code exists.
	/**
	 * @param $PageCode string Code identifying the page.
	 * @return integer/FALSE Page id or FALSE on failure.
	 */
	function PageCodeInUse($PageCode)
	{
		$sql = 'SELECT pages.page_id FROM pages WHERE pages.page_codename=?';
		$query = $this->db->query($sql, $PageCode);
		$results = $query->result_array();
		if (count($results) >= 1)
			return $results[0]['page_id'];
		else
			return FALSE;
	}
	
	/// Get the page title given certain parameters.
	/**
	 * @param $Parameters array[string=>string] Array of parameters.
	 *	Each parameter can be referred to in the database and is replaced here.
	 * @return array(string,string) Page head and body title with parameters substituted.
	 * @pre PageCodeSet() === TRUE
	 *
	 * For example if the title in the db is: 'Events for %%organisation%%',
	 *	and @a $Parameters is array('organisation'=>'The Yorker'),
	 *	then the result is 'Events for The Yorker'.
	 */
	function GetTitles($Parameters = array())
	{
		$PageCode = $this->mPageCode;
		if (!array_key_exists($PageCode,$this->mPageInfo)) {
			assert('$this->PageCodeSet()');
			$this->mPageInfo[$PageCode] = $this->GetSpecificPage($PageCode);
		}
		$titles = array(
			$this->mPageInfo[$PageCode]['head_title'],
			$this->mPageInfo[$PageCode]['body_title'],
		);
		if (NULL === $titles[1]) {
			$titles[1] = $titles[0];
		}
		if ($this->mInlineEditMode) {
			$titles['edit_url'] = site_url("admin/pages/page/edit/$PageCode");
		}
		if (empty($Parameters)) {
			return $titles;
		} else {
			$keys = array_keys($Parameters);
			foreach ($keys as $id => $key) {
				$keys[$id] = '%%'.$key.'%%';
			}
			$values = array_values($Parameters);
			return str_replace($keys, $values, $titles);
		}
	}
	
	/// Get the http header from the page description.
	/**
	 * @return NULL,string http header string or NULL for none.
	 */
	function GetHttpHeader()
	{
		$PageCode = $this->mPageCode;
		if (!array_key_exists($PageCode,$this->mPageInfo)) {
			assert('$this->PageCodeSet()');
			$this->mPageInfo[$PageCode] = $this->GetSpecificPage($PageCode);
		}
		return $this->mPageInfo[$PageCode]['http_header'];
	}
	
	/// Get the page description.
	/**
	 * @return string Page description.
	 * @pre PageCodeSet() === TRUE
	 */
	function GetDescription()
	{
		$PageCode = $this->mPageCode;
		if (!array_key_exists($PageCode,$this->mPageInfo)) {
			assert('$this->PageCodeSet()');
			$this->mPageInfo[$PageCode] = $this->GetSpecificPage($PageCode);
		}
		return $this->mPageInfo[$PageCode]['description'];
	}
	
	/// Get the page keywords.
	/**
	 * @return string Page keywords.
	 * @pre PageCodeSet() === TRUE
	 */
	function GetKeywords()
	{
		$PageCode = $this->mPageCode;
		if (!array_key_exists($PageCode,$this->mPageInfo)) {
			assert('$this->PageCodeSet()');
			$this->mPageInfo[$PageCode] = $this->GetSpecificPage($PageCode);
		}
		return $this->mPageInfo[$PageCode]['keywords'];
	}
	
	
	/// Get all pages
	/**
	 */
	function GetAllPages()
	{
		$this->db->from('pages');
		$this->db->select(
			'pages.page_id,'.
			'pages.page_codename,'.
			'pages.page_head_title,'.
			'pages.page_body_title,'.
			'pages.page_description,'.
			'pages.page_keywords,'.
			'pages.page_page_type_id'
		);
		$this->db->orderby('pages.page_codename');
		
		$query = $this->db->get();
		return $query->result_array();
	}
	
	/// Get all page types.
	/**
	 * @return array(id => array('name' =>, 'http_header' => ))
	 */
	function GetAllPageTypes()
	{
		$this->db->from('page_types');
		$this->db->select(
			'page_types.page_type_id           AS id,'.
			'page_types.page_type_http_header  AS http_header,'.
			'page_types.page_type_name         AS name'
		);
		
		$query = $this->db->get();
		$results = array();
		foreach ($query->result_array() as $page_type) {
			$page_type['id'] = (int)$page_type['id'];
			$results[$page_type['id']] = $page_type;
		}
		return $results;
	}
	
	/// Get a specific page
	/**
	 * @param $PageCode string/FALSE Codename of page or FALSE for common.
	 * @param $Properties bool Whether to retrieve properties as well.
	 * @return array of information about the page or FALSE on failure.
	 * @post (@a PageCode === FALSE) => (result != FALSE)
	 */
	function GetSpecificPage($PageCode, $Properties = FALSE)
	{
		$this->db->trans_start();
		
		$global_scope = (FALSE === $PageCode);
		if ($global_scope) {
			$Properties = TRUE;
		} else {
			$this->db->select(
				' pages.page_id,'.
				' pages.page_codename,'.
				' pages.page_head_title,'.
				' pages.page_body_title,'.
				' pages.page_description,'.
				' pages.page_keywords, '.
				' pages.page_page_type_id, '.
				' page_types.page_type_http_header '
			);
			$this->db->from('pages');
			$this->db->where(array('pages.page_codename' => $PageCode));
			$this->db->join('page_types','page_types.page_type_id = pages.page_page_type_id','left');
			$query = $this->db->get();
			$results = $query->result_array();
		}
		if ($global_scope || count($results) == 1) {
			$data = array();
			if (!$global_scope) {
				$result = $results[0];
				$data['page_id']      = $result['page_id'];
				$data['codename']     = $result['page_codename'];
				$data['head_title']   = $result['page_head_title'];
				$data['body_title']   = $result['page_body_title'];
				$data['description']  = $result['page_description'];
				$data['keywords']     = $result['page_keywords'];
				$data['type_id']      = $result['page_page_type_id'];
				$data['http_header']  = $result['page_type_http_header'];
			}
			if ($Properties) {
				$sql =
					'SELECT'.
					' page_properties.page_property_id,'.
					' page_properties.page_property_label,'.
					' page_properties.page_property_text,'.
					' property_types.property_type_name '.
					'FROM page_properties '.
					'INNER JOIN property_types '.
					' ON page_properties.page_property_property_type_id = property_types.property_type_id '.
					'WHERE page_properties.page_property_page_id ';
				if ($global_scope) {
					$sql .= ' IS NULL';
					$query_params = array();
				} else {
					$sql .= ' = ?';
					$query_params = array($data['page_id']);
				}
				$sql .= ' ORDER BY page_properties.page_property_label, page_properties.page_property_label';
				$query = $this->db->query($sql,$query_params);
				$property_results = $query->result_array();
				$data['properties'] = array();
				foreach ($property_results as $property) {
					$data['properties'][] = array(
						'id'    => $property['page_property_id'],
						'label' => $property['page_property_label'],
						'text'  => $property['page_property_text'],
						'type'  => $property['property_type_name'],
					);
				}
			}
		} else {
			$data = FALSE;
		}
		$this->db->trans_complete();
		
		return $data;
	}
	
	/// Save a specific page
	/**
	 * @param $PageCode string/FALSE Codename of page or FALSE for common.
	 * @param $Data array of data in similar format to output of GetSpecificPage.
	 * @return bool Whether the save was successful.
	 */
	function SaveSpecificPage($PageCode, $Data)
	{
		$this->db->trans_start();
		
		$global_scope = (FALSE === $PageCode);
		if (!$global_scope) {
			if (array_key_exists('type_id', $Data) && $Data['type_id'] < 0)
			{
				$Data['type_id'] = NULL;
			}
			
			$translation = array(
				'codename'    => 'page_codename',
				'head_title'  => 'page_head_title',
				'body_title'  => 'page_body_title',
				'description' => 'page_description',
				'keywords'    => 'page_keywords',
				'type_id'     => 'page_page_type_id',
			);
			$save_data = array();
			foreach ($Data as $key => $value) {
				if (array_key_exists($key, $translation)) {
					$save_data[$translation[$key]] = $value;
				}
			}
			if (count($save_data) > 0) {
				$sql = 'UPDATE pages SET ';
				$assignments = array();
				foreach ($save_data as $key => $value) {
					$assignments[] = $key.'=?';
				}
				$sql .= implode(', ', $assignments);
				$sql .= ' WHERE page_codename=? ';
				$sql .= 'LIMIT 1;';
				$save_data = array_values($save_data);
				$save_data[] = $PageCode;
				
				$this->db->query($sql,$save_data);
			}
		}
		if (array_key_exists('properties',$Data)) {
			foreach ($Data['properties'] as $property) {
				$save_data = array();
				$sql = 'UPDATE page_properties ';
				if (!$global_scope) {
					$sql .= 'INNER JOIN pages
						ON page_properties.page_property_page_id=pages.page_id ';
				}
				$sql .= 'SET page_properties.page_property_text=?
					WHERE page_properties.page_property_id=? AND ';
				$save_data[] = $property['text'];
				$save_data[] = $property['id'];
				if (!$global_scope) {
					$sql .= 'pages.page_codename=?;';
					$save_data[] = $PageCode;
				} else {
					$sql .= 'page_properties.page_property_page_id IS NULL';
				}
				
				$this->db->query($sql,$save_data);
			}
		}
		if (array_key_exists('property_add',$Data)) {
			foreach ($Data['property_add'] as $property) {
				$text = $property['text'];
				
				$save_data = array();
				$sql = '
					INSERT INTO page_properties (
						page_property_property_type_id,
						page_property_page_id,
						page_property_label,
						page_property_text)
					SELECT
						property_types.property_type_id,';
				if (!$global_scope) {
					$sql .= 'pages.page_id,';
				} else {
					$sql .= 'NULL,';
				}
				$sql .= '?,	? FROM ';
				$save_data[] = $property['label'];
				$save_data[] = $text;
				if (!$global_scope)
					$sql .= 'pages,';
				$sql .= 'property_types WHERE ';
				if (!$global_scope) {
					$sql .= 'pages.page_codename=? AND ';
					$save_data[] = $PageCode;
				}
				$sql .= 'property_types.property_type_name=?
					ON DUPLICATE KEY UPDATE page_property_text=?';
				$save_data[] = $property['type'];
				$save_data[] = $text;
				
				$query = $this->db->query($sql, $save_data);
			}
		}
		if (array_key_exists('property_remove',$Data)) {
			foreach ($Data['property_remove'] as $property_type => $labels) {
				foreach ($labels as $label) {
					$save_data = array();
					$sql = 'DELETE FROM page_properties
						USING ';
					if (!$global_scope)
						$sql .= 'pages, ';
					$sql .= 'page_properties, property_types
WHERE page_properties.page_property_property_type_id=property_types.property_type_id
	AND property_types.property_type_name=?
	AND page_properties.page_property_label=?';
					$save_data[] = $property_type;
					$save_data[] = $label;
					if (!$global_scope) {
						$sql .= '
	AND page_properties.page_property_page_id=pages.page_id
	AND pages.page_codename=?';
						$save_data[] = $PageCode;
					} else {
						$sql .= '
	AND page_properties.page_property_page_id IS NULL';
					}
					
					$this->db->query($sql,$save_data);
				}
			}
		}
		$this->db->trans_complete();
		
		if ($this->db->trans_status() === FALSE) {
			return TRUE;
		} else {
			return TRUE;
		}
	}
	
	
	/// Create a new page
	/**
	 * @param $Data array of data in similar format to output of GetSpecificPage.
	 * @return bool Whether the save was successful.
	 */
	function CreatePage($Data)
	{
		$translation = array(
				'codename'    => 'page_codename',
				'head_title'  => 'page_head_title',
				'body_title'  => 'page_body_title',
				'description' => 'page_description',
				'keywords'    => 'page_keywords',
				'type_id'     => 'page_page_type_id',
			);
		$save_data = array();
		foreach ($Data as $key => $value) {
			if (array_key_exists($key, $translation)) {
				$save_data[$translation[$key]] = $value;
			}
		}
		if (count($save_data) > 0) {
			$sql  = 'INSERT INTO pages (';
			$sql .= implode(',', array_keys($save_data));
			$sql .= ') VALUES (';
			$sql .= implode(',', array_fill(1, count($save_data), '?'));
			$sql .= ');';
			
			$this->db->query($sql,array_values($save_data));
		}
		
		return ($this->db->affected_rows() > 0);
	}
	
	/// Delete a specific page.
	/**
	 * @param $PageCode string Codename of page.
	 * @return bool Whether successful.
	 */
	function DeletePage($PageCode)
	{
		$sql_delete_properties =
			'DELETE FROM page_properties
			USING pages, page_properties
			WHERE	page_properties.page_property_page_id = pages.page_id
				AND	pages.page_codename = ?';
		
		$sql_delete_page =
			'DELETE FROM pages
			WHERE	pages.page_codename = ?';
		
		
		$this->db->trans_start();
		
		$this->db->query($sql_delete_properties,$PageCode);
		$properties_deleted = $this->db->affected_rows();
		
		$this->db->query($sql_delete_page,$PageCode);
		$pages_deleted = $this->db->affected_rows();
		
		$this->db->trans_complete();
		
		return ($pages_deleted > 0);
	}
}

?>
