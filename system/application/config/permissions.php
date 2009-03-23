<?php

/**
 * @file config/permissions.php
 * @brief Permission names.
 * @author James Hogan <james_hogan@theyorker.co.uk>
 */

/// Permission types
/**
 * Vocab guidelines:
 * - all upper case
 * - use MODIFY instead of EDIT
 * - use ADD instead of CREATE
 */
$config['permissions'] = array(
	/// @todo Review and implement advertising permissions
	'ADVERTISING_VIEW'                 => '(unimplemented) View adverts',
	'ADVERTISING_ADD'                  => '(unimplemented) Create new adverts',
	'ADVERTISING_MODIFY'               => '(unimplemented) Modify adverts',
	'ADVERTISING_LIVE'                 => '(unimplemented) Make an advert live',
	'ADVERTISING_PULL'                 => '(unimplemented) Pull a live advert',

	'ANNOUNCEMENT_VIEW'                => 'View all announcements',
	'ANNOUNCEMENT_SEND'                => 'Send an announcement to a group of users',
	'ANNOUNCEMENT_MODIFY'              => 'Modify announcements',
	'ANNOUNCEMENT_DELETE'              => 'Delete announcements',

	/// @todo Review and implement article permissions
	'ARTICLE_VIEW'                     => 'View article contents',
	'ARTICLE_ADD'                      => 'Create new articles',
	'ARTICLE_MODIFY'                   => '(unimplemented) Modify articles',
	'ARTICLE_DELETE'                   => '(unimplemented) Delete articles',
	'ARTICLE_PUBLISH'                  => '(unimplemented) Publish articles',
	'ARTICLE_PULL'                     => '(unimplemented) Pull published articles',
	
	/// @todo Review and implement article type permissions
	'ARTICLETYPES_VIEW'                => '(unimplemented) View article types manager',
	'ARTICLETYPES_ADD'                 => '(unimplemented) Create new article types',
	'ARTICLETYPES_MODIFY'              => '(unimplemented) Modify article types',
	'ARTICLETYPES_DELETE'              => '(unimplemented) Delete article types',
	
	/// @todo Review and implement homepage banner permissions
	'BANNERS_VIEW'                     => '(unimplemented) View banners',
	'BANNERS_UPLOAD'                   => '(unimplemented) Upload new banners',
	'BANNERS_MODIFY'                   => '(unimplemented) Modify banners',

	/// @todo Review and implement bylines permissions
	'BYLINES_VIEW'                     => '(unimplemented) User can have a byline',

	/// @todo Review and implement campaign permissions
	'CAMPAIGN_VIEW'                    => '(unimplemented) View campaigns',
	'CAMPAIGN_ADD'                     => '(unimplemented) Add new campaign',
	'CAMPAIGN_MODIFY'                  => '(unimplemented) Modify campgaigns',
	'CAMPAIGN_LIVE'                    => '(unimplemented) Change which campaign is live',
	
	/// @todo Review and implement charity permissions
	'CHARITY_VIEW'                     => '(unimplemented) View charities',
	'CHARITY_ADD'                      => '(unimplemented) Add new charities',
	'CHARITY_MODIFY'                   => '(unimplemented) Modify charities',
	'CHARITY_LIVE'                     => '(unimplemented) Change which charity is live',
	
	'COMMENT_MODERATE'                 => '(unimplemented) Access moderator interface',
	'COMMENT_FLAG'                     => '(unimplemented) Flag and unflag a comment as good',
	'COMMENT_DELETE'                   => '(unimplemented) Delete and undelete comments written by others',
	'COMMENT_MODIFY'                   => '(unimplemented) Modify comments written by others',
	'COMMENT_DELETED_VIEW'             => '(unimplemented) View deleted comments',

	'CROSSWORD_INDEX'                  => 'Main crossword management page',
	'CROSSWORD_TIPS_INDEX'             => 'Main crossword tips management page',
	'CROSSWORD_TIP_CATEGORY_ADD'       => 'Add a category of crossword tips',
	'CROSSWORD_TIP_CATEGORY_MODIFY'    => 'Modify a category of crossword tips',
	'CROSSWORD_LAYOUTS_INDEX'          => 'Main crossword layouts management page',
	'CROSSWORD_LAYOUT_ADD'             => 'Add a new crossword layout',
	'CROSSWORD_LAYOUT_MODIFY'          => 'Modify a crossword layout',
	'CROSSWORD_CATEGORIES_INDEX'       => 'Main crossword categories management page',
	'CROSSWORD_CATEGORY_ADD'           => 'Add a new category of crosswords',
	'CROSSWORD_CATEGORY_VIEW'          => 'View crossword category management page',
	'CROSSWORD_CATEGORY_MODIFY'        => 'Modify a category of crosswords',
	'CROSSWORD_ADD'                    => 'Add a crossword puzzle',
	'CROSSWORD_VIEW'                   => 'View a crossword puzzle',
	'CROSSWORD_MODIFY'                 => 'Modify a crossword puzzle',
	'CROSSWORD_STATS_BASIC'            => 'View basic stats about a crossword puzzle',
	
	'FEEDBACK_VIEW'                    => '(unimplemented) View site feedback messages',
	'FEEDBACK_VIEW_DELETED'            => '(unimplemented) View deleted site feedback messages',
	'FEEDBACK_DELETE'                  => '(unimplemented) Delete site feedback messages',

	/// @todo Review and implement gallery permissions
	'GALLERY_VIEW'                     => '(unimplemented) View photo gallery',
	'GALLERY_UPLOAD'                   => '(unimplemented) Upload new photos into gallery',
	'GALLERY_TAG'                      => '(unimplemented) Tag photos in gallery',
	'GALLERY_THUMBNAIL'                => '(unimplemented) Thumbnail photos in gallery',
	
	/// @todo Review and implement gamezone permissions
	'GAMEZONE_VIEW'                    => '(unimplemented) View gamezone management page',
	'GAMEZONE_UPLOAD'                  => '(unimplemented) Upload new games to gamezone',
	'GAMEZONE_MODIFY'                  => '(unimplemented) Modify games in gamezone',
	'GAMEZONE_DELETE'                  => '(unimplemented) Delete games in gamezone',

	/// @todo Implement HOWDOI permissions
	'HOWDOI_VIEW'                      => '(unimplemented) View How-do-I management pages',
	'HOWDOI_MAKE_SUGGESTION'           => '(unimplemented) Make a new suggestion',
	'HOWDOI_MAKE_REQUEST'              => '(unimplemented) Make a new request (bypassing suggestion phase)',
	'HOWDOI_SUGGESTION_REJECT'         => '(unimplemented) Reject suggestions (deleting them)',
	'HOWDOI_SUGGESTION_ACCEPT'         => '(unimplemented) Accept suggestions (turn into requests)',
	'HOWDOI_REQUEST_MODIFY'            => '(unimplemented) Modify requests',
	'HOWDOI_REQUEST_WRITER_ADD'        => '(unimplemented) Request a writer to work on a question',
	'HOWDOI_REQUEST_WRITER_DELETE'     => '(unimplemented) Remove a writer from a question',
	'HOWDOI_QUESTION_MODIFY'           => '(unimplemented) Modify a question',
	'HOWDOI_REQUEST_DELETE'            => '(unimplemented) Delete How-do-I unpublished articles',
	'HOWDOI_PUBLISH'                   => '(unimplemented) Publish How-do-I articles',
	'HOWDOI_PULL'                      => '(unimplemented) Pull How-do-I live articles',
	'HOWDOI_CATEGORY_VIEW'             => '(unimplemented) View How-do-I categories list',
	'HOWDOI_CATEGORY_MODIFY'           => '(unimplemented) Modify How-do-I categories',
	'HOWDOI_CATEGORY_DELETE'           => '(unimplemented) Delete How-do-I categories',
	'HOWDOI_CATEGORY_ADD'              => '(unimplemented) Add new How-do-I categories',
	'HOWDOI_CATEGORY_REORDER'          => '(unimplemented) Reorder How-do-I categories',
	
	'IRC_CHAT'                         => '(unimplemented) Access to interactive office chat',
	
	'LINKS_VIEW'                       => '(unimplemented) View available and nominated links',
	'LINKS_NOMINATE'                   => '(unimplemented) Nominate a new link',
	'LINKS_MODIFY'                     => '(unimplemented) Modify available and nominated links',
	'LINKS_PROMOTE'                    => '(unimplemented) Promote nominated links to official status',
	'LINKS_REJECT'                     => '(unimplemented) Reject a nominated link',
	
	/// @todo Implement management area permissions
	'MANAGE'                           => '(unimplemented) Access office management area',

	'ORGCHART_VIEW'                    => 'View the organisation chart for The Yorker LTD',

	/// @todo Implement page permissions
	'PAGES_VIEW'                       => '(unimplemented) View pages and pages list',
	'PAGES_CUSTOM_NEW'                 => '(unimplemented) Create new custom pages',
	'PAGES_CUSTOM_MODIFY'              => '(unimplemented) Modify existing custom pages',
	'PAGES_CUSTOM_RENAME'              => '(unimplemented) Rename existing custom pages',
	'PAGES_CUSTOM_DELETE'              => '(unimplemented) Delete existing custom pages',
	'PAGES_CUSTOM_PROPERTY_ADD'        => '(unimplemented) Add page properties to custom pages',
	'PAGES_CUSTOM_PROPERTY_MODIFY'     => '(unimplemented) Modify page properties of custom pages',
	'PAGES_CUSTOM_PROPERTY_DELETE'     => '(unimplemented) Delete page properties of custom pages',
	'PAGES_COMMON_ADD'                 => '(unimplemented) Add common page properties',
	'PAGES_COMMON_MODIFY'              => '(unimplemented) Modify custom page properties',
	'PAGES_COMMON_DELETE'              => '(unimplemented) Delete custom page properties',
	'PAGES_PAGE_NEW'                   => '(unimplemented) Create new pages',
	'PAGES_PAGE_MODIFY'                => '(unimplemented) Modify existing pages',
	'PAGES_PAGE_RENAME'                => '(unimplemented) Rename existing pages',
	'PAGES_PAGE_DELETE'                => '(unimplemented) Delete existing pages',
	'PAGES_PAGE_PROPERTY_ADD'          => '(unimplemented) Add page properties to pages',
	'PAGES_PAGE_PROPERTY_MODIFY'       => '(unimplemented) Modify page properties of pages',
	'PAGES_PAGE_PROPERTY_DELETE'       => '(unimplemented) Delete page properties of pages',
	
	'PERMISSIONS_VIEW'                 => 'View permissions',
	'PERMISSIONS_MODIFY_ROLES'         => 'Modify role permissions',
	'PERMISSIONS_MODIFY_USERS'         => 'Modify user roles',
	
	/// @todo Review and implement poll permissions
	'POLLS_VIEW'                       => '(unimplemented) View polls',
	'POLLS_ADD'                        => '(unimplemented) Create new polls',
	'POLLS_MODIFY'                     => '(unimplemented) Modify polls',
	'POLLS_LIVE'                       => '(unimplemented) Make a poll live',
	'POLLS_PULL'                       => '(unimplemented) Pull a live site',
	
	/// @todo Review and implement PR permissions
	'PR_REP_REQUEST_SELF'              => '(unimplemented) Request to be a rep for an organisation',
	'PR_REP_REQUEST_OTHER'             => '(unimplemented) Request another to be a rep for an organisation',
	'PR_ORG_DELETE'                    => '(unimplemented) Delete an organisation',
	'PR_ORG_DIRECTORY_VIEW'            => '(unimplemented) View directory entries',
	'PR_ORG_DIRECTORY_MODIFY'          => '(unimplemented) Modify directory entries',
	'PR_ORG_DIRECTORY_PUBLISH'         => '(unimplemented) Publish directory entries',
	'PR_ORG_DIRECTORY_DELETE'          => '(unimplemented) Delete directory entries',
	'PR_ORG_CALENDAR_VIEW'             => '(unimplemented) View organisation calendars',
	'PR_ORG_CALENDAR_MODIFY'           => '(unimplemented) Modify organisation calendars',
	
	'STATS_VIEW'                       => '(unimplemented) View site stats page',
	
	/// @todo Review and Implement quotes permissions
	'QUOTES_VIEW'                      => '(unimplemented) View upcoming quotes',
	'QUOTES_MODIFY'                    => '(unimplemented) Modify upcoming quotes',
	
	/// @todo Review and implement VIP manage permissions
	'VIPMANAGER_VIEW'                  => '(unimplemented) View VIP manager',
	'VIPMANAGER_MODIFY'                => '(unimplemented) Modify VIP manager',
);

?>
