----------------------------------------------------------------
-- Tables about entities                                      --
-- An entity is anything which may be able to log in or has   --
--	an entry in the directory                             --
----------------------------------------------------------------

CREATE TABLE entities (
	entity_id	INTEGER		NOT NULL	AUTO_INCREMENT,
	entity_username	VARCHAR(255)	NULL,
	entity_password	CHAR(32)	NULL,
	entity_entity_type_id	INTEGER		NOT NULL,
	entity_deleted		BOOL		NOT NULL	FALSE,
	entity_timestamp	TIMESTAMP	NOT NULL	CURRENT_TIMESTAMP,

	PRIMARY KEY(entity_id)
);

-- This table stores both organisations and parts of organisations (e.g. sports
--  teams)
CREATE TABLE organisations (
	organisation_entity_id	INTEGER		NOT NULL,
	organisation_organisation_type_id INTEGER	NOT NULL,
	organisation_organisation_entity_id_parent INTEGER	NULL,
	organisation_name		VARCHAR(255)	NOT NULL,
	organisation_description	TEXT		NULL,
	organisation_location	VARCHAR(15)	NULL,
	organisation_address		VARCHAR(255)	NULL,
	organisation_postcode	VARCHAR(15)	NULL,
	organisation_url		VARCHAR(255)	NULL,
	organisation_opening_hours	VARCHAR(255)	NULL,
	organisation_directory	BOOL		NOT NULL,
	organisation_events		BOOL		NOT NULL,
	organisation_yorkipedia_entry VARCHAR(255)	NULL,
	organisation_hits		INTEGER		NOT NULL,
	organisation_timestamp	TIMESTAMP	NOT NULL	CURRENT_TIMESTAMP,
	
	PRIMARY KEY(organisation_entity_id),
);

-- Stores the type of organisations e.g. an on campus society, an AU club, an
--  external organisation.  If directory is set to false, this type of
--  organisation does not appear in the directory.  
CREATE TABLE organisation_types (
	organisation_type_id		INTEGER		NOT NULL	AUTO_INCREMENT,
	organisation_type_name		VARCHAR(255)	NOT NULL,
	organisation_type_directory	BOOL		NOT NULL,

	PRIMARY KEY(organisation_type_id)
);

CREATE TABLE organisation_slideshows (
	organisation_slideshows_organisation_entity_id INTEGER		NOT NULL,
	organisation_slideshows_photo_id	INTEGER		NOT NULL,
	organisation_slideshows_order		INTEGER		NULL,

	PRIMARY KEY(organisation_entity_id, photo_id),
);

-- Organisations can be taged to create lists of related organisations etc.
CREATE TABLE organisation_tags (
	organisation_tag_organisation_entity_id	INTEGER		NOT NULL,
	organisation_tag_tag_id		INTEGER		NOT NULL,

	PRIMARY KEY(organisation_tag_organisation_entity_id, organisation_tag_tag_id),
);

-- Stores users of the yorker.	These must be members of the university.	
CREATE TABLE users (
 	user_entity_id	INTEGER		NOT NULL,
	user_college_id	INTEGER		NULL,
	user_image_id	INTEGER		NULL,
	user_firstname	VARCHAR(255)	NOT NULL,
	user_surname		VARCHAR(255)	NOT NULL,
	user_email		VARCHAR(255)	NOT NULL,
	user_nickname	VARCHAR(255)	NULL,
	user_gender		ENUM('m','f')	NULL,
	user_enrolled_year	INTEGER		NULL,
	user_store_password	BOOL		NOT NULL,
	user_permission	INTEGER		NOT NULL,
	user_office_password	CHAR(32)	NULL,
	user_timestamp	TIMESTAMP	NOT NULL	CURRENT_TIMESTAMP,

	PRIMARY KEY(user_entity_id),
);

-- The types of articles a user can write - reviews, features, news etc.
CREATE TABLE users_article_types (
	users_article_type_user_id		INTEGER		NOT NULL,
	users_article_type_article_type_id	INTEGER		NOT NULL,

	PRIMARY KEY(users_article_type_user_id, users_article_type_article_type_id),
);

-- Organisation can request to see properties of members.  
CREATE TABLE organisation_request_properties (
	organisation_request_properties_organisation_entity_id	INTEGER		NOT NULL,
	organisation_request_properties_user_property_id INTEGER	NOT NULL,
	organisation_request_properties_preferred	BOOL		NOT NULL,

	PRIMARY KEY(organisation_request_properties_organisation_entity_id, organisation_request_properties_user_property_id),
);

