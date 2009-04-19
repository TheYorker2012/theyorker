<?php

/**
 * @file helpers/feeds_helper.php
 * @author James Hogan <james_hogan@theyorker.co.uk>
 * @brief Helper library for serving RSS/Atom feeds.
 */

/// Person in a feed.
class FeedPerson
{
	private $name = null;
	private $email = null;
	private $url = null;

	function __construct($name = null, $email = null, $url = null)
	{
		$this->name = $name;
		$this->email = $email;
		$this->url = $url;
	}
	function SetName($name)
	{
		$this->name = $name;
	}
	function SetEmail($email)
	{
		$this->email = $email;
	}
	function SetUrl($url)
	{
		$this->url = $url;
	}

	function Name()
	{
		return $this->name;
	}
	function Email()
	{
		return $this->email;
	}
	function Url()
	{
		return $this->url;
	}
}

/// Generic feed item.
class FeedItem
{
	private $title = null;
	private $authors = array();
	private $link = null;
	private $permaLink = null;
	private $description = null;
	private $publicationDate = null;
	private $categories = array();

	function SetTitle($title)
	{
		$this->title = $title;
	}
	function AddAuthor($author, $email = null)
	{
		$this->authors[] = new FeedPerson($author, $email);
	}
	function SetLink($link, $perma = true)
	{
		$this->link = $link;
		if ($perma) {
			$this->permaLink = $link;
		}
	}
	function SetPermaLink($permaLink)
	{
		$this->permaLink = $permaLink;
	}
	function SetDescription($description)
	{
		$this->description = $description;
	}
	function SetPublicationDate($publicationDate)
	{
		$this->publicationDate = $publicationDate;
	}
	function AddCategory($category)
	{
		$this->categories[] = $category;
	}

	function Title()
	{
		return $this->title;
	}
	function Authors()
	{
		return $this->authors;
	}
	function Link()
	{
		return $this->link;
	}
	function PermaLink()
	{
		return $this->permaLink;
	}
	function Description()
	{
		return $this->description;
	}
	function PublicationDate()
	{
		return $this->publicationDate;
	}
	function Categories()
	{
		return $this->categories;
	}
}

/// Generic feed channel.
class FeedChannel
{
	private $items = array();
	private $title = null;
	private $description = null;
	private $language = 'en-gb';
	private $copyright = null;
	private $publicationDate = null;
	private $lastBuildDate = null;
	private $documentation = 'http://www.rssboard.org/rss-specification';
	private $imageUrl = null;
	private $imageWidth = null;
	private $imageHeight = null;
	private $feedUrl = null;
	private $altUrl = null;
	private $editor = null;
	private $webmaster = null;

	function NewItem()
	{
		$item = new FeedItem;
		$this->AddItem($item);
		return $item;
	}
	function AddItem(&$item)
	{
		$this->items[] = $item;
	}
	function SetTitle($title)
	{
		$this->title = $title;
	}
	function SetDescription($description)
	{
		$this->description = $description;
	}
	function SetImage($url = null, $width = null, $height = null)
	{
		$this->imageUrl = $url;
		$this->imageWidth = $width;
		$this->imageHeight = $height;
	}
	function SetFeedUrl($feedUrl)
	{
		$this->feedUrl = $feedUrl;
	}
	function SetAltUrl($altUrl)
	{
		$this->altUrl = $altUrl;
	}
	function SetEditor($editor)
	{
		$this->editor = $editor;
	}
	function SetWebmaster($webmaster)
	{
		$this->webmaster = $webmaster;
	}
	
	function Items()
	{
		return $this->items;
	}
	function Title()
	{
		return $this->title;
	}
	function Description()
	{
		return $this->description;
	}
	function Language()
	{
		return $this->language;
	}
	function Copyright()
	{
		return $this->copyright;
	}
	function PublicationDate()
	{
		return $this->publicationDate;
	}
	function LastBuildDate()
	{
		return $this->lastBuildDate;
	}
	function Documentation()
	{
		return $this->documentation;
	}
	function ImageUrl()
	{
		return $this->imageUrl;
	}
	function ImageWidth()
	{
		return $this->imageWidth;
	}
	function ImageHeight()
	{
		return $this->imageHeight;
	}
	function ImageLink()
	{
		return $this->imageLink;
	}
	function FeedUrl()
	{
		return $this->feedUrl;
	}
	function AltUrl()
	{
		return $this->altUrl;
	}
	function Editor()
	{
		return $this->editor;
	}
	function Webmaster()
	{
		return $this->webmaster;
	}
}

/// Feed view.
class FeedView
{
	private $channel;

	function __construct($link = null)
	{
		$get_data = http_build_query($_GET);
		if ('' !== $get_data) {
			$get_data = '?'.$get_data;
		}
		$feed_link = 'http://'.$_SERVER['HTTP_HOST'].get_instance()->uri->uri_string().$get_data;
		if (null === $link) {
			$link = 'http://'.$_SERVER['HTTP_HOST'].OutputModeChangeUri(DefaultOutputMode());
		}
		$this->channel = new FeedChannel;
		$this->channel->SetFeedUrl($feed_link);
		$this->channel->SetAltUrl($link);
	}

	function SetChannel(&$channel)
	{
		$this->channel = &$channel;
	}
	function Channel()
	{
		return $this->channel;
	}

	function FormatDate($date)
	{
		if (null === $date) {
			return null;
		}
		else {
			return date('r',$date);
		}
	}

	function Load()
	{
		$channel = array(
			'title' => $this->channel->Title(),
			'link' => $this->channel->AltUrl(),
			'atom:link' => array(
				'_attr' => array(
					'href' => $this->channel->AltUrl(),
					'rel' => 'self',
					'type' => 'application/rss+xml',
				),
			),
			'description' => $this->channel->Description(),
			'language' => $this->channel->Language(),
			'copyright' => $this->channel->Copyright(),
			'pubDate' => $this->FormatDate($this->channel->PublicationDate()),
			'lastBuildDate' => $this->FormatDate($this->channel->LastBuildDate()),
			'docs' => $this->channel->Documentation(),
			'image' => array(
				'url' => $this->channel->ImageUrl(),
				'width' => $this->channel->ImageWidth(),
				'height' => $this->channel->ImageHeight(),
				'title' => $this->channel->Title(),
				'link' => $this->channel->AltUrl(),
			),
			'managingEditor' => $this->channel->Editor(),
			'webMaster' => $this->channel->Webmaster(),
		);
		$root = array(
			'_tag' => 'rss',
			'_attr' => array(
				'version' => '2.0',
				'xmlns:atom' => 'http://www.w3.org/2005/Atom',
			),
			'channel' => &$channel,
		);
		$items = $this->channel->Items();
		foreach ($items as &$item) {
			$feed = array(
				'_tag' => 'item',
				'title' => $item->Title(),
				'link' => $item->Link(),
				'description' => $item->Description(),
				'pubDate' => $this->FormatDate($item->PublicationDate()),
				'guid' => array(
					'_attr' => array(
						'isPermaLink' => 'true',
					),
					$item->PermaLink(),
				),
			);
			foreach ($item->Authors() as $author) {
				$feed[] = array(
					'_tag' => 'author',
					$author->Email().' ('.$author->Name().')',
				);
			}
			foreach ($item->Categories() as $category) {
				$feed[] = array(
					'_tag' => 'category',
					$category,
				);
			}
			$channel[] = $feed;
		}

		// Load XML
		get_instance()->load->view('general/xml',array(
			'HeaderContentType' => 'application/rss+xml',
			'RootTag' => $root,
		));
	}
}

?>
