----------------------------------------------------------------
-- Tables about entities                                      --
-- An entity is anything which may be able to log in or has   --
--	an entry in the directory                             --
----------------------------------------------------------------

DROP TABLE IF EXISTS entities;
CREATE TABLE entities (
	entity_id					INTEGER		NOT NULL	AUTO_INCREMENT,
	entity_username					VARCHAR(255)	NULL,
	entity_password					CHAR(32)	NULL,
	entity_entity_type_id				INTEGER		NOT NULL,
	entity_deleted					BOOL		NOT NULL	DEFAULT FALSE,
	entity_timestamp				TIMESTAMP	NOT NULL	DEFAULT CURRENT_TIMESTAMP,

	PRIMARY KEY(entity_id)
);

-- This table stores both organisations and parts of organisations (e.g. sports
--  teams)
DROP TABLE IF EXISTS organisations;
CREATE TABLE organisations (
	organisation_entity_id				INTEGER		NOT NULL,
	organisation_organisation_type_id	 	INTEGER		NOT NULL,
	organisation_organisation_entity_id_parent	INTEGER	NULL,
	organisation_name				VARCHAR(255)	NOT NULL,
	organisation_description			TEXT		NULL,
	organisation_location				VARCHAR(15)	NULL,
	organisation_address				VARCHAR(255)	NULL,
	organisation_postcode				VARCHAR(15)	NULL,
	organisation_url				VARCHAR(255)	NULL,
	organisation_opening_hours			VARCHAR(255)	NULL,
	organisation_directory				BOOL		NOT NULL,
	organisation_events				BOOL		NOT NULL,
	organisation_yorkipedia_entry 			VARCHAR(255)	NULL,
	organisation_hits				INTEGER		NOT NULL,
	organisation_timestamp				TIMESTAMP	NOT NULL	DEFAULT CURRENT_TIMESTAMP,
	
	PRIMARY KEY(organisation_entity_id)
);

-- Stores the type of organisations e.g. an on campus society, an AU club, an
--  external organisation.  If directory is set to false, this type of
--  organisation does not appear in the directory.  
DROP TABLE IF EXISTS organisation_types;
CREATE TABLE organisation_types (
	organisation_type_id				INTEGER		NOT NULL	AUTO_INCREMENT,
	organisation_type_name				VARCHAR(255)	NOT NULL,
	organisation_type_directory			BOOL		NOT NULL,

	PRIMARY KEY(organisation_type_id)
);

DROP TABLE IF EXISTS organisation_slideshows;
CREATE TABLE organisation_slideshows (
	organisation_slideshow_organisation_entity_id	INTEGER		NOT NULL,
	organisation_slideshow_photo_id			INTEGER		NOT NULL,
	organisation_slideshow_order			INTEGER		NOT NULL,

	PRIMARY KEY(organisation_slideshow_organisation_entity_id, organisation_slideshow_photo_id)
);

-- Organisations can be taged to create lists of related organisations etc.
DROP TABLE IF EXISTS organisation_tags;
CREATE TABLE organisation_tags (
	organisation_tag_organisation_entity_id		INTEGER		NOT NULL,
	organisation_tag_tag_id				INTEGER		NOT NULL,

	PRIMARY KEY(organisation_tag_organisation_entity_id, organisation_tag_tag_id)
);

-- Stores users of the yorker.	These must be members of the university.	
DROP TABLE IF EXISTS users;
CREATE TABLE users (
 	user_entity_id					INTEGER		NOT NULL,
	user_college_id					INTEGER		NULL,
	user_image_id					INTEGER		NULL,
	user_firstname					VARCHAR(255)	NOT NULL,
	user_surname					VARCHAR(255)	NOT NULL,
	user_email					VARCHAR(255)	NOT NULL,
	user_nickname					VARCHAR(255)	NULL,
	user_gender					ENUM('m','f')	NULL,
	user_enrolled_year				INTEGER		NULL,
	user_store_password				BOOL		NOT NULL,
	user_permission					INTEGER		NOT NULL,
	user_office_password				CHAR(32)	NULL,
	user_timestamp					TIMESTAMP	NOT NULL	DEFAULT CURRENT_TIMESTAMP,

	PRIMARY KEY(user_entity_id)
);

-- The types of articles a user can write - reviews, features, news etc.
DROP TABLE IF EXISTS users_article_types;
CREATE TABLE users_article_types (
	users_article_type_user_entity_id		INTEGER		NOT NULL,
	users_article_type_article_type_id		INTEGER		NOT NULL,

	PRIMARY KEY(users_article_type_user_entity_id, users_article_type_article_type_id)
);

-- Organisation can request to see properties of members.  
DROP TABLE IF EXISTS organisation_request_properties;
CREATE TABLE organisation_request_properties (
	organisation_request_properties_organisation_entity_id INTEGER	NOT NULL,
	organisation_request_properties_user_property_id INTEGER	NOT NULL,
	organisation_request_properties_preferred	BOOL		NOT NULL,

	PRIMARY KEY(organisation_request_properties_organisation_entity_id, organisation_request_properties_user_property_id)
);

