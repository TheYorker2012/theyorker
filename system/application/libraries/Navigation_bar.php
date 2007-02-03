<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file Navigation_bar.php
 * @author James Hogan (jh559@cs.york.ac.uk)
 * @brief Library for rendering navigation bars.
 */

// Load the Frames library
$CI = &get_instance();
$CI->load->library('frames');

/// Navigation bar outputter.
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 */
class NavigationBar extends FramesView
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
		parent::__construct('general/navbar');

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
		$this->SetData('style',$this->mStyle);
		$this->SetData('items',$this->mItems);
		$this->SetData('selected',$this->mSelected);
		parent::Load();
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