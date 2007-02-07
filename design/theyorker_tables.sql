----------------------------------------------------------------
-- Tables about entities				      --
----------------------------------------------------------------

DROP TABLE IF EXISTS entities;
CREATE TABLE entities (
	entity_id					INTEGER		NOT NULL	AUTO_INCREMENT,
	entity_username					VARCHAR(255)	NULL,
	entity_password					CHAR(40)	NULL,
	entity_salt					CHAR(32)	NULL,
	entity_deleted					BOOL		NOT NULL	DEFAULT 0,
	entity_timestamp				TIMESTAMP	NOT NULL	DEFAULT CURRENT_TIMESTAMP,

	PRIMARY KEY(entity_id)
) COMMENT='An entity is anything which may be able to log in or has an entry in the directory.';

DROP TABLE IF EXISTS organisations;
CREATE TABLE organisations (
	organisation_entity_id				INTEGER		NOT NULL,
	organisation_organisation_type_id	 	INTEGER		NOT NULL,
	organisation_parent_organisation_entity_id	INTEGER		NULL,
	organisation_name				VARCHAR(255)	NOT NULL,
	organisation_fileas				VARCHAR(255)	NULL,
	organisation_description			TEXT		NULL,
	organisation_location				VARCHAR(15)	NULL,
	organisation_address				VARCHAR(255)	NULL,
	organisation_postcode				VARCHAR(15)	NULL,
        organisation_phone_external			VARCHAR(255)	NULL,
        organisation_phone_internal			VARCHAR(255)	NULL,
        organisation_fax_number				VARCHAR(255)	NULL,
        organisation_email_address			VARCHAR(255)	NULL,
	organisation_url				VARCHAR(255)	NULL,
	organisation_opening_hours			VARCHAR(255)	NULL,
	organisation_directory_entry_name		VARCHAR(50)	NOT NULL	COMMENT='If null, the organisation does not have a directory page. Must  match regular expression [a-z0-9_]+.',
	organisation_events				BOOL		NOT NULL,
	organisation_yorkipedia_entry 			VARCHAR(255)	NULL		COMMENT='If this is null, the name will be used.',
	organisation_hits				INTEGER		NOT NULL	DEFAULT 0,
	organisation_timestamp				TIMESTAMP	NOT NULL	DEFAULT CURRENT_TIMESTAMP,

	PRIMARY KEY(organisation_entity_id)
	UNIQUE KEY directory_entry_name (organisation_directory_entry_name)
) COMMENT='This table stores both organisations and parts of organisations (e.g. sports teams).';

DROP TABLE IF EXISTS organisation_types;
CREATE TABLE organisation_types (
	organisation_type_id				INTEGER		NOT NULL	AUTO_INCREMENT,
	organisation_type_name				VARCHAR(255)	NOT NULL,
	organisation_type_directory			BOOL		NOT NULL	COMMENT='If false this type of organisation does not appear in the directory.',
  	organisation_type_codename			VARCHAR(30)	NOT NULL,

	PRIMARY KEY(organisation_type_id)
	UNIQUE KEY organisation_type_codename (organisation_type_codename)
) COMMENT='Stores the type of organisations (e.g. an on campus society, an AU club, an external organisation).';

DROP TABLE IF EXISTS organisation_slideshows;
CREATE TABLE organisation_slideshows (
	organisation_slideshow_organisation_entity_id	INTEGER		NOT NULL,
	organisation_slideshow_photo_id			INTEGER		NOT NULL,
	organisation_slideshow_order			INTEGER		NOT NULL,

	PRIMARY KEY(organisation_slideshow_organisation_entity_id, organisation_slideshow_photo_id)
) COMMENT='Each organisation can have a single slideshow in which photos are displayed in the order as specified by organisation_slideshow_order.';

DROP TABLE IF EXISTS organisation_tags;
CREATE TABLE organisation_tags (
	organisation_tag_organisation_entity_id		INTEGER		NOT NULL,
	organisation_tag_tag_id				INTEGER		NOT NULL,

	PRIMARY KEY(organisation_tag_organisation_entity_id, organisation_tag_tag_id)
) COMMENT='Organisations can be tagged to create lists of related organisations etc.';

DROP TABLE IF EXISTS users;
CREATE TABLE users (
 	user_entity_id					INTEGER		NOT NULL,
	user_college_organisation_entity_id		INTEGER		NULL,
	user_image_id					INTEGER		NULL,
	user_firstname					VARCHAR(255)	NOT NULL,
	user_surname					VARCHAR(255)	NOT NULL,
	user_email					VARCHAR(255)	NOT NULL,
	user_nickname					VARCHAR(255)	NULL,
	user_gender					ENUM('m','f')	NULL,
	user_enrolled_year				INTEGER		NULL,
	user_time_format				ENUM('12', '24')	NOT NULL,
	user_store_password				BOOL		NOT NULL,
  	user_office_interface_id			INTEGER		NULL		COMMENT='The primary office interface that will be used.',
  	user_office_password				VARCHAR(40)	NULL		COMMENT='Second level password for the office (high level access only).',
  	user_office_access				BOOL		NOT NULL	COMMENT='Whether the user has access to the office (at any level).',
  	user_admin					BOOL		NOT NULL	COMMENT='User is a root admin.',
	user_timestamp					TIMESTAMP	NOT NULL	DEFAULT CURRENT_TIMESTAMP,

	PRIMARY KEY(user_entity_id)
) COMMENT='Stores users of the yorker. These must be members of the university.';

DROP TABLE IF EXISTS users_article_types;
CREATE TABLE users_article_types (
	user_article_type_user_entity_id		INTEGER		NOT NULL,
	user_article_type_article_type_id		INTEGER		NOT NULL,

	PRIMARY KEY(user_article_type_user_entity_id, user_article_type_article_type_id)
) COMMENT='The types of articles a user can write (eg. Reviews, Features, News etc).';

DROP TABLE IF EXISTS organisation_request_properties;
CREATE TABLE organisation_request_properties (
	organisation_request_properties_organisation_entity_id INTEGER	NOT NULL,
	organisation_request_properties_user_property_id INTEGER	NOT NULL,
	organisation_request_properties_preferred	BOOL		NOT NULL	COMMENT='True if the organisation would prefer have this over other user information.',

	PRIMARY KEY(organisation_request_properties_organisation_entity_id, organisation_request_properties_user_property_id)
) COMMENT='Organisation can request to see particular properties (eg. phone number) of their users.';

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
) COMMENT='Links specific property data to a user.';