-- TODO: possibly work out a nice way of doing this. 
DROP TABLE IF EXISTS user_has_properties;
CREATE TABLE user_has_properties (
	user_has_properties_user_entity_id		INTEGER		NOT NULL	AUTO_INCREMENT,
	user_has_properties_property_id			INTEGER		NOT NULL,
	user_has_properties_text			TEXT		NULL,
	user_has_properties_photo_id			INTEGER		NULL,
	user_has_properties_date			TIMESTAMP	NULL,
	user_has_properties_bool			BOOL		NULL,
	user_has_properties_number			FLOAT		NULL,

	PRIMARY KEY(user_has_properties_user_entity_id, user_has_properties_property_id)
);

-- Properties which users can associate with themselves.  
DROP TABLE IF EXISTS user_properties;
CREATE TABLE user_properties (
	user_property_id				INTEGER		NOT NULL	AUTO_INCREMENT,
	user_property_property_type_id			INTEGER		NULL,
	user_property_name				TEXT		NULL,

	PRIMARY KEY(user_property_id)
);

-- TODO: make this nice :)
DROP TABLE IF EXISTS property_types;
CREATE TABLE property_types (
	property_type_id				INTEGER		NOT NULL	AUTO_INCREMENT,
	property_type_name				VARCHAR(255)	NOT NULL,
	property_type_is_user_prop			BOOL		NOT NULL,
	property_type_text				BOOL		NOT NULL,
	property_type_photo				BOOL		NOT NULL,
	property_type_image				BOOL		NOT NULL,
	property_type_date				BOOL		NOT NULL,
	property_type_bool				BOOL		NOT NULL,
	property_type_number				BOOL		NOT NULL,
	
	PRIMARY KEY(property_type_id)
);

-- Links that can appear on the homepage
DROP TABLE IF EXISTS links;
CREATE TABLE links (
	link_id						INTEGER		NOT NULL	AUTO_INCREMENT,
	link_image_id					INTEGER		NOT NULL,
	link_url					VARCHAR(255)	NOT NULL,
	link_name					VARCHAR(255)	NOT NULL,
	
	PRIMARY KEY(link_id)
);

-- Stores either association between user and predifined links or custom links.  
DROP TABLE IF EXISTS user_links;
CREATE TABLE user_links (
	user_link_id					INTEGER		NOT NULL	AUTO_INCREMENT,
	user_link_user_entity_id			INTEGER		NOT NULL,
	user_link_link_id				INTEGER		NULL,
	user_link_image_id				INTEGER		NULL,
	user_link_url					VARCHAR(255)	NULL,
	user_link_name					VARCHAR(255)	NULL,
		-- True to make a request to put a user link into predifined links.  
	user_link_request				BOOL		NULL,
	user_link_order					INTEGER		NOT NULL,
	
	PRIMARY KEY(user_link_id)
);

-- What organisations can see a users properties.  
DROP TABLE IF EXISTS user_subscription_properties;
CREATE TABLE user_subscription_properties (
	user_subscription_property_organisation_entity_id INTEGER	NOT NULL,
	user_subscription_property_property_id		INTEGER		NOT NULL,
	user_subscription_property_user_entity_id	INTEGER		NOT NULL,

	PRIMARY KEY(user_subscription_property_organisation_entity_id, user_subscription_property_property_id, user_subscription_property_user_entity_id)
);

DROP TABLE IF EXISTS departments;
CREATE TABLE departments (
	department_id					INTEGER		NOT NULL	AUTO_INCREMENT,
	department_name					VARCHAR(255)	NOT NULL,

	PRIMARY KEY(department_id)
);

DROP TABLE IF EXISTS department_modules;
CREATE TABLE department_modules (
	department_module_department_id			INTEGER		NOT NULL,
		-- The module is an entity
	department_module_entity_id			INTEGER		NOT NULL,

	PRIMARY KEY(department_module_department_id, department_module_entity_id)
);

----------------------------------------------------------------
-- Subscription and business card related tables              --
----------------------------------------------------------------
DROP TABLE IF EXISTS business_cards;
CREATE TABLE business_cards (
	business_card_id				INTEGER		NOT NULL	AUTO_INCREMENT,
	business_card_organisation_entity_id		INTEGER		NOT NULL,
	business_card_user_entity_id			INTEGER		NOT NULL,
	business_card_name				VARCHAR(255)	NOT NULL,
	business_card_title				VARCHAR(255)	NOT NULL,
	business_card_blurb				TEXT		NULL,
	business_card_business_card_type_id 		INTEGER		NOT NULL,
	business_card_email				VARCHAR(255)	NULL,
	business_card_mobile				VARCHAR(31)	NULL,
	business_card_phone_internal			VARCHAR(31)	NULL,
	business_card_phone_external			VARCHAR(31)	NULL,
	business_card_postal_address			VARCHAR(255)	NULL,
	business_card_business_card_colour_id		INTEGER		NOT NULL,
	business_card_deleted				BOOL		NOT NULL,
	business_card_timestamp				TIMESTAMP	NOT NULL	DEFAULT CURRENT_TIMESTAMP,

	PRIMARY KEY(business_card_id)
);

DROP TABLE IF EXISTS business_card_colours;
CREATE TABLE business_card_colours (
	business_card_colour_id				INTEGER		NOT NULL	AUTO_INCREMENT,
	business_card_colour_name			VARCHAR(255)	NOT NULL,
	business_card_colour_background			CHAR(6)		NOT NULL,
	business_card_colour_foreground			CHAR(6)		NOT NULL,

	PRIMARY KEY(business_card_colour_id)
);

