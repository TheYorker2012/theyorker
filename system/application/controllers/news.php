<?php
/**
 * This is the controller for the news section.
 *
 * @author Chris Travis, Neil Brehon
 */
class News extends Controller {
    
    /// The Campus News section (default).
	function index()
	{
    	
    	/// test "news preview" data for use until we can use the database
        $preview_data = array(
            array(
                'id' => '8',
                'image' => '/images/prototype/news/thumb1.jpg',
                'image_description' => 'Soldier about to get run over by a tank',
                'headline' => 'Israel vows ceasefire \'patience\'',
                'writer_id' => '2',
                'writer' => 'Matthew Tole',
                'date' => '5th December 2006',
                'blurb' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nunc elementum arcu non risus. Vestibulum arcu enim, placerat nec, malesuada eget, pharetra at, mi. Nullam rhoncus porttitor nunc.'
            ),
            array(
                'id' => '7',
                'image' => '/images/prototype/news/thumb2.jpg',
                'image_description' => 'Tony Blair',
                'headline' => 'Blair \'sorrow\' over slave trade',
                'writer' => 'Jo Shelley',
                'date' => '4th December 2006',
                'blurb' => 'Prime Minister Tony Blair has said he feels "deep sorrow" for Britain\'s role in the slave trade. In an article for the New Nation newspaper, the prime minister said it had been "profoundly shameful".'
            ),
            array(
                'id' => '6',
                'image' => '/images/prototype/news/thumb3.jpg',
                'image_description' => 'Some Spy',
                'headline' => 'Advice sought after ex-spy death',
                'writer' => 'Dan Ashby',
                'date' => '3rd December 2006',
                'blurb' => 'Hundreds of people have called the NHS Direct hotline following the death of Russian ex-spy Alexander Litvinenko.'
            )
        );
        
        /// test "other news" data for use until we can use the database
        $other_data = array(
            array(
                'id' => '5',
                'image' => '/images/prototype/news/thumb3.jpg',
                'image_description' => 'Some Spy',
                'headline' => 'Ex-spy death inquiry stepped up.',
                'writer' => 'Jo Shelley',
                'date' => '2nd December 2006'
            ),
            array(
                'id' => '4',
                'image' => '/images/prototype/news/thumb2.jpg',
                'image_description' => 'Tony Blair',
                'headline' => 'Tony Blair finds £10, keeps it.',
                'writer' => 'Jo Shelley',
                'date' => '1st December 2006'
            ),
            array(
                'id' => '3',
                'image' => '/images/prototype/news/thumb9.jpg',
                'image_description' => 'Man in a wig',
                'headline' => 'Mass panic as world ends twice in one day.',
                'writer' => 'Neil Brehon',
                'date' => '30th November 2006'
            ),
            array(
                'id' => '2',
                'image' => '/images/prototype/news/thumb3.jpg',
                'image_description' => 'Some Spy',
                'headline' => 'Ex-spy death inquiry stepped up again.',
                'writer' => 'Owen Jones',
                'date' => '29th November 2006'
            ),
            array(
                'id' => '1',
                'image' => '/images/prototype/news/thumb2.jpg',
                'image_description' => 'Some Spy',
                'headline' => 'Ex-spy death inquiry stepped up.',
                'writer' => 'Owen Jones',
                'date' => '28th November 2006'
            ),
        );
        
    	/// The data passed to the view will come from the database once it is available.
		$data = array(
			'content_view' => 'news/news.php',
			'news_previews' => $preview_data,
			'news_others' => $other_data
		);
		$this->load->view('frames/student_frame',$data);
	}

	/// The National News section.
	function national()
	{
		$data = array(
			'content_view' => 'news/national.php'
		);
		$this->load->view('frames/student_frame',$data);
	}

	/// The Features section.
	function features()
	{
		$this->index();
	}

	/// The Lifestyle section.
	function lifestyle()
	{
		$data = array(
			'content_view' => 'news/lifestyle.php'
		);
		$this->load->view('frames/student_frame',$data);
	}

	/// General controller for all news articles.
	function article()
	{
		$data = array(
			'content_view' => 'news/article.php'
		);
		$this->load->view('frames/student_frame',$data);
	}
	
	/// The Archive section.
	function archive()
	{
		$data = array(
			  'content_view' => 'news/archive.php'
		);
		$this->load->view('frames/student_frame',$data);
	}
}
?>