DROP TABLE IF EXISTS user_properties;
CREATE TABLE user_properties (
	user_property_id				INTEGER		NOT NULL	AUTO_INCREMENT,
	user_property_property_type_id			INTEGER		NULL,
	user_property_name				TEXT		NULL,

	PRIMARY KEY(user_property_id)
) COMMENT='Provides a link between the property data and its type.';

-- TODO: make this nice :)
DROP TABLE IF EXISTS property_types;
CREATE TABLE property_types (
	property_type_id				INTEGER		NOT NULL	AUTO_INCREMENT,
	property_type_name				VARCHAR(255)	NOT NULL,
	property_type_is_user_prop			BOOL		NOT NULL,
	property_type_primitive				ENUM('text', 'photo', 'image', 'date', 'bool', 'number')	NOT NULL,
	
	PRIMARY KEY(property_type_id)
) COMMENT='Specifies which type the property data is of.';

DROP TABLE IF EXISTS links;
CREATE TABLE links (
	link_id						INTEGER		NOT NULL	AUTO_INCREMENT,
	link_image_id					INTEGER		NOT NULL,
	link_url					VARCHAR(255)	NOT NULL,
	link_name					VARCHAR(255)	NOT NULL,
	
	PRIMARY KEY(link_id)
) COMMENT='Links that can appear on the homepage.';

DROP TABLE IF EXISTS user_links;
CREATE TABLE user_links (
	user_link_id					INTEGER		NOT NULL	AUTO_INCREMENT,
	user_link_user_entity_id			INTEGER		NOT NULL,
	user_link_link_id				INTEGER		NULL,
	user_link_image_id				INTEGER		NULL,
	user_link_url					VARCHAR(255)	NULL,
	user_link_name					VARCHAR(255)	NULL,
	user_link_request				BOOL		NULL		COMMENT='True to make a request to put a user link into predefined links.',
	user_link_order					INTEGER		NOT NULL,
	
	PRIMARY KEY(user_link_id)
) COMMENT='Stores associations between user and either predifined links or custom links.';

-- What organisations can see a users properties.  
DROP TABLE IF EXISTS user_subscription_properties;
CREATE TABLE user_subscription_properties (
	user_subscription_property_organisation_entity_id INTEGER	NOT NULL,
	user_subscription_property_user_property_id	INTEGER		NOT NULL,
	user_subscription_property_user_entity_id	INTEGER		NOT NULL,

	PRIMARY KEY(user_subscription_property_organisation_entity_id, user_subscription_property_property_id, user_subscription_property_user_entity_id)
) COMMENT='Stores the organisations that can see certain user properties.';

----------------------------------------------------------------
-- Subscription and business card related tables	      --
----------------------------------------------------------------

DROP TABLE IF EXISTS business_cards;
CREATE TABLE business_cards (
	business_card_id				INTEGER		NOT NULL	AUTO_INCREMENT,
	business_card_user_entity_id			INTEGER		NULL,
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
) COMMENT='Provides contact information for specified members of an organisation.';

DROP TABLE IF EXISTS business_card_colours;
CREATE TABLE business_card_colours (
	business_card_colour_id				INTEGER		NOT NULL	AUTO_INCREMENT,
	business_card_colour_name			VARCHAR(255)	NOT NULL,
	business_card_colour_background			CHAR(6)		NOT NULL,
	business_card_colour_foreground			CHAR(6)		NOT NULL,

	PRIMARY KEY(business_card_colour_id)
) COMMENT='Stores the variety of different colour schemes for the business cards.';

-- TODO: discrepancy between this and design
DROP TABLE IF EXISTS business_card_types;
CREATE TABLE business_card_types (
	business_card_type_id				INTEGER		NOT NULL	AUTO_INCREMENT,
	business_card_type_name				VARCHAR(255)	NOT NULL,
	business_card_type_organisation_entity_id 	INTEGER		NULL,

	PRIMARY KEY(business_card_type_id)
) COMMENT='Each organisation can define their structure here, so business cards can be given one of the organisations types (eg. Editor, Writer).';

DROP TABLE IF EXISTS subscriptions;
CREATE TABLE subscriptions (
	subscription_organisation_entity_id		INTEGER		NOT NULL,
	subscription_user_entity_id			INTEGER		NOT NULL,
	subscription_interested				BOOL		NOT NULL	COMMENT='True if the events are on the calendar.',
	subscription_member				BOOL		NOT NULL,
	subscription_paid				BOOL		NOT NULL,
	subscription_email				BOOL		NOT NULL	COMMENT='Does the organisation have access to the users e-mail address.',
	subscription_vip				BOOL		NOT NULL,
	subscription_user_confirmed			BOOL		NOT NULL,
	subscription_deleted				BOOL		NOT NULL,
	subscription_timestamp				TIMESTAMP	NOT NULL	DEFAULT CURRENT_TIMESTAMP,
	
	PRIMARY KEY(subscription_organisation_entity_id, subscription_user_entity_id)
) COMMENT='Stores the links between users and organisations. User can be interested (see events on calender), be a member or paid member (organisation can see them) and the organisation can request membership from the user.';

----------------------------------------------------------------
-- Article related tables				      --
----------------------------------------------------------------

DROP TABLE IF EXISTS articles;
CREATE TABLE articles (
	article_id					INTEGER		NOT NULL	AUTO_INCREMENT,
	article_updated					TIMESTAMP	NOT NULL,
	article_content_type_id				INTEGER		NULL		COMMENT='If null, assume the article is not displayed in the standard format.',
	article_organisation_entity_id			INTEGER		NULL		COMMENT='If not null, assume the article is a review of the type specified in content_type_id, or a directory review if that is null.',
	article_last_editor_user_entity_id 		INTEGER		NULL,
	article_created					TIMESTAMP	NOT NULL	DEFAULT CURRENT_TIMESTAMP,
	article_publish_date				TIMESTAMP	NULL,
	article_initial_editor_user_entity_id		INTEGER		NULL,
	article_location				VARCHAR(15)	NULL,
	article_breaking				BOOL		NOT NULL	DEFAULT FALSE,
	article_pulled					BOOL		NOT NULL	DEFAULT FALSE,
	article_hits					INTEGER		NOT NULL	DEFAULT 0,
	article_deleted					BOOL		NOT NULL	DEFAULT FALSE,
	article_live_content_id				INTEGER		NULL,

	PRIMARY KEY(article_id)
) COMMENT='Stores the information about the articles on the site and the current live version of the article (not the article itself).';

