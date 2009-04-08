<?php

/**
 * @file helpers/feeds_helper.php
 * @author James Hogan <james_hogan@theyorker.co.uk>
 * @brief Helper library for serving RSS/Atom feeds.
 */

/// Generic feed item.
class FeedItem
{
	private $title = null;
	private $author = null;
	private $link = null;
	private $permaLink = null;
	private $description = null;
	private $publicationDate = null;

	function setTitle($title)
	{
		$this->title = $title;
	}
	function setAuthor($author)
	{
		$this->author = $author;
	}
	function setLink($link, $perma = true)
	{
		$this->link = $link;
		if ($perma) {
			$this->permaLink = $link;
		}
	}
	function setPermaLink($permaLink)
	{
		$this->permaLink = $permaLink;
	}
	function setDescription($description)
	{
		$this->description = $description;
	}
	function setPublicationDate($publicationDate)
	{
		$this->publicationDate = $publicationDate;
	}

	function Title()
	{
		return $this->title;
	}
	function Author()
	{
		return $this->author;
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
	private $link = null;
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
	function SetLink($link)
	{
		$this->link = $link;
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
	function Link()
	{
		return $this->link;
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
		if (null === $link) {
			$get_data = http_build_query($_GET);
			if ('' !== $get_data) {
				$get_data = '?'.$get_data;
			}
			$link = 'http://'.$_SERVER['HTTP_HOST'].get_instance()->uri->uri_string().$get_data;
		}
		$this->channel = new FeedChannel;
		$this->channel->SetLink($link);
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
			'link' => $this->channel->Link(),
			'atom:link' => array(
				'_attr' => array(
					'href' => $this->channel->Link(),
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
				'link' => $this->channel->Link(),
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
				'author' => $item->Author(),
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
