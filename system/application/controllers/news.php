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
	}
	
    /// The Campus News section (default).
	function index()
	{
    	/// The data passed to the view will come from the database once it is available.
		$data = array(
			'news_previews' => array_slice(self::$news_data, 0, 3),
			'news_others' => array_slice(self::$news_data, 3)
		);
		
		// Set up the public frame
		$this->frame_public->SetTitle('Campus News');
		$this->frame_public->SetContentSimple('news/news', $data);
		
		// Load the public frame view (which will load the content view)
		$this->frame_public->Load();
	}

	/// The National News section.
	function national()
	{
		// Set up the public frame
		$this->frame_public->SetTitle('National News');
		$this->frame_public->SetContentSimple('news/national');
		
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
		$this->frame_public->SetContentSimple('news/news', $data);
		
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
    	
    	/// The data passed to the view will come from the database once it is available.
    	switch ($this->uri->segment(3))
    	{
        	case '3':
        	    $data = self::$article3_data;
        	    break;
        	default:
        	    $data = self::$article_data;
        	    break;
        }
    	
    	/// Format the relevant text with wikiparser
    	$data['headline'] = $this->wikiparser->parse($data['headline']);
    	$data['subheading'] = $this->wikiparser->parse($data['subheading']);
    	$data['body'] = $this->wikiparser->parse($data['body']);
    	
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
	
    /// test data for use until we can use the database (latest 8 stories)
    private static $news_data = array(
        array(
            'id' => '8',
            'image' => '/images/prototype/news/thumb1.jpg',
            'image_description' => 'Soldier about to get run over by a tank',
            'headline' => 'Israel vows ceasefire \'patience\'',
            'writer_id' => '2',
            'writer' => 'Matthew Tole',
            'date' => '5th December 2006',
            'subtext' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.'
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
            'headline' => 'Ex-spy death inquiry stepped up.',
            'writer' => 'Jo Shelley',
            'date' => '2nd December 2006',
            'subtext' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.'
        ),
        array(
            'id' => '4',
            'image' => '/images/prototype/news/thumb2.jpg',
            'image_description' => 'Tony Blair',
            'headline' => 'Tony Blair finds 10 items, keeps them all.',
            'writer' => 'Jo Shelley',
            'date' => '1st December 2006',
            'subtext' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.'
        ),
        array(
            'id' => '3',
            'image' => '/images/prototype/news/thumb9.jpg',
            'image_description' => 'Man in a wig',
            'headline' => 'Mass panic as world ends twice in one day.',
            'writer' => 'Neil Brehon',
            'date' => '30th November 2006',
            'subtext' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.'
        ),
        array(
            'id' => '2',
            'image' => '/images/prototype/news/thumb3.jpg',
            'image_description' => 'Some Spy',
            'headline' => 'Ex-spy death inquiry stepped up again.',
            'writer' => 'Owen Jones',
            'date' => '29th November 2006',
            'subtext' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.'
        ),
        array(
            'id' => '1',
            'image' => '/images/prototype/news/thumb2.jpg',
            'image_description' => 'Some Spy',
            'headline' => 'Ex-spy death inquiry stepped up.',
            'writer' => 'Owen Jones',
            'date' => '28th November 2006',
            'subtext' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.'
        ),
    );
    
    /// test data for use until we can use the database (article)
    private static $article_data = array(
        'id' => '1',
        'timestamp' => '1165538071',
        'headline' => 'REPORT SHOWS THAT STUDENTS ARE 90% HUNGRY',
        'subheading' => 'He\'s been shagging girls for years',
        'subtext' => 'Hundreds of people have called the NHS Direct hotline following the death of Russian ex-spy Alexander Litvinenko.',
        'writer_id' => '2',
        'writer_name' => 'IAN BENEST',
        'writer_image' => '/images/prototype/news/benest.png',
        'pull_quotes' => array(
            array(
                'name' => 'Supt Darren Curtis',
                'text' => 'Clearly there is a possibility that for one reason or another he is on land and has not come to our notice'
            ),
            array(
                'name' => 'John Houston - Buchanan View Resident',
                'text' => 'We got back in at three o\'clock in the morning with no explanation'
            )
        ),
        'factbox_title' => 'UK Facts',
        'factbox_contents' => '<ul class=\'ArticleFacts\'>
		 <li><b>Full name:</b> United Kingdom of Great Britain and Northern Ireland</li>
		 <li><b>Population:</b> 60.2 million (National Statistics, 2005)</li>
		 <li><b>Capital:</b> London</li>
		 <li><b>Area:</b> 242,514 sq km (93,638 sq miles)</li>
		 <li><b>Major language:</b> English</li>
		 <li><b>Major religion:</b> Christianity</li>
		 <li><b>Life expectancy:</b> 76 years (men), 81 years (women) (UN)</li>
		 <li><b>Monetary unit:</b> 1 pound sterling = 100 pence</li>
		 <li><b>Main exports:</b> Manufactured goods, chemicals, foodstuffs</li>
		 <li><b>GNI per capita:</b> US $37,600 (World Bank, 2006)</li>
		 <li><b>Internet domain:</b> .uk</li>
		 <li><b>International dialling code:</b> +44</li>
 		 </ul>',
        'body' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc
elementum arcu non risus. The Yorker Vestibulum arcu enim, placerat nec,
malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.
Phasellus semper. Sed lobortis porta purus. Morbi egestas elit vitae
magna. Morbi mollis consequat diam. Phasellus mauris. Pellentesque
non tortor. Morbi sit amet lorem eu nisl sollicitudin fringilla.

===Heading===
Nulla a nibh et tortor dapibus auctor. Morbi semper libero. Pellentesque volutpat, velit consequat hendrerit blandit, tellus orci imperdiet nisl, sit amet sodales risus augue aliquet turpis. Aliquam a sapien. In hac habitasse platea dictumst. Nulla elit. Nulla facilisi. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nunc ac odio ac nisi malesuada varius. Morbi convallis vestibulum nisl. Morbi in risus et augue varius dapibus. Nulla pulvinar libero et dui. Aenean semper. Ut fermentum, ligula nec iaculis bibendum, sem mi rutrum lorem, nec vestibulum urna velit vel ante. Cras tempus enim sed dolor. Maecenas elementum. Morbi faucibus malesuada risus. Donec condimentum facilisis nibh.

[[Image:news/CompSci.jpg]]

===Some Other Heading===
Sed sapien magna, vestibulum a, pellentesque id, tempor et, eros. Proin
ante nibh, convallis non, rutrum vel, pretium vel, lectus. Aliquam
congue malesuada augue. Duis tellus. Integer arcu odio, scelerisque
a, mattis a, interdum porta, ante. The Yorker Nulla diam. Fusce nisl sapien,
mattis quis, sagittis in, auctor id, sem. Etiam congue dolor vitae
neque. Praesent libero metus, aliquet vel, lobortis eget, porta et,
justo.

*Donec elementum lectus venenatis odio.
*Cras eget ante et urna vehicula pulvinar.
*Nam dapibus justo et nunc.
*Aenean et arcu nec tortor aliquam consectetuer.

Aenean volutpat convallis leo. Nunc varius laoreet justo. Duis vel dolor quis tortor porttitor volutpat. Proin id orci sed augue dignissim tempor. Donec diam lacus, mattis a, aliquet at, sodales vel, libero. Proin aliquam nulla id enim. In elit dui, egestas et, elementum ut, consectetuer nec, dui. Suspendisse id velit. Quisque varius lectus. Proin eget nibh. Duis consequat, augue id porttitor feugiat, erat mi aliquet nunc, dapibus dictum lectus elit at odio. Praesent nec est a quam sollicitudin consectetuer. Pellentesque porttitor turpis commodo ipsum. In ac sem. Nulla facilisi. Pellentesque tempus diam ac elit. Sed laoreet molestie nibh. Donec in sem eget ante porta aliquet.

===Another Heading===
Nulla a nibh et tortor dapibus auctor. Morbi semper libero. Pellentesque volutpat, velit consequat hendrerit blandit, tellus orci imperdiet nisl, sit amet sodales risus augue aliquet turpis. Aliquam a sapien. In hac habitasse platea dictumst. Nulla elit. Nulla facilisi. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nunc ac odio ac nisi malesuada varius. Morbi convallis vestibulum nisl. Morbi in risus et augue varius dapibus. Nulla pulvinar libero et dui. Aenean semper. Ut fermentum, ligula nec iaculis bibendum, sem mi rutrum lorem, nec vestibulum urna velit vel ante. Cras tempus enim sed dolor. Maecenas elementum. Morbi faucibus malesuada risus. Donec condimentum facilisis nibh.
'
    );
    
    /// article-specific test data (article 3)
    private static $article3_data = array(
        'id' => '3',
        'timestamp' => '1165538070',
        'headline' => 'MASS PANIC AS WORLD ENDS TWICE IN ONE DAY',
        'subheading' => 'The contents of this article are different to others.',
        'subtext' => 'Politicians attempt to restore peace in the aftermath of the latest string of total destructions of the human race.',
        'writer_id' => '2',
        'writer_name' => 'IAN BENEST',
        'writer_image' => '/images/prototype/news/benest.png',
        'pull_quotes' => array(
            array(
                'name' => 'Some Guy',
                'text' => 'Hey, man. What\'s up?'
            ),
            array(
                'name' => 'Some Other Guy',
                'text' => 'Not much, really. You know how it is.'
            ),
            array(
                'name' => 'First Guy',
                'text' => 'Yeah.'
            )
        ),
        'factbox_title' => 'World Facts',
        'factbox_contents' => '<ul class=\'ArticleFacts\'>
		 <li><b>Full name:</b> Earth</li>
		 <li><b>Population:</b> 0</li>
		 <li><b>Capital:</b> E</li>
		 <li><b>Area:</b> Unknown (too many little bits)</li>
		 <li><b>Major language:</b> None</li>
		 <li><b>Major religion:</b> None</li>
		 <li><b>Life expectancy:</b> Not really</li>
		 <li><b>Monetary unit:</b> None</li>
		 <li><b>Main exports:</b> Chunks of rock</li>
 		 </ul>',
        'body' => 'The entire human race was destroyed a total of two times yesterday. There were no survivors.

===Calm in a crisis===
The team of writers here at The Yorker are dedicated to bringing you the latest headlines, even in spite of the fact that they are dead.'
    );
}
?>