-- Title on buisiness card, may well not be related to "teams" etc.
-- TODO: discrepancy between this and design
DROP TABLE IF EXISTS business_card_types;
CREATE TABLE business_card_types (
	business_card_type_id				INTEGER		NOT NULL	AUTO_INCREMENT,
	business_card_type_name				VARCHAR(255)	NOT NULL,
	business_card_type_organisation_entity_id 	INTEGER		NULL,
	
	PRIMARY KEY(business_card_type_id)
);

DROP TABLE IF EXISTS subscriptions;
CREATE TABLE subscriptions (
	subscription_organisation_entity_id		INTEGER		NOT NULL,
	subscription_user_entity_id			INTEGER		NOT NULL,
		-- Are the events on the calendar
	subscription_interested				BOOL		NOT NULL,
	subscription_member				BOOL		NOT NULL,
	subscription_paid				BOOL		NOT NULL,
		-- Does the organisation have access to e-mail address
	subscription_email				BOOL		NOT NULL,
	subscription_admin				BOOL		NOT NULL,
	subscription_user_confirmed			BOOL		NOT NULL,
	subscription_deleted				BOOL		NOT NULL,
	subscription_timestamp				TIMESTAMP	NOT NULL	DEFAULT CURRENT_TIMESTAMP,
	
	PRIMARY KEY(subscription_organisation_entity_id, subscription_user_entity_id)
);

----------------------------------------------------------------
-- Article related tables                                     --
----------------------------------------------------------------
DROP TABLE IF EXISTS articles;
CREATE TABLE articles (
	article_id					INTEGER		NOT NULL	AUTO_INCREMENT,
	article_timestamp				TIMESTAMP	NOT NULL	DEFAULT CURRENT_TIMESTAMP,
	article_article_type_id				INTEGER		NOT NULL,
	article_last_editor_user_entity_id 		INTEGER		NULL,
	article_created					TIMESTAMP	NOT NULL,
	article_publish_date				TIMESTAMP	NULL,
	article_initial_editor_user_entity_id		INTEGER		NULL,
	article_location				VARCHAR(15)	NULL,
	article_breaking				BOOL		NOT NULL	DEFAULT FALSE,
	article_pulled					BOOL		NOT NULL	DEFAULT FALSE,
	article_hits					INTEGER		NOT NULL	DEFAULT 0,
	article_deleted					BOOL		NOT NULL	DEFAULT FALSE,
	article_current_article_content_id		INTEGER		NULL,

	PRIMARY KEY(article_id)
);

-- TODO: what if someone edits and checks at the same time
-- An article can have a number of contents (i.e. revisions) written.  Only 1
--  is live at a time.  
DROP TABLE IF EXISTS article_contents;
CREATE TABLE article_contents (
	article_content_id				INTEGER		NOT NULL	AUTO_INCREMENT,
	article_content_article_id			INTEGER		NOT NULL,
	article_content_heading				VARCHAR(255)	NOT NULL,
	article_content_subheading			TEXT		NULL,
	article_content_subtext				TEXT		NULL,
	article_content_text				TEXT		NOT NULL,
	article_content_blurb				TEXT		NULL,
	
	PRIMARY KEY(article_content_id)
);

-- Articles about events are linked to the events.  
DROP TABLE IF EXISTS article_events;
CREATE TABLE article_events (
	article_event_article_id			INTEGER		NOT NULL,
	article_event_event_id				INTEGER		NOT NULL,
	
	PRIMARY KEY(article_event_article_id, article_event_event_id)
);

DROP TABLE IF EXISTS article_links;
CREATE TABLE article_links (
	article_link_id					INTEGER		NOT NULL	AUTO_INCREMENT,
	article_link_article_id				INTEGER		NOT NULL,
	article_link_name				VARCHAR(255)	NOT NULL,
	article_link_url				VARCHAR(255)	NOT NULL,
	article_link_deleted				BOOL		NOT NULL	DEFAULT FALSE,
	article_link_timestamp				TIMESTAMP	NOT NULL	DEFAULT CURRENT_TIMESTAMP,
	
	PRIMARY KEY(article_link_id)
);

-- Photos that may appear in the wikitext.  
DROP TABLE IF EXISTS article_photos;
CREATE TABLE article_photos (
	article_photo_article_id	INTEGER		NOT NULL,
	article_photo_photo_id	INTEGER		NOT NULL,
	article_photo_number		INTEGER		NOT NULL,

	PRIMARY KEY(article_photo_article_id, article_photo_photo_id)
);

DROP TABLE IF EXISTS article_tags;
CREATE TABLE article_tags (
	article_tag_article_id	INTEGER		NOT NULL,
	article_tag_tag_id		INTEGER		NOT NULL,
	
	PRIMARY KEY(article_tag_article_id, article_tag_tag_id)
);

-- News, reviews features, lifstyle etc..  Can be in a hierachy.  
DROP TABLE IF EXISTS article_types;
CREATE TABLE article_types (
	article_type_id		INTEGER		NOT NULL	AUTO_INCREMENT,
	article_type_parent_article_type_id INTEGER	NULL,
	article_type_name		VARCHAR(255)	NOT NULL,
	article_type_archive		BOOL		NOT NULL,
	article_type_blurb		TEXT		NOT NULL,
	
	PRIMARY KEY(article_type_id)
);