-- TODO: possibly work out a nice way of doing this.  
CREATE TABLE user_has_properties (
	user_has_properties_user_id		INTEGER		NOT NULL	AUTO_INCREMENT,
	user_has_properties_property_id	INTEGER		NOT NULL,
	user_has_properties_text		TEXT		NULL,
	user_has_properties_photo_id	INTEGER		NULL,
	user_has_properties_date		TIMESTAMP	NULL,
	user_has_properties_bool		BOOL		NULL,
	user_has_properties_number		FLOAT		NULL,

	PRIMARY KEY(user_has_properties_user_id, user_has_properties_property_id),
);

-- Properties which users can associate with themselves.  
CREATE TABLE user_properties (
	user_property_id		INTEGER		NOT NULL	AUTO_INCREMENT,
	user_property_property_type_id INTEGER	NULL,
	user_property_name		TEXT		NULL,

	PRIMARY KEY(user_property_id),
);

-- TODO: make this nice :)
CREATE TABLE property_types (
	property_type_id		INTEGER		NOT NULL	AUTO_INCREMENT,
	property_type_name		VARCHAR(255)	NOT NULL,
	property_type_is_user_prop	BOOL		NOT NULL,
	property_type_text		BOOL		NOT NULL,
	property_type_photo		BOOL		NOT NULL,
	property_type_image		BOOL		NOT NULL,
	property_type_date		BOOL		NOT NULL,
	property_type_bool		BOOL		NOT NULL,
	property_type_number		BOOL		NOT NULL,
	
	PRIMARY KEY(property_type_id)
);

-- Links that can appear on the homepage
CREATE TABLE links (
	link_id		INTEGER		NOT NULL	AUTO_INCREMENT,
	link_image_id	INTEGER		NOT NULL,
	link_url		VARCHAR(255)	NOT NULL,
	link_name		VARCHAR(255)	NOT NULL,
	
	PRIMARY KEY(link_id),
);

-- Stores either association between user and predifined links or custom links.
CREATE TABLE user_links (
	user_link_id		INTEGER		NOT NULL	AUTO_INCREMENT,
	user_link_user_id		INTEGER		NOT NULL,
	user_link_link_id		INTEGER		NULL,
	user_link_image_id	INTEGER		NULL,
	user_link_url		VARCHAR(255)	NULL,
	user_link_name		VARCHAR(255)	NULL,
		-- True to make a request to put a user link into predifined links.  
	user_link_request	BOOL		NULL,
	user_link_order		INTEGER		NOT NULL,
	
	PRIMARY KEY(user_link_id),
);

-- What organisations can see a users properties.  
CREATE TABLE user_subscription_properties (
	user_subscription_property_organisation_entity_id	INTEGER		NOT NULL,
	user_subscription_property_property_id	INTEGER		NOT NULL,
	user_subscription_property_user_id		INTEGER		NOT NULL,

	PRIMARY KEY(user_subscription_property_organisation_entity_id, user_subscription_property_property_id, user_subscription_property_user_id),
);

CREATE TABLE departments (
	department_id		INTEGER		NOT NULL	AUTO_INCREMENT,
	department_name		VARCHAR(255)	NOT NULL,

	PRIMARY KEY(department_id)
);

CREATE TABLE department_modules (
	department_module_department_id	INTEGER		NOT NULL,
		-- The module is an entity
	department_module_entity_id	INTEGER		NOT NULL,

	PRIMARY KEY(department_module_department_id, department_module_entity_id),
);

----------------------------------------------------------------
-- Subscription and business card related tables              --
----------------------------------------------------------------
CREATE TABLE business_cards (
	business_card_id		INTEGER		NOT NULL	AUTO_INCREMENT,
	business_card_organisation_entity_id	INTEGER		NOT NULL,
	business_card_user_id		INTEGER		NOT NULL,
	business_card_name		VARCHAR(255)	NOT NULL,
	business_card_title		VARCHAR(255)	NOT NULL,
	business_card_blurb		TEXT		NULL,
	business_card_business_card_type_id INTEGER	NOT NULL,
	business_card_email		VARCHAR(255)	NULL,
	business_card_mobile		VARCHAR(31)	NULL,
	business_card_phone_internal	VARCHAR(31)	NULL,
	business_card_phone_external	VARCHAR(31)	NULL,
	business_card_postal_address	VARCHAR(255)	NULL,
	business_card_business_card_colour_id INTEGER	NOT NULL,
	business_card_deleted		BOOL		NOT NULL,
	business_card_timestamp	TIMESTAMP	NOT NULL	CURRENT_TIMESTAMP,

	PRIMARY KEY(business_card_id),
);

CREATE TABLE business_card_colours (
	business_card_colour_id		INTEGER		NOT NULL	AUTO_INCREMENT,
	business_card_colour_name		VARCHAR(255)	NOT NULL,
	business_card_colour_background	CHAR(6)		NOT NULL,
	business_card_colour_foreground	CHAR(6)		NOT NULL,

	PRIMARY KEY(business_card_colour_id)
);

