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
	'ADVERTISING_VIEW'                 => '(unimplemented) View adverts',
	'ADVERTISING_ADD'                  => '(unimplemented) Create new adverts',
	'ADVERTISING_MODIFY'               => '(unimplemented) Modify adverts',
	'ADVERTISING_LIVE'                 => '(unimplemented) Make an advert live',
	'ADVERTISING_PULL'                 => '(unimplemented) Pull a live advert',
	
	/// @todo Review and implement article permissions
	'ARTICLE_VIEW'                     => '(unimplemented) View article contents',
	'ARTICLE_ADD'                      => '(unimplemented) Create new articles',
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
	
	'COMMENT_MODERATE'                 => 'Access moderator interface',
	'COMMENT_FLAG'                     => 'Flag and unflag a comment as good',
	'COMMENT_DELETE'                   => 'Delete and undelete comments written by others',
	'COMMENT_MODIFY'                   => 'Modify comments written by others',
	'COMMENT_DELETED_VIEW'             => 'View deleted comments',
	
	'FEEDBACK_VIEW'                    => 'View site feedback messages',
	'FEEDBACK_VIEW_DELETED'            => 'View deleted site feedback messages',
	'FEEDBACK_DELETE'                  => 'Delete site feedback messages',
	
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
	
	/// @todo Review and implement howdoi permissions
	'HOWDOI_VIEW'                      => '(unimplemented) View How-do-I management page',
	'HOWDOI_MODIFY'                    => '(unimplemented) Modify How-do-I items',
	
	'IRC_CHAT'                         => 'Access to interactive office chat',
	
	'LINKS_VIEW'                       => 'View available and nominated links',
	'LINKS_NOMINATE'                   => 'Nominate a new link',
	'LINKS_MODIFY'                     => 'Modify available and nominated links',
	'LINKS_PROMOTE'                    => 'Promote nominated links to official status',
	'LINKS_REJECT'                     => 'Reject a nominated link',
	
	/// @todo Implement management area permissions
	'MANAGE'                           => '(unimplemented) Access office management area',
	
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
	
	'STATS_VIEW'                       => 'View site stats page',
	
	/// @todo Review and Implement quotes permissions
	'QUOTES_VIEW'                      => '(unimplemented) View upcoming quotes',
	'QUOTES_MODIFY'                    => '(unimplemented) Modify upcoming quotes',
	
	/// @todo Review and implement VIP manage permissions
	'VIPMANAGER_VIEW'                  => '(unimplemented) View VIP manager',
	'VIPMANAGER_MODIFY'                => '(unimplemented) Modify VIP manager',
);

?>
