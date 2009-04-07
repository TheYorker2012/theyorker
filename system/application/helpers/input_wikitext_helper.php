<?php

get_instance()->load->helper('input');

/// Input wikitext interface with ajax preview etc.
class InputWikitextInterface extends InputTextInterface
{
	public function __construct($name, $default = null, $enabled = null, $auto = true)
	{
		parent::__construct($name, $default, $enabled, $auto);
		$this->SetMultiLine(true);
		get_instance()->main_frame->includeCss('stylesheets/input_wikitext.css');
		//get_instance()->main_frame->includeJs('javascript/simple_ajax.js');
		//get_instance()->main_frame->includeJs('javascript/css_classes.js');
		//get_instance()->main_frame->includeJs('javascript/input_wikitext.js');
		get_instance()->main_frame->includeJs('javascript/wikitoolbar.js');

		$this->div_classes[] = "input_wikitext";
	}

	protected function _Load()
	{
		?><div id="<?php echo($this->name.'__toolbar'); ?>"></div><?php
		parent::_Load();

		?><script type="text/javascript"><?php
		echo(xml_escape(
			'onLoadFunctions.push(function() {'.
				'mwSetupToolbar('.js_literalise($this->name.'__toolbar').','.js_literalise($this->name.'__val').', false);'.
			'})',
			false));
		?></script><?php
	}

	protected function _Import(&$arr)
	{
		$this->value = $arr['val'];
		return array();
	}
}

?>