-- Title on buisiness card, may well not be related to "teams" etc.
-- TODO: discrepancy between this and design
CREATE TABLE business_card_types (
	business_card_type_id		INTEGER		NOT NULL	AUTO_INCREMENT,
	business_card_type_name		VARCHAR(255)	NOT NULL,
	business_card_type_organisation_entity_id INTEGER NULL,
	
	PRIMARY KEY(business_card_type_id),
);

CREATE TABLE subscriptions (
	subscription_organisation_entity_id INTEGER NOT NULL,
	subscription_user_entity_id INTEGER NOT NULL,
		-- Are the events on the calendar
	subscription_interested BOOL NOT NULL,
	subscription_member BOOL NOT NULL,
	subscription_paid BOOL NOT NULL,
		-- Does the organisation have access to e-mail address
	subscription_email BOOL NOT NULL,
	subscription_admin BOOL NOT NULL,
	subscription_user_confirmed BOOL NOT NULL,
	subscription_deleted BOOL NOT NULL,
	subscription_timestamp TIMESTAMP NOT NULL CURRENT_TIMESTAMP,
	PRIMARY KEY(subscription_organisation_entity_id, subscription_user_entity_id),
);

----------------------------------------------------------------
-- Article related tables                                     --
----------------------------------------------------------------
CREATE TABLE articles (
	article_id		INTEGER		NOT NULL	AUTO_INCREMENT,
	article_article_type_id	INTEGER		NOT NULL,
	article_last_editor_user_id INTEGER	NULL,
	article_created		TIMESTAMP	NOT NULL,
	article_publish_date	TIMESTAMP	NULL,
	article_initial_editor_user_id INTEGER	NULL,
	article_location	VARCHAR(15)	NULL,
	article_breaking	BOOL		NOT NULL	FALSE,
	article_pulled		BOOL		NOT NULL	FALSE,
	article_hits		INTEGER		NOT NULL	0,
	article_deleted		BOOL		NOT NULL	FALSE,
	article_timestamp	TIMESTAMP	NOT NULL	CURRENT_TIMESTAMP,
	article_current_article_content_id INTEGER NULL,

	PRIMARY KEY(article_id),
);

-- An article can have a number of contents (i.e. revisions) written.  Only 1
--  is live at a time.  
CREATE TABLE article_contents (
	article_content_id		INTEGER		NOT NULL	AUTO_INCREMENT,
	article_content_article_id	INTEGER		NOT NULL,
	article_content_heading		VARCHAR(255)	NOT NULL,
	article_content_subheading	TEXT		NULL,
	article_content_subtext		TEXT		NULL,
	article_content_text		TEXT		NOT NULL,
	article_content_blurb		TEXT		NULL,
	
	PRIMARY KEY(article_content_id),
);

-- Articles about events are linked to the events.  
CREATE TABLE article_events (
	article_event_article_id	INTEGER		NOT NULL,
	article_event_event_id	INTEGER		NOT NULL,
	
	PRIMARY KEY(article_event_article_id, article_event_event_id), 
);

CREATE TABLE article_links (
	article_link_id		INTEGER		NOT NULL	AUTO_INCREMENT,
	article_link_article_id	INTEGER		NOT NULL,
	article_link_name		VARCHAR(255)	NOT NULL,
	article_link_url		VARCHAR(255)	NOT NULL,
	article_link_deleted		BOOL		NOT NULL	FALSE,
	article_link_timestamp	TIMESTAMP	NOT NULL	CURRENT_TIMESTAMP,
	
	PRIMARY KEY(article_link_id),
);

-- Photos that may appear in the wikitext.  
CREATE TABLE article_photos (
	article_photo_article_id	INTEGER		NOT NULL,
	article_photo_photo_id	INTEGER		NOT NULL,
	article_photo_number		INTEGER		NOT NULL,

	PRIMARY KEY(article_photo_article_id, article_photo_photo_id),
);

CREATE TABLE article_tags (
	article_tag_article_id	INTEGER		NOT NULL,
	article_tag_tag_id		INTEGER		NOT NULL,
	
	PRIMARY KEY(article_tag_article_id, article_tag_tag_id),
);

-- News, reviews features, lifstyle etc..  Can be in a hierachy.  
CREATE TABLE article_types (
	article_type_id		INTEGER		NOT NULL	AUTO_INCREMENT,
	article_type_parent_article_type_id INTEGER	NULL,
	article_type_name		VARCHAR(255)	NOT NULL,
	article_type_archive		BOOL		NOT NULL,
	
	PRIMARY KEY(article_type_id),
);

