<?php

get_instance()->load->helper('input');

/// Input wikitext interface with ajax preview etc.
class InputWikitextInterface extends InputTextInterface
{
	private $wikiparser = null;
	private $preview = false;
	private $xml = null;

	public function __construct($name, $default = null, $enabled = null, $auto = true)
	{
		parent::__construct($name, $default, $enabled, $auto);
		$this->SetMultiLine(true);
		$ci = &get_instance();
		if (isset($ci->main_frame)) {
			get_instance()->main_frame->includeCss('stylesheets/input_wikitext.css');
			get_instance()->main_frame->includeJs('javascript/simple_ajax.js');
			get_instance()->main_frame->includeJs('javascript/wikitoolbar.js');
		}

		$this->div_classes[] = "input_wikitext";
	}

	public function ValueXhtml()
	{
		if (null === $this->xml) {
			if (null === $this->wikiparser) {
				get_instance()->load->library('Wikiparser');
				$parser = new Wikiparser();
			}
			else {
				$parser = &$this->wikiparser;
			}
			$this->xml = $parser->parse($this->value);
		}
		return $this->xml;
	}

	/// Set the wikiparser to use.
	function SetWikiparser($wiki = null, $preview = true)
	{
		$this->wikiparser = $wiki;
		$this->preview = $preview;

		// Generate preview for ajax?
		if ($preview && isset($_GET['input_wikitext_preview_field']) && $_GET['input_wikitext_preview_field'] == $this->name) {
			if (null === $this->wikiparser) {
				get_instance()->load->library('Wikiparser');
				$parser = new Wikiparser();
			}
			else {
				$parser = &$this->wikiparser;
			}
			if (isset($_POST['input_wikitext_preview'])) {
				$xml = $parser->parse($_POST['input_wikitext_preview']);
			}
			else {
				$xml = "POST input_wikitext_preview missing";
			}
			header('content-type: text/xml');
			?><<?php ?>?xml version="1.0" encoding="UTF-8"?><?php
			?><wikitext><?php
			echo(xml_escape($xml));
			?></wikitext><?php
			exit(0);
		}
	}

	protected function _ValueChanged()
	{
		$this->xml = null;
	}

	protected function _Load()
	{
		if (null === $this->wikiparser) {
			// Plain standard parsing can use the efficient ajax url
			$parse_uri = '/ajax/wikiparse';
		}
		else {
			$parse_uri = get_instance()->uri->uri_string().'?input_wikitext_preview_field='.urlencode($this->name);
		}

		// Toolbar
		?><div id="<?php echo($this->name.'__toolbar'); ?>"></div><?php
		// Textarea
		parent::_Load();
		// Preview
		if ($this->preview) {
			?><div id="<?php echo($this->name.'__preview'); ?>" class="input_wikitext_preview"<?php
				if ($this->value===''){
					?>	style="display:none"<?php
				}
				?>><?php
			echo($this->ValueXhtml());
			?></div><?php
		}
		// Toolbar initialisation
		echo(js_block(
			'mwSetupToolbar('.js_literalise($this->name.'__toolbar').','.
							js_literalise($this->name.'__val').','.
							'false'.
							($this->preview ? (',['.js_literalise($this->name.'__preview').','.js_literalise($parse_uri).']') : '').
							');'
		));
	}

	protected function _Import(&$arr)
	{
		$this->value = $arr['val'];
		return array();
	}
}

?>
