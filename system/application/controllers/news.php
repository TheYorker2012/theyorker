<?php
/**
 * This is the controller for the news section.
 *
 * @author Chris Travis	(cdt502 - ctravis@gmail.com)
 * @author Neil Brehon	(nb525)
 */
class News extends Controller {

	/**
	 * @brief Default Constructor.
	 */
	function __construct()
	{
		parent::Controller();

		// Load the public frame
		$this->load->library('frame_public');
		$this->load->model('News_model');
	}

    /// The Campus News section (default).
	function index()
	{
    	/// Get the latest article ids from the model.
    	$latest_article_ids = $this->News_model->GetLatestId(1,9);

    	/// Get all of the latest article
    	$main_article = $this->News_model->GetFullArticle($latest_article_ids[0]);

    	/// Get some of the 2nd- and 3rd-latest articles
    	$news_previews = array();
    	for ($index = 1; $index <= 2 && $index < count($latest_article_ids); $index++)
    	{
        	array_push($news_previews, $this->News_model->GetSummaryArticle($latest_article_ids[$index]));
    	}

    	/// Get less of the next 6 newest articles
    	$news_others = array();
    	for ($index = 3; $index < count($latest_article_ids); $index++)
    	{
        	array_push($news_others, $this->News_model->GetSimpleArticle($latest_article_ids[$index]));
    	}

    	/// Gather all the data into an array to be passed to the view
		$data = array(
       	    'main_article' => $main_article,
			'news_previews' => $news_previews,
			'news_others' => $news_others
		);

		/// Wikiparse the article's body text and fact boxes
		$this->load->library('wikiparser');
		$data['main_article']['text'] = $this->wikiparser->parse($data['main_article']['text']);

		foreach ($data['main_article']['fact_boxes'] as &$fact_box)
		{
			$fact_box = $this->wikiparser->parse($fact_box['wikitext']);
		}

		/// Temporarily fill in a few gaps in the model data
		$data['main_article']['image'] = '/images/prototype/news/thumb1.jpg';
		$data['main_article']['image_description'] = 'temp image';
		$data['main_article']['writer'] = 'Temp Name';

		for ($i = 0; $i < count($data['news_previews']); $i++)
		{
    		$data['news_previews'][$i]['image'] = '/images/prototype/news/thumb2.jpg';
    		$data['news_previews'][$i]['image_description'] = 'temp image';
    		$data['news_previews'][$i]['writer'] = 'Temp Name';
		}

		for ($i = 0; $i < count($data['news_others']); $i++)
		{
    		$data['news_others'][$i]['image'] = '/images/prototype/news/thumb3.jpg';
    		$data['news_others'][$i]['image_description'] = 'temp image';
    		$data['news_others'][$i]['writer'] = 'Temp Name';
		}

		// Set up the public frame
		$this->frame_public->SetTitle('Campus News');
		$this->frame_public->SetContentSimple('news/news', $data);

		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}

	/// The National News section.
	function national()
	{
		$data = array(
			'news_previews' => self::$national_data
		);

		// Set up the public frame
		$this->frame_public->SetTitle('National News');
		$this->frame_public->SetContentSimple('news/national', $data);

		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}

	/// The Features section.
	function features()
	{
    	/// Get the latest article ids from the model.
    	$latest_article_ids = $this->News_model->GetLatestId(2,9);

    	/// Get all of the latest article
    	$main_article = $this->News_model->GetFullArticle($latest_article_ids[0]);

    	/// Get some of the 2nd- and 3rd-latest articles
    	$news_previews = array();
    	for ($index = 1; $index <= 2 && $index < count($latest_article_ids); $index++)
    	{
        	array_push($news_previews, $this->News_model->GetSummaryArticle($latest_article_ids[$index]));
    	}

    	/// Get less of the next 6 newest articles
    	$news_others = array();
    	for ($index = 3; $index < count($latest_article_ids); $index++)
    	{
        	array_push($news_others, $this->News_model->GetSimpleArticle($latest_article_ids[$index]));
    	}

    	/// The data passed to the view will come from the database once it is available.
		$data = array(
		    'main_article' => $main_article,
			'news_previews' => $news_previews,
			'news_others' => $news_others
		);

		/// Wikiparse the article's body text
		$this->load->library('wikiparser');
		$data['main_article']['text'] = $this->wikiparser->parse($data['main_article']['text']);

		/// Temporarily fill in a few gaps in the model data
		$data['main_article']['image'] = '/images/prototype/news/thumb1.jpg';
		$data['main_article']['image_description'] = 'temp image';
		$data['main_article']['writer'] = 'Temp Name';

		for ($i = 0; $i < count($data['news_previews']); $i++)
		{
    		$data['news_previews'][$i]['image'] = '/images/prototype/news/thumb2.jpg';
    		$data['news_previews'][$i]['image_description'] = 'temp image';
    		$data['news_previews'][$i]['writer'] = 'Temp Name';
		}

		for ($i = 0; $i < count($data['news_others']); $i++)
		{
    		$data['news_others'][$i]['image'] = '/images/prototype/news/thumb3.jpg';
    		$data['news_others'][$i]['image_description'] = 'temp image';
    		$data['news_others'][$i]['writer'] = 'Temp Name';
		}

		// Set up the public frame
		$this->frame_public->SetTitle('Features');
		$this->frame_public->SetContentSimple('news/features', $data);

		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}