-- TODO: what if someone edits and checks at the same time
DROP TABLE IF EXISTS article_contents;
CREATE TABLE article_contents (
	article_content_id				INTEGER		NOT NULL	AUTO_INCREMENT,
	article_content_article_id			INTEGER		NOT NULL,
	article_content_heading				VARCHAR(255)	NOT NULL,
	article_content_subheading			TEXT		NULL,
	article_content_subtext				TEXT		NULL,
	article_content_wikitext			TEXT		NOT NULL,
	article_content_wikitext_cache			TEXT		NULL,
	article_content_blurb				TEXT		NULL,
	
	PRIMARY KEY(article_content_id)
) COMMENT='An article can have a number of contents (i.e. revisions) written. Only 1 is live at a time.';

DROP TABLE IF EXISTS article_events;
CREATE TABLE article_events (
	article_event_article_id			INTEGER		NOT NULL,
	article_event_event_id				INTEGER		NOT NULL,
	
	PRIMARY KEY(article_event_article_id, article_event_event_id)
) COMMENT='Articles about events are linked to the events here.';

DROP TABLE IF EXISTS article_links;
CREATE TABLE article_links (
	article_link_id					INTEGER		NOT NULL	AUTO_INCREMENT,
	article_link_article_id				INTEGER		NOT NULL,
	article_link_name				VARCHAR(255)	NOT NULL,
	article_link_url				VARCHAR(255)	NOT NULL,
	article_link_deleted				BOOL		NOT NULL	DEFAULT FALSE,
	article_link_timestamp				TIMESTAMP	NOT NULL	DEFAULT CURRENT_TIMESTAMP,

	PRIMARY KEY(article_link_id)
) COMMENT='Articles can have external links related to them.';

DROP TABLE IF EXISTS article_photos;
CREATE TABLE article_photos (
	article_photo_id				INTEGER		NOT NULL,
	article_photo_article_id			INTEGER		NOT NULL,
	article_photo_photo_id				INTEGER		NOT NULL,
	article_photo_number				INTEGER		NULL		COMMENT='Null if the photo is to be used as a thumbnail.',
	article_photo_image_type			INTEGER		NULL		COMMENT='If this is null, the image is given a photo_number and is accessable using wiki text. Otherwise it will be used for the thumbnail specified here.',

	PRIMARY KEY(article_photo_id)
	UNIQUE KEY article_photo_article_id (article_photo_article_id, article_photo_photo_id, article_photo_number, article_photo_image_type)
) COMMENT='Photos that may appear in the wikitext.';

DROP TABLE IF EXISTS article_tags;
CREATE TABLE article_tags (
	article_tag_article_id				INTEGER		NOT NULL,
	article_tag_tag_id				INTEGER		NOT NULL,

	PRIMARY KEY(article_tag_article_id, article_tag_tag_id)
) COMMENT='Adds tags to an article, used in searching.';

DROP TABLE IF EXISTS article_writers;
CREATE TABLE article_writers (
	article_writer_user_entity_id			INTEGER		NOT NULL,
	article_writer_article_content_id		INTEGER		NOT NULL,
	
	PRIMARY KEY(article_writer_user_entity_id, article_writer_article_content_id)
) COMMENT='Links the authors of a revision of an article to an article.';

DROP TABLE IF EXISTS fact_boxes;
CREATE TABLE fact_boxes (
	fact_box_id					INTEGER		NOT NULL	AUTO_INCREMENT,
	fact_box_article_content_id			INTEGER		NOT NULL,
	fact_box_wikitext				TEXT		NOT NULL,
	fact_box_deleted				BOOL		NOT NULL	DEFAULT FALSE,
	fact_box_timestamp				TIMESTAMP	NOT NULL	DEFAULT CURRENT_TIMESTAMP,

	PRIMARY KEY(fact_box_id)
) COMMENT='Stores the fact boxes which are linked to revisions of an article.';

DROP TABLE IF EXISTS related_articles;
CREATE TABLE related_articles (
	related_article_1_article_id			INTEGER		NOT NULL,
	related_article_2_article_id			INTEGER		NOT NULL,

	PRIMARY KEY(related_article_1_article_id, related_article_2_article_id)
) COMMENT='Relates two articles together.';

DROP TABLE IF EXISTS pull_quotes;
CREATE TABLE pull_quotes (
	pull_quote_id					INTEGER 	NOT NULL	AUTO_INCREMENT,
	pull_quote_article_content_id			INTEGER 	NOT NULL,
	pull_quote_text 				TEXT		NOT NULL,
	pull_quote_person				VARCHAR(255)	NOT NULL,
	pull_quote_position				VARCHAR(255)	NOT NULL,
	pull_quote_order				INTEGER 	NOT NULL,
	pull_quote_deleted				BOOL		NOT NULL,

	PRIMARY KEY(pull_quote_id)
) COMMENT='Stores the pull quotes which are linked to revisions of an article.';

DROP TABLE IF EXISTS requests;
CREATE TABLE requests (
	request_id					INTEGER 	NOT NULL	AUTO_INCREMENT,
	request_timestamp				TIMESTAMP	NOT NULL	DEFAULT CURRENT_TIMESTAMP,
	request_entity_id				INTEGER 	NOT NULL	COMMENT='Who requested the thing.',
	request_article_id				INTEGER 	NOT NULL	COMMENT='An article is created with a request for an article.',
	request_type_id 				INTEGER 	NOT NULL,
	request_organisation_entity_id			INTEGER 	NULL,
	request_text					TEXT		NOT NULL,
	request_blurb					TEXT		NOT NULL,
	request_deadline				TIMESTAMP	NOT NULL,
	request_accepted				BOOL		NOT NULL,
	request_deleted 				BOOL		NOT NULL,

	PRIMARY KEY(request_id)
) COMMENT='Stores requests of various types (eg. Photo, Article) with a description of the request.';