-- Links the authors of a revision of an article.  
DROP TABLE IF EXISTS article_writers;
CREATE TABLE article_writers (
	article_writer_user_entity_id		INTEGER		NOT NULL,
	article_writer_article_content_id INTEGER	NOT NULL,
	
	PRIMARY KEY(article_writer_user_entity_id, article_writer_article_content_id)
);

DROP TABLE IF EXISTS fact_boxes;
CREATE TABLE fact_boxes (
	fact_box_id		INTEGER		NOT NULL	AUTO_INCREMENT,
	fact_box_article_content_id INTEGER	NOT NULL,
	fact_box_text		TEXT		NOT NULL,
	fact_box_deleted	BOOL		NOT NULL	DEFAULT FALSE,
	fact_box_timestamp	TIMESTAMP	NOT NULL	DEFAULT CURRENT_TIMESTAMP,
	
	PRIMARY KEY(fact_box_id)
);

DROP TABLE IF EXISTS related_articles;
CREATE TABLE related_articles (
	related_article_1_article_id	INTEGER		NOT NULL,
	related_article_2_article_id	INTEGER		NOT NULL,

	PRIMARY KEY(related_article_1_article_id, related_article_2_article_id)
);

DROP TABLE IF EXISTS pull_quotes;
CREATE TABLE pull_quotes (
	pull_quote_id INTEGER NOT NULL AUTO_INCREMENT,
	pull_quote_article_content_id INTEGER NOT NULL,
	pull_quote_text TEXT NOT NULL,
	pull_quote_person VARCHAR(255) NOT NULL,
	pull_quote_position VARCHAR(255) NOT NULL,
	pull_quote_order INTEGER NOT NULL,
	pull_quote_deleted BOOL NOT NULL,

	PRIMARY KEY(pull_quote_id)
);

DROP TABLE IF EXISTS requests;
CREATE TABLE requests (
	request_id INTEGER NOT NULL AUTO_INCREMENT,
	request_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		-- Who requested the thing
	request_entity_id INTEGER NOT NULL,
		-- An article is created with a request for an article
	request_article_id INTEGER NOT NULL,
	request_type_id INTEGER NOT NULL,
	request_article_type_id INTEGER NOT NULL,
	request_organisation_entity_id INTEGER NULL,
	request_text TEXT NOT NULL,
	request_blurb TEXT NOT NULL,
	request_deadline TIMESTAMP NOT NULL,
	request_accepted BOOL NOT NULL,
	request_deleted BOOL NOT NULL,
	
	PRIMARY KEY(request_id)
);

DROP TABLE IF EXISTS request_photos;
CREATE TABLE request_photos (
	request_photo_request_id INTEGER NOT NULL,
	request_photo_photo_id INTEGER NULL,
	request_photo_deleted BOOL NOT NULL,
	request_photo_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

	PRIMARY KEY(request_photo_request_id, request_photo_photo_id)
);

-- Request types (e.g. photo, article, suggestion)
DROP TABLE IF EXISTS request_types;
CREATE TABLE request_types (
	request_type_id INTEGER NOT NULL AUTO_INCREMENT,
	request_type_name VARCHAR(255) NOT NULL,
	
	PRIMARY KEY(request_type_id)
);

DROP TABLE IF EXISTS request_users;
CREATE TABLE request_users (
	request_user_request_id INTEGER NOT NULL,
	request_user_user_entity_id INTEGER NOT NULL,
	request_user_accepted BOOL NOT NULL,
	request_user_rejected BOOL NOT NULL,
	
	PRIMARY KEY(request_user_request_id, request_user_user_entity_id)
);
----------------------------------------------------------------
-- Event and calendar related tables                          --
----------------------------------------------------------------
DROP TABLE IF EXISTS events;
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
	
	PRIMARY KEY(event_id)
);

-- An event can be linked to a number of organisations.  
DROP TABLE IF EXISTS events_entities;
CREATE TABLE events_entities (
	event_entity_entity_id INTEGER NOT NULL,
	event_entity_event_id INTEGER NOT NULL,
	
	PRIMARY KEY(event_entity_entity_id, event_entity_event_id)
);

-- Users can customize ocurrencies on the timetable.  
DROP TABLE IF EXISTS event_occurrence_users;
CREATE TABLE event_occurrence_users (
	event_occurrence_user_user_entity_id INTEGER NOT NULL,
	event_occurrence_user_event_occurrence_id INTEGER NOT NULL,
	event_occurrence_user_hide BOOL NOT NULL,
	event_occurrence_user_rsvp BOOL NOT NULL,
	
	PRIMARY KEY(event_occurrence_user_user_entity_id, event_occurrence_user_event_occurrence_id)
);

DROP TABLE IF EXISTS event_occurrences;
CREATE TABLE event_occurrences (
	event_occurrence_id INTEGER NOT NULL,
	event_occurrence_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	event_occurrence_next_id INTEGER NOT NULL,
	event_occurrence_event_id INTEGER NOT NULL,
	event_occurrence_state_id INTEGER NOT NULL,
	event_occurrence_description TEXT NOT NULL,
	event_occurrence_location VARCHAR(15) NOT NULL,
	event_occurrence_postcode VARCHAR(15) NOT NULL,
	event_occurrence_start_time TIMESTAMP NOT NULL,
	event_occurrence_end_time TIMESTAMP NOT NULL,
	event_occurrence_all_day BOOL NOT NULL,
	event_occurrence_ends_late BOOL NOT NULL,
	
	PRIMARY KEY(event_occurrence_id)
);