-- Links the authors of a revision of an article.  
CREATE TABLE article_writers (
	article_writer_user_id		INTEGER		NOT NULL,
	article_writer_article_content_id INTEGER	NOT NULL,
	
	PRIMARY KEY(article_writer_user_id, article_writer_article_content_id),
);

CREATE TABLE fact_boxes (
	id		INTEGER		NOT NULL	AUTO_INCREMENT,
	article_content_id INTEGER	NOT NULL,
	text		TEXT		NOT NULL,
	deleted		BOOL		NOT NULL	FALSE,
	timestamp	TIMESTAMP	NOT NULL	CURRENT_TIMESTAMP,
	
	PRIMARY KEY(fact_box_id),
);

CREATE TABLE related_articles (
	article_id1	INTEGER		NOT NULL,
	article_id2	INTEGER		NOT NULL,

	PRIMARY KEY(article_id1, article_id2),
);

CREATE TABLE pull_quotes (
	pull_quote_id INTEGER NOT NULL AUTO_INCREMENT,
	pull_quote_article_content_id INTEGER NOT NULL,
	pull_quote_text TEXT NULL,
	pull_quote_person TEXT NULL,
	pull_quote_position TEXT NULL,
	pull_quote_order INTEGER NULL,
	pull_quote_deleted BOOL NULL,

	PRIMARY KEY(pull_quote_id),
);

CREATE TABLE requests (
	request_id INTEGER NOT NULL AUTO_INCREMENT,
		-- Who requested the thing
	request_entity_id INTEGER NOT NULL,
		-- An article is created with a request for an article
	request_article_id INTEGER NOT NULL,
	request_type_id INTEGER NOT NULL,
	request_article_type_id INTEGER NOT NULL,
	request_organisation_entity_id INTEGER NULL,
	request_text TEXT NOT NULL,
	request_deadline TIMESTAMP NOT NULL,
	request_accepted BOOL NOT NULL,
	request_deleted BOOL NOT NULL,
	request_timestamp TIMESTAMP NOT NULL CURRENT_TIMESTAMP,
	PRIMARY KEY(request_id),
);

CREATE TABLE request_photos (
	request_photo_request_id INTEGER NOT NULL,
	request_photo_photo_id INTEGER NULL,
	request_photo_deleted BOOL NOT NULL,
	request_photo_timestamp TIMESTAMP NOT NULL CURRENT_TIMESTAMP,

	PRIMARY KEY(request_photo_request_id, request_photo_photo_id),
);

-- Request types (e.g. photo, article, suggestion)
CREATE TABLE request_types (
	request_type_id INTEGER NOT NULL AUTO_INCREMENT,
	request_type_name TEXT VARCHAR(255),
	PRIMARY KEY(request_type_id)
);

CREATE TABLE request_users (
	request_user_request_id INTEGER NOT NULL,
	request_user_user_id INTEGER NOT NULL,
	request_user_accepted BOOL NULL,
	request_user_rejected BOOL NULL,
	PRIMARY KEY(request_user_request_id, request_user_user_id),
);
----------------------------------------------------------------
-- Event and calendar related tables                          --
----------------------------------------------------------------
CREATE TABLE events (
	event_id INTEGER NOT NULL,
	event_image_id INTEGER NOT NULL,
	event_parent_id INTEGER NOT NULL,
	event_type_id INTEGER NOT NULL,
	event_name TEXT NULL,
	event_description TEXT NULL,
	event_blurb TEXT NULL,
	event_deleted BOOL NULL,
	event_timestamp TIMESTAMP NULL,
	PRIMARY KEY(event_id),
);

-- An event can be linked to a number of organisations.  
CREATE TABLE events_entities (
	event_entity_entity_id INTEGER NOT NULL,
	event_id INTEGER NOT NULL,
	PRIMARY KEY(event_entity_entity_id, event_id),
);

-- Users can customize ocurrencies on the timetable.  
CREATE TABLE event_occurence_users (
	event_occurence_user_user_id INTEGER NOT NULL,
	event_occurence_user_event_occurrence_id INTEGER NOT NULL,
	event_occurrence_user_hide BOOL NULL,
	event_occurrence_user_rsvp BOOL NULL,
	PRIMARY KEY(event_occurence_user_user_id, event_occurence_user_event_occurrence_id),
);