	/// The Lifestyle section.
	function lifestyle()
	{
		// Set up the public frame
		$this->frame_public->SetTitle('Lifestyle');
		$this->frame_public->SetContentSimple('news/lifestyle');

		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}

	/// General controller for all news articles.
	function article()
	{
    	/// Load the library for parsing wikitext
    	$this->load->library('wikiparser');

    	/// Fetch the requested article from the model
    	$data = $this->News_model->GetFullArticle($this->uri->segment(3));

    	/// Format the relevant text with wikiparser
    	$data['text'] = $this->wikiparser->parse($data['text']);

    	/// Temporarily fill in a few gaps in the model data
    	$data['writer_id'] = 1;
		$data['writer_name'] = 'Temp Name';
		$data['related_articles'] = array();

		// Set up the public frame
		$this->frame_public->SetTitle('Article');
		$this->frame_public->SetContentSimple('news/article', $data);

		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}

	/// The Archive section.
	function archive()
	{
		// Set up the public frame
		$this->frame_public->SetTitle('Archive');
		$this->frame_public->SetContentSimple('news/archive');

		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}

	/// RSS Feed Generation
	function rss()
	{
		$data['rss_title'] = 'York Uni Campus News';
		$data['rss_link'] = 'http://www.theyorker.co.uk/news/';
		$data['rss_desc'] = 'All the news you need to know about from York University\'s Campus!';
		$data['rss_category'] = 'News';
		$data['rss_pubdate'] = 'Thu, 14 Dec 2006 00:00:01 GMT';
		$data['rss_lastbuild'] = 'Tue, 10 Jun 2003 09:41:01 GMT';
		$data['rss_image'] = 'http://www.theyorker.co.uk/images/prototype/news/rss-campus.jpg';
		$data['rss_width'] = '274';
		$data['rss_height'] = '108';
		$data['rss_email_ed'] = 'news@theyorker.co.uk';
		$data['rss_email_web'] = 'webmaster@theyorker.co.uk';

		/// Get latest article ids
		$latest_article_ids = $this->News_model->GetLatestId(1,9);

		/// Get preview data for articles
		$data['rss_items'] = array();
		foreach ($latest_article_ids as $id)
		{
    		array_push($data['rss_items'], $this->News_model->GetSummaryArticle($id));
		}

		$this->load->view('news/rss', $data);
	}


    /// test data for use until we can use the database (example national news)
	private static $national_data = array(
		array(
			'link' => 'http://news.bbc.co.uk/go/rss/-/1/hi/uk/6186194.stm',
            'image' => '/images/prototype/news/bbc_news.gif',
            'image_description' => 'Taken from BBC News',
            'headline' => 'Ex-spy death inquiry stepped up',
            'writer' => 'Google',
            'date' => '5th December 2006',
            'subtext' => 'Police step up inquiries into the death of Russian ex-spy Alexander Litvinenko, with officers due to fly to Moscow.'
		),
		array(
			'link' => 'http://news.bbc.co.uk/go/rss/-/1/hi/uk_politics/6186348.stm',
            'image' => '/images/prototype/news/bbc_news.gif',
            'image_description' => 'Taken from BBC News',
            'headline' => 'Olympics audio surveillance row',
            'writer' => 'Google',
            'date' => '5th December 2006',
            'subtext' => 'A police plan to use high-powered microphones to help the Olympics 2012 security is opposed by David Blunkett.'
		),
		array(
			'link' => 'http://news.bbc.co.uk/go/rss/-/1/hi/england/dorset/6186284.stm',
            'image' => '/images/prototype/news/bbc_news.gif',
            'image_description' => 'Taken from BBC News',
            'headline' => 'Missing boy search scaled down',
            'writer' => 'Google',
            'date' => '5th December 2006',
            'subtext' => 'The search for a boy who is missing after the rowing boat he stole with a friend capsized is scaled down overnight.'
		),
	);
}
?>