DROP TABLE IF EXISTS event_occurrence_states;
CREATE TABLE event_occurrence_states (
	event_occurrence_state_id INTEGER NOT NULL AUTO_INCREMENT,
	event_occurrence_state_name VARCHAR(255) NOT NULL,
	event_occurrence_state_published BOOL NOT NULL,
	
	PRIMARY KEY(event_occurrence_state_id)
);

-- Types of event (e.g. social, meeting, training etc.).  
DROP TABLE IF EXISTS event_types;
CREATE TABLE event_types (
	event_type_id INTEGER NOT NULL AUTO_INCREMENT,
	event_type_name VARCHAR(255) NOT NULL,
	
	PRIMARY KEY(event_type_id)
);

DROP TABLE IF EXISTS reminders;
CREATE TABLE reminders (
	reminder_id INTEGER NOT NULL AUTO_INCREMENT,
	reminder_user_entity_id INTEGER NOT NULL,
	reminder_name VARCHAR(255) NOT NULL,
	reminder_description TEXT NOT NULL,
	reminder_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	
	PRIMARY KEY(reminder_id)
);

DROP TABLE IF EXISTS anniversaries;
CREATE TABLE anniversaries (
	anniversary_id INTEGER NOT NULL AUTO_INCREMENT,
	anniversary_entity_id INTEGER NOT NULL,
	anniversary_name VARCHAR(255) NOT NULL,
	anniversary_start_date TIMESTAMP NOT NULL,
	
	PRIMARY KEY(anniversary_id)
);

DROP TABLE IF EXISTS todo_list_items;
CREATE TABLE todo_list_items (
	todo_list_item_id INTEGER NOT NULL AUTO_INCREMENT,
	todo_list_item_event_occurrence_id INTEGER NOT NULL,
	todo_list_item_reminder_id INTEGER NOT NULL,
	todo_list_item_todo_priority_id INTEGER NOT NULL,
	todo_list_item_user_entity_id INTEGER NOT NULL,
	todo_list_item_name VARCHAR(255) NOT NULL,
	todo_list_item_description TEXT NOT NULL,
	todo_list_item_done BOOL NOT NULL,
	todo_list_item_event_occurence_id INTEGER NOT NULL,
	todo_list_item_deadline TIMESTAMP NOT NULL,
	
	PRIMARY KEY(todo_list_item_id)
);

DROP TABLE IF EXISTS todo_priorities;
CREATE TABLE todo_priorities (
	todo_priority_id INTEGER NOT NULL AUTO_INCREMENT,
	todo_priority_name VARCHAR(255) NOT NULL,
	todo_priority_order INTEGER NOT NULL,
	
	PRIMARY KEY(todo_priority_id)
);

DROP TABLE IF EXISTS years;
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
DROP TABLE IF EXISTS reviews;
CREATE TABLE reviews (
	review_id INTEGER NOT NULL AUTO_INCREMENT,
	rto_review_type_id INTEGER NOT NULL,
	rto_organisation_entity_id INTEGER NOT NULL,
	review_article_id INTEGER NOT NULL,
	
	PRIMARY KEY(review_id)
);

DROP TABLE IF EXISTS review_types;
CREATE TABLE review_types (
	review_type_id INTEGER NOT NULL AUTO_INCREMENT,
	review_type_article_type_id INTEGER NOT NULL,
	review_type_image_id INTEGER NOT NULL,
	review_type_name VARCHAR(255) NOT NULL,
	review_type_blurb TEXT NOT NULL,
	review_type_blurb_draft TEXT NOT NULL,
	
	PRIMARY KEY(review_type_id)
);

-- Information about an organisation in a specific category (e.g. evil eye for
--  food)
DROP TABLE IF EXISTS review_type_organisations;
CREATE TABLE review_type_organisations (
	rto_organisation_entity_id INTEGER NOT NULL,
	rto_review_type_id INTEGER NOT NULL,
	rto_live_content_id INTEGER NOT NULL,
	rto_user_rate_count INTEGER NOT NULL,
	rto_average_user_rating INTEGER NOT NULL,
	rto_deleted BOOL NOT NULL,
	rto_timestamp TIMESTAMP NOT NULL,
	
	PRIMARY KEY(rto_organisation_entity_id, rto_review_type_id)
);

-- RTO content is like article content.  
DROP TABLE IF EXISTS rto_content;
CREATE TABLE rto_content (
	rto_content_id INTEGER NOT NULL AUTO_INCREMENT,
	rto_content_organisation_entity_id INTEGER NOT NULL,
	rto_content_review_type_id INTEGER NOT NULL,
	rto_content_cost_id INTEGER NOT NULL,
	rto_content_blurb TEXT NOT NULL,
	rto_content_price INTEGER NOT NULL,
	rto_content_recommend TEXT NOT NULL,
	rto_content_average_price_upper INTEGER NOT NULL,
	rto_content_average_price_lower INTEGER NOT NULL,
	rto_content_rating INTEGER NOT NULL,
	rto_content_directions TEXT NOT NULL,
	rto_content_book_online BOOL NOT NULL,
	
	PRIMARY KEY(rto_content_id)
);

DROP TABLE IF EXISTS rto_tags;
CREATE TABLE rto_tags (
	rto_tag_tag_id INTEGER NOT NULL,
	rto_organisation_entity_id INTEGER NOT NULL,
	rto_review_type_id INTEGER NOT NULL,

	PRIMARY KEY(rto_tag_tag_id, rto_organisation_entity_id, rto_review_type_id)
);