DROP TABLE IF EXISTS request_photos;
CREATE TABLE request_photos (
	request_photo_request_id			INTEGER 	NOT NULL,
	request_photo_photo_id				INTEGER 	NOT NULL	DEFAULT 0,
	request_photo_deleted				BOOL		NOT NULL,
	request_photo_timestamp 			TIMESTAMP	NOT NULL	DEFAULT CURRENT_TIMESTAMP,

	PRIMARY KEY(request_photo_request_id, request_photo_photo_id)
) COMMENT='Links photos with photo requests.';

DROP TABLE IF EXISTS request_types;
CREATE TABLE request_types (
	request_type_id 				INTEGER 	NOT NULL	AUTO_INCREMENT,
	request_type_name				VARCHAR(255)	NOT NULL,
	
	PRIMARY KEY(request_type_id)
) COMMENT='Stores the possible request types (eg. Photo, Article, Suggestion).';

DROP TABLE IF EXISTS request_users;
CREATE TABLE request_users (
	request_user_request_id 			INTEGER		NOT NULL,
	request_user_user_entity_id			INTEGER 	NOT NULL,
	request_user_accepted				BOOL		NOT NULL,
	request_user_rejected				BOOL		NOT NULL,
	
	PRIMARY KEY(request_user_request_id, request_user_user_entity_id)
) COMMENT='Stores the users who have had a request made for them and their responses.';

DROP TABLE IF EXISTS content_types;
CREATE TABLE content_types (
	content_type_id					INTEGER		NOT NULL	AUTO_INCREMENT,
	content_type_codename				VARCHAR(30)	NULL,
	content_type_parent_content_type_id		INTEGER		NULL,
	content_type_name				VARCHAR(255)	NOT NULL,
	content_type_archive				BOOL		NOT NULL,
	content_type_blurb				TEXT		NOT NULL,
	content_type_has_reviews			BOOL		NOT NULL,

	PRIMARY KEY(content_type_id)
) COMMENT='Article and review types, can be in a hierarchy (eg. News, Reviews, Features etc).';

----------------------------------------------------------------
-- Event and calendar related tables			      --
----------------------------------------------------------------

DROP TABLE IF EXISTS events;
CREATE TABLE events (
	event_id					INTEGER 	NOT NULL,
	event_image_id					INTEGER 	NULL,
	event_parent_id 				INTEGER 	NULL,
	event_type_id					INTEGER 	NOT NULL,
	event_name					TEXT		NULL,
	event_description				TEXT		NULL,
	event_blurb					TEXT		NULL,
	event_deleted					BOOL		NOT NULL,
	event_timestamp 				TIMESTAMP	NULL,
	
	PRIMARY KEY(event_id)
) COMMENT='A list of all events with information about them.';

DROP TABLE IF EXISTS event_entities;
CREATE TABLE event_entities (
	event_entity_entity_id				INTEGER 	NOT NULL,
	event_entity_event_id				INTEGER 	NOT NULL,
	event_entity_relationship			ENUM('own','subscribe') NOT NULL	DEFAULT 'subscribe',
  	event_entity_confirmed				BOOL		NOT NULL,
	
	PRIMARY KEY(event_entity_entity_id, event_entity_event_id)
) COMMENT='An event can be linked to a number of organisations.';

DROP TABLE IF EXISTS event_occurrence_users;
CREATE TABLE event_occurrence_users (
	event_occurrence_user_user_entity_id		INTEGER		NOT NULL,
	event_occurrence_user_event_occurrence_id	INTEGER 	NOT NULL,
	event_occurrence_user_hide			BOOL		NOT NULL,
	event_occurrence_user_rsvp			BOOL		NOT NULL,

	PRIMARY KEY(event_occurrence_user_user_entity_id, event_occurrence_user_event_occurrence_id)
) COMMENT='Users can customize event ocurrences on the timetable.';

DROP TABLE IF EXISTS event_occurrences;
CREATE TABLE event_occurrences (
	event_occurrence_id				INTEGER 	NOT NULL,
	event_occurrence_timestamp			TIMESTAMP	NOT NULL	DEFAULT CURRENT_TIMESTAMP,
	event_occurrence_next_id			INTEGER 	NULL,
	event_occurrence_event_id			INTEGER 	NOT NULL,
	event_occurrence_state				ENUM('draft','trashed','published','cancelled','deleted')	 NOT NULL	DEFAULT 'draft',
	event_occurrence_description			TEXT		NOT NULL,
	event_occurrence_location			VARCHAR(15)	NULL,
	event_occurrence_postcode			VARCHAR(15)	NULL,
	event_occurrence_start_time			TIMESTAMP	NOT NULL,
	event_occurrence_end_time			TIMESTAMP	NOT NULL,
	event_occurrence_all_day			BOOL		NOT NULL,
	event_occurrence_ends_late			BOOL		NOT NULL,
	
	PRIMARY KEY(event_occurrence_id)
) COMMENT='An event occurence is a specific instance of an event with time and location.';

DROP TABLE IF EXISTS event_types;
CREATE TABLE event_types (
	event_type_id					INTEGER 	NOT NULL	AUTO_INCREMENT,
	event_type_name 				VARCHAR(255)	NOT NULL,
	
	PRIMARY KEY(event_type_id)
) COMMENT='Types of event (e.g. Social, Meeting, Training etc).';

DROP TABLE IF EXISTS reminders;
CREATE TABLE reminders (
	reminder_id					INTEGER 	NOT NULL	AUTO_INCREMENT,
	reminder_user_entity_id 			INTEGER 	NOT NULL,
	reminder_name					VARCHAR(255)	NOT NULL,
	reminder_description				TEXT		NOT NULL,
	reminder_timestamp				TIMESTAMP	NOT NULL	DEFAULT CURRENT_TIMESTAMP

	PRIMARY KEY(reminder_id)
) COMMENT='';

DROP TABLE IF EXISTS todo_list_items;
CREATE TABLE todo_list_items (
	todo_list_item_id				INTEGER 	NOT NULL	AUTO_INCREMENT,
	todo_list_item_event_occurrence_id		INTEGER 	NOT NULL,
	todo_list_item_reminder_id			INTEGER 	NOT NULL,
	todo_list_item_todo_priority_id 		INTEGER 	NOT NULL,
	todo_list_item_user_entity_id			INTEGER 	NOT NULL,
	todo_list_item_name				VARCHAR(255)	NOT NULL,
	todo_list_item_description			TEXT		NOT NULL,
	todo_list_item_done				BOOL		NOT NULL,
	todo_list_item_event_occurence_id		INTEGER 	NOT NULL,
	todo_list_item_deadline 			TIMESTAMP	NOT NULL	DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

	PRIMARY KEY(todo_list_item_id)
);

