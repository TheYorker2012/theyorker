<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file Frames.php
 * @author James Hogan (jh559@cs.york.ac.uk)
 * @brief Library for managing frames and views nicely.
 */

/// Abstract class that outputs something when Load() is called
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 */
abstract class Outputter
{
	/// Outputs some sort of data, such as HTML, XML or whatever.
	abstract function Load();
}

/// Quite simply represents a view.
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * Initialise with a view path and use the data functions to set data to send to
 *	the view. Finally call Load() to load the view.
 * @code
 *	// Set up the view for some simple page.
 *	$simple_view = $this->frames->view('simple/view');
 *	$simple_view->AddData($data);
 *	
 *	// Load the view
 *	$simple_view->Load();
 * @endcode
 *
 * @see FramesFrame for handling subviews.
 */
class FramesView extends Outputter
{
	/// array The data array to send to the view.
	protected $mDataArray;
	/// string The view to load when the time comes.
	protected $mViewPath;
	
	/// Primary constructor.
	/**
	 * @param $ViewPath string The path of a CI view.
	 * @param $Data array The initial data array.
	 */
	function __construct($ViewPath, $Data = array())
	{
		$this->mDataArray = $Data;
		$this->mViewPath = $ViewPath;
	}
	
	/// Set the view to use.
	/**
	 * @param $ViewPath string The path of a CI view.
	 */
	function SetView($ViewPath)
	{
		$this->mViewPath = $ViewPath;
	}
	
	/// Get the value of a specific data field.
	/**
	 * @param $Index integer The index of the data.
	 * @return The data associated with @a $Index in the data array.
	 */
	function GetData($Index)
	{
		return $this->mDataArray[$Index];
	}
	
	/// Set the value of a specific data field.
	/**
	 * @param $Index integer The index of the data.
	 * @param $Value The value to set to the data element @a Index.
	 */
	function SetData($Index, $Value)
	{
		$this->mDataArray[$Index] = $Value;
	}
	
	/// Merge a data array with that already in the data array.
	/**
	 * @param $Data array Data to merge.
	 */
	function AddData($Data)
	{
		$this->mDataArray = array_merge($this->mDataArray, $Data);
	}
	
	/// Use code igniter to load the view.
	/**
	 * If the specified view is empty, then nothing happens.
	 */
	function Load()
	{
		if (!empty($this->mViewPath)) {
			$CI = &get_instance();
			$CI->load->view($this->mViewPath, $this->mDataArray);
		}
	}
}

/// Represents a view with subviews
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * Initialise with a view path and use the data functions to set data, as well
 *	as SetContent() to set subviews. Finally call Load() to load the frame:
 * @code
 *	// Set up the subview for calendar.
 *	$calendar_view = $this->frames->view('calendar/calendar');
 *	$calendar_view->AddData($data);
 *	
 *	// Set up the master frame.
 *	$main_frame = $this->frames->frame('frames/student_frame');
 *	$main_frame->SetData('extra_head', $extra_head);
 *	$main_frame->SetContent($calendar_view);
 *	
 *	// Load the master frame (this should in turn load the calendar view)
 *	$main_frame->Load();
 * @endcode
 *
 * To load the content in a slot from the view, use the following code:
 * @code
 *	<?php
 *		// Load a subview.
 *		$content[$slot_index]->Load();
 *	?>
 * @endcode
 */
class FramesFrame extends FramesView
{
	/// Primary constructor.
	/**
	 * @param $ViewPath string The path of a CI view of the frame.
	 * @param $Data array The initial data array.
	 */
	function __construct($ViewPath, $Data = array())
	{
		parent::__construct($ViewPath, $Data);
		$this->mDataArray['content'] = array(0 => new FramesView(''));
	}
	
	/// Set a specific content slot to a view.
	/**
	 * @param $SubView FramesView The view to set as content.
	 * @param $Index The index of the content slot to set.
	 *	Although this defaults to 0, it doesn't have to be an integer.
	 */
	function SetContent(&$SubView, $Index = 0)
	{
		$this->mDataArray['content'][$Index] = $SubView;
	}
	
	/// Set a specific content slot to a simple view.
	/**
	 * @param $ViewPath string The path of a CI view of the view.
	 * @param $Data array The initial data array.
	 * @param $Index The index of the content slot to set.
	 *	Although this defaults to 0, it doesn't have to be an integer.
	 * @return FramesView The newly created view.
	 */
	function &SetContentSimple($SubView, $Data = array(), $Index = 0)
	{
		$new_view = new FramesView($SubView, $Data);
		$this->mDataArray['content'][$Index] = $new_view;
		return $new_view;
	}
}

/// An abstract frame which simply loads its content.
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * The purpose is that a class can extend this class and choose which view class
 *	to show dynamically.
 */
class FramesFrameEcho extends FramesFrame
{
	/// Default constructor.
	function __construct()
	{
		parent::__construct('');
	}
	
	/// Get the content.
	/**
	 * @return Reference to the content class.
	 */
	function &GetContent()
	{
		return $this->mDataArray['content'][0];
	}
	
	/// Use code igniter to load the content view.
	function Load()
	{
		$this->mDataArray['content'][0]->Load();
	}
}

/// Frames library.
/**
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * At the moment this is simply shortcuts for constructing views and frames.
 */
class Frames
{
	/// Shortcut to construct a bog standard view (FramesView).
	/**
	 * @param $ViewPath string The path of a CI view of the view.
	 * @param $Data array The initial data array.
	 * @return FramesView The new view object.
	 */
	function View($ViewPath, $Data = array())
	{
		return new FramesView($ViewPath, $Data);
	}
	
	/// Shortcut to construct a frame (FramesFrame).
	/**
	 * @param $ViewPath string The path of a CI view of the frame.
	 * @param $Data array The initial data array.
	 * @return FramesFrame The new frame object.
	 */
	function Frame($ViewPath, $Data = array())
	{
		return new FramesFrame($ViewPath, $Data);
	}
}

?>