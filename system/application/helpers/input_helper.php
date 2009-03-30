<?php

/// Input interfaces manager.
class InputInterfaces
{
	protected $interfaces = array();

	public function __construct()
	{
		$ci = &get_instance();
		$ci->main_frame->includeCss('stylesheets/input.css');
	}

	public function Add($label, &$interface)
	{
		$this->interfaces[$label] = &$interface;
	}

	public function Validate()
	{
		$ci = &get_instance();
		foreach ($this->interfaces as $label => &$interface) {
			if ($interface->Enabled()) {
				$interface->Validate();
				$errors = $interface->Errors();
				if (count($errors) > 0) {
					$enter_js = 'input_error_mouse('.js_literalise($interface->Name()).', true);';
					$exit_js = 'input_error_mouse('.js_literalise($interface->Name()).', false);';
					$ci->messages->AddMessage('error',
						'<a href="#'.$interface->Name().'"'.
							' onmouseover="'.xml_escape($enter_js).'"'.
							' onmouseout="'.xml_escape($exit_js).'"'.
							'>'.
							'Errors were found in "'.xml_escape($label).'"'.
						'</a>'.
						xml_escape(': '.join(', ', $errors).'.')
					);
				}
			}
		}
	}

	public function Load()
	{
		foreach ($this->interfaces as $label => &$interface) {
			$error = (count($interface->Errors()) > 0);
			if ($error) {
				?><div id="<?php echo($interface->Name().'__error'); ?>" class="input_error"><?php
			}
			?><label for="<?php echo($interface->Name()); ?>"><?php
				echo(xml_escape($label));
			?></label><?php
			$interface->Load();
			if ($error) {
				?></div><?php
			}
		}
	}
}

/// Input interface object.
abstract class InputInterface
{
	protected $name;
	protected $value;
	protected $enabled;
	protected $errors = array();

	/// Construct and initialise.
	public function __construct($name, $default = null, $enabled = null, $auto = true)
	{
		$this->name = $name;
		$this->value = $default;
		$this->enabled = $enabled;
		if (null !== $enabled) {
			get_instance()->main_frame->includeJs('javascript/input.js');
		}
		if ($auto) {
			$this->Import($_POST);
		}
	}

	/// Echo HTML for this input element.
	protected abstract function _Load();

	/// Echo HTML for this entire input element.
	public function Load()
	{
		if (null !== $this->enabled) {
			?><script type="text/javascript"><?php
				echo(xml_escape(
					'onLoadFunctions.push(function(){input_enabled_changed('.js_literalise($this->name).');});'
				, false));
			?></script><?php
			?><input	type="checkbox"<?php
					?>	name="<?php echo("$this->name[_enabled]"); ?>"<?php
					?>	onclick="<?php echo(xml_escape('input_enabled_changed('.js_literalise($this->name).');')); ?>"<?php
				if ($this->enabled) {
					?>	checked="checked"<?php
				}
					?>	><?php
		}
		$this->_Load();
	}

	/** Import from POST/GET array.
	 * @return array Any error messages.
	 */
	public abstract function _Import(&$arr);

	/// Import from entire POST/GET array.
	public function Import(&$arr)
	{
		if (isset($_POST[$this->name])) {
			if (null !== $this->enabled) {
				$this->enabled = isset($_POST[$this->name]['_enabled']);
			}
			$this->errors = $this->_Import($_POST[$this->name]);
		}
	}

	public function _Validate()
	{
		return array();
	}

	public function Validate()
	{
		$errors = $this->_Validate();
		foreach ($errors as &$error) {
			$this->errors[] = $error;
		}
	}

	public function Name()
	{
		return $this->name;
	}

	public function Enabled()
	{
		return $this->enabled === null || $this->enabled;
	}

	/// Get the value.
	public function Value()
	{
		return $this->value;
	}

	/// Get any error messages.
	public function Errors()
	{
		return $this->errors;
	}
}

/// Simple checkbox interface
class InputCheckboxInterface extends InputInterface
{
	public function __construct($name, $default = null, $auto = true)
	{
		parent::__construct($name, $default, null, $auto);
	}

	public function _Load()
	{
		?><input	type="hidden" name="<?php echo("$this->name[a]"); ?>"<?php
		?><input	type="checkbox" name="<?php echo("$this->name[val]"); ?>"<?php
			if ($this->value === true) {
				?>	checked="checked"<?php
			}
				?>	/><?php
	}

	public function _Import(&$arr)
	{
		$this->value = isset($arr['val']);
		return array();
	}
}

/// Text input interface
class InputTextInterface extends InputInterface
{
	protected $max_length = null;

	public function __construct($name, $default = null, $enabled = null, $auto = true)
	{
		parent::__construct($name, $default, $enabled, $auto);
	}

	public function SetMaxLength($max_length)
	{
		$this->max_length = (int)$max_length;
	}

	public function _Load()
	{
		?><input	type="text"<?php
				?>	name="<?php echo("$this->name"); ?>"<?php
				?>	id="<?php echo("$this->name"); ?>"<?php
				?>	value="<?php echo(xml_escape($this->value)); ?>"<?php
			if ($this->max_length !== null) {
				?>	maxlength="<?php echo($this->max_length); ?>"<?php
			}
				?>	/><?php
	}

	public function _Import(&$arr)
	{
		$this->value = $arr;
		return array();
	}

	public function _Validate()
	{
		if ($this->max_length !== null) {
			$len = strlen($this->value);
			if ($len > $this->max_length) {
				return array("length of $len exceeds maximum of $this->max_length");
			}
		}
		return array();
	}
}

/// Integer number input interface
class InputIntInterface extends InputTextInterface
{
	protected $min = null;
	protected $max = null;

	public function __construct($name, $default = null, $enabled = null, $auto = true)
	{
		parent::__construct($name, $default, $enabled, $auto);
	}

	public function SetRange($min = null, $max = null)
	{
		$this->min = (int)$min;
		$this->max = (int)$max;
	}

	public function _Validate()
	{
		$errors = parent::_Validate();
		if (!is_numeric($this->value)) {
			$errors[] = "not a number";
		}
		else {
			$this->value = (int)$this->value;
			if ($this->min !== null && $this->value < $this->min) {
				$errors[] = "below the lower limit of $this->min";
			}
			else if ($this->max !== null && $this->value > $this->max) {
				$errors[] = "above the upper limit of $this->max";
			}
		}
		return $errors;
	}
}

?>
