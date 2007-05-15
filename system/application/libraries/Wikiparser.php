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
	protected $ignore_images;
	protected $emphasis;
	protected $quote_template;
	protected $templates;

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
	function Wikiparser() {
		$CI = &get_instance();
		$CI->load->helper('wikilink');

		$this->reference_wiki = 'local';
		$this->external_wikis = PresetWikis();
		$this->image_uri = '/images/';
		$this->image_overrides = array();
		$this->ignore_images = FALSE;
		$this->emphasis[1] = FALSE;
		$this->emphasis[2] = FALSE;
		$this->emphasis[3] = FALSE;
		$this->emphasis[4] = FALSE;
		$this->emphasis[5] = FALSE;
		$this->emphasis[6] = FALSE;

		$this->quote_template = 'pull_quote';
		$this->templates = array(
				'pull_quote' => '<blockquote>
<img src="/images/prototype/news/quote_open.png" alt="Quote" title="Quote" />
{{1}}
<img src="/images/prototype/news/quote_close.png" alt="Quote" title="Quote" />
<br /><span class="author">{{2}}</span></blockquote>',
				'frame' => '<div class="BlueBox"><h4>{{1}}</h4>{{2}}</div>',
				'br' => '<br />',
			);
	}

	/**
	 *	@brief		Adds a rule to the wikiparser making it replace [[Image:$id]] with <img src="$url" />
	 *	@author		Chris Travis (cdt502 - ctravis@gmail.com)
	 *	@date		Tue 15th May 2007 16:42
	 */
	function add_image_override($id, $url)
	{
		$this->image_overrides[$id] = $url;
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
			return "\n".$matches[0];
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

	function handle_image($href,$title,$options) {
		if ($this->ignore_images) return "";
		if (!$this->image_uri) return $title;
		if (array_key_exists($href,$this->image_overrides)) {
			$href = $this->image_overrides[$href];
		} else {
			$href = $this->image_uri . $href;
		}

		$imagetag = sprintf(
			'<img src="%s" alt="%s" />',
			$href,
			$title
		);
		foreach ($options as $k=>$option) {
			switch($option) {
				case 'frame':
					$imagetag = sprintf(
						'<div style="float: right; background-color: #F5F5F5; border: 1px solid #D0D0D0; padding: 2px">'.
						'<img src="%s" />'.
						'<div>%s</div>'.
						'</div>',
						$href,
						$title
					);
					if ($this->in_paragraph) {
						// divs aren't allowed in paragraphs, so close and reopen
						$imagetag = $this->emphasize_off()."</p>\n" . $imagetag . "\n<p>";
					}
					break;
				case 'centre':
					$imagetag = sprintf(
						'<div style="text-align: center;">%s</div>',
						$imagetag
					);
					if ($this->in_paragraph) {
						// divs aren't allowed in paragraphs, so close and reopen
						$imagetag = $this->emphasize_off()."</p>\n" . $imagetag . "\n<p>";
					}
					break;
			}
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
			$title = array_pop($options);
			return $this->handle_image($href,$title,$options);
		}
		if (array_key_exists($namespace, $this->external_wikis)) {
			$reference_wiki = $this->external_wikis[$namespace];
			$namespace = '';
		} elseif ('youtube' === $namespace) {
			$output = '
<center>
<script language="JavaScript" type="text/javascript">
<!--
// Version check based upon the values entered above in "Globals"
var hasReqestedVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);

// Check to see if the version meets the requirements for playback
if (hasReqestedVersion) {
	// if we\'ve detected an acceptable version
	// embed the Flash Content SWF when all tests are passed
	AC_FL_RunContent(
				"src", "http://www.youtube.com/v/'.$href.'",
				"width", "340",
				"height", "280",
				"align", "center",
				"id", "movie",
				"quality", "high",
				"bgcolor", "#FFFFFF",
				"name", "movie",
				"allowScriptAccess","sameDomain",
				"type", "application/x-shockwave-flash",
				"codebase", "http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab",
				"pluginspage", "http://www.adobe.com/go/getflashplayer"
	);
} else {  // flash is too old or we can\'t detect the plugin
	var alternateContent = \'<div style="width: 340px; height: 280px; border: 1px solid #999999;"><br />\'
	+ "<b>YouTube Video Clip</b><br /><br /> "
	+ "This content requires the Adobe Flash Player 9. "
	+ "<a href=http://www.adobe.com/go/getflash/>Get Flash</a>"
	+ "</div>";
	document.write(alternateContent);  // insert non-flash content
}
// -->
</script>
<noscript>
	<div style="width: 340px; height: 280px; border: 1px solid #999999;"><br />
	<b>YouTube Video Clip</b><br /><br />
  	This content requires the Adobe Flash Player 9 and a browser with JavaScript enabled.
  	<a href="http://www.adobe.com/go/getflash/">Get Flash</a>
  	</div>
</noscript>
</center>';
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

		return sprintf(
			'[%s %s]%s',
			$href,
			$title,
			(isset($matches[7]) ? $matches[7] : "")
		);
	}

	function handle_externallink($matches) {
		$href = $matches[2];
		if (array_key_exists(4,$matches)) {
			// implicit mailto
			$href = 'Mailto:'.$matches[4];
			$title= $matches[4];
		/*} else if (array_key_exists(4,$matches)) {
			// implicit url
			$href = $matches[4];
			$title= $matches[4];*/
		} else if (!isset($matches[3])) {
			// explicit unamed
			$this->linknumber++;
			$title = '['.$this->linknumber.']';
		} else {
			// explicit named
			$title = $matches[3];
		}
		$newwindow = false;

		return sprintf(
			'<a href="%s"%s>%s</a>',
			$href,
			($newwindow?' target="_blank"':''),
			$title
		);
	}

	function emphasize($amount) {
		$amounts = array(
			2=>array('<em>','</em>'),
			3=>array('<strong>','</strong>'),
			4=>array('<strong>','</strong>'),
			5=>array('<em><strong>','</strong></em>'),
		);

		$output = '';

		// handle cases where emphasized phrases end in an apostrophe, eg: ''somethin'''
		// should read <em>somethin'</em> rather than <em>somethin<strong>
		if (isset($this->emphasis) and (!$this->emphasis[$amount]) && ($this->emphasis[$amount-1]) ) {
			$amount--;
			$output = '\'';
		}

		$output .= $amounts[$amount][(int) $this->emphasis[$amount]];

		$this->emphasis[$amount] = !$this->emphasis[$amount];

		return $output;
	}

	function handle_emphasize($matches) {
		$amount = strlen($matches[1]);
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
		if (isset($this->emphasis)) {
//			while (list($amount, $state) = each($this->emphasis)) {
			foreach ($this->emphasis as $amount=>$state) {
				if ($state) {
					$output .= $this->emphasize($amount);
					$this->emphasis[$amount] = FALSE;
				}
			}
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
			$replacement = $this->templates[$this->template_elements[0]];
			$replacement = preg_replace_callback(
					'/\{\{(\d+)\}\}/i',
					array(&$this,'handle_template_parameter'),
					$replacement);
			return $replacement;
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
		//echo var_dump($matches);
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
		$line_regexes = array(
			'special_quote'=>'^"""(.*)"""\s*(.*)$',
			'startparagraph'=>'^([^\{\s\*\#;\:=-].*?)$',
			//'preformat'=>'^\s(.*?)$',
			'definitionlist'=>'^([\;\:])\s*(.*?)$',
			'newline'=>'^$',
			'list'=>'^([\*\#]+)(.*?)$',
			'sections'=>'^(={1,6})(.*?)(={1,6})$',
			'horizontalrule'=>'^----$',
		);
		$char_regexes = array(
//			'link'=>'(\[\[((.*?)\:)?(.*?)(\|(.*?))?\]\]([a-z]+)?)',
			'internallink'=>'('.
				'\[\['. // opening brackets
					'(([^\]]*?)\:)?'. // namespace (if any)
					'([^\]]*?)'. // target
					'(\|([^\]]*?))?'. // title (if any)
				'\]\]'. // closing brackets
				'([a-z]+)?'. // any suffixes
				')',
			'externallink'=>'('.
				'\['. // explicit with [ and ]
					'([^\]]*?)'. // href
					'(\s+[^\]]*?)?'. // with optional title
				'\]'.
				//'|'. // or
				//'((https?):\/\/[^\s\,\<\>\{\}]*[^\s\.\,\<\>\{\}])'. // implicit url
				'|'. // or
				'([^\s,@\<\>\{\}]+@([^\s,@\.\<\>\{\}]+\.)*[^\s,@\.\<\>\{\}]+)'. // implicit email address
				')',
			'emphasize'=>'(\'{2,5})',
			'eliminate'=>'(__TOC__|__NOTOC__|__NOEDITSECTION__)',
			'addemphasis'=>'(the yorker)',
			'variable'=>'(\{\{([^\}]*?)\}\})',
		);

		$this->stop = false;
		$this->stop_all = false;

		$called = array();

		$line = rtrim($line);

		// escape some symbols
		$line = htmlentities($line, ENT_NOQUOTES, 'UTF-8');
		//$line = preg_replace_callback('/([&<>])/i',array(&$this,'handle_symbols'),$line);

		foreach ($line_regexes as $func=>$regex) {
			if (preg_match("/$regex/i",$line,$matches)) {
				$called[$func] = true;
				$func = 'handle_'.$func;
				$line = $this->$func($matches);
				if ($this->stop || $this->stop_all) break;
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

		// if this wasn't a list item, and we are in a list, close the list tag(s)
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

		$this->deflist = false;
		$this->linknumber = 0;
		$this->suppress_linebreaks = false;
		$this->in_paragraph = false;

		$this->page_title = $title;

		$output = '';

		$text = preg_replace_callback('/<nowiki>(.*?)<\/nowiki>/i',array(&$this,"handle_save_nowiki"),$text);

		// add a newline at the end if there isn't already one there
		$lines = explode("\n",$text);
		if (empty($lines[count($lines)-1])) {
			$lines[] = '';
		}

		if (preg_match('/^\#REDIRECT\s+\[\[(.*?)\]\]$/',trim($lines[0]),$matches)) {
			$this->redirect = $matches[1];
		}

		foreach ($lines as $k=>$line) {
			$line = $this->parse_line($line);
			$output .= $line;
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
}
?>