CREATE TABLE event_occurrences (
	event_occurrence_id INTEGER NOT NULL,
	event_occurrence_next_id INTEGER NOT NULL,
	event_occurrence_event_id INTEGER NOT NULL,
	event_occurrence_state_id INTEGER NOT NULL,
	event_occurrence_description TEXT NULL,
	event_occurrence_location TEXT NULL,
	event_occurrence_postcode TEXT NULL,
	event_occurrence_start_time TIMESTAMP NULL,
	event_occurrence_end_time TIMESTAMP NULL,
	event_occurrence_all_day BOOL NULL,
	event_occurrence_ends_late BOOL NULL,
	event_occurrence_timestamp TIMESTAMP NULL,
	PRIMARY KEY(event_occurrence_id),
);

CREATE TABLE event_occurrence_states (
	event_occurrences_state_id INTEGER NOT NULL AUTO_INCREMENT,
	event_occurrence_state_name TEXT NULL,
	event_occurrence_state_published BOOL NULL,
	PRIMARY KEY(event_occurrences_state_id)
);

-- Types of event (e.g. social, meeting, training etc.).  
CREATE TABLE event_types (
	event_type_id INTEGER NOT NULL AUTO_INCREMENT,
	event_type_name TEXT NULL,
	PRIMARY KEY(event_type_id)
);

CREATE TABLE reminders (
	reminder_id INTEGER NOT NULL AUTO_INCREMENT,
	reminder_user_id INTEGER NOT NULL,
	reminder_name INTEGER NULL,
	reminder_description TEXT NULL,
	reminder_timestamp TIMESTAMP NULL,
	
	PRIMARY KEY(reminder_id),
);

CREATE TABLE anniversaries (
	anniversary_id INTEGER NOT NULL AUTO_INCREMENT,
	anniversary_entity_id INTEGER NOT NULL,
	anniversary_name TEXT NULL,
	anniversary_start_date TIMESTAMP NULL,
	PRIMARY KEY(anniversary_id),
);

CREATE TABLE todo_list_items (
	todo_list_item_id INTEGER NOT NULL AUTO_INCREMENT,
	todo_list_item_event_occurrence_id INTEGER NOT NULL,
	todo_list_item_reminder_id INTEGER NOT NULL,
	todo_list_item_todo_priority_id INTEGER NOT NULL,
	todo_list_item_user_id INTEGER NULL,
	todo_list_item_name TEXT NULL,
	todo_list_item_description TEXT NULL,
	todo_list_item_done BOOL NULL,
	todo_list_item_event_occurence_id INTEGER NULL,
	todo_list_item_deadline TIMESTAMP NULL,
	PRIMARY KEY(todo_list_item_id),
);

CREATE TABLE todo_priorities (
	todo_priority_id INTEGER NOT NULL AUTO_INCREMENT,
	todo_priority_name TEXT NULL,
	todo_priority_order INTEGER NULL,
	PRIMARY KEY(todo_priority_id)
);

CREATE TABLE years (
	year_id INTEGER NOT NULL,
	start_autumn TIMESTAMP NOT NULL,
	end_autumn TIMESTAMP NOT NULL,
	start_spring TIMESTAMP NOT NULL,
	end_spring TIMESTAMP NOT NULL,
	start_summer TIMESTAMP NOT NULL,
	end_summer TIMESTAMP NOT NULL,
	PRIMARY KEY(year_id)
);


----------------------------------------------------------------
-- Review related tables                                      --
----------------------------------------------------------------
CREATE TABLE reviews (
	review_id INTEGER NOT NULL AUTO_INCREMENT,
	rto_review_type_id INTEGER NOT NULL,
	rto_organisation_entity_id INTEGER NOT NULL,
	review_article_id INTEGER NULL,
	PRIMARY KEY(review_id),
);

CREATE TABLE review_types (
	review_type_id INTEGER NOT NULL AUTO_INCREMENT,
	review_type_article_type_id INTEGER NOT NULL,
	review_type_image_id INTEGER NULL,
	review_type_name TEXT NULL,
	review_type_blurb TEXT NULL,
	review_type_blurb_draft TEXT NULL,
	PRIMARY KEY(review_type_id),
);

-- Information about an organisation in a specific category (e.g. evil eye for
--  food)
CREATE TABLE review_type_organisations (
	rto_organisation_entity_id INTEGER NOT NULL,
	rto_review_type_id INTEGER NOT NULL,
	rto_live_content_id INTEGER NOT NULL,
	rto_user_rate_count INTEGER NULL,
	rto_average_user_rating FLOAT NULL,
	rto_deleted BOOL NULL,
	rto_timestamp TIMESTAMP NULL,
	PRIMARY KEY(rto_organisation_entity_id, rto_review_type_id),
);

