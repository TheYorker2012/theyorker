<?php

get_instance()->load->helper('input');

/// Input interface for progress bars.
class InputProgressInterface extends InputIntInterface
{
	public function __construct($name, $default = null, $enabled = null, $auto = true)
	{
		$this->SetRange(0,100);
		$this->SetMaxLength(3);
		parent::__construct($name, $default, $enabled, $auto);
		get_instance()->main_frame->includeJs('javascript/input_progress.js');
		get_instance()->main_frame->includeCss('stylesheets/input_progress.css');

		$this->div_classes = array("input_progress");
	}

	protected function _Load()
	{
		$this->SetEvent('onkeyup', 'input_progress_changed('.js_literalise($this->name).');');

		$progress = $this->value;
		if (null === $progress || !is_numeric($progress)) {
			$progress = 50;
		}
		if ($progress < 0) {
			$progress = 0;
		}
		if ($progress > 100) {
			$progress = 100;
		}
		$rounded = 10*(int)($progress/10);
		?><script type="text/javascript"><?php
		echo(xml_escape('onLoadFunctions.push(function() {'.
							'input_progress_changed('.js_literalise($this->name).');'.
						'});', false));
		?></script><?php
		parent::_Load(false);
		?><div	id="<?php echo($this->name.'__progress'); ?>"<?php
			?>	class="progress"<?php
			?>	onmousedown="<?php echo(xml_escape(
					'input_progress_mousedown('.js_literalise($this->name).', event);'
					)); ?>"<?php
			?>	onmousemove="<?php echo(xml_escape(
					'input_progress_mousemove('.js_literalise($this->name).', event);'
					)); ?>"<?php
			?>	onmouseup="<?php echo(xml_escape(
					'input_progress_mouseup('.js_literalise($this->name).', event);'
					)); ?>"<?php
			?>	><?php
			?><div	id="<?php echo($this->name.'__bar'); ?>"<?php
				?>	class="bar progress<?php echo($rounded); ?>"<?php
				?>	style="width: <?php echo($progress); ?>%"<?php
				?>	><?php
			?></div><?php
		?></div><?php
	}
}

?>
