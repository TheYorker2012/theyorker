<?php

/**
 * This model retrieves data about articles from the db.
 *
 * @author James Hogan (jh559@cs.york.ac.uk)
 */
class Articles_model extends Model {

	function __construct()
	{
		// Call the Model constructor
		parent::Model();
	}

	/// Update an article's wikitext cache if it needs updating
	/**
	 * @param $Data data from database.
	 * @pre @a $Data must include the following keys:
	 *	- 'article_content_id'
	 *	- 'article_content_wikitext'
	 *	- 'article_content_wikitext_cache'
	 * @return @a $Data['article_content_wikitext_cache']
	 */
	function UpdateArticleWikitextCache(&$Data)
	{
		if ($Data['article_content_wikitext_cache'] === NULL) {
			// Parse the wikitext
			$this->load->library('wikiparser');
			$Data['article_content_wikitext_cache'] = $this->wikiparser->parse(
					$Data['article_content_wikitext']
				);
			// Update the database
			$sql =
				'UPDATE article_contents '.
				'SET article_contents.article_content_wikitext_cache=? '.
				'WHERE article_contents.article_content_id=?';
			$query = $this->db->query($sql,
				array(
					$Data['article_content_wikitext_cache'],
					$Data['article_content_id'],
				)
			);
		}
		return $Data['article_content_wikitext_cache'];
	}

	/// Get an organisation's reviews.
	/**
	 * @param $DirectoryEntryName string Directory entry name of the organisation.
	 * @return array[review].
	 *
	 * Perhaps this is in the wrong place.
	 * It is used by yorkerdirectory.reviews()
	 */
	function GetDirectoryOrganisationReviewsByEntryName($DirectoryEntryName)
	{
		$sql =
			'SELECT'.
			' articles.article_id,'.
			' articles.article_publish_date,'.
			' content_types.content_type_name,'.
			' article_contents.article_content_id,'.
			' article_contents.article_content_wikitext,'.
			' article_contents.article_content_wikitext_cache,'.
			' business_cards.business_card_name,'.
			' business_cards.business_card_email '.
			//' users.user_image_id '.
			'FROM articles '.
			'INNER JOIN organisations '.
			' ON organisations.organisation_entity_id = articles.article_organisation_entity_id '.
			'INNER JOIN organisation_types '.
			' ON organisations.organisation_organisation_type_id = organisation_types.organisation_type_id '.
			'INNER JOIN article_contents '.
			' ON articles.article_live_content_id = article_contents.article_content_id '.
			'LEFT JOIN content_types '.
			' ON articles.article_content_type_id = content_types.content_type_id '.
			'INNER JOIN article_writers '.
			' ON articles.article_id = article_writers.article_writer_article_id '.
			'INNER JOIN business_cards '.
			' ON article_writers.article_writer_byline_business_card_id = business_cards.business_card_id '.
			'WHERE organisations.organisation_directory_entry_name=? '.
			' AND organisation_types.organisation_type_directory=1 ';
			//' AND reviews.rto_content_type_id IS NULL ';

		$query = $this->db->query($sql, $DirectoryEntryName);
		$authors = $query->result_array();
		$reviews = array();
		// Go through all authors, each will have the rest
		foreach ($authors as $data) {
			// If the review isn't there, get the main data
			if (!array_key_exists($data['article_id'], $reviews)) {
				$reviews[$data['article_id']] = array(
					'type' => $data['content_type_name'],
					'publish_date' => $data['article_publish_date'],
					'content' => $this->UpdateArticleWikitextCache($data),
					/// @todo Where is this link supposed to point?
					'link' => '/news/archive/reporter/2',
					'authors' => array(),
				);
			}
			// Now add the author
			$reviews[$data['article_id']]['authors'][] = array(
				'name' => $data['business_card_name'],
				'email' => $data['business_card_email'],
			);
		}

		// Return the full array of reviews
		return $reviews;
	}

}
?>