-- RTO content is like article content.  
CREATE TABLE rto_content (
	rto_content_id INTEGER NOT NULL AUTO_INCREMENT,
	rto_content_organisation_entity_id INTEGER NOT NULL,
	rto_content_review_type_id INTEGER NOT NULL,
	rto_content_cost_id INTEGER NULL,
	rto_content_blurb TEXT NULL,
	rto_content_price FLOAT NULL,
	rto_content_reccommend TEXT NULL,
	rto_content_average_price_upper INTEGER NULL,
	rto_content_average_price_lower INTEGER NULL,
	rto_content_rating INTEGER NULL,
	rto_content_directions TEXT NULL,
	rto_content_book_online BOOL NULL,
	PRIMARY KEY(rto_content_id),
);

CREATE TABLE rto_tags (
	rto_tag_tag_id INTEGER NOT NULL,
	rto_organisation_entity_id INTEGER NOT NULL,
	rto_review_type_id INTEGER NOT NULL,
	PRIMARY KEY(rto_tag_tag_id, rto_organisation_entity_id, rto_review_type_id),
);

CREATE TABLE rto_costs (
	cost_id INTEGER NOT NULL AUTO_INCREMENT,
	cost_name TEXT NULL,
	PRIMARY KEY(cost_id)
);

CREATE TABLE reviews_slideshow (
	slideshow_review_rto_review_type_id INTEGER NOT NULL,
	slideshow_review_rto_organisation_entity_id INTEGER NOT NULL,
	slideshow_review_photo_id INTEGER NOT NULL,
);


CREATE TABLE bar_crawl_organisations (
	bar_crawl_organisation_bar_crawl_id INTEGER NOT NULL,
	bar_crawl_organisation_organisation_entity_id INTEGER NOT NULL,
	bar_crawl_organisation_order INTEGER NULL,
	bar_crawl_organisation_recommend TEXT NULL,
	bar_crawl_organisation_recommend_price FLOAT NULL,
	PRIMARY KEY(bar_crawl_organisation_organisation_entity_id),
);


CREATE TABLE leagues (
	league_id INTEGER NOT NULL AUTO_INCREMENT,
	league_image_id INTEGER NULL,
	league_review_type_id INTEGER NOT NULL,
	league_name TEXT NULL,
	league_size INTEGER NULL,
	PRIMARY KEY(league_id),
);

CREATE TABLE league_entries (
	league_entry_league_id INTEGER NOT NULL AUTO_INCREMENT,
	rto_organisation_entity_id INTEGER NOT NULL,
	league_entry_position INTEGER NULL,
	PRIMARY KEY(league_entry_league_id, rto_organisation_entity_id, rto_review_type_id),
);

----------------------------------------------------------------
-- Random shit related tables                                 --
----------------------------------------------------------------
CREATE TABLE campaigns (
	campaign_id INTEGER NOT NULL AUTO_INCREMENT,
	campaign_article_id INTEGER NULL,
	campaign_name TEXT NULL,
	campaign_votes INTEGER NULL,
	campaign_petition BOOL NULL,
	campaign_petition_signatures INTEGER NULL,
	campaign_deleted BOOL NULL,
	campaign_timestamp TIMESTAMP NULL,
	PRIMARY KEY(campaign_id),
);

-- Stores who has voted for a campaign
CREATE TABLE campaign_users (
	campaign_user_campaign_id INTEGER NOT NULL,
	campaign_user_user_id INTEGER NOT NULL,
	PRIMARY KEY(campaign_user_campaign_id, campaign_user_user_id),
);

CREATE TABLE progress_reports (
	progress_report_id INTEGER NOT NULL AUTO_INCREMENT,
	progress_report_charity_id INTEGER NULL,
	progress_report_campaign_id INTEGER NULL,
	progress_report_text TEXT NULL,
	progress_report_order INTEGER NULL,
	progress_report_deleted BOOL NULL,
	progress_report_timestamp TIMESTAMP NULL,
	PRIMARY KEY(progress_report_id),
);

CREATE TABLE charities (
	charity_id INTEGER NOT NULL AUTO_INCREMENT,
	charity_name TEXT NULL,
	charity_article_id INTEGER NULL,
	charity_goal_text TEXT NULL,
	charity_goal INTEGER NULL,
	charity_total FLOAT NULL,
	PRIMARY KEY(charity_id)
);

CREATE TABLE charity_donors (
	charity_donor_id INTEGER NOT NULL AUTO_INCREMENT,
	charity_donor_charity_id INTEGER NULL,
	charity_donor_name TEXT NULL,
	charity_donor_organisation_entity_id INTEGER NULL,
	charity_donor_amount INTEGER NULL,
	charity_donor_timestamp TIMESTAMP NULL,
	PRIMARY KEY(charity_donor_id),
);


