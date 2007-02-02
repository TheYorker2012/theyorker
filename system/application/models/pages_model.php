<?php

/// Represents a particular property type of a page property.
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 */
class PagePropertyType
{
	/// string Text value
	protected $mText;
	
	/// Primary constructor
	/**
	 * @param $Text string Text value.
	 */
	function __construct($Data)
	{
		$this->mText = $Data['text'];
	}
	
	/// Get the text value
	/**
	 * @return string The text value of the property.
	 */
	function GetText()
	{
		return $this->mText;
	}
	
	/// Get the integer value
	/**
	 * @return integer/NULL The integer value of the property.
	 */
	function GetInteger()
	{
		if (is_numeric($this->mText))
			return (int)$this->mText;
		else
			return NULL;
	}
	
	/// Set the text value
	/**
	 * @param $Text string The text value of the property.
	 */
	function SetText($Text)
	{
		$this->mText = $Text;
	}
	
}

/// Represents a single page property.
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 */
class PageProperty
{
	/// array[string=>PagePropertyType] Array of page property types.
	protected $mTypes;
	
	/// Primary constructor
	/**
	 * @param $Types array[string=>array[]] array of data arrays indexed by type.
	 */
	function __construct($Types)
	{
		$this->mTypes = array();
		foreach ($Types as $property_type_name => $data) {
			$this->mTypes[$property_type_name] = new PagePropertyType($data);
		}
	}
	
	/// Find whether a type of the page property exists.
	/**
	 * @param $PropertyTypeName string Property type name.
	 * @return boolean Whether the property has a type of the specified type.
	 */
	function TypeExists($PropertyTypeName)
	{
		return array_key_exists($PropertyTypeName, $this->mTypes);
	}
	
	/// Get a particular type of the page property.
	/**
	 * @param $PropertyTypeName string Property type name.
	 * @return PagePropertyType or FALSE if the form doesn't exist.
	 * @pre TypeExists(@a $PropertyTypeName)
	 */
	function GetPropertyType($PropertyTypeName)
	{
		return $this->mTypes[$PropertyTypeName];
	}
}

/// This model retrieves data about pages.
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 */
class Pages_model extends Model
{
	/// string The code string identifying the page.
	protected $mPageCode;
	
	/// array Information about the page.
	protected $mPageInfo;
	
	/// array[string=>PageProperty] Array of PageProperty's indexed by scope then label.
	protected $mProperties;
	
