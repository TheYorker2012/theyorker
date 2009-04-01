<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file Frame_public.php
 * @author James Hogan (jh559@cs.york.ac.uk)
 * @brief Shortcut library for using the public frame.
 */

// Load the Frames library
$CI = &get_instance();
$CI->load->library('frames');
$CI->load->library('messages');
$CI->load->library('adverts');
$CI->load->library('frame_navbar');
$CI->load->model('pages_model');

/**
 * @brief Main public frame library class.
 *
 * Automatically loads the Frames library.
 *
 * Load the library from the controller constructor:
 * @code
 *	// Load the public frame
 *	$this->load->library('frame_public');
 * @endcode
 *
 * You can then refer to it as $this->frame_public in order to SetContent().
 * The view can then be loaded using Load() (including the content view
 *	specified using SetContent()).
 *
 * Example of usage from a controller function:
 * @code
 *	// Set up the subview
 *	$calendar_view = $this->frames->view('calendar/calendar', $data);
 *	
 *	// Set up the public frame
 *	$this->frame_public->SetTitle($page_title);
 *	$this->frame_public->IncludeJs('javascript/something.js');
 *	$this->frame_public->IncludeCss('stylehssets/something.css');
 *	$this->frame_public->SetContent($calendar_view);
 *	
 *	// Load the public frame view (which will load the content view)
 *	$this->frame_public->Load();
 * @endcode
 */
class Frame_public extends FrameNavbar
{
	/// Has the title been set yet
	private $mTitleSet;
	/// array Title parameters.
	private $mTitleParameters = NULL;
	/// string HTTP header.
	private $mHttpHeader = NULL;
	/// enable adverts for the public frame
	protected $mHasAdverts = TRUE;
	/// array of JS modules included.
	protected $mJsModules = array();
	
	/**
	 * @brief Default constructor.
	 */
	function __construct()
	{
		parent::__construct('frames/public_frame.php');

		$this->mDataArray['description'] = '';
		$this->mDataArray['keywords'] = '';
		$this->mTitleSet = FALSE;

		$this->mDataArray['login'] = array(
				'logged_in' => FALSE,
			);
		$CI = &get_instance();
		if ($CI->user_auth->isLoggedIn) {
			$this->mDataArray['login']['logged_in'] = TRUE;
			$this->mDataArray['login']['username'] = $entity_id = $CI->user_auth->username;
		}
		$this->mDataArray['uri'] = $CI->uri->uri_string();

		$CI->load->library('academic_calendar');
		$academic_time = new Academic_time(mktime());
		$this->mDataArray['date'] = array(
			'time'	=> date('H:i'),
			'day'	=> date('l'),
			'week'	=> $academic_time->AcademicWeek(),
			'date'	=> date('jS'),
			'month'	=> date('F')
		);
		
		$CI->load->model('ticker_model');
		$this->mDataArray['ticker'] = $CI->ticker_model->getNews();
		
		// Get user's Links
		$CI->load->model('links_model');
		if ($CI->user_auth->isLoggedIn) {
			$this->mDataArray['links'] = $CI->links_model->GetFormattedLinks($CI->user_auth->entityId);
		} else {
			$this->mDataArray['links'] = $CI->links_model->GetFormattedLinks(0);
		}
	}
	
	/// Add keywords to the page.
	/**
	 * @param $Keywords string Comma seperated keywords.
	 */
	function AddKeywords($Keywords)
	{
		if (!empty($this->mDataArray['keywords'])) {
			$this->mDataArray['keywords'] .= ',';
		}
		$this->mDataArray['keywords'] .= $Keywords;
	}
	
	/// Add description to the page.
	/**
	 * @param $Description string Description.
	 */
	function AddDescription($Description)
	{
		if (!empty($this->mDataArray['description'])) {
			$this->mDataArray['description'] .= '. ';
		}
		$this->mDataArray['description'] .= $Description;
	}
	
	/// Include a JS (javascript) file.
	/**
	 * @param $JsFile string Javascript source file, not site_url'd.
	 */
	function IncludeJs($JsFile)
	{
		if (!isset($this->mJsModules[$JsFile])) {
			$this->AddExtraHead('<script src="'.site_url($JsFile).'" type="text/javascript"></script>');
			$this->mJsModules[$JsFile] = true;
		}
	}
	
