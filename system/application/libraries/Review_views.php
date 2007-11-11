<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @file Review_views.php
 * @brief Library for displaying reviews from multiple controllers.
 * @author James Hogan (jh559@cs.york.ac.uk)
 *
 * The primary purpose of this is to allow previewing in the office without
 * duplication of code.
 */

/// Reviews helper library.
class Review_views
{
	/// bool Display comments?
	protected $mCommentsEnabled = TRUE;

	/// int Revision number to display.
	protected $mRevisionId = -1;

	/// Disable comments.
	function DisableComments()
	{
		$this->mCommentsEnabled = FALSE;
	}

	/// Set the content revision id to display.
	function SetRevision($RevisionId)
	{
		$this->mRevisionId = $RevisionId;
	}

	/// Display a review page
	function DisplayReview($content_type, $organisation_name, $IncludedComment = 0)
	{
		$CI = & get_instance();

		//Load news model
		$CI->load->model('News_model');
		$CI->load->model('Review_model');
		$CI->load->model('Slideshow');

		//Set page code
		$CI->pages_model->SetPageCode('review_context');

		//Pass content_type to view
		$data['content_type'] = $content_type;

		//This needs to be altered to throw errors incase of unknown content_types...
		$content_id = $CI->Review_model->GetContentTypeID($content_type);

		//Find our article_id
		$article_id = $CI->Review_model->GetArticleID($organisation_name,$content_id);

		$data['organisation_id'] = $CI->Review_model->FindOrganisationID($organisation_name);
		$data['type_id'] 	= $content_id;

		if ($this->mCommentsEnabled) {
			$CI->load->library('comment_views');
			$CI->comment_views->SetUri('/reviews/'.$content_type.'/'.$organisation_name.'/');
			$thread = $CI->Review_model->GetReviewContextCommentThread($data['organisation_id'], $content_id);
			$data['comments'] = $CI->comment_views->CreateStandard($thread, $IncludedComment);
		}

		//Load bylines support
		$CI->load->library('byline');

		$article = array();
		//Get the article for each article on the page
		for ($article_no = 0; $article_no < count($article_id); $article_no++)
		{
			//Load article from news model
			$article_database_result = $CI->News_model->GetFullArticle($article_id[$article_no]);

			//Bylines
			$article[$article_no]['authors'] = $article_database_result['authors'];
			$article[$article_no]['date'] = $article_database_result['date'];

			//The rest
			$article[$article_no]['heading'] = $article_database_result['heading'];
			$article[$article_no]['text'] = $article_database_result['text'];

		}

		//Place articles into the data array to be passed along
		$data['article'] = $article;

		//Review context content
		$review_database_result = $CI->Review_model->GetReview($organisation_name,$content_type, $this->mRevisionId);
		$review_database_result = $review_database_result[0]; //Unique so just first row

		/// If there are no reviews for this particular section then show a page anyway
		$CI->load->model('slideshow');
		$slideshow_array = $CI->slideshow->getPhotos($data['organisation_id']);
		$slideshow = array();

		$CI->load->library('image');
		foreach ($slideshow_array->result() as $slide){
			$slideshow[] = array(
				'title' => $slide->photo_title,
				'id' => $slide->photo_id,
				'url' => $CI->image->getPhotoURL($slide->photo_id, 'slideshow')
			);
		}
		$data['slideshow'] = $slideshow;

		$data['article_id'] = $article_id;
		$data['review_title'] 			= $review_database_result['organisation_name'];
		$data['review_blurb']			= $review_database_result['review_context_content_blurb'];
		$data['review_quote']			= $review_database_result['review_context_content_quote'];
		$data['email'] 				= $review_database_result['organisation_email_address'];
		$data['organisation_description'] = $review_database_result['organisation_description'];
		$data['address_main']			= $review_database_result['organisation_postal_address'];
		$data['address_postcode']		= $review_database_result['organisation_postcode'];
		$data['website']				= $review_database_result['organisation_url'];
		$data['telephone']				= $review_database_result['organisation_phone_external'];
		$data['average_price']			= ''.$review_database_result['review_context_content_average_price'];
		$data['review_rating'] 			= $review_database_result['review_context_content_rating'];
		$data['opening_times']			= $review_database_result['organisation_opening_hours'];
		$data['yorker_recommendation']	= $review_database_result['review_context_content_recommend_item'];
		$data['serving_times']			= $review_database_result['review_context_content_serving_times'];

		$CI->main_frame->SetExtraHead('
		<script type="text/javascript" src="/javascript/prototype.js"></script>
		<script type="text/javascript" src="/javascript/scriptaculous.js"></script>
		<script src="/javascript/slideshow_new.js" type="text/javascript"></script>
		');

		//Set title parameters
		$CI->main_frame->SetTitleParameters(array(
			'content_type' => $content_type,
			'organisation' => $review_database_result['organisation_name'],
		));

		// Load the public frame view (which will load the content view)
		$CI->main_frame->SetContentSimple('reviews/mainreview', $data);

	}
}

?>
