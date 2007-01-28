<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file Navigation_bar.php
 * @author James Hogan (jh559@cs.york.ac.uk)
 * @brief Library for rendering navigation bars.
 */

// Load the Frames library
$CI = &get_instance();
$CI->load->library('frames');

/// Outputter with functions for outputting XML style tags.
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 * @todo Extend tagging functions if required.
 */
abstract class XmlOutputter extends Outputter
{
	/// Stack of tag names currently open.
	protected $mTags;

	/// Default constructor.
	function __construct()
	{
		$this->mTags = array();
	}

	/// Produce a tag with the specified attributes.
	/**
	 * @param $TagName string Name of the tag.
	 * @param $Attributes array Attributes indexed by attribute name.
	 * @param $Close bool Whether to close the tag (e.g. &lt;br/&gt;).
	 * @return string XML tag.
	 */
	function Tag($TagName, $Attributes = array(), $Close = TRUE)
	{
		$result = '<'.$TagName;
		foreach ($Attributes as $attribute => $value) {
			$result .= ' '.$attribute . '="' . $value . '"';
		}
		if (!$Close) {
			array_push($this->mTags, $TagName);
		} else {
			$result .= '/';
		}
		$result .= '>';
		return $result;
	}

	/// Open a tag with the specified attributes.
	/**
	 * @param $TagName string Name of the tag.
	 * @param $Attributes array Attributes indexed by attribute name.
	 * @return string XML opening tag.
	 */
	function OpenTag($TagName, $Attributes = array())
	{
		return $this->Tag($TagName, $Attributes, FALSE);
	}

	/// Close the last opened tag.
	/**
	 * @return string XML closing tag.
	 */
	function CloseTag()
	{
		$tag_name = array_pop($this->mTags);
		return '</'.$tag_name.'>';
	}

	/// Close all open tags.
	/**
	 * @return string XML closing tags.
	 */
	function CloseAllTags()
	{
		$result = '';
		while (count($this->mTags) > 0) {
			$result .= $this->CloseTag();
		}
		return $result;
	}

}

/// Navigation bar outputter.
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 */
class NavigationBar extends XmlOutputter
{
	/// string Style of navbar to use.
	protected $mStyle;
	/// array Items in navbar.
	protected $mItems;
	/// string Key of selected item.
	protected $mSelected;

	/// Primary constructor.
	/**
	 * @param $Style string Style of navbar to use.
	 */
	function __construct($Style = 'navbar')
	{
		parent::__construct();

		$this->mStyle = $Style;
		$this->mItems = array();
		$this->mSelected = '';

	}

	/// Add an item to the nav bar.
	/**
	 * @param $Key string Unique name of item.
	 * @param $Title string Title to be displayed.
	 * @param $Link string URL to link to.
	 * @param $Class string Class name.
	 */
	function AddItem($Key, $Title, $Link, $Class = '')
	{
		$this->mItems[$Key] = array(
				'title' => $Title,
				'link' => $Link,
				'class' => $Class,
			);
	}

	/// Set the key of the item to select.
	/**
	 * @param $Key string Unique name of item to select.
	 */
	function SetSelected($Key)
	{
		$this->mSelected = $Key;
	}

	/// Echo the HTML for the nav bar.
	function Load()
	{
		echo '<div id="'.$this->mStyle.'">';
		echo '<ul>'."\n";
		foreach ($this->mItems as $key => $item) {
			$link_attributes = array('href' => $item['link']);
			if ($key === $this->mSelected) {
				$link_attributes['class'] = 'current';
			}

			echo $this->OpenTag('li');
			echo $this->OpenTag('a',$link_attributes);
			echo $item['title'];
			echo $this->CloseTag();
			echo $this->CloseTag()."\n";

			$link_attributes = array('class' => 'thin');

			echo $this->OpenTag('li');
			echo $this->OpenTag('div',$link_attributes);
			echo "&nbsp;";
			echo $this->CloseTag();
			echo $this->CloseTag()."\n";
		}
		echo '</ul></div>'."\n";
	}
}

/// Library class for nav bars.
class Navigation_bar
{
	/// Create a navigation bar and initialise using an array.
	/**
	 * @param $Items array of items, each with the following fields:
	 *	- 'title' (string Title of the item).
	 *	- 'link' (string URL to link to).
	 *	- 'class' (optional string Class name).
	 */
	function &Create($Items = array())
	{
		$result = new NavigationBar();

		foreach ($Items as $key => $item) {
			$result->AddItem(
					$key,
					$item['title'],
					$item['link'],
					array_key_exists('class',$item) ? $item['class'] : ''
				);
		}

		return $result;
	}
}

?>