DROP TABLE IF EXISTS todo_priorities;
CREATE TABLE todo_priorities (
	todo_priority_id				INTEGER 	NOT NULL	AUTO_INCREMENT,
	todo_priority_name				VARCHAR(255)	NOT NULL,
	todo_priority_order				INTEGER 	NOT NULL,
	
	PRIMARY KEY(todo_priority_id)
);

DROP TABLE IF EXISTS years;
CREATE TABLE years (
	year_id 					INTEGER 	NOT NULL,
	year_start_autumn				TIMESTAMP	NOT NULL,
	year_end_autumn					TIMESTAMP	NOT NULL,
	year_start_spring				TIMESTAMP	NOT NULL,
	year_end_spring					TIMESTAMP	NOT NULL,
	year_start_summer				TIMESTAMP	NOT NULL,
	year_end_summer					TIMESTAMP	NOT NULL,

	PRIMARY KEY(year_id)
) COMMENT='Stores term dates for academic years.';


----------------------------------------------------------------
-- Review related tables				      --
----------------------------------------------------------------

DROP TABLE IF EXISTS review_contexts;
CREATE TABLE review_contexts (
	review_context_organisation_entity_id		INTEGER 	NOT NULL,
	review_context_content_type_id			INTEGER 	NOT NULL,
	review_context_live_content_id			INTEGER 	NOT NULL,
	review_context_deleted				BOOL		NOT NULL,
	
	PRIMARY KEY(review_context_organisation_entity_id, review_context_content_type_id)
) COMMENT='Information about an organisation in a specific category (e.g. evil eye for food).';

DROP TABLE IF EXISTS review_context_contents;
CREATE TABLE review_context_contents (
	review_context_content_id 			INTEGER 	NOT NULL 	AUTO_INCREMENT,
	review_context_content_organisation_entity_id 	INTEGER 	NOT NULL,
	review_context_content_content_type_id		INTEGER 	NOT NULL,
	review_context_content_blurb 			TEXT		NOT NULL,
	review_context_content_recommend_item_price	INTEGER 	NULL,
	review_context_content_recommend_item		TEXT		NULL,
	review_context_content_average_price_upper 	INTEGER 	NULL,
	review_context_content_average_price_lower 	INTEGER 	NULL,
	review_context_content_rating 			INTEGER		NOT NULL,
	review_context_content_directions 		TEXT		NULL,
	review_context_content_book_online 		BOOL		NOT NULL,
	
	PRIMARY KEY(review_context_content_id)
) COMMENT='Similar to article content, but contains specific information for review type organisations.';

DROP TABLE IF EXISTS review_context_tags;
CREATE TABLE review_context_tags (
	review_context_tag_tag_id 			INTEGER 	NOT NULL,
	review_context_organisation_entity_id 		INTEGER 	NOT NULL,
	review_context_content_type_id 			INTEGER 	NOT NULL,

	PRIMARY KEY(review_context_tag_tag_id, review_context_organisation_entity_id, review_context_content_type_id)
) COMMENT='Allows the tagging of a category of a specific organisaion for use in searching.';

DROP TABLE IF EXISTS review_context_slideshows;
CREATE TABLE review_context_slideshows (
	review_context_slideshow_organisation_entity_id	INTEGER 	NOT NULL,
	review_context_slideshow_content_type_id	INTEGER 	NOT NULL,
	review_context_slideshow_photo_id 		INTEGER 	NOT NULL,
	review_context_slideshow_order 			INTEGER 	NOT NULL,
	
	PRIMARY KEY(review_context_slideshow_review_context_content_type_id, review_context_slideshow_review_context_organisation_entity_id, review_context_slideshow_order)
) COMMENT='Each review type organisation can have a single slideshow in which photos are displayed in the order as specified by review_slideshow_order.';


DROP TABLE IF EXISTS bar_crawl_organisations;
CREATE TABLE bar_crawl_organisations (
	bar_crawl_organisation_bar_crawl_id 		INTEGER 	NOT NULL,
	bar_crawl_organisation_organisation_entity_id 	INTEGER 	NOT NULL,
	bar_crawl_organisation_order 			INTEGER 	NOT NULL,
	bar_crawl_organisation_recommend 		VARCHAR(255) 	NOT NULL,
	bar_crawl_organisation_recommend_price 		INTEGER 	NOT NULL,
	
	PRIMARY KEY(bar_crawl_organisation_bar_crawl_id, bar_crawl_organisation_organisation_entity_id)
) COMMENT='Each entry is a bar which is part of the bar crawl. The order is specified by the order field and recommend suggests what to drink.';


DROP TABLE IF EXISTS leagues;
CREATE TABLE leagues (
	league_id 					INTEGER 	NOT NULL 	AUTO_INCREMENT,
	league_image_id 				INTEGER 	NOT NULL,
	league_content_type_id 				INTEGER 	NOT NULL,
	league_name 					VARCHAR(255) 	NOT NULL,
	league_size 					INTEGER 	NOT NULL,
	league_autogenerated				BOOL		NOT NULL	DEFAULT 0	COMMENT='If true, this league is automatically generated from user opinion.',
	league_codename					VARCHAR(30)	NOT NULL,
	
	PRIMARY KEY(league_id)
) COMMENT='top 10 for food - blah rewrite me. NOTE: league_content_type_id';

DROP TABLE IF EXISTS league_entries;
CREATE TABLE league_entries (
	league_entry_league_id 				INTEGER 	NOT NULL 	AUTO_INCREMENT,
	league_entry_organisation_entity_id		INTEGER 	NOT NULL,
	league_entry_position 				INTEGER 	NOT NULL,

	PRIMARY KEY(league_entry_league_id, league_entry_review_context_organisation_entity_id)
) COMMENT='NOTE: league_entry_review_context_organisation_entity_id';

----------------------------------------------------------------
-- Random shit related tables				      --
----------------------------------------------------------------

