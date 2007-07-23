<?php
/**
 * This model does queries for the searches.
 *
 * @author Mark Goodall (mg512)
 * 
 */

// To add a new filter:
// 1. use a new bit in a define and name it well
// 2. add a new if statement to full and ajax functions, with desired query

define("ORDER_RELEVANCE", 		1);
define("ORDER_EARLY", 			2);
define("ORDER_LATE", 			3);

define("FILTER_ALL", 			16777216);
define("FILTER_ALL_ARTICLES", 	511);
define("FILTER_NEWS", 			1);
define("FILTER_FEATURES", 		2);
define("FILTER_LIFESTYLE", 		4);
define("FILTER_FOOD",			8);
define("FILTER_DRINK", 			16);
define("FILTER_unused",			32);	//unused
define("FILTER_ARTS",			64);
define("FILTER_SPORTS",			128);
define("FILTER_BLOGS",			256);
define("FILTER_YORK",			512);
define("FILTER_DIR",			1024);
define("FILTER_EVENTS",			2048);

class Orwell extends Model {
	function Orwell()
	{
		//Call the Model Constructor
		parent::Model();
	}
	


	/**
	 * @brief Do a full search
	 * @param $string string Search string entered.
	 * @param $ordering integer Ordering of the search, if defined.
	 * @param $filter integer Filtering of the search, if defined.
	 */
	function full($string, $limit = 10, $offset = 0, $ordering = ORDER_RELEVANCE, $filter = FILTER_ALL) {
		function ordering_addition($ordering) {
			switch($ordering) {
				case ORDER_RELEVANCE: return 'ORDER BY relevance DESC ';
				break;
				case ORDER_EARLY: return 'ORDER BY date ASC ';
				break;
				case ORDER_LATE: return 'ORDER BY date DESC ';
				break;
				default: return '';
			}
		}
		
		$result = array();
		if ($filter & FILTER_ALL_ARTICLES) {
			$sql = <<<QUERY
SELECT articles.article_id AS id,
	UNIX_TIMESTAMP(articles.article_publish_date) AS date,
	content_types.content_type_codename AS type_codename,
	parent_type.content_type_codename AS parent_codename,
	IF (content_types.content_type_parent_content_type_id IS NOT NULL,
		CONCAT(parent_type.content_type_name, " - ", content_types.content_type_name),
		content_types.content_type_name) AS type_name,
	article_contents.article_content_heading AS heading,
	article_contents.article_content_blurb AS blurb,
	photo_requests.photo_request_chosen_photo_id AS photo_id,
	photo_requests.photo_request_title AS photo_title
	MATCH(article_content_heading, article_content_subheading, article_contents.article_content_blurb,article_content_wikitext_cache)
		AGAINST(?) as score,
FROM articles 
LEFT JOIN photo_requests 
	ON ( articles.article_thumbnail_photo_id = photo_requests.photo_request_relative_photo_number
		AND articles.article_id = photo_requests.photo_request_article_id
		AND photo_requests.photo_request_deleted = 0
		AND photo_requests.photo_request_chosen_photo_id IS NOT NULL
		AND photo_requests.photo_request_approved_user_entity_id IS NOT NULL ),
	article_contents, content_types
LEFT JOIN content_types AS parent_type
	ON content_types.content_type_parent_content_type_id = parent_type.content_type_id
WHERE
	articles.article_content_type_id = content_types.content_type_id
	AND articles.article_pulled = 0 AND articles.article_publish_date < CURRENT_TIMESTAMP()
	AND articles.article_deleted = 0 AND articles.article_live_content_id IS NOT NULL
	AND articles.article_live_content_id = article_contents.article_content_id
	AND articles.article_id = article_contents.article_content_article_id
	AND content_types.content_type_archive = 1
	AND MATCH(article_content_heading, article_content_subheading, article_contents.article_content_blurb,article_content_wikitext_cache)
AGAINST(?)  AND (';
QUERY;
			if ($filter & FILTER_NEWS) {
				$sql.= 'content_types.content_type_codename = \'uninews\' OR parent_type.content_type_codename = \'uninews\' OR ';
			}
			if ($filter & FILTER_FEATURES) {
				$sql.= 'content_types.content_type_codename = \'features\' OR parent_type.content_type_codename = \'features\' OR ';
			}
			if ($filter & FILTER_LIFESTYLE) {
				$sql.= 'content_types.content_type_codename = \'lifestyle\' OR parent_type.content_type_codename = \'lifestyle\' OR ';
			}
			if ($filter & FILTER_FOOD) {
				$sql.= 'content_types.content_type_codename = \'food\' OR parent_type.content_type_codename = \'food\' OR ';
			}
			if ($filter & FILTER_DRINK) {
				$sql.= 'content_types.content_type_codename = \'drink\' OR parent_type.content_type_codename = \'drink\' OR ';
			}
			if ($filter & FILTER_ARTS) {
				$sql.= 'content_types.content_type_codename = \'arts\' OR parent_type.content_type_codename = \'arts\' OR ';
			}
			if ($filter & FILTER_SPORTS) {
				$sql.= 'content_types.content_type_codename = \'sport\' OR parent_type.content_type_codename = \'sport\' OR ';
			}
			if ($filter & FILTER_BLOGS) {
				$sql.= 'content_types.content_type_codename = \'blogs\' OR parent_type.content_type_codename = \'blogs\' OR ';
			}
			$sql.= 'FALSE) ';
			$sql.= ordering_addition($ordering);
			$sql.= 'LIMIT '.$offset', '.$limit;
			$result['articles'] = $this->db->query($sql, array($string, $string));
		}
		if ($filter & FILTER_YORK) {
			curl_init('http://yorkipedia.theyorker.co.uk/api.php?action=opensearch&search='.urlencode(utf8_decode($filter)));
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0.2); //possibly not valid, manual says integer only, but we wil try...
			$wikiResult = curl_exec($curl);
			$result['wiki'] = array();
			if ($wikiResult or $wikiResult != 1) {
				//example result ["Tet",["Tet","Tet-off","Tet-on","TetR","Tet 1969","Tet Corporation"]]
				preg_match_all("/[^.|\"\[\]]*/", $wikiResult, $wikiResult);
				foreach ($wikiResult as $page) if (!empty($page) & $page != ',') $result['wiki'][] = $page;
			}
		}
		if ($filter & FILTER_DIR) {
			// This query is possibly even slower than the one above, because It has to match against
			// two separate indexes!
			$sql = <<<QUERY
				SELECT
					organisations.organisation_name AS heading,
					organisations.organisation_directory_entry_name AS link,
					organisation_contents.organisation_content_description AS blurb,
					organisation_types.organisation_type_name,
					UNIX_TIMESTAMP(organisation_timestamp) AS date,
					SUM((MATCH(organisations.organisation_name) AGAINST(?)),
						(MATCH(organisation_contents.organisation_content_description) AGAINST(?)))/2 AS score
				FROM organisations
				INNER JOIN organisation_types
				ON organisations.organisation_organisation_type_id=organisation_types.organisation_type_id
				LEFT JOIN organisation_contents
				ON organisations.organisation_live_content_id = organisation_contents.organisation_content_id
				INNER JOIN entities
				ON entities.entity_id = organisations.organisation_entity_id
				AND entities.entity_deleted = 0
				WHERE organisations.organisation_directory_entry_name IS NOT NULL
					AND organisation_types.organisation_type_directory=1
					AND organisations.organisation_show_in_directory=1
					AND organisations.organisation_needs_approval=0
					AND (MATCH (organisations.organisation_name) AGAINST (?)
					OR MATCH (organisation_contents.organisation_content_description) AGAINST (?))
QUERY;
			$sql.= ordering_addition($ordering);
			$result['directory'] = $this->db->query($sql, array($string, $string, $string, $string));
		}
		if ($filter & FILTER_EVENTS) {
			// TODO hook into James' code
			$result['events'] = array();
		}
		return $result;
	}

