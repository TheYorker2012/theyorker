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
	
	/// string The title string of the page.
	protected $mPageTitle;
	
	/// array[string=>PageProperty] Array of PageProperty's indexed by label.
	protected $mPageProperties;
	
	/// Primary constructor.
	function __construct()
	{
		$this->mPageCode = FALSE;
		$this->mPageTitle = FALSE;
		$this->mPageProperties = FALSE;
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
	
	/// Get a specific property associated with the page.
	/**
	 * @param $PropertyLabel string Label of desired property.
	 * @param $PropertyTypeName string Property type name.
	 * @return PagePropertyType/FALSE if property doesn't exist.
	 * @pre PageCodeSet() === TRUE
	 */
	function GetProperty($PropertyLabel, $PropertyTypeName)
	{
		if (FALSE === $this->mPageProperties) {
			$this->GetProperties();
		}
		if (array_key_exists($PropertyLabel, $this->mPageProperties)) {
			$property = $this->mPageProperties[$PropertyLabel];
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
	 * @param $Default string Default string.
	 * @return string Property value or $Default if it doesn't exist.
	 * @pre PageCodeSet() === TRUE
	 */
	function GetPropertyText($PropertyLabel, $Default = '')
	{
		$value = $this->pages_model->GetProperty($PropertyLabel, 'text');
		if (FALSE === $value) {
			return $Default;
		} else {
			return $value->GetText();
		}
	}
	
	/// Get a specific integer property associated with the page.
	/**
	 * @param $PropertyLabel string Label of desired property.
	 * @param $Default string Default number.
	 * @return string Property value or $Default if it doesn't exist.
	 * @pre PageCodeSet() === TRUE
	 */
	function GetPropertyInteger($PropertyLabel, $Default = -1)
	{
		$value = $this->pages_model->GetProperty($PropertyLabel, 'integer');
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
	 * @param $Default string Default string.
	 * @return string Property value or $Default if it doesn't exist.
	 * @pre PageCodeSet() === TRUE
	 */
	function GetPropertyWikitext($PropertyLabel, $Default = '')
	{
		$cache = $this->pages_model->GetProperty($PropertyLabel, 'wikitext_cache');
		if (FALSE === $cache) {
			// No cache, see if the wikitext is there
			$wikitext = $this->pages_model->GetProperty($PropertyLabel, 'wikitext');
			if (FALSE === $wikitext) {
				return $Default;
			} else {
				// Build the cache
				$this->load->library('wikiparser');
				$cached_wikitext = $this->wikiparser->parse($wikitext->GetText());
				// Save the cache back to the database
				$cache = new PagePropertyType(array('text' => $cached_wikitext));
				$this->InsertProperty($this->mPageCode, $PropertyLabel, 'wikitext_cache', $cache);
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
		if (FALSE === $this->mPageTitle) {
			$sql = 'SELECT `page_title` FROM `pages` WHERE `page_codename`=?';
			$query = $this->db->query($sql, $this->mPageCode);
			$results = $query->result_array();
			if (0 === count($results)) {
				return '';
			} else {
				foreach ($results as $data) {
					$this->mPageTitle = $data['page_title'];
				}
			}
		}
		$keys = array_keys($Parameters);
		foreach ($keys as $id => $key) {
			$keys[$id] = '%%'.$key.'%%';
		}
		$values = array_values($Parameters);
		return str_replace($keys, $values, $this->mPageTitle);
	}
	
	
	
	/// Get the properties associated with the page.
	/**
	 * @pre PageCodeSet() === TRUE
	 */
	protected function GetProperties()
	{
		$sql = 'SELECT'.
			' `page_properties`.`page_property_label`,'.
			' `page_properties`.`page_property_text`,'.
			' `property_types`.`property_type_name` '.
			'FROM `page_properties` '.
			'INNER JOIN `pages`'.
			' ON `pages`.`page_id`=`page_properties`.`page_property_page_id` '.
			'INNER JOIN `property_types`'.
			' ON `property_types`.`property_type_id`'.
			' =`page_properties`.`page_property_property_type_id` '.
			'WHERE `pages`.`page_codename`=?';
		
		$query = $this->db->query($sql, $this->mPageCode);
		
		$results = $query->result_array();
		$property_forms = array();
		
		// Go through properties, sorting into $properties by label
		foreach ($results as $property) {
			$property_name = $property['page_property_label'];
			if (!array_key_exists($property_name, $property_forms)) {
				$property_forms[$property_name] = array();
			}
			$property_forms[$property_name][$property['property_type_name']] = array(
					'text' => $property['page_property_text'],
				);
		}
		$property_objects = array();
		// Term property labels into PageProperty objects
		foreach ($property_forms as $label => $forms) {
			$property_objects[$label] = new PageProperty($forms);
		}
		
		$this->mPageProperties =  $property_objects;
	}
	
	/// Insert a property
	/**
	 * @param $PageCode string Page code of page to set property of.
	 * @param $PropertyLabel string Label of property.
	 * @param $PropertyType string Name of the property type.
	 * @param $Property PagePropertyType Property object.
	 * @pre PageCodeSet() === TRUE
	 */
	function InsertProperty($PageCode, $PropertyLabel, $PropertyType, $Property)
	{
		$sql =
			'INSERT INTO page_properties ('.
			' page_property_property_type_id, '.
			' page_property_page_id, '.
			' page_property_label, '.
			' page_property_text) '.
			'SELECT'.
			' property_types.property_type_id, '.
			' pages.page_id, '.
			' ?,'.
			' ? '.
			'FROM pages, property_types '.
			'WHERE pages.page_codename=? '.
			' AND property_types.property_type_name=? '.
			'ON DUPLICATE KEY UPDATE page_property_text=?';
			;
		
		$text = $Property->GetText();
		if (get_magic_quotes_gpc()) {
			// If magic quotes are on, code igniter doesn't escape
			$text = addslashes($text);
		}
		$query = $this->db->query($sql,
				array($PropertyLabel, $text, $PageCode, $PropertyType, $text)
			);
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
			;
		
		$query = $this->db->query($sql);
		return $query->result_array();
	}
}

?>