DROP TABLE IF EXISTS campaigns;
CREATE TABLE campaigns (
	campaign_id 					INTEGER 	NOT NULL 	AUTO_INCREMENT,
	campaign_article_id 				INTEGER 	NOT NULL,
	campaign_name 					VARCHAR(255) 	NOT NULL,
	campaign_votes 					INTEGER 	NOT NULL,
	campaign_petition 				BOOL 		NOT NULL,
	campaign_petition_signatures 			INTEGER 	NOT NULL,
	campaign_deleted 				BOOL 		NOT NULL,
	campaign_timestamp 				TIMESTAMP 	NOT NULL 	DEFAULT CURRENT_TIMESTAMP,

	PRIMARY KEY(campaign_id)
) COMMENT='Holds the current possible campaigns that can be voted for and then petitioned.';

DROP TABLE IF EXISTS campaign_users;
CREATE TABLE campaign_users (
	campaign_user_campaign_id 			INTEGER 	NOT NULL,
	campaign_user_user_entity_id 			INTEGER 	NOT NULL,
	
	PRIMARY KEY(campaign_user_campaign_id, campaign_user_user_entity_id)
) COMMENT='Stores who has voted for a campaign.';

DROP TABLE IF EXISTS progress_report_articles;
CREATE TABLE progress_report_articles (
	progress_report_article_article_id		INTEGER 	NOT NULL,
	progress_report_article_campaign_id		INTEGER 	NOT NULL,
	progress_report_article_charity_id		INTEGER 	NOT NULL,

	PRIMARY KEY(progress_report_article_article_id, progress_report_article_campaign_id, progress_report_article_charity_id)
) COMMENT='Stores who has voted for a campaign.';

DROP TABLE IF EXISTS charities;
CREATE TABLE charities (
	charity_id					INTEGER 	NOT NULL 	AUTO_INCREMENT,
	charity_name 					VARCHAR(255) 	NOT NULL,
	charity_article_id 				INTEGER 	NOT NULL,
	charity_goal_text 				TEXT 		NOT NULL,
	charity_goal 					INTEGER 	NOT NULL,
	charity_total 					FLOAT 		NOT NULL,
	
	PRIMARY KEY(charity_id)
) COMMENT='Current charities with related article and specific goal total.';

DROP TABLE IF EXISTS charity_donors;
CREATE TABLE charity_donors (
	charity_donor_id 				INTEGER 	NOT NULL 	AUTO_INCREMENT,
	charity_donor_charity_id 			INTEGER 	NOT NULL,
	charity_donor_name 				VARCHAR(255) 	NOT NULL,
	charity_donor_organisation_entity_id 		INTEGER 	NOT NULL,
	charity_donor_amount 				INTEGER 	NOT NULL,
	charity_donor_timestamp 			TIMESTAMP 	NOT NULL 	DEFAULT CURRENT_TIMESTAMP,

	PRIMARY KEY(charity_donor_id)
) COMMENT='How much a donor has donated to a particular charity.';


DROP TABLE IF EXISTS tags;
CREATE TABLE tags (
	tag_id 						INTEGER 	NOT NULL 	AUTO_INCREMENT,
	tag_name 					VARCHAR(255) 	NOT NULL,
	tag_type					ENUM('article','photo','grouped')	NOT NULL,
	tag_tag_group_id				INTEGER		NULL		COMMENT='If tag_type=grouped then this must be set to the group of the tag.',
	tag_order					INTEGER		NULL		COMMENT='If the tag group is ordered, this must be non-null.',
	tag_banner_name 				VARCHAR(255) 	NULL		COMMENT='Used for article tags such as Exclusive, where the tag will enable code related to the tag.',
	tag_archive 					BOOL 		NOT NULL 	DEFAULT 0,
	tag_deleted 					BOOL 		NOT NULL,
	
	PRIMARY KEY(tag_id)
) COMMENT='List of tags that are used in searching.';

DROP TABLE IF EXISTS tag_groups;
CREATE TABLE tag_groups (
	tag_group_id 					INTEGER 	NOT NULL 	AUTO_INCREMENT,
	tag_group_name					VARCHAR(255)	NOT NULL,
	tag_group_content_type_id			INTEGER		NULL		COMMENT='Allows groups to be specific to content_types, if null the group is general.',
	tag_group_ordered				BOOL		NOT NULL	COMMENT='For example, the 'Price' group can be ordered.',
	
	PRIMARY KEY(tag_group_id)
) COMMENT='Groups a set of tags that have similar types (eg. Food, Drink).';
	
DROP TABLE IF EXISTS colleges;
CREATE TABLE colleges (
	college_organisation_entity_id			INTEGER 	NOT NULL 	AUTO_INCREMENT,
	college_name 					VARCHAR(255) 	NOT NULL,
	college_ranking 				INTEGER 	NOT NULL,
	college_image_id				INTEGER		NOT NULL,
	
	PRIMARY KEY(college_organisation_entity_id)
) COMMENT='The colleges and their rankings (a college is an organisation).';

DROP TABLE IF EXISTS college_rankings;
CREATE TABLE college_rankings (
	college_ranking_id 				INTEGER 	NOT NULL 	AUTO_INCREMENT,
	college_ranking_college_organisation_entity_id	INTEGER 	NOT NULL,
	college_ranking_user_entity_id			INTEGER 	NOT NULL	COMMENT='Writers ID.',
	college_ranking_publisher_id 			INTEGER 	NOT NULL,
	college_ranking_text 				TEXT 		NOT NULL,
	college_ranking_rank 				INTEGER SIGNED 	NOT NULL,
	college_ranking_published 			BOOL 		NOT NULL,
	college_ranking_deleted 			BOOL 		NOT NULL,
	college_ranking_timestamp 			TIMESTAMP 	NOT NULL 	DEFAULT CURRENT_TIMESTAMP,

	PRIMARY KEY(college_ranking_id)
) COMMENT='Information about the colleges rankings.';

DROP TABLE IF EXISTS quiz_questions;
CREATE TABLE quiz_questions (
	quiz_question_id 				INTEGER 	NOT NULL 	AUTO_INCREMENT,
	quiz_question_question 				TEXT 		NOT NULL,
	quiz_question_answer1 				TEXT 		NOT NULL,
	quiz_question_answer2 				TEXT 		NOT NULL,
	quiz_question_answer3 				TEXT 		NOT NULL,
	quiz_question_answer4 				TEXT 		NOT NULL,
	quiz_question_correct_answer 			INTEGER 	NOT NULL,
	quiz_question_hits 				INTEGER 	NOT NULL,
	quiz_question_active 				BOOL 		NOT NULL,

	PRIMARY KEY(quiz_question_id)
) COMMENT='Active questions from this list can be asked on the quiz.';

