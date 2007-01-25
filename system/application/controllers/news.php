<?php
/**
 * This is the controller for the news section.
 *
 * @author Chris Travis, Neil Brehon
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
		$this->frame_public->SetTitle('Campus News');
		$this->frame_public->SetContentSimple('news/news', $data);

		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}

	/// The National News section.
	function national()
	{
		$data = array(
			'news_previews' => self::$national_data,
			'news_others' => array_slice(self::$news_data, 3)
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
    	/// The data passed to the view will come from the database once it is available.
		$data = array(
			'news_previews' => array_slice(self::$news_data, 0, 3),
			'news_others' => array_slice(self::$news_data, 3)
		);
    	
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
		$data['rss_title'] = 'Campus News';
		$data['rss_link'] = 'http://www.theyorker.co.uk/news/';
		$data['rss_desc'] = 'All the news you need to know about from York University\'s Campus!';
		$data['rss_category'] = 'News';
		$data['rss_pubdate'] = 'Thu, 14 Dec 2006 00:00:01 GMT';
		$data['rss_lastbuild'] = 'Tue, 10 Jun 2003 09:41:01 GMT';
		$data['rss_image'] = 'http://localhost/images/prototype/news/rss-campus.jpg';
		$data['rss_width'] = '274';
		$data['rss_height'] = '108';
		$data['rss_email_ed'] = 'news@theyorker.co.uk';
		$data['rss_email_web'] = 'webmaster@theyorker.co.uk';
		$data['rss_items'] = self::$news_data;

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

    /// test data for use until we can use the database (latest 9 stories)
    private static $news_data = array(
        array(
            'id' => '8',
            'image' => '/images/prototype/news/thumb1.jpg',
            'image_description' => 'Soldier about to get run over by a tank',
            'headline' => 'Israel vows ceasefire \'patience\'',
            'writer_id' => '2',
            'writer' => 'Matthew Tole',
            'date' => '5th December 2006',
            'subtext' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.<br /><br />Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nulla consequat, orci vel iaculis sagittis, felis elit malesuada massa, vel scelerisque dui erat vel ipsum. Etiam nec massa. Suspendisse risus nunc, tincidunt vel, porttitor at, molestie rhoncus, odio. In hac habitasse platea dictumst. In enim nibh, scelerisque sit amet, posuere ut, sagittis a, ipsum. <blockquote>Testing new quotey thing to see how sexual it is, 1337 roflmao n00bz0r!</blockquote> Mauris vulputate. Cras neque enim, sagittis vel, varius vel, congue ac, purus. Duis imperdiet, purus eu aliquet posuere, turpis diam nonummy nulla, non ultricies lectus lacus id urna. Sed suscipit, libero id pretium dapibus, turpis enim elementum ligula, eu tempus turpis turpis a elit.<br /><br />Pellentesque posuere mauris et ante tempus cursus. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Duis egestas facilisis nibh. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos hymenaeos. Sed egestas nulla ac lorem. Aliquam nunc. Maecenas tincidunt venenatis sem. Nunc sit amet risus. Aliquam ultrices rhoncus sem. Praesent cursus. Ut hendrerit nunc. Curabitur congue rutrum felis. Nunc nisi leo, porta in, vulputate vitae, fringilla id, enim. Integer mollis. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos hymenaeos. Fusce volutpat lacinia ante. Vestibulum semper ipsum vel nibh. Mauris luctus, nulla non hendrerit euismod, lectus nunc accumsan odio, ut laoreet orci ligula nec felis. Vestibulum lectus.<br /><br />Nulla facilisi. Sed erat eros, gravida at, interdum et, tincidunt vitae, nunc. Vivamus mattis justo in massa. Vivamus malesuada erat vel pede. Nulla molestie mauris vitae ipsum. Nam mauris sem, consectetuer vitae, iaculis sed, aliquam laoreet, risus. Nunc auctor. Nullam auctor. Vestibulum id diam in pede lobortis aliquet. Suspendisse sit amet ante eget diam aliquet tincidunt. Phasellus ut lacus ut augue egestas semper. Morbi urna. Nulla purus ipsum, facilisis in, blandit in, gravida non, justo.'
        ),
        array(
            'id' => '7',
            'image' => '/images/prototype/news/thumb2.jpg',
            'image_description' => 'Tony Blair',
            'headline' => 'Blair \'sorrow\' over slave trade',
            'writer' => 'Jo Shelley',
            'date' => '4th December 2006',
            'subtext' => 'Prime Minister Tony Blair has said he feels "deep sorrow" for Britain\'s role in the slave trade. In an article for the New Nation newspaper, the prime minister said it had been "profoundly shameful".'
        ),
        array(
            'id' => '6',
            'image' => '/images/prototype/news/thumb3.jpg',
            'image_description' => 'Some Spy',
            'headline' => 'Advice sought after ex-spy death',
            'writer' => 'Dan Ashby',
            'date' => '3rd December 2006',
            'subtext' => 'Hundreds of people have called the NHS Direct hotline following the death of Russian ex-spy Alexander Litvinenko.'
        ),
        array(
            'id' => '5',
            'image' => '/images/prototype/news/thumb3.jpg',
            'image_description' => 'Some Spy',
            'headline' => 'Ex-spy death inquiry stepped up',
            'writer' => 'Jo Shelley',
            'date' => '2nd December 2006',
            'subtext' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.'
        ),
        array(
            'id' => '4',
            'image' => '/images/prototype/news/thumb2.jpg',
            'image_description' => 'Tony Blair',
            'headline' => 'Tony Blair finds 10 items, keeps them all',
            'writer' => 'Jo Shelley',
            'date' => '1st December 2006',
            'subtext' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.'
        ),
        array(
            'id' => '3',
            'image' => '/images/prototype/news/thumb9.jpg',
            'image_description' => 'Man in a wig',
            'headline' => 'Mass panic as world ends twice in one day',
            'writer' => 'Neil Brehon',
            'date' => '30th November 2006',
            'subtext' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.'
        ),
        array(
            'id' => '2',
            'image' => '/images/prototype/news/thumb3.jpg',
            'image_description' => 'Some Spy',
            'headline' => 'Ex-spy death inquiry stepped up again',
            'writer' => 'Owen Jones',
            'date' => '29th November 2006',
            'subtext' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.'
        ),
        array(
            'id' => '1',
            'image' => '/images/prototype/news/thumb2.jpg',
            'image_description' => 'Some Spy',
            'headline' => 'Ex-spy death inquiry stepped up',
            'writer' => 'Owen Jones',
            'date' => '28th November 2006',
            'subtext' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.'
        ),
        array(
            'id' => '1',
            'image' => '/images/prototype/news/thumb2.jpg',
            'image_description' => 'Some Spy',
            'headline' => 'Ex-spy death inquiry stepped up',
            'writer' => 'Owen Jones',
            'date' => '28th November 2006',
            'subtext' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.'
        ),
    );
}
?>