	/**
	 * @brief Do a small search, reply with max 16 results
	 * @param $string string Search string entered.
	 */
	function ajax($string) {
		$result = array();
		$sql = <<<QUERY
		SELECT articles.article_id AS id,
			article_content_heading AS title
			UNIX_TIMESTAMP(articles.article_publish_date) AS date,
			content_types.content_type_codename AS type_codename,
			parent_type.content_type_codename AS parent_codename,
			IF (content_types.content_type_parent_content_type_id IS NOT NULL,
				parent_type.content_type_name,
				content_types.content_type_name) AS category,
			MATCH(article_content_heading, article_content_subheading, article_contents.article_content_blurb,article_content_wikitext_cache)
				AGAINST(?) as score
		FROM articles, article_contents, content_types
		LEFT JOIN content_types AS parent_type
			ON content_types.content_type_parent_content_type_id = parent_type.content_type_id
		WHERE
			articles.article_content_type_id = content_types.content_type_id
			AND articles.article_pulled = 0 AND articles.article_publish_date < CURRENT_TIMESTAMP()
			AND articles.article_deleted = 0 AND articles.article_live_content_id IS NOT NULL
			AND articles.article_live_content_id = article_contents.article_content_id
			AND articles.article_id = article_contents.article_content_article_id
			AND content_types.content_type_archive = 1
			AND MATCH(article_content_heading, article_content_subheading, article_contents.article_content_blurb,article_content_wikitext_cache)
		AGAINST(?)
		AND (category = 'uninews'
			OR category = 'features'
			OR category = 'lifestyle'
			OR category = 'food'
			OR category = 'drink'
			OR category = 'arts'
			OR category = 'sport'
			OR category = 'blogs'
		ORDER BY score DESC LIMIT 4
QUERY;
		$result['articles'] = $this->db->query($sql, array($string, $string));

		curl_init('http://yorkipedia.theyorker.co.uk/api.php?action=opensearch&search='.urlencode(utf8_decode($filter)));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 0.2); //possibly not valid, manual says integer only, but we wil try...
		$wikiResult = curl_exec($curl);
		$result['wiki'] = array();
		if ($wikiResult or $wikiResult != 1) {
			//example result ["Tet",["Tet","Tet-off","Tet-on","TetR","Tet 1969","Tet Corporation"]]
			preg_match_all("/[^.|\"\[\]]*/", $wikiResult, $wikiResult);
			foreach ($wikiResult as $page) if (!empty($page) & $page != ',') $result['wiki'][] = $page;
		}

		// This query is possibly even slower than the one above, because It has to match against
		// two separate indexes!
		$sql = <<<QUERY
			SELECT
				organisations.organisation_name AS title,
				organisations.organisation_directory_entry_name AS link,
				UNIX_TIMESTAMP(organisation_timestamp) AS date,
				((MATCH(organisations.organisation_name) AGAINST(?))+
					(MATCH(organisation_contents.organisation_content_description) AGAINST(?))) AS score
			FROM organisations
			INNER JOIN organisation_types
			ON organisations.organisation_organisation_type_id=organisation_types.organisation_type_id
			LEFT JOIN organisation_contents
			ON organisations.organisation_live_content_id = organisation_contents.organisation_content_id
			INNER JOIN entities
			ON entities.entity_id = organisations.organisation_entity_id
			AND entities.entity_deleted = 0
			WHERE organisations.organisation_directory_entry_name IS NOT NULL
				AND organisation_types.organisation_type_directory=1
				AND organisations.organisation_show_in_directory=1
				AND organisations.organisation_needs_approval=0
				AND (MATCH (organisations.organisation_name) AGAINST (?)
				 OR MATCH (organisation_contents.organisation_content_description) AGAINST (?))
			ORDER BY score DESC LIMIT 4
QUERY;
		$result['directory'] = $this->db->query($sql, array($string, $string, $string, $string));

		$this->load->library('calendar_backend');
		$this->load->library('calendar_source_my_calendar');
		$this->load->library('facebook');
		$source = new CalendarSourceMyCalendar();
		// Use the search phrase
		$source->SetSearchPhrase($string);

		$source->EnableGroup('all');
		$calendar_data = new CalendarData();
		$source->FetchEvents($calendar_data);
		$result['events'] = $calendar_data->GetEvents();

		return $result;
	}
}
?>