DROP TABLE IF EXISTS quiz_results;
CREATE TABLE quiz_results (
	quiz_result_user_entity_id 			INTEGER 	NOT NULL,
	quiz_result_date 				TIMESTAMP 	NOT NULL	DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	quiz_result_score 				INTEGER 	NOT NULL,
	quiz_result_winner				BOOL		NOT NULL	DEFAULT 0	COMMENT='Fortnightly winners are kept in with this bool set to true.',

	PRIMARY KEY(quiz_result_user_entity_id)
) COMMENT='Stores the results for all the quizs that the user has taken.';

----------------------------------------------------------------
-- Page related tables					      --
----------------------------------------------------------------

DROP TABLE IF EXISTS comments;
CREATE TABLE comments (
	comment_id 					INTEGER 	NOT NULL 	AUTO_INCREMENT,
	comment_content_type_id				INTEGER 	NOT NULL,
	comment_article_id 				INTEGER 	NULL,
	comment_organisation_entity_id			INTEGER		NULL,
	comment_user_entity_id				INTEGER		NOT NULL,
	comment_text 					TEXT 		NOT NULL,
	comment_rating 					INTEGER 	NOT NULL,
	comment_reported_count 				INTEGER 	NOT NULL,
	comment_deleted 				BOOL 		NOT NULL,
	comment_timestamp 				TIMESTAMP 	NOT NULL	DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

	PRIMARY KEY(comment_id)
) COMMENT='Comment made by users on articles.';

DROP TABLE IF EXISTS comment_summary_cache;
CREATE TABLE comment_summary_cache (
	comment_summary_cache_content_type_id  		INTEGER  	NOT NULL,
	comment_summary_cache_organisation_entity_id 	INTEGER 	NULL,
	comment_summary_cache_article_id 		INTEGER 	NULL,
	comment_summary_cache_comment_count 		INTEGER		NOT NULL,
	comment_summary_cache_average_rating		INTEGER		NOT NULL,
	
	UNIQUE KEY comment_summary_cache_content_type_id (comment_summary_cache_content_type_id, comment_summary_cache_organisation_entity_id, comment_summary_cache_article_id)
) COMMENT='Generalises comments caching.';

DROP TABLE IF EXISTS pages;
CREATE TABLE pages (
	page_id 					INTEGER 	NOT NULL 	AUTO_INCREMENT,
	page_codename					VARCHAR(30)	NOT NULL 	COMMENT='This string is hard coded into the page, used to identify it.',
	page_title 					VARCHAR(255) 	NOT NULL,
	page_description				VARCHAR(255)	NULL,
	page_keywords					VARCHAR(255)	NULL,
	page_comments 					BOOL 		NOT NULL,
	page_ratings 					BOOL 		NOT NULL,
	page_permission 				INTEGER 	NOT NULL,

	PRIMARY KEY(page_id)
	UNIQUE KEY page_codename (page_codename)
) COMMENT='Each page has its .';

DROP TABLE IF EXISTS page_properties;
CREATE TABLE page_properties (
	page_property_id 				INTEGER 	NOT NULL 	AUTO_INCREMENT,
	page_property_property_type_id 			INTEGER 	NOT NULL,
	page_property_page_id 				INTEGER 	NULL,
	page_property_photo_id 				INTEGER 	NULL,
	page_property_image_id 				INTEGER 	NULL,
	page_property_label 				VARCHAR(255) 	NOT NULL,
	page_property_text 				TEXT 		NULL,
	page_property_permission 			INTEGER 	NOT NULL,

	PRIMARY KEY(page_property_id)
	UNIQUE KEY page_property_property_type_id (page_property_property_type_id, page_property_page_id, page_property_label)
) COMMENT='Specifies which type the property data is of.';

DROP TABLE IF EXISTS images;
CREATE TABLE images (
	image_id 					INTEGER 	NOT NULL	AUTO_INCREMENT,
	image_photo_id					INTEGER		NULL		COMMENT='Null for images that are not thumbnails.',
	image_title 					VARCHAR(255) 	NULL		COMMENT='Null for thumbnails, which use photo title as title.',
	image_image_type_id 				INTEGER 	NOT NULL,
	image_file_extension 				CHAR(4) 	NULL		COMMENT='If null, assume .jpg',

	PRIMARY KEY(image_id)
) COMMENT='';

DROP TABLE IF EXISTS image_types;
CREATE TABLE image_types (
	image_type_id 					INTEGER 	NOT NULL 	AUTO_INCREMENT,
	image_type_name 				VARCHAR(255) 	NOT NULL,
	image_type_width 				INTEGER 	NOT NULL,
	image_type_height 				INTEGER 	NOT NULL,
	image_type_photo_thumbnail			BOOL 		NOT NULL	COMMENT='True if all photos must have a thumb of this size.',
	image_type_codename				VARCHAR(30)	NOT NULL,

	PRIMARY KEY(image_type_id)
) COMMENT='';

DROP TABLE IF EXISTS photos;
CREATE TABLE photos (
	photo_id 					INTEGER 	NOT NULL 	AUTO_INCREMENT,
	photo_timestamp 				TIMESTAMP 	NOT NULL 	DEFAULT CURRENT_TIMESTAMP,
	photo_author_user_entity_id			INTEGER 	NOT NULL,
	photo_title 					VARCHAR(255) 	NOT NULL,
	photo_width 					INTEGER 	NOT NULL,
	photo_height 					INTEGER 	NOT NULL,
	photo_gallery 					BOOL 		NOT NULL,
	photo_homepage 					TIMESTAMP 	NULL,
	photo_deleted 					BOOL 		NOT NULL,

	PRIMARY KEY(photo_id)
) COMMENT='';

DROP TABLE IF EXISTS photo_tags;
CREATE TABLE photo_tags (
	photo_tag_photo_id 				INTEGER 	NOT NULL,
	photo_tag_tag_id 				INTEGER 	NOT NULL,
	
	PRIMARY KEY(photo_tag_photo_id, photo_tag_tag_id)
) COMMENT='';