DROP TABLE IF EXISTS rto_costs;
CREATE TABLE rto_costs (
	rto_cost_id INTEGER NOT NULL AUTO_INCREMENT,
	rto_cost_name VARCHAR(255) NOT NULL,

	PRIMARY KEY(rto_cost_id)
);

DROP TABLE IF EXISTS reviews_slideshows;
CREATE TABLE reviews_slideshows (
	reviews_slideshow_rto_review_type_id INTEGER NOT NULL,
	reviews_slideshow_rto_organisation_entity_id INTEGER NOT NULL,
	reviews_slideshow_photo_id INTEGER NOT NULL,
	reviews_slideshow_order INTEGER NOT NULL,
	
	PRIMARY KEY(reviews_slideshow_rto_review_type_id, reviews_slideshow_rto_organisation_entity_id, reviews_slideshow_order)
);


DROP TABLE IF EXISTS bar_crawl_organisations;
CREATE TABLE bar_crawl_organisations (
	bar_crawl_organisation_bar_crawl_id INTEGER NOT NULL,
	bar_crawl_organisation_organisation_entity_id INTEGER NOT NULL,
	bar_crawl_organisation_order INTEGER NOT NULL,
	bar_crawl_organisation_recommend VARCHAR(255) NOT NULL,
	bar_crawl_organisation_recommend_price INTEGER NOT NULL,
	
	PRIMARY KEY(bar_crawl_organisation_bar_crawl_id, bar_crawl_organisation_organisation_entity_id)
);


DROP TABLE IF EXISTS leagues;
CREATE TABLE leagues (
	league_id INTEGER NOT NULL AUTO_INCREMENT,
	league_image_id INTEGER NOT NULL,
	league_review_type_id INTEGER NOT NULL,
	league_name VARCHAR(255) NOT NULL,
	league_size INTEGER NOT NULL,
	
	PRIMARY KEY(league_id)
);

DROP TABLE IF EXISTS league_entries;
CREATE TABLE league_entries (
	league_entry_league_id INTEGER NOT NULL AUTO_INCREMENT,
	league_entry_rto_organisation_entity_id INTEGER NOT NULL,
	league_entry_position INTEGER NOT NULL,

	PRIMARY KEY(league_entry_league_id, league_entry_rto_organisation_entity_id)
);

----------------------------------------------------------------
-- Random shit related tables                                 --
----------------------------------------------------------------
DROP TABLE IF EXISTS campaigns;
CREATE TABLE campaigns (
	campaign_id INTEGER NOT NULL AUTO_INCREMENT,
	campaign_article_id INTEGER NOT NULL,
	campaign_name VARCHAR(255) NOT NULL,
	campaign_votes INTEGER NOT NULL,
	campaign_petition BOOL NOT NULL,
	campaign_petition_signatures INTEGER NOT NULL,
	campaign_deleted BOOL NOT NULL,
	campaign_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

	PRIMARY KEY(campaign_id)
);

-- Stores who has voted for a campaign
DROP TABLE IF EXISTS campaign_users;
CREATE TABLE campaign_users (
	campaign_user_campaign_id INTEGER NOT NULL,
	campaign_user_user_entity_id INTEGER NOT NULL,
	
	PRIMARY KEY(campaign_user_campaign_id, campaign_user_user_entity_id)
);

DROP TABLE IF EXISTS progress_reports;
CREATE TABLE progress_reports (
	progress_report_id INTEGER NOT NULL AUTO_INCREMENT,
	progress_report_charity_id INTEGER NOT NULL,
	progress_report_campaign_id INTEGER NOT NULL,
	progress_report_text TEXT NOT NULL,
	progress_report_order INTEGER NOT NULL,
	progress_report_deleted BOOL NOT NULL,
	progress_report_timestamp TIMESTAMP NOT NULL,
	progress_report_good_bad ENUM('good', 'bad') NOT NULL,
	
	PRIMARY KEY(progress_report_id)
);

DROP TABLE IF EXISTS charities;
CREATE TABLE charities (
	charity_id INTEGER NOT NULL AUTO_INCREMENT,
	charity_name VARCHAR(255) NOT NULL,
	charity_article_id INTEGER NOT NULL,
	charity_goal_text TEXT NOT NULL,
	charity_goal INTEGER NOT NULL,
	charity_total FLOAT NOT NULL,
	
	PRIMARY KEY(charity_id)
);

DROP TABLE IF EXISTS charity_donors;
CREATE TABLE charity_donors (
	charity_donor_id INTEGER NOT NULL AUTO_INCREMENT,
	charity_donor_charity_id INTEGER NOT NULL,
	charity_donor_name VARCHAR(255) NOT NULL,
	charity_donor_organisation_entity_id INTEGER NOT NULL,
	charity_donor_amount INTEGER NOT NULL,
	charity_donor_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

	PRIMARY KEY(charity_donor_id)
);


DROP TABLE IF EXISTS tags;
CREATE TABLE tags (
	tag_id INTEGER NOT NULL AUTO_INCREMENT,
	tag_name VARCHAR(255) NOT NULL,
	tag_banner_name VARCHAR(255) NOT NULL,
	tag_archive BOOL NOT NULL DEFAULT 0,
	tag_deleted BOOL NOT NULL,
	
	PRIMARY KEY(tag_id)
);