CREATE TABLE tags (
	tag_id INTEGER NOT NULL AUTO_INCREMENT,
	tag_name TEXT NULL,
	tag_banner_name VARCHAR(255) NULL,
	tag_archive BOOL NULL DEFAULT 0,
	tag_deleted BOOL NULL,
	PRIMARY KEY(tag_id)
);


CREATE TABLE colleges (
	college_id INTEGER NOT NULL AUTO_INCREMENT,
	college_name CHAR NOT NULL,
	college_ranking INTEGER NULL,
	PRIMARY KEY(college_id),
);

CREATE TABLE college_rankings (
	college_ranking_id INTEGER NOT NULL AUTO_INCREMENT,
	college_ranking_college_id INTEGER NULL,
		-- Writers ID.  
	college_ranking_user_id INTEGER NULL,
	college_ranking_publisher_id INTEGER,
	college_ranking_text TEXT NULL,
	college_ranking_rank INTEGER SIGNED NULL,
	college_ranking_published BOOL NULL,
	college_ranking_deleted BOOL NULL,
	college_ranking_timestamp TIMESTAMP NULL,
	PRIMARY KEY(college_ranking_id),
);



CREATE TABLE quiz_questions (
	quiz_question_id INTEGER NOT NULL AUTO_INCREMENT,
	quiz_question_question TEXT NULL,
	quiz_question_answer1 TEXT NULL,
	quiz_question_answer2 TEXT NULL,
	quiz_question_answer3 TEXT NULL,
	quiz_question_answer4 TEXT NULL,
	quiz_question_answer INTEGER NULL,
	quiz_question_hits INTEGER NULL,
	quiz_question_correct INTEGER NULL,
	quiz_question_active BOOL NULL,
	PRIMARY KEY(quiz_question_id)
);

CREATE TABLE quiz_results (
	quiz_result_user_id INTEGER NOT NULL AUTO_INCREMENT,
	user_id INTEGER NOT NULL,
	quiz_result_date TIMESTAMP NULL,
	quiz_result_score INTEGER NULL,
	PRIMARY KEY(quiz_result_user_id),
);

CREATE TABLE quiz_winners (
	quiz_winner_id INTEGER NOT NULL AUTO_INCREMENT,
	quiz_winner_user_id INTEGER NULL,
	quiz_winner_score INTEGER NULL,
	quiz_winner_date TIMESTAMP NULL,
	PRIMARY KEY(quiz_winner_id),
);
----------------------------------------------------------------
-- Page related tables                                        --
----------------------------------------------------------------
CREATE TABLE comments (
	comment_id INTEGER NOT NULL AUTO_INCREMENT,
		-- The type of page eg. news, faq
	comment_page_id INTEGER NOT NULL,
		-- The id of the content of the page e.g. article number
	comment_subject_id INTEGER NULL,
	comment_text TEXT NULL,
	comment_rating INTEGER NULL,
	comment_reported_count INTEGER NULL,
	comment_deleted BOOL NOT NULL,
	comment_timestamp TIMESTAMP NOT NULL,

	PRIMARY KEY(comment_id),
);

CREATE TABLE pages (
	page_id INTEGER NOT NULL AUTO_INCREMENT,
	page_title TEXT NOT NULL,
	page_comments BOOL NOT NULL,
	page_ratings BOOL NOT NULL,
	page_permission INTEGER NOT NULL,

	PRIMARY KEY(page_id)
);

CREATE TABLE page_properties (
	page_property_id INTEGER NOT NULL AUTO_INCREMENT,
	page_property_property_type_id INTEGER NOT NULL,
	page_property_page_id INTEGER NOT NULL,
	page_property_photo_id INTEGER NULL,
	page_property_image_id INTEGER NULL,
	page_property_label TEXT NULL,
	page_property_text TEXT NULL,
	page_property_permission INTEGER NULL,

	PRIMARY KEY(page_property_id),
);

CREATE TABLE images (
	image_id INTEGER NOT NULL AUTO_INCREMENT,
	image_title VARCHAR(255) NOT NULL,
	image_image_type_id INTEGER NOT NULL,
	image_file_extension CHAR(4) NULL,

	PRIMARY KEY(image_id),
);

CREATE TABLE image_types (
	image_type_id INTEGER NOT NULL AUTO_INCREMENT,
	image_type_name VARCHAR(255) NOT NULL,
	image_type_width INTEGER NULL,
	image_type_height INTEGER NULL,
		-- True if all photos must have a thumb of this size
	image_type_photo BOOL NOT NULL,
	PRIMARY KEY(image_type_id)
);

CREATE TABLE photos (
	photo_id INTEGER NOT NULL AUTO_INCREMENT,
	photo_user_id INTEGER NULL,
	photo_title VARCHAR(255) NOT NULL,
	photo_width INTEGER NULL,
	photo_height INTEGER NULL,
	photo_gallery BOOL NULL,
	photo_homepage TIMESTAMP NULL,
	photo_deleted BOOL NOT NULL,
	photo_timestamp TIMESTAMP NULL,
	PRIMARY KEY(photo_id),
);