	/// Include a CSS (cascading stylesheets) file.
	/**
	 * @param $CssFile string CSS source file, not site_url'd.
	 */
	function IncludeCss($CssFile, $Media = null, $Title = null)
	{
		$style_tag = '<link href="'.site_url($CssFile).'" rel="stylesheet" type="text/css" ';
		if (null !== $Title) {
			$style_tag .= 'title="'.xml_escape($Title).'" ';
		}
		if (is_array($Media) && !empty($Media)) {
			$style_tag .= 'media="'.xml_escape(implode(',',$Media)).'" ';
		}
		$style_tag .= '/>';
		$this->AddExtraHead($style_tag);
	}

	/**
	 * @brief Add extra code to go in the page header.
	 * @param $literal string Extra code to go in the page header.
	 */
	function AddExtraHead($literal)
	{
		$this->AddExtraHeadView(new SimpleView($literal));
	}

	/**
	 * @brief Add an extra view to go in the page header.
	 * @param $view view Extra view to go in the page header.
	 */
	function AddExtraHeadView($view)
	{
		$this->AppendContent($view, 'head');
	}
	
	/**
	 * @brief Set the extra code to go in the page header.
	 * @param $ExtraHead string Extra code to go in the page header.
	 * @deprecated use AddExtraHead instead.  
	 */
	function SetExtraHead($ExtraHead)
	{
		$this->AddExtraHead($ExtraHead);
	}
	
	/// Add a message to the page.
	/**
	 * @param $Type string/Message Message type or Message class.
	 * @param $Message string Message (if $Type isn't Message class).
	 * @param $Persistent bool Whether the message should be shown on the next
	 *	page if this page is never displayed.
	 */
	function AddMessage($Type, $Message = '', $Persistent = TRUE)
	{
		$CI = &get_instance();
		$CI->messages->AddMessage($Type, $Message, $Persistent);
	}
	
	/**
	 * @brief Set the page title.
	 * @param $Title string Page title.
	 */
	function SetTitle($Title)
	{
		$this->SetData('head_title', $Title);
		$this->SetData('body_title', $Title);
		$this->mTitleSet = TRUE;
	}
	
	/**
	 * @brief Set the page title parameters.
	 * @param $Parameters array[string=>string] Array of parameters to be
	 *	replaced.
	 */
	function SetTitleParameters($Parameters = array())
	{
		if (NULL === $this->mTitleParameters) {
			$this->mTitleParameters = $Parameters;
		} else {
			$this->mTitleParameters = array_merge($this->mTitleParameters, $Parameters);
		}
	}
	
	/// Load the frame.
	function Load()
	{
		$CI = &get_instance();
		if ($this->mHasAdverts) {
			$advert = $CI->adverts->SelectNextAdvert();
			$this->SetData('advert', $advert);
		}
		if ($CI->pages_model->PageCodeSet()) {
			if (NULL !== $this->mTitleParameters) {
				$titles = $CI->pages_model->GetTitles($this->mTitleParameters);
				$title_set = FALSE;
			} elseif (!$this->mTitleSet) {
				$titles = $CI->pages_model->GetTitles();
				$title_set = FALSE;
			} else {
				// Title has already been set manually.
				$title_set = TRUE;
			}
			if (!$title_set) {
				$this->SetData('head_title', $titles[0]);
				$this->SetData('body_title', $titles[1]);
				if (isset($titles['edit_url'])) {
					$this->SetData('paged_edit_url', $titles['edit_url']);
				} else {
					$this->SetData('paged_edit_url', NULL);
				}
			} else {
				$this->SetData('paged_edit_url', NULL);
			}
			$this->AddDescription($CI->pages_model->GetDescription());
			$this->AddKeywords($CI->pages_model->GetKeywords());
			
			if (NULL === $this->mHttpHeader) {
				$this->mHttpHeader = $CI->pages_model->GetHttpHeader();
			}
		}
		
		$this->mDataArray['messages'] = $CI->messages->GetMessages();
		// if applicable, send http header
		if (NULL !== $this->mHttpHeader) {
			header($this->mHttpHeader);
		}
		// write the output
		parent::Load();
		unset($this->mDataArray['messages']);
		$CI->messages->ClearQueue();
	}
}

?>