DROP TABLE IF EXISTS colleges;
CREATE TABLE colleges (
	college_id INTEGER NOT NULL AUTO_INCREMENT,
	college_name VARCHAR(255) NOT NULL,
	college_ranking INTEGER NOT NULL,
	
	PRIMARY KEY(college_id)
);

DROP TABLE IF EXISTS college_rankings;
CREATE TABLE college_rankings (
	college_ranking_id INTEGER NOT NULL AUTO_INCREMENT,
	college_ranking_college_id INTEGER NOT NULL,
		-- Writers ID.  
	college_ranking_user_entity_id INTEGER NOT NULL,
	college_ranking_publisher_id INTEGER NOT NULL,
	college_ranking_text TEXT NOT NULL,
	college_ranking_rank INTEGER SIGNED NOT NULL,
	college_ranking_published BOOL NOT NULL,
	college_ranking_deleted BOOL NOT NULL,
	college_ranking_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

	PRIMARY KEY(college_ranking_id)
);



DROP TABLE IF EXISTS quiz_questions;
CREATE TABLE quiz_questions (
	quiz_question_id INTEGER NOT NULL AUTO_INCREMENT,
	quiz_question_question TEXT NOT NULL,
	quiz_question_answer1 TEXT NOT NULL,
	quiz_question_answer2 TEXT NOT NULL,
	quiz_question_answer3 TEXT NOT NULL,
	quiz_question_answer4 TEXT NOT NULL,
	quiz_question_answer INTEGER NOT NULL,
	quiz_question_hits INTEGER NOT NULL,
		-- TODO: wtf
	quiz_question_correct INTEGER NOT NULL,
	quiz_question_active BOOL NOT NULL,

	PRIMARY KEY(quiz_question_id)
);

DROP TABLE IF EXISTS quiz_results;
CREATE TABLE quiz_results (
	quiz_result_user_entity_id INTEGER NOT NULL,
	quiz_result_date TIMESTAMP NOT NULL,
	quiz_result_score INTEGER NOT NULL,

	PRIMARY KEY(quiz_result_user_entity_id)
);

DROP TABLE IF EXISTS quiz_winners;
CREATE TABLE quiz_winners (
	quiz_winner_user_entity_id INTEGER NOT NULL,
	quiz_winner_score INTEGER NOT NULL,
	quiz_winner_date TIMESTAMP NOT NULL,

	PRIMARY KEY(quiz_winner_user_entity_id)
);


----------------------------------------------------------------
-- Page related tables                                        --
----------------------------------------------------------------
DROP TABLE IF EXISTS comments;
CREATE TABLE comments (
	comment_id INTEGER NOT NULL AUTO_INCREMENT,
		-- The type of page eg. news, faq
	comment_page_id INTEGER NOT NULL,
		-- The id of the content of the page e.g. article number
	comment_subject_id INTEGER NOT NULL,
	comment_text TEXT NOT NULL,
	comment_rating INTEGER NOT NULL,
	comment_reported_count INTEGER NOT NULL,
	comment_deleted BOOL NOT NULL,
	comment_timestamp TIMESTAMP NOT NULL,

	PRIMARY KEY(comment_id)
);

DROP TABLE IF EXISTS pages;
CREATE TABLE pages (
	page_id INTEGER NOT NULL AUTO_INCREMENT,
	page_title VARCHAR(255) NOT NULL,
	page_comments BOOL NOT NULL,
	page_ratings BOOL NOT NULL,
	page_permission INTEGER NOT NULL,

	PRIMARY KEY(page_id)
);

DROP TABLE IF EXISTS page_properties;
CREATE TABLE page_properties (
	page_property_id INTEGER NOT NULL AUTO_INCREMENT,
	page_property_property_type_id INTEGER NOT NULL,
	page_property_page_id INTEGER NOT NULL,
	page_property_photo_id INTEGER NOT NULL,
	page_property_image_id INTEGER NOT NULL,
	page_property_label VARCHAR(255) NOT NULL,
	page_property_text TEXT NOT NULL,
	page_property_permission INTEGER NOT NULL,

	PRIMARY KEY(page_property_id)
);

DROP TABLE IF EXISTS images;
CREATE TABLE images (
	image_id INTEGER NOT NULL AUTO_INCREMENT,
	image_title VARCHAR(255) NOT NULL,
	image_image_type_id INTEGER NOT NULL,
	image_file_extension CHAR(4) NOT NULL,

	PRIMARY KEY(image_id)
);

DROP TABLE IF EXISTS image_types;
CREATE TABLE image_types (
	image_type_id INTEGER NOT NULL AUTO_INCREMENT,
	image_type_name VARCHAR(255) NOT NULL,
	image_type_width INTEGER NOT NULL,
	image_type_height INTEGER NOT NULL,
		-- True if all photos must have a thumb of this size
	image_type_photo BOOL NOT NULL,

	PRIMARY KEY(image_type_id)
);

DROP TABLE IF EXISTS photos;
CREATE TABLE photos (
	photo_id INTEGER NOT NULL AUTO_INCREMENT,
	photo_timestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	photo_user_entity_id INTEGER NOT NULL,
	photo_title VARCHAR(255) NOT NULL,
	photo_width INTEGER NOT NULL,
	photo_height INTEGER NOT NULL,
	photo_gallery BOOL NOT NULL,
	photo_homepage TIMESTAMP NOT NULL,
	photo_deleted BOOL NOT NULL,

	PRIMARY KEY(photo_id)
);