CREATE TABLE photo_tags (
	photo_tag_photo_id INTEGER NOT NULL,
	photo_tag_tag_id INTEGER NOT NULL,
	PRIMARY KEY(photo_tag_photo_id, photo_tag_tag_id),
);

----------------------------------------------------------------
-- Advert related tables                                      --
----------------------------------------------------------------
CREATE TABLE adverts (
	advert_id INTEGER NOT NULL AUTO_INCREMENT,
	advert_organisation_entity_id INTEGER NOT NULL,
	advert_name TEXT NULL,
	advert_description TEXT NULL,
	advert_url TEXT NULL,
	advert_start_date TIMESTAMP NULL,
	advert_end_date TIMESTAMP NULL,
	advert_max_total INTEGER NULL,
	PRIMARY KEY(advert_id),
);

CREATE TABLE advert_bills (
	advert_bill_id INTEGER NOT NULL AUTO_INCREMENT,
	advert_bill_organisation_entity_id INTEGER NOT NULL,
	advert_bill_total INTEGER NULL,
	advert_bill_date TIMESTAMP NULL,
	advert_bill_paid BOOL NULL,
	PRIMARY KEY(advert_bill_id),
);

CREATE TABLE advert_bill_items (
	advert_bill_item_id INTEGER NOT NULL AUTO_INCREMENT,
	advert_instance_id INTEGER NOT NULL,
	advert_bill_item_advert_bill_id INTEGER NOT NULL,
	advert_bill_item_amount INTEGER NULL,
	advert_bill_item_clicks INTEGER NULL,
	advert_bill_item_views INTEGER NULL,
	advert_bill_item_made_date INTEGER NULL,
	PRIMARY KEY(advert_bill_item_id),
);

CREATE TABLE advert_instances (
	advert_instance_id INTEGER NOT NULL AUTO_INCREMENT,
	advert_instance_space_type_id INTEGER NOT NULL,
	advert_instance_advert_id INTEGER NOT NULL,
	advert_instance_views INTEGER NULL,
	advert_instance_clicks INTEGER NULL,
	advert_instance_view_cost INTEGER NULL,
	advert_instance_click_cost INTEGER NULL,
	advert_instance_extension TEXT NULL,
	advert_instance_deleted BOOL NULL,
	advert_instance_timestamp TIMESTAMP NULL,
	PRIMARY KEY(advert_instance_id),
);

CREATE TABLE advert_related_articles (
	advert_related_article_advert_id INTEGER NOT NULL,
	advert_related_article_article_id INTEGER NOT NULL,
	PRIMARY KEY(advert_related_article_advert_id, advert_related_article_article_id),
);

CREATE TABLE advert_related_organisations (
	advert_related_organisation_advert_id INTEGER NOT NULL,
	advert_related_organisation_organisation_entity_id INTEGER NOT NULL,
	PRIMARY KEY(advert_related_organisation_advert_id, advert_related_organisation_organisation_entity_id),
);

CREATE TABLE page_space_types (
	page_space_type_page_id INTEGER NOT NULL,
	page_space_type_space_type_id INTEGER NOT NULL,
	page_space_type_number INTEGER NOT NULL,
	PRIMARY KEY(page_space_type_page_id, page_space_type_space_type_id, page_space_type_number),
);

CREATE TABLE space_types (
	space_type_id INTEGER NOT NULL AUTO_INCREMENT,
	space_type_view_cost FLOAT NULL,
	space_type_click_cost FLOAT NULL,
	space_type_width INTEGER NULL,
	space_type_height INTEGER NULL,
	PRIMARY KEY(space_type_id)
);


----------------------------------------------------------------
-- Maps stuff                                                 --
----------------------------------------------------------------

CREATE TABLE buildings (
	building_id INTEGER NOT NULL AUTO_INCREMENT,
	buidling_name TEXT NULL,
	building_code CHAR(5) NULL,
	building_x INTEGER NULL,
	buidling_y INTEGER NULL,
	PRIMARY KEY(building_id)
);

CREATE TABLE rooms (
	room_id CHAR(10) NOT NULL AUTO_INCREMENT,
	room_room_building_id INTEGER NOT NULL,
	room_type_id INTEGER NOT NULL,
	PRIMARY KEY(room_id),
);

CREATE TABLE room_types (
	room_type_id INTEGER NOT NULL AUTO_INCREMENT,
	room_type_name TEXT NULL,
	PRIMARY KEY(room_type_id)
);