----------------------------------------------------------------
-- Advert related tables				      --
----------------------------------------------------------------
DROP TABLE IF EXISTS adverts;
CREATE TABLE adverts (
	advert_id 					INTEGER 	NOT NULL 	AUTO_INCREMENT,
	advert_organisation_entity_id 			INTEGER 	NOT NULL,
	advert_name 					VARCHAR(255) 	NOT NULL,
	advert_description 				TEXT 		NOT NULL,
	advert_url 					VARCHAR(255) 	NOT NULL,
	advert_start_date 				TIMESTAMP 	NOT NULL,
	advert_end_date 				TIMESTAMP 	NOT NULL,
	advert_max_total 				INTEGER 	NOT NULL,
	
	PRIMARY KEY(advert_id)
) COMMENT='All adverts currently displayed on the site are stored here.';

DROP TABLE IF EXISTS advert_bills;
CREATE TABLE advert_bills (
	advert_bill_id 					INTEGER 	NOT NULL 	AUTO_INCREMENT,
	advert_bill_organisation_entity_id 		INTEGER 	NOT NULL,
	advert_bill_total 				INTEGER 	NOT NULL,
	advert_bill_date 				TIMESTAMP 	NOT NULL,
	advert_bill_paid 				BOOL 		NOT NULL,
	
	PRIMARY KEY(advert_bill_id)
) COMMENT='Bill the advert companies for the amount generated by their adverts.';

DROP TABLE IF EXISTS advert_bill_items;
CREATE TABLE advert_bill_items (
	advert_bill_item_id 				INTEGER 	NOT NULL 	AUTO_INCREMENT,
	advert_bill_item_advert_instance_id 		INTEGER 	NOT NULL,
	advert_bill_item_advert_bill_id 		INTEGER 	NOT NULL,
	advert_bill_item_amount 			INTEGER 	NOT NULL,
	advert_bill_item_clicks 			INTEGER 	NOT NULL,
	advert_bill_item_views 				INTEGER 	NOT NULL,
	advert_bill_item_made_date 			INTEGER 	NOT NULL,
	
	PRIMARY KEY(advert_bill_item_id)
) COMMENT='Keeps track of the number of adverts owned by a company and the number of clicks since last billing.';

DROP TABLE IF EXISTS advert_instances;
CREATE TABLE advert_instances (
	advert_instance_id 				INTEGER 	NOT NULL 	AUTO_INCREMENT,
	advert_instance_space_type_id 			INTEGER 	NOT NULL,
	advert_instance_advert_id 			INTEGER 	NOT NULL,
	advert_instance_views 				INTEGER 	NOT NULL,
	advert_instance_clicks 				INTEGER 	NOT NULL,
	advert_instance_view_cost 			INTEGER 	NOT NULL,
	advert_instance_click_cost 			INTEGER 	NOT NULL,
	advert_instance_extension 			CHAR(4) 	NOT NULL,
	advert_instance_deleted 			BOOL 		NOT NULL,

	PRIMARY KEY(advert_instance_id)
) COMMENT='';

DROP TABLE IF EXISTS advert_related_articles;
CREATE TABLE advert_related_articles (
	advert_related_article_advert_id 		INTEGER 	NOT NULL,
	advert_related_article_article_id 		INTEGER 	NOT NULL,

	PRIMARY KEY(advert_related_article_advert_id, advert_related_article_article_id)
);

DROP TABLE IF EXISTS advert_related_organisations;
CREATE TABLE advert_related_organisations (
	advert_related_organisation_advert_id 		INTEGER 	NOT NULL,
	advert_related_organisation_organisation_entity_id INTEGER 	NOT NULL,

	PRIMARY KEY(advert_related_organisation_advert_id, advert_related_organisation_organisation_entity_id)
);

DROP TABLE IF EXISTS page_space_types;
CREATE TABLE page_space_types (
	page_space_type_page_id 			INTEGER 	NOT NULL,
	page_space_type_space_type_id 			INTEGER 	NOT NULL,
	page_space_type_number 				INTEGER 	NOT NULL,

	PRIMARY KEY(page_space_type_page_id, page_space_type_space_type_id, page_space_type_number)
);

DROP TABLE IF EXISTS space_types;
CREATE TABLE space_types (
	space_type_id 					INTEGER 	NOT NULL 	AUTO_INCREMENT,
	space_type_view_cost 				FLOAT 		NOT NULL,
	space_type_click_cost 				FLOAT 		NOT NULL,
	space_type_width 				INTEGER 	NOT NULL,
	space_type_height 				INTEGER 	NOT NULL,

	PRIMARY KEY(space_type_id)
);


----------------------------------------------------------------
-- Maps stuff						      --
----------------------------------------------------------------

DROP TABLE IF EXISTS buildings;
CREATE TABLE buildings (
	building_id 					INTEGER 	NOT NULL 	AUTO_INCREMENT,
	buidling_name 					VARCHAR(255) 	NOT NULL,
	building_code 					CHAR(15) 	NOT NULL,
	building_x 					INTEGER 	NOT NULL,
	buidling_y 					INTEGER 	NOT NULL,
	
	PRIMARY KEY(building_id)
) COMMENT='Campus building information for the maps.';

DROP TABLE IF EXISTS rooms;
CREATE TABLE rooms (
	room_id 					CHAR(15) 	NOT NULL,
	room_building_id 				INTEGER 	NOT NULL,
	room_type_id 					INTEGER 	NOT NULL,
	
	PRIMARY KEY(room_id)
) COMMENT='Links rooms to the building that they are contained within.';

DROP TABLE IF EXISTS room_types;
CREATE TABLE room_types (
	room_type_id 					INTEGER 	NOT NULL 	AUTO_INCREMENT,
	room_type_name 					VARCHAR(255) 	NOT NULL,
	
	PRIMARY KEY(room_type_id)
) COMMENT='The possible types of room (eg. )';

----------------------------------------------------------------
-- Extras						      --
----------------------------------------------------------------

DROP TABLE IF EXISTS quotes;
CREATE TABLE quotes (
	quote_id 					INTEGER 	NOT NULL 	AUTO_INCREMENT,
	quote_text					TEXT		NOT NULL,
	quote_author 					VARCHAR(255) 	NOT NULL,
	quote_last_displayed				TIMESTAMP	NOT NULL,

	PRIMARY KEY(quote_id)
) COMMENT='';

DROP TABLE IF EXISTS office_interfaces;
CREATE TABLE office_interfaces (
	office_interface_id 				INTEGER 	NOT NULL 	AUTO_INCREMENT,
	office_interface_codename 			VARCHAR(30) 	NOT NULL,

	PRIMARY KEY(office_interface_id)
) COMMENT='';