DROP TABLE IF EXISTS photo_tags;
CREATE TABLE photo_tags (
	photo_tag_photo_id INTEGER NOT NULL,
	photo_tag_tag_id INTEGER NOT NULL,
	
	PRIMARY KEY(photo_tag_photo_id, photo_tag_tag_id)
);

----------------------------------------------------------------
-- Advert related tables                                      --
----------------------------------------------------------------
DROP TABLE IF EXISTS adverts;
CREATE TABLE adverts (
	advert_id INTEGER NOT NULL AUTO_INCREMENT,
	advert_organisation_entity_id INTEGER NOT NULL,
	advert_name VARCHAR(255) NOT NULL,
	advert_description TEXT NOT NULL,
	advert_url VARCHAR(255) NOT NULL,
	advert_start_date TIMESTAMP NOT NULL,
	advert_end_date TIMESTAMP NOT NULL,
	advert_max_total INTEGER NOT NULL,
	
	PRIMARY KEY(advert_id)
);

DROP TABLE IF EXISTS advert_bills;
CREATE TABLE advert_bills (
	advert_bill_id INTEGER NOT NULL AUTO_INCREMENT,
	advert_bill_organisation_entity_id INTEGER NOT NULL,
	advert_bill_total INTEGER NOT NULL,
	advert_bill_date TIMESTAMP NOT NULL,
	advert_bill_paid BOOL NOT NULL,
	
	PRIMARY KEY(advert_bill_id)
);

DROP TABLE IF EXISTS advert_bill_items;
CREATE TABLE advert_bill_items (
	advert_bill_item_id INTEGER NOT NULL AUTO_INCREMENT,
	advert_bill_item_advert_instance_id INTEGER NOT NULL,
	advert_bill_item_advert_bill_id INTEGER NOT NULL,
	advert_bill_item_amount INTEGER NOT NULL,
	advert_bill_item_clicks INTEGER NOT NULL,
	advert_bill_item_views INTEGER NOT NULL,
	advert_bill_item_made_date INTEGER NOT NULL,
	
	PRIMARY KEY(advert_bill_item_id)
);

DROP TABLE IF EXISTS advert_instances;
CREATE TABLE advert_instances (
	advert_instance_id INTEGER NOT NULL AUTO_INCREMENT,
	advert_instance_space_type_id INTEGER NOT NULL,
	advert_instance_advert_id INTEGER NOT NULL,
	advert_instance_views INTEGER NOT NULL,
	advert_instance_clicks INTEGER NOT NULL,
	advert_instance_view_cost INTEGER NOT NULL,
	advert_instance_click_cost INTEGER NOT NULL,
	advert_instance_extension CHAR(4) NOT NULL,
	advert_instance_deleted BOOL NOT NULL,

	PRIMARY KEY(advert_instance_id)
);

DROP TABLE IF EXISTS advert_related_articles;
CREATE TABLE advert_related_articles (
	advert_related_article_advert_id INTEGER NOT NULL,
	advert_related_article_article_id INTEGER NOT NULL,

	PRIMARY KEY(advert_related_article_advert_id, advert_related_article_article_id)
);

DROP TABLE IF EXISTS advert_related_organisations;
CREATE TABLE advert_related_organisations (
	advert_related_organisation_advert_id INTEGER NOT NULL,
	advert_related_organisation_organisation_entity_id INTEGER NOT NULL,

	PRIMARY KEY(advert_related_organisation_advert_id, advert_related_organisation_organisation_entity_id)
);

DROP TABLE IF EXISTS page_space_types;
CREATE TABLE page_space_types (
	page_space_type_page_id INTEGER NOT NULL,
	page_space_type_space_type_id INTEGER NOT NULL,
	page_space_type_number INTEGER NOT NULL,

	PRIMARY KEY(page_space_type_page_id, page_space_type_space_type_id, page_space_type_number)
);

DROP TABLE IF EXISTS space_types;
CREATE TABLE space_types (
	space_type_id INTEGER NOT NULL AUTO_INCREMENT,
	space_type_view_cost FLOAT NOT NULL,
	space_type_click_cost FLOAT NOT NULL,
	space_type_width INTEGER NOT NULL,
	space_type_height INTEGER NOT NULL,

	PRIMARY KEY(space_type_id)
);


----------------------------------------------------------------
-- Maps stuff                                                 --
----------------------------------------------------------------

DROP TABLE IF EXISTS buildings;
CREATE TABLE buildings (
	building_id INTEGER NOT NULL AUTO_INCREMENT,
	buidling_name VARCHAR(255) NOT NULL,
	building_code CHAR(15) NOT NULL,
	building_x INTEGER NOT NULL,
	buidling_y INTEGER NOT NULL,
	
	PRIMARY KEY(building_id)
);

DROP TABLE IF EXISTS rooms;
CREATE TABLE rooms (
	room_id CHAR(15) NOT NULL,
	room_room_building_id INTEGER NOT NULL,
	room_type_id INTEGER NOT NULL,
	
	PRIMARY KEY(room_id)
);

DROP TABLE IF EXISTS room_types;
CREATE TABLE room_types (
	room_type_id INTEGER NOT NULL AUTO_INCREMENT,
	room_type_name VARCHAR(255) NOT NULL,
	
	PRIMARY KEY(room_type_id)
);