	/// Primary constructor.
	function __construct()
	{
		$this->mPageCode = FALSE;
		$this->mPageInfo = FALSE;
		$this->mProperties = FALSE;
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
		return (FALSE !== $this->mPageCode);
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
	
	/// Get a specific property associated with the page.
	/**
	 * @param $GlobalScope bool Whether property has global scope.
	 * @param $PropertyLabel string Label of desired property.
	 * @param $PropertyTypeName string Property type name.
	 * @return PagePropertyType/FALSE if property doesn't exist.
	 * @pre PageCodeSet() === TRUE
	 */
	function GetProperty($GlobalScope, $PropertyLabel, $PropertyTypeName)
	{
		if (FALSE === $this->mProperties) {
			$this->GetProperties();
		}
		assert('is_bool($GlobalScope)');
		if (array_key_exists($PropertyLabel, $this->mProperties[$GlobalScope])) {
			$property = $this->mProperties[$GlobalScope][$PropertyLabel];
			if ($property->TypeExists($PropertyTypeName)) {
				return $property->GetPropertyType($PropertyTypeName);
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
	
	
	/// Get a specific text property associated with the page.
	/**
	 * @param $PropertyLabel string Label of desired property.
	 * @param $GlobalScope bool Whether property has global scope.
	 * @param $Default string Default string.
	 * @return string Property value or $Default if it doesn't exist.
	 * @pre PageCodeSet() === TRUE
	 */
	function GetPropertyText($PropertyLabel, $GlobalScope = FALSE, $Default = '')
	{
		$value = $this->GetProperty($GlobalScope, $PropertyLabel, 'text');
		if (FALSE === $value) {
			return $Default;
		} else {
			return $value->GetText();
		}
	}
	
	/// Get a specific integer property associated with the page.
	/**
	 * @param $PropertyLabel string Label of desired property.
	 * @param $GlobalScope bool Whether property has global scope.
	 * @param $Default string Default number.
	 * @return string Property value or $Default if it doesn't exist.
	 * @pre PageCodeSet() === TRUE
	 */
	function GetPropertyInteger($PropertyLabel, $GlobalScope = FALSE, $Default = -1)
	{
		$value = $this->GetProperty($GlobalScope, $PropertyLabel, 'integer');
		if (FALSE === $value) {
			return $Default;
		} else {
			$int = $value->GetInteger();
			if (NULL === $int)
				return $Default;
			else
				return $int;
		}
	}
	
	/// Get a specific text property associated with the page.
	/**
	 * @param $PropertyLabel string Label of desired property.
	 * @param $GlobalScope bool Whether property has global scope.
	 * @param $Default string Default string.
	 * @return string Property value or $Default if it doesn't exist.
	 * @pre PageCodeSet() === TRUE
	 */
	function GetPropertyWikitext($PropertyLabel, $GlobalScope = FALSE, $Default = '')
	{
		$cache = $this->GetProperty($GlobalScope, $PropertyLabel, 'wikitext_cache');
		if (FALSE === $cache) {
			// No cache, see if the wikitext is there
			$wikitext = $this->GetProperty($GlobalScope, $PropertyLabel, 'wikitext');
			if (FALSE === $wikitext) {
				return $Default;
			} else {
				// Build the cache
				$this->load->library('wikiparser');
				
				$cached_wikitext = $this->wikiparser->parse($wikitext->GetText()."\n");
				if (get_magic_quotes_gpc()) {
					// If magic quotes are on, code igniter doesn't escape
					$cached_wikitext = addslashes($cached_wikitext);
				}
				// Save the cache back to the database
				$cache = new PagePropertyType(array('text' => $cached_wikitext));
				$this->InsertProperty($GlobalScope ? FALSE : $this->mPageCode,
						$PropertyLabel, 'wikitext_cache', $cache);
				return $cached_wikitext;
			}
		} else {
			// Use the cache
			return $cache->GetText();
		}
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
	function GetTitle($Parameters)
	{
		if (FALSE === $this->mPageInfo) {
			assert('$this->PageCodeSet()');
			$this->mPageInfo = $this->GetSpecificPage($this->mPageCode);
		}
		$keys = array_keys($Parameters);
		foreach ($keys as $id => $key) {
			$keys[$id] = '%%'.$key.'%%';
		}
		$values = array_values($Parameters);
		return str_replace($keys, $values, $this->mPageInfo['title']);
	}
	
	/// Get the page description.
	/**
	 * @return string Page description.
	 * @pre PageCodeSet() === TRUE
	 */
	function GetDescription()
	{
		if (FALSE === $this->mPageInfo) {
			assert('$this->PageCodeSet()');
			$this->mPageInfo = $this->GetSpecificPage($this->mPageCode);
		}
		return $this->mPageInfo['description'];
	}
	
	/// Get the page keywords.
	/**
	 * @return string Page keywords.
	 * @pre PageCodeSet() === TRUE
	 */
	function GetKeywords()
	{
		if (FALSE === $this->mPageInfo) {
			assert('$this->PageCodeSet()');
			$this->mPageInfo = $this->GetSpecificPage($this->mPageCode);
		}
		return $this->mPageInfo['keywords'];
	}
	
	
	
	/// Get the properties associated with the page.
	/**
	 * @pre PageCodeSet() === TRUE
	 */
	protected function GetProperties()
	{
		$sql =
			'SELECT
				page_properties.page_property_page_id,
				page_properties.page_property_label,
				page_properties.page_property_text,
				property_types.property_type_name
			FROM page_properties
			LEFT JOIN pages
				ON pages.page_id = page_properties.page_property_page_id
			INNER JOIN property_types
				ON property_types.property_type_id
						= page_properties.page_property_property_type_id
			WHERE	page_properties.page_property_page_id IS NULL
				OR	pages.page_codename=?';
		
		$query = $this->db->query($sql, $this->mPageCode);
		
		$results = $query->result_array();
		$property_forms = array(
			FALSE => array(),
			TRUE => array()
		);
		
		// Go through properties, sorting into $properties by label
		foreach ($results as $property) {
			$property_name = $property['page_property_label'];
			$property_scope = (NULL === $property['page_property_page_id']);
			if (!array_key_exists($property_name, $property_forms[$property_scope])) {
				$property_forms[$property_scope][$property_name] = array();
			}
			$property_forms[$property_scope][$property_name][$property['property_type_name']] = array(
					'text' => $property['page_property_text'],
				);
		}
		$property_objects = array(
			FALSE => array(),
			TRUE => array()
		);
		// Term property labels into PageProperty objects
		foreach ($property_forms as $property_scope => $properties) {
			foreach ($properties as $label => $forms) {
				$property_objects[$property_scope][$label] = new PageProperty($forms);
			}
		}
		
		$this->mProperties = $property_objects;
	}
	
	/// Insert a property
	/**
	 * @param $PageCode string/bool Page code of page to set property of (FALSE for global).
	 * @param $PropertyLabel string Label of property.
	 * @param $PropertyType string Name of the property type.
	 * @param $Property PagePropertyType Property object.
	 * @pre PageCodeSet() === TRUE
	 */
	function InsertProperty($PageCode, $PropertyLabel, $PropertyType, $Property)
	{
		$text = $Property->GetText();
		
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
		$bind_data = array($PropertyLabel, $text);
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
		$bind_data[] = $text;
		
		$query = $this->db->query($sql, $bind_data);
		return ($this->db->affected_rows() > 0);
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
			' pages.page_keywords,'.
			' pages.page_comments,'.
			' pages.page_ratings '.
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
				' pages.page_keywords,'.
				' pages.page_comments,'.
				' pages.page_ratings '.
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
				$data['comments']    = $result['page_comments'];
				$data['ratings']     = $result['page_ratings'];
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
					'WHERE page_properties.page_property_page_id';
				if ($global_scope) {
					$sql .= ' IS NULL';
					$query_params = array();
				} else {
					$sql .= ' = ?';
					$query_params = array($data['page_id']);
				}
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
		$global_scope = (FALSE === $PageCode);
		if (!$global_scope) {
			$translation = array(
					'codename'    => 'page_codename',
					'title'       => 'page_title',
					'description' => 'page_description',
					'keywords'    => 'page_keywords',
					'comments'    => 'page_comments',
					'ratings'     => 'page_ratings',
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
		
		return TRUE;
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
				'comments'    => 'page_comments',
				'ratings'     => 'page_ratings',
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