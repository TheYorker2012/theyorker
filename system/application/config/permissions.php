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
 */
$config['permissions'] = array(
	/// @todo Review and implement advertising permissions
	'ADVERTISING_VIEW'                 => 'View adverts',
	'ADVERTISING_ADD'                  => 'Create new adverts',
	'ADVERTISING_MODIFY'               => 'Modify adverts',
	'ADVERTISING_LIVE'                 => 'Make an advert live',
	'ADVERTISING_PULL'                 => 'Pull a live advert',
	
	/// @todo Review and implement article permissions
	'ARTICLE_VIEW'                     => 'View article contents',
	'ARTICLE_ADD'                      => 'Create new articles',
	'ARTICLE_MODIFY'                   => 'Modify articles',
	'ARTICLE_DELETE'                   => 'Delete articles',
	'ARTICLE_PUBLISH'                  => 'Publish articles',
	'ARTICLE_PULL'                     => 'Pull published articles',
	
	/// @todo Review and implement article type permissions
	'ARTICLETYPES_VIEW'                => 'View article types manager',
	'ARTICLETYPES_ADD'                 => 'Create new article types',
	'ARTICLETYPES_MODIFY'              => 'Modify article types',
	'ARTICLETYPES_DELETE'              => 'Delete article types',
	
	/// @todo Review and implement homepage banner permissions
	'BANNERS_VIEW'                     => 'View banners',
	'BANNERS_UPLOAD'                   => 'Upload new banners',
	'BANNERS_MODIFY'                   => 'Modify banners',
	
	/// @todo Review and implement campaign permissions
	'CAMPAIGN_VIEW'                    => 'View campaigns',
	'CAMPAIGN_ADD'                     => 'Add new campaign',
	'CAMPAIGN_MODIFY'                  => 'Modify campgaigns',
	'CAMPAIGN_LIVE'                    => 'Change which campaign is live',
	
	/// @todo Review and implement charity permissions
	'CHARITY_VIEW'                     => 'View charities',
	'CHARITY_ADD'                      => 'Add new charities',
	'CHARITY_MODIFY'                   => 'Modify charities',
	'CHARITY_LIVE'                     => 'Change which charity is live',
	
	/// @todo Implement comment permissions
	'COMMENT_MODERATE'                 => 'Access moderator interface',
	'COMMENT_DELETE'                   => 'Delete comments written by others',
	'COMMENT_MODIFY'                   => 'Modify comments written by others',
	'COMMENT_DELETED_VIEW'             => 'View deleted comments',
	
	/// @todo Implement feedback permissions
	'FEEDBACK_VIEW'                    => 'View site feedback',
	
	/// @todo Review and implement gallery permissions
	'GALLERY_VIEW'                     => 'View photo gallery',
	'GALLERY_UPLOAD'                   => 'Upload new photos into gallery',
	'GALLERY_TAG'                      => 'Tag photos in gallery',
	'GALLERY_THUMBNAIL'                => 'Thumbnail photos in gallery',
	
	/// @todo Review and implement gamezone permissions
	'GAMEZONE_VIEW'                    => 'View gamezone management page',
	'GAMEZONE_UPLOAD'                  => 'Upload new games to gamezone',
	'GAMEZONE_MODIFY'                  => 'Modify games in gamezone',
	'GAMEZONE_DELETE'                  => 'Delete games in gamezone',
	
	/// @todo Review and implement howdoi permissions
	'HOWDOI_VIEW'                      => 'View How-do-I management page',
	'HOWDOI_MODIFY'                    => 'Modify How-do-I items',
	
	'IRC_CHAT'                         => 'Access to interactive office chat',
	
	/// @todo Review and Implement links permissions
	'LINKS_VIEW'                       => 'View available and nominated links',
	'LINKS_MODIFY'                     => 'Modify available and nominated links',
	
	/// @todo Implement management area permissions
	'MANAGE'                           => 'Access office management area',
	
	/// @todo Implement page permissions
	'PAGES_VIEW'                       => 'View pages and pages list',
	'PAGES_CUSTOM_NEW'                 => 'Create new custom pages',
	'PAGES_CUSTOM_MODIFY'              => 'Modify existing custom pages',
	'PAGES_CUSTOM_RENAME'              => 'Rename existing custom pages',
	'PAGES_CUSTOM_DELETE'              => 'Delete existing custom pages',
	'PAGES_CUSTOM_PROPERTY_ADD'        => 'Add page properties to custom pages',
	'PAGES_CUSTOM_PROPERTY_MODIFY'     => 'Modify page properties of custom pages',
	'PAGES_CUSTOM_PROPERTY_DELETE'     => 'Delete page properties of custom pages',
	'PAGES_COMMON_ADD'                 => 'Add common page properties',
	'PAGES_COMMON_MODIFY'              => 'Modify custom page properties',
	'PAGES_COMMON_DELETE'              => 'Delete custom page properties',
	'PAGES_PAGE_NEW'                   => 'Create new pages',
	'PAGES_PAGE_MODIFY'                => 'Modify existing pages',
	'PAGES_PAGE_RENAME'                => 'Rename existing pages',
	'PAGES_PAGE_DELETE'                => 'Delete existing pages',
	'PAGES_PAGE_PROPERTY_ADD'          => 'Add page properties to pages',
	'PAGES_PAGE_PROPERTY_MODIFY'       => 'Modify page properties of pages',
	'PAGES_PAGE_PROPERTY_DELETE'       => 'Delete page properties of pages',
	
	'PERMISSIONS_VIEW'                 => 'View permissions',
	'PERMISSIONS_MODIFY_ROLES'         => 'Modify role permissions',
	'PERMISSIONS_MODIFY_USERS'         => 'Modify user roles',
	
	/// @todo Review and implement poll permissions
	'POLLS_VIEW'                       => 'View polls',
	'POLLS_ADD'                        => 'Create new polls',
	'POLLS_MODIFY'                     => 'Modify polls',
	'POLLS_LIVE'                       => 'Make a poll live',
	'POLLS_PULL'                       => 'Pull a live site',
	
	/// @todo Review and implement PR permissions
	'PR_REP_REQUEST_SELF'              => 'Request to be a rep for an organisation',
	'PR_REP_REQUEST_OTHER'             => 'Request another to be a rep for an organisation',
	'PR_ORG_DELETE'                    => 'Delete an organisation',
	'PR_ORG_DIRECTORY_VIEW'            => 'View directory entries',
	'PR_ORG_DIRECTORY_MODIFY'          => 'Modify directory entries',
	'PR_ORG_DIRECTORY_PUBLISH'         => 'Publish directory entries',
	'PR_ORG_DIRECTORY_DELETE'          => 'Delete directory entries',
	'PR_ORG_CALENDAR_VIEW'             => 'View organisation calendars',
	'PR_ORG_CALENDAR_MODIFY'           => 'Modify organisation calendars',
	
	/// @todo Implement site stats permissions
	'STATS_VIEW'                       => 'View site stats page',
	
	/// @todo Review and Implement quotes permissions
	'QUOTES_VIEW'                      => 'View upcoming quotes',
	'QUOTES_MODIFY'                    => 'Modify upcoming quotes',
	
	/// @todo Review and implement VIP manage permissions
	'VIPMANAGER_VIEW'                  => 'View VIP manager',
	'VIPMANAGER_MODIFY'                => 'Modify VIP manager',
);

?>