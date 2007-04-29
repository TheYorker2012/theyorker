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
	
	/// Primary constructor.
	function __construct()
	{
		$this->mPageCode = FALSE;
		$this->mPageInfo = array();
		$this->mProperties = array();
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
	 * @param $Default string Default string.
	 * @return string Property value or $Default if it doesn't exist.
	 * @pre (@a $PageCode === FALSE) => (PageCodeSet() === TRUE))
	 */
	function GetPropertyWikitext($PropertyLabel, $PageCode = FALSE, $Default = '')
	{
		$cache = $this->GetRawProperty($PageCode, $PropertyLabel, 'wikitext_cache');
		if (FALSE === $cache) {
			// No cache, see if the wikitext is there
			$wikitext = $this->GetRawProperty($PageCode, $PropertyLabel, 'wikitext');
			if (FALSE === $wikitext) {
				return $Default;
			} else {
				// Build the cache
				$this->load->library('wikiparser');
				
				$cached_wikitext = $this->wikiparser->parse($wikitext['text']."\n");
				if (get_magic_quotes_gpc()) {
					// If magic quotes are on, code igniter doesn't escape
					$cached_wikitext = addslashes($cached_wikitext);
				}
				// Save the cache back to the database
				$cache = array('text' => $cached_wikitext);
				$this->InsertProperty($PageCode,
						$PropertyLabel, 'wikitext_cache', $cache);
				return $cached_wikitext;
			}
		} else {
			// Use the cache
			return $cache['text'];
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
		$message_text = $this->GetPropertyWikitext($PropertyLabel, $PageCode, FALSE);
		if (FALSE !== $message_text) {
			return array(
				'class' => $this->GetPropertyText($PropertyLabel, $PageCode, $DefaultClass),
				'text' => $message_text,
			);
		} else {
			return FALSE;
		}
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
						$value = $this->GetPropertyWikitext($field_name, $PageCode, FALSE);
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
			if (FALSE === $PropertyTypeName) {
				return $property;
			} elseif (array_key_exists($PropertyTypeName, $property)) {
				return $property[$PropertyTypeName];
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
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
		if (FALSE === $PageCode) {
			$sql .= 'NULL,';
		} else {
			$sql .= 'pages.page_id,';
		}
		$sql .= '?, ? FROM ';
		$bind_data = array($PropertyLabel, $Property['text']);
		if (FALSE !== $PageCode) {
			$sql .= 'pages,';
		}
		$sql .=
			'	property_types
			WHERE ';
		if (FALSE !== $PageCode) {
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
	 * @return string Page title with parameters substituted.
	 * @pre PageCodeSet() === TRUE
	 *
	 * For example if the title in the db is: 'Events for %%organisation%%',
	 *	and @a $Parameters is array('organisation'=>'The Yorker'),
	 *	then the result is 'Events for The Yorker'.
	 */
	function GetTitle($Parameters = array())
	{
		$PageCode = $this->mPageCode;
		if (!array_key_exists($PageCode,$this->mPageInfo)) {
			assert('$this->PageCodeSet()');
			$this->mPageInfo[$PageCode] = $this->GetSpecificPage($PageCode);
		}
		$title = $this->mPageInfo[$PageCode]['title'];
		if (empty($Parameters)) {
			return $title;
		} else {
			$keys = array_keys($Parameters);
			foreach ($keys as $id => $key) {
				$keys[$id] = '%%'.$key.'%%';
			}
			$values = array_values($Parameters);
			return str_replace($keys, $values, $title);
		}
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
		$sql =
			'SELECT'.
			' pages.page_id,'.
			' pages.page_codename,'.
			' pages.page_title,'.
			' pages.page_description,'.
			' pages.page_keywords '.
			'FROM pages '.
			'ORDER BY pages.page_codename';
		
		$query = $this->db->query($sql);
		return $query->result_array();
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
			$sql =
				'SELECT'.
				' pages.page_id,'.
				' pages.page_codename,'.
				' pages.page_title,'.
				' pages.page_description,'.
				' pages.page_keywords '.
				'FROM pages '.
				'WHERE pages.page_codename=?';
			$query = $this->db->query($sql,$PageCode);
			$results = $query->result_array();
		}
		if ($global_scope || count($results) == 1) {
			$data = array();
			if (!$global_scope) {
				$result = $results[0];
				$data['page_id']     = $result['page_id'];
				$data['codename']    = $result['page_codename'];
				$data['title']       = $result['page_title'];
				$data['description'] = $result['page_description'];
				$data['keywords']    = $result['page_keywords'];
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
			$translation = array(
					'codename'    => 'page_codename',
					'title'       => 'page_title',
					'description' => 'page_description',
					'keywords'    => 'page_keywords',
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
				'title'       => 'page_title',
				'description' => 'page_description',
				'keywords'    => 'page_keywords',
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