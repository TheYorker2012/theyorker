<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
 * I did not write this so I appologise for the crappyness of this code, it will definately
 * require changing to fit into the yorker, Like enabling images and the links. Send stuff
 * to the parse function.
 */
/* WikiParser
 * Version 1.0
 * Copyright 2005, Steve Blinch
 * http://code.blitzaffe.com
 *
 * This class parses and returns the HTML representation of a document containing
 * basic MediaWiki-style wiki markup.
 *
 *
 * USAGE
 *
 * Refer to class_WikiRetriever.php (which uses this script to parse fetched
 * wiki documents) for an example.
 *
 *
 * LICENSE
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 *
 *
 */

/// Wikitext parsing library.
class Wikiparser {
	protected $reference_wiki;
	protected $external_wikis;
	protected $image_uri;
	protected $image_overrides;
	protected $title_overrides;
	protected $ignore_images;
	protected $emphasis;
	protected $quote_template;
	protected $templates;
	protected $newline_mode;
	protected $multi_line;
	protected $enable_headings;
	protected $enable_youtube;
	protected $enable_mediaplayer;
	protected $enable_quickquotes;
	protected $enable_ordered_lists;
	protected $entities;

	protected $spoiler;
	protected $list_level_chars;
	protected $list_level;
	protected $deflist;
	protected $linknumber;
	protected $suppress_linebreaks;
	protected $in_paragraph;
	protected $page_title;
	protected $stop;
	protected $stop_all;
	protected $nextnowiki;
	protected $redirect;
	protected $nowikis;

	/// Default constructor.
	function __construct() {
		$CI = &get_instance();
		$CI->load->helper('wikilink');
		$CI->load->library('image');

		$this->reference_wiki = 'local';
		$this->external_wikis = PresetWikis();
		$this->image_uri = '/';
		$this->image_overrides = array();
		$this->ignore_images = FALSE;
		$this->emphasis = array();

		$this->quote_template = 'pull_quote';
		$this->templates = array(
				'pull_quote' => array('<blockquote>
<div>
<img src="/images/prototype/news/quote_open.png" alt="Quote" title="Quote" />
{{1}}
<img src="/images/prototype/news/quote_close.png" alt="Quote" title="Quote" />
<br /><span class="author">{{2}}</span>
</div></blockquote>', true),
				'frame' => array('<div class="BlueBox"><h4>{{1}}</h4>{{2}}</div>', true),
				'br' => array('<br />', false),
			);
		$this->newline_mode = '';
		$this->multi_line = true;
		$this->enable_headings = true;
		$this->enable_youtube = true;
		$this->enable_mediaplayer = true;
		$this->enable_quickquotes = true;
		$this->enable_ordered_lists = true;
		$this->entities = array(
			'\'' => xml_escape('\''),
			'"'  => xml_escape('"'),
		);
	}

	/**
	 *	@brief		Adds a rule to the wikiparser making it replace [[Image:$id]] with <img src="$url" />
	 *	@author		Chris Travis (cdt502 - ctravis@gmail.com)
	 *	@date		Tue 15th May 2007 16:42
	 */
	function add_image_override($id, $url, $title = '')
	{
		$this->image_overrides[$id] = $url;
		$this->title_overrides[$id] = $title;

	}

	function handle_sections($matches) {
		$level = strlen($matches[1]);
		$content = $matches[2];

		$this->stop = true;
		// avoid accidental run-on emphasis
		return $this->end_paragraph() . "\n\n<h{$level}>{$content}</h{$level}>\n\n";
	}

	function handle_startparagraph($matches)
	{
		$this->stop = true;
		if (!$this->in_paragraph) {
			$this->in_paragraph = true;
			return '<p>'.$matches[0];
		} else {
			if ('br' === $this->newline_mode) {
				// line break
				$prefix = '<br />';
			} elseif ('p' === $this->newline_mode) {
				// end and start paragraph tag
				$prefix = $this->end_paragraph().'<p>';
				$this->in_paragraph = true;
			} else {
				// no change at new line
				$prefix = '';
			}
			return $prefix."\n".$matches[0];
		}
	}

	function end_paragraph()
	{
		if ($this->in_paragraph) {
			$this->in_paragraph = false;
			return $this->emphasize_off()."</p>\n";
		} else {
			return $this->emphasize_off();
		}
	}

	function handle_newline($matches) {
		if ($this->suppress_linebreaks) return $this->emphasize_off();

		$this->stop = true;
		// avoid accidental run-on emphasis
		return $this->end_paragraph();
	}

	/**
	 * Used by handle_list.
	 * @param string $containee
	 * @param string $container
	 * @return bool Whether @a $container starts with the string @a $containee.
	 * @author James Hogan (jh559@cs.york.ac.uk)
	 */
	function string_contained($containee, $container)
	{
		$len_containee = strlen($containee);
		$len_container = strlen($container);
		return ($len_containee <= $len_container and
				$containee == substr($container,0,$len_containee));
	}

	/**
	 * @note Modified by James Hogan (jh559@cs.york.ac.uk) to fix bullet
	 *	mixing problem.
	 */
	function handle_list($matches,$close=false) {
		$listtypes = array(
			'0'=>'dummie value',
			'*'=>'ul',
			'#'=>'ol',
		);

		$output = '';

		$newlevel = ($close) ? 0 : strlen($matches[1]);

		$new_list = false;

		// While the new list types aren't compatible with the old
		// close the last list
		while (!$this->string_contained($this->list_level_chars, $matches[1])) {
			// Get the last list type
			$listchar = substr($this->list_level_chars,-1);
			$listtype = $listtypes[$listchar];

			// and close it
			$output .= '</li></'.$listtype.'>'."\n";
			$this->list_level_chars = substr($this->list_level_chars,0,-1);
			$this->list_level--;
		}

		// Remember the list types string
		$this->list_level_chars = $matches[1];
		// and if we're closing all lists, don't bother continuing
		if ($close) return $output;

		// While theres more lists in the new set of lists
		// open new lists
		while ($this->list_level<$newlevel) {
			// Get the next new list type
			$listchar = substr($matches[1],$this->list_level,1);
			$listtype = $listtypes[$listchar];

			// and open it
			++$this->list_level;
			$output .= "\n".'<'.$listtype.'><li>';

			// We've opened a new list so we don't need to start a new list list
			// item in a few moments.
			$new_list = true;
		}

		// close and open a new list item if the current list hasn't just
		// been created.
		if (!$new_list) {
			$output .= "\n".'</li><li>';
		}
		$output .= $matches[2]."\n";

		return $this->end_paragraph().$output;
	}

	function handle_definitionlist($matches,$close=false) {

		if ($close) {
			$this->deflist = false;
			return "</dl>\n";
		}


		$output = "";
		if (!$this->deflist) $output .= "<dl>\n";
		$this->deflist = true;

		switch($matches[1]) {
			case ';':
				$term = $matches[2];
				$p = strpos($term,' :');
				if ($p!==false) {
					list($term,$definition) = explode(':',$term);
					$output .= "<dt>{$term}</dt><dd>{$definition}</dd>";
				} else {
					$output .= "<dt>{$term}</dt>";
				}
				break;
			case ':':
				$definition = $matches[2];
				$output .= "<dd>{$definition}</dd>\n";
				break;
		}

		return $this->end_paragraph().$output;
	}

	function handle_preformat($matches,$close=false) {
		if ($close) {
			if ($this->preformat) {
				$this->preformat = false;
				return "</pre>\n";
			} else {
				return '';
			}
		}

		$this->stop_all = true;

		$output = "";
		if (!isset($this->preformat) or !$this->preformat) $output .= '<pre>';
		$this->preformat = true;

		$output .= $matches[1];

		return $this->end_paragraph().$output."\n";
	}

	function handle_horizontalrule($matches) {
		return $this->end_paragraph().'<hr />';
	}

	function handle_spoiler($matches, $close = false) {
		if ($close) {
			if ($this->spoiler) {
				$this->spoiler = false;
				return '<div class="end">&nbsp;</div></div>';
			}
			return '';
		}
		if (!$this->spoiler) {
			$this->spoiler = true;
			$prefix = '<div class="spoiler"><div class="start">&nbsp;</div>';
			return array($this->end_paragraph().$prefix, $matches[1]);
		}
		else {
			return $matches[1];
		}
	}

	function handle_image($href,$title,$options) {
		if ($this->ignore_images) return '';
		if (!$this->image_uri) return $title;

		$imagetag = '';
		if (array_key_exists($href,$this->image_overrides)) {
			$imagetag = $this->image_overrides[$href];
			if (strlen($title) == 0 && isset($this->title_overrides[$href])) {
				$title = $this->title_overrides[$href];
			}
		} else {
			$href = $this->image_uri.$href;
			$imagetag =
				'<img src="'.$href.'" alt="'.$title.'" />';
		}

		$option = array_pop($options); //Will return null if empty

		switch($option) {
			case 'right':
				$imagetag = //Olds style: background-color: #F5F5F5; border: 1px solid #D0D0D0;
					'<div style="float: right; width: 180px; padding: 2px">'.
					$imagetag.
					'<div>'.$title.'</div>'.
					'</div>';
				if ($this->in_paragraph) {
					// divs aren't allowed in paragraphs, so close and reopen
					$imagetag = $this->emphasize_off()."</p>\n" . $imagetag . "\n<p>";
				}
				break;
			case 'left':
				$imagetag =
					'<div style="float: left; width: 180px; padding: 2px">'.
					$imagetag.
					'<div>'.$title.'</div>'.
					'</div>';
				if ($this->in_paragraph) {
					// divs aren't allowed in paragraphs, so close and reopen
					$imagetag = $this->emphasize_off()."</p>\n" . $imagetag . "\n<p>";
				}
				break;
			case 'centre':
				$imagetag =
					'<div style="text-align: center;">'.$imagetag.'<br />'.$title.'</div>';
				if ($this->in_paragraph) {
					// divs aren't allowed in paragraphs, so close and reopen
					$imagetag = $this->emphasize_off()."</p>\n" . $imagetag . "\n<p>";
				}
				break;
		}
		return $imagetag;
	}

	function handle_internallink($matches) {
		$nolink = false;

		$href = $matches[4];
		$title = (isset($matches[6]) and $matches[6]) ? $matches[6] : $matches[4];
		$namespace = $matches[3];

		if ($namespace=='image') {
			$options = explode('|',$title);
			$title = '';
			if (count($options) > 1) $title = array_pop($options);
			return $this->handle_image($href,$title,$options);
		}
		if (array_key_exists($namespace, $this->external_wikis)) {
			$reference_wiki = $this->external_wikis[$namespace];
			$namespace = '';
		} elseif ($this->enable_youtube && 'youtube' === $namespace) {
			//$href = xml_escape($href);
			$params = array('src', 'http://www.youtube.com/v/' . $href,
					'width', '340',
					'height', '280');
			$output = $this->get_inline_flash_code($params);

			if ($this->in_paragraph) {
				// divs aren't allowed in paragraphs, so close and reopen
				$output = $this->emphasize_off()."</p>\n" . $output . "\n<p>";
			}
			return $output;
		} elseif ($this->enable_mediaplayer && 'media' === $namespace) {
			static $mediaplayer_count = 0;
			$mediaplayer_count++;
			$control_width = ((strlen($href) > 4) && (substr($href, -4) == '.mp3')) ? 340 : 580;
			$control_height = ((strlen($href) > 4) && (substr($href, -4) == '.mp3')) ? 20 : 346;
			$output = '
				<div id="mp' . $mediaplayer_count . '_container" style="text-align:center">
					<div style="border: 1px solid #999999;">
						<b>Flash Video/Audio Player</b><br /><br />
						This content requires Adobe Flash Player 8.
						<a href="http://www.adobe.com/go/getflash/">Get Flash</a>
					</div>
				</div>
				<script type="text/javascript">
				var so = new SWFObject("/flash/mediaplayer.swf","mp' . $mediaplayer_count . '","' . $control_width . '","' . $control_height . '","8");
				so.addParam("allowscriptaccess","samedomain");
				so.addParam("allowfullscreen","true");
				// File properties
				so.addVariable("file","' . $href . '");
//				so.addVariable("image","##TODO##");
				// Colours
				so.addVariable("backcolor","FF6A00");
				so.addVariable("frontcolor","FFFFFF");
				so.addVariable("lightcolor","FFFFFF");
				so.addVariable("screencolor","FFFFFF");
				// Layout
				so.addVariable("height","' . $control_height . '");
				so.addVariable("width","' . $control_width . '");
				// Behaviour
				so.addVariable("logo","/images/prototype/news/video_overlay_icon.png");
				// External
				so.addVariable("id","' . $href . '");
				// Plugins
				so.addVariable("plugins","googlytics-1");
				// Print out player
				so.write("mp' . $mediaplayer_count . '_container");
				</script>';
			if ($this->in_paragraph) {
				// divs aren't allowed in paragraphs, so close and reopen
				$output = $this->emphasize_off()."</p>\n" . $output . "\n<p>";
			}
			return $output;
		} else {
			$reference_wiki = $this->external_wikis[$this->reference_wiki];
		}

		$title = preg_replace('/\(.*?\)/','',$title);
		$title = preg_replace('/^.*?\:/','',$title);

		/*if ($reference_wiki) {
			$href = $reference_wiki.($namespace?$namespace.':':'').$href;
		} else {
			$nolink = true;
		}*/
		$href = WikiLink($reference_wiki, $href);

		//if ($nolink) return $title;

		return '['.$href.' '.$title.']'.(isset($matches[7]) ? $matches[7] : "");
	}

	function handle_externallink($matches) {
		$href = $matches[2];
		if (substr($href, 0, 4) !== 'http' &&
			substr($href, 0, 1) !== '/' &&
			substr($href, 0, 7) !== 'mailto:' /*&&
			!isset($matches[4]) &&
			!isset($matches[6])*/)
		{
			return $matches[0];
		}
		/*
		// Implicit email
		if (isset($matches[6])) {
			$href = 'Mailto:'.$matches[6];
			$title= $matches[6];
		}
		// Implicit url
		elseif (isset($matches[4])) {
			$href = $matches[4];
			$title= $matches[4];
		}
		*/
		// explicit unamed
		elseif (!isset($matches[3])) {
			$this->linknumber++;
			$title = '['.$this->linknumber.']';
		}
		// explicit named
		else {
			$title = $matches[3];
		}
		$newwindow = false;

		return '<a href="'.$href.'"'.($newwindow?' target="_blank"':'').'>'.$title.'</a>';
	}

	function emphasize($amount) {
		if ($amount == 5) {
			$active_2 = array_search(2, $this->emphasis);
			$active_3 = array_search(3, $this->emphasis);
			if (false === $active_2 || (false !== $active_3 && $active_3 > $active_2)) {
				return $this->emphasize(3).$this->emphasize(2);
			}
			else {
				return $this->emphasize(2).$this->emphasize(3);
			}
		}
		elseif ($amount == 4) {
			if (!in_array(3, $this->emphasis)) {
				return $this->entities['\''].$this->emphasize(3);
			}
			else {
				return $this->emphasize(3).$this->entities['\''];
			}
		}
		$amounts = array(
			2=>array('<em>','</em>'),
			3=>array('<strong>','</strong>'),
		);

		$output = '';

		$active = array_search($amount, $this->emphasis);
		$to_add = array();
		if (false !== $active) {
			while (count($this->emphasis) > $active) {
				$cur_amount = array_pop($this->emphasis);
				$to_add[] = $cur_amount;
				$output .= $amounts[$cur_amount][1];
			}
			// last element will be one we're removing
			array_pop($to_add);
		}
		else {
			$to_add[] = $amount;
		}
		while (!empty($to_add)) {
			$cur_amount = array_pop($to_add);
			$this->emphasis[] = $cur_amount;
			$output .= $amounts[$cur_amount][0];
		}
		
		return $output;
	}

	function handle_emphasize($matches) {
		$amount = strlen(xml_unescape($matches[1]));
		return $this->emphasize($amount);
	}

	/**
	 * @brief Add emphasis to certain text matches
	 * @author James Hogan (jh559@cs.york.ac.uk)
	 * @see Wikiparser::parse_line in char_regexes['addemphasis']
	 *
	 * This function determines how to format words such as 'the yorker'.
	 * At the moment it does emphasis, bold, orange (feel free to change)!
	 */
	function handle_addemphasis($matches) {
		$output = '<span class="theyorker">'; // Orange emphasis
		$output .= $matches[0];               // Actual text
		$output .= '</span>';                 // Orange emphasis
		return $output;

	}

	function emphasize_off() {
		$output = '';
		while (!empty($this->emphasis)) {
			$amount = $this->emphasis[count($this->emphasis)-1];
			$output .= $this->emphasize($amount);
		}
		return $output;
	}

	function handle_eliminate($matches) {
		return '';
	}

	function handle_special_quote($matches)
	{
		return '{{'.$this->quote_template.'|'.$matches[1].'|'.$matches[2].'}}';
	}

	function handle_template_parameter($matches) {
		if (array_key_exists($matches[1],$this->template_elements)) {
			return $this->template_elements[$matches[1]];
		} else {
			return '';
		}
	}

	function handle_variable($matches) {
		$this->template_elements = explode('|',$matches[2]);
		if (array_key_exists($this->template_elements[0], $this->templates)) {
			list($replacement, $block) = $this->templates[$this->template_elements[0]];
			$replacement = preg_replace_callback(
					'/\{\{(\d+)\}\}/i',
					array(&$this,'handle_template_parameter'),
					$replacement);
			if (!$block) {
				return $replacement;
			} else {
				return $this->end_paragraph().$replacement;
			}
		} else {
			switch($this->template_elements[0]) {
				case 'CURRENTMONTH': return date('m');
				case 'CURRENTMONTHNAMEGEN':
				case 'CURRENTMONTHNAME': return date('F');
				case 'CURRENTDAY': return date('d');
				case 'CURRENTDAYNAME': return date('l');
				case 'CURRENTYEAR': return date('Y');
				case 'CURRENTTIME': return date('H:i');
				case 'NUMBEROFARTICLES': return 0;
				case 'PAGENAME': return $this->page_title;
				case 'NAMESPACE': return 'None';
				case 'SITENAME': return $_SERVER['HTTP_HOST'];
				default: return $matches[0];
			}
		}
		unset($this->template_elements);
	}

	function handle_symbols($matches)
	{
		if ($matches[1] == '&') {
			return '&amp;';
		} elseif ($matches[0] == '<') {
			return '&lt;';
		} elseif ($matches[0] == '>') {
			return '&gt;';
		} else {
			return $matches[0];
		}
	}

	function parse_line($line) {
		$first_characters = '\s\*;\:-';
		$list_chars = '\*';
		if ($this->multi_line && $this->enable_headings) {
			$first_characters .= '=';
		}
		if (empty($this->newline_mode)) {
// 			$first_characters .= '\{';
		}
		if ($this->multi_line && $this->enable_ordered_lists) {
			$first_characters .= '\#';
			$list_chars .= '\#';
		}
		
		$line_regexes = array();
		$line_regexes['spoiler'] = '^\!(.*?)$';
		if ($this->enable_quickquotes) {
			$line_regexes['special_quote'] = '^'.$this->entities['"'].$this->entities['"'].$this->entities['"'].'(.*)'.$this->entities['"'].$this->entities['"'].$this->entities['"'].'\s*(.*)$';
		}
		$line_regexes['startparagraph'] = '^\s*([^'.$first_characters.'].*?)$';
		//$line_regexes['preformat'] = '^\s(.*?)$';
		$line_regexes['definitionlist'] = '^([\;\:])\s*(.*?)$';
		$line_regexes['newline'] = '^$';
		$line_regexes['list'] = '^(['.$list_chars.']+)\s*(.*?)$';
		if ($this->enable_headings) {
			$line_regexes['sections'] = '^(={1,6})(.*?)(={1,6})$';
		}
		$line_regexes['horizontalrule'] = '^----$';
		
		$char_regexes = array();
		if ($this->multi_line) {
//			$char_regexes['link'] = '(\[\[((.*?)\:)?(.*?)(\|(.*?))?\]\]([a-z]+)?)';
			$char_regexes['internallink'] = '('.
				'\[\['. // opening brackets
					'(([^\]]*?)\:)?'. // namespace (if any)
					'([^\]]*?)'. // target
					'(\|([^\]]*?))?'. // title (if any)
				'\]\]'. // closing brackets
				'([a-z]+)?'. // any suffixes
				')';
			$char_regexes['externallink'] = '('.
				'\['. // explicit with [ and ]
					'([^\]]*?)'. // href
					'(?:\s+([^\]]*?))?'. // with optional title
				'\]'.
				/*
				'|'. // or
				'((https?):\/\/[^\s\,\<\>\{\}]*[^\s\.\,\<\>\{\}])'. // implicit url
				'|'. // or
				'([^\s,@\<\>\{\}:\[\]\#]+@([^\s,@\.\<\>\{\}:\[\]\#]+\.)*[^\s,@\.\<\>\{\}:\[\]\#]+)'. // implicit email address
				*/
				')';
		}
		$char_regexes['emphasize'] = '(('.$this->entities['\''].'){2,5})';
		if ($this->multi_line) {
			$char_regexes['eliminate'] = '(__TOC__|__NOTOC__|__NOEDITSECTION__)';
		}
		$char_regexes['addemphasis'] = '(the yorker)';
		if ($this->multi_line) {
			$char_regexes['variable'] = '(\{\{([^\}]*?)\}\})';
		}

		$this->stop = false;
		$this->stop_all = false;

		$called = array();

		$line = rtrim($line);

		// escape some symbols
		$line = xml_escape($line);
		//$line = preg_replace_callback('/([&<>])/i',array(&$this,'handle_symbols'),$line);

		$prefix = '';
		$postfix = '';
		if ($this->multi_line) {
			foreach ($line_regexes as $func=>$regex) {
				if (preg_match("/$regex/i",$line,$matches)) {
					$called[$func] = true;
					$func = 'handle_'.$func;
					$line = $this->$func($matches);
					if (is_array($line)) {
						$prefix .= $line[0];
						if (isset($line[2])) {
							$postfix = $line[2].$postfix;
						}
						$line = $line[1];
					}
					if ($this->stop || $this->stop_all) break;
				}
			}
		}
		if (!$this->stop_all) {
			$this->stop = false;
			foreach ($char_regexes as $func=>$regex) {
				$line = preg_replace_callback("/$regex/i",array(&$this,"handle_".$func),$line);
				if ($this->stop) break;
			}
		}

		$isline = strlen(trim($line))>0;

		$line = $prefix.$line.$postfix;

		// if this wasn't a list item, and we are in a list, close the list tag(s)
		if ($this->spoiler && (!isset($called['spoiler']) || !$called['spoiler'])) $line = $this->handle_spoiler(false,true) . $line;
		if (($this->list_level>0) && (!isset($called['list']) or !$called['list'])) $line = $this->handle_list(false,true) . $line;
		if (isset($this->deflist) and $this->deflist && (!isset($called['definitionlist']) or !$called['definitionlist'])) $line = $this->handle_definitionlist(false,true) . $line;
		if (isset($this->preformat) and $this->preformat && (!isset($called['preformat']) or !$called['preformat'])) $line = $this->handle_preformat(false,true) . $line;

		// suppress linebreaks for the next line if we just displayed one; otherwise re-enable them
		if ($isline) $this->suppress_linebreaks = (isset($called['newline']) || isset($called['sections']));

		return $line;
	}

	/**
	 * @brief Perform a stress test.
	 * @return string Processed wikitext (HTML).
	 */
	function test() {
		$text = 'WikiParser stress tester. <br /> Testing...
__TOC__

== Nowiki test ==
<nowiki>[[wooticles|narf]] and \'\'\'test\'\'\' and stuff.</nowiki>

== Character formatting ==
This is \'\'emphasized\'\', this is \'\'\'really emphasized\'\'\', this is \'\'\'\'grossly emphasized\'\'\'\',
and this is just \'\'\'\'\'freeking insane\'\'\'\'\'.
Done.

== Variables ==
{{CURRENTDAY}}/{{CURRENTMONTH}}/{{CURRENTYEAR}}
Done.

== Image test ==
[[Image:bao1.jpg]]
[[Image:bao1.jpg|frame|alternate text]]
[[Image:bao1.jpg|right|alternate text]]
Done.

== Horizontal Rule ==
Above the rule.
----
Done.

== Hyperlink test ==
This is a [[namespace:link target|bitchin hypalink]] to another document for [[click]]ing, with [[(some) hidden text]] and a [[namespace:hidden namespace]].

A link to an external site [http://www.google.ca] as well another [http://www.esitemedia.com], and a [http://www.blitzaffe.com titled link] -- woo!
Done.

== Preformat ==
Not preformatted.
 Totally preformatted 01234    o o
 Again, this is preformatted    b    <-- It\'s a face
 Again, this is preformatted   ---\'
Done.

== Bullet test ==
* One bullet
* Another \'\'\'bullet\'\'\'
*# a list item
*# another list item
*#* unordered, ordered, unordered
*#* again
*# back down one
Done.

== Definition list ==
; yes : opposite of no
; no : opposite of yes
; maybe
: somewhere in between yes and no
Done.

== Indent ==
Normal
: indented woo
: more indentation
Done.

';
		return $this->parse($text);
	}

	/**
	 * @brief Parse a piece wikitext.
	 * @param $text string Wikitext to parse.
	 * @param $title string Title.
	 * @return string HTML processed wikitext.
	 */
	function parse($text,$title='') {
		assert('is_string($text)');

		$this->redirect = false;

		$this->nowikis = array();
		$this->list_level_chars = '';
		$this->list_level = 0;
		$this->spoiler = false;

		$this->deflist = false;
		$this->linknumber = 0;
		$this->suppress_linebreaks = false;
		$this->in_paragraph = false;

		$this->page_title = $title;

		$output = '';

		$text = preg_replace_callback('/<nowiki>(.*?)<\/nowiki>/is',array(&$this,"handle_save_nowiki"),$text);

		// add a newline at the end if there isn't already one there
		$lines = explode("\n",$text);
		if ($this->multi_line && !empty($lines[count($lines)-1])) {
			$lines[] = '';
		}

		if (preg_match('/^\#REDIRECT\s+\[\[(.*?)\]\]$/',trim($lines[0]),$matches)) {
			$this->redirect = $matches[1];
		}

		foreach ($lines as $k=>$line) {
			$line = $this->parse_line($line);
			$output .= $line;
		}
		if (!$this->multi_line) {
			$output .= $this->emphasize_off();
		}

		$this->nextnowiki = 0;
		$output = preg_replace_callback('/&lt;nowiki&gt;&lt;\/nowiki&gt;/i',array(&$this,'handle_restore_nowiki'),$output);

		return $output;
	}

	function handle_save_nowiki($matches) {
		array_push($this->nowikis,$matches[1]);
		return '<nowiki></nowiki>';
	}

	function handle_restore_nowiki($matches) {
		return $this->nowikis[$this->nextnowiki++];
	}

	/**
	 *	@brief	Return inline JS to detect Flash Version and generate embed tags (used by MediaPlayer and YouTube)
	 *	@author	Chris Travis (cdt502 - ctravis@gmail.com)
	 *	@date	01/02/2008
	 *	@param	$params - array containing all the parameters that should be passed to flash movie
	 */
	function get_inline_flash_code ($params) {
		static $player_count = 0;
		$player_count++;
		$default_params = array(
			'align', 'center',
			'id', 'movie' . $player_count,
			'quality', 'high',
			'bgcolor', '#FFFFFF',
			'name', 'movie' . $player_count,
			'allowScriptAccess', 'sameDomain',
			'type', 'application/x-shockwave-flash',
			'codebase', 'http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab',
			'pluginspace', 'http://www.adobe.com/go/getflashplayer'
		);
		$params = array_merge($default_params, $params);
		foreach ($params as &$param) {
			$param = '"' . $param . '"';
		}
		$params = implode(',', $params);
		$output = '
<div style="text-align:center;">
<script type="text/javascript">
// <![CDATA[
// Version check based upon the values entered above in "Globals"
var hasReqestedVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);

// Check to see if the version meets the requirements for playback
if (hasReqestedVersion) {
	// if we\'ve detected an acceptable version
	// embed the Flash Content SWF when all tests are passed
	AC_FL_RunContent(' . $params . ');
} else {  // flash is too old or we can\'t detect the plugin
	var alternateContent = \'<div style="width: 340px; height: 280px; border: 1px solid #999999;"><br />\'
	+ "<b>Flash Video Clip</b><br /><br /> "
	+ "This content requires the Adobe Flash Player 9. "
	+ "<a href=http://www.adobe.com/go/getflash/>Get Flash</a>"
	+ "</div>";
	document.write(alternateContent);  // insert non-flash content
}
// ]]>
</script>
<noscript>
	<div style="width: 340px; height: 280px; border: 1px solid #999999;"><br />
	<b>Flash Video Clip</b><br /><br />
  	This content requires the Adobe Flash Player 9 and a browser with JavaScript enabled.
  	<a href="http://www.adobe.com/go/getflash/">Get Flash</a>
  	</div>
</noscript>
</div>';
		return $output;
	}
}

?>
