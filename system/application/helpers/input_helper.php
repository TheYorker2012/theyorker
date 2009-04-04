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
		$this->interfaces[$interface->Name()] = array($label, &$interface);
	}

	public function Updated()
	{
		foreach ($this->interfaces as $id => &$interface) {
			if ($interface[1]->Changed() !== null) {
				return true;
			}
		}
		return false;
	}

	public function Changed()
	{
		foreach ($this->interfaces as $id => &$interface) {
			if ($interface[1]->Changed === true) {
				return true;
			}
		}
		return false;
	}

	public function UpdatedValues()
	{
		$values = array();
		foreach ($this->interfaces as $id => &$interface) {
			if ($interfaces[1]->Changed() !== null) {
				$values[$id] = $interface[1]->Value();
			}
		}
		return $values;
	}

	public function ChangedValues()
	{
		$values = array();
		foreach ($this->interfaces as $id => &$interface) {
			if ($interface[1]->Changed() === true) {
				$values[$id] = $interface[1]->Value();
			}
		}
		return $values;
	}

	public function Validate()
	{
		$ci = &get_instance();
		$error_count = 0;
		foreach ($this->interfaces as $name => &$interface) {
			$error_count += $interface[1]->Validate();
			if ($interface[1]->Enabled()) {
				$errors = $interface[1]->Errors();
				if (count($errors) > 0) {
					$enter_js = 'input_error_mouse('.js_literalise($name).', true);';
					$exit_js = 'input_error_mouse('.js_literalise($name).', false);';
					$ci->messages->AddMessage('error',
						'<a href="#'.$name.'"'.
							' onmouseover="'.xml_escape($enter_js).'"'.
							' onmouseout="'.xml_escape($exit_js).'"'.
							'>'.
							'Errors were found in "'.xml_escape($interface[0]).'"'.
						'</a>'.
						xml_escape(': '.join(', ', $errors).'.')
					);
				}
			}
		}
		return $error_count;
	}

	public function Load()
	{
		foreach ($this->interfaces as $name => &$interface) {
			$error = (count($interface[1]->Errors()) > 0);
			if ($error) {
				?><div id="<?php echo($name.'__error'); ?>" class="input_error"><?php
			}
			?><label for="<?php echo($name); ?>"><?php
				echo(xml_escape($interface[0]));
			?></label><?php
			$interface[1]->Load();
			if ($error) {
				?></div><?php
			}
			?><br /><?php
		}
	}
}

/// Input interface object.
abstract class InputInterface
{
	protected $name;
	protected $default;
	protected $value;
	protected $enabled;
	protected $changed = null;
	protected $errors = array();
	protected $div_classes = null;

	/// Construct and initialise.
	public function __construct($name, $default = null, $enabled = null, $auto = true)
	{
		$this->name = $name;
		$this->value = $default;
		$this->enabled = $enabled;
		if ($this->enabled !== null && $this->enabled && $default === null) {
			$this->enabled = false;
		}
		$this->default = $this->Value();
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
		?><div	id="<?php echo($this->name); ?>"<?php
		if (null != $this->div_classes) {
			?>	class="<?php echo(xml_escape(join(' ',$this->div_classes))); ?>"<?php
		}
			?>	><?php
			$this->_Load();
		?></div><?php
	}

	/** Import from POST/GET array.
	 * @return array Any error messages.
	 */
	protected abstract function _Import(&$arr);

	protected function _Equal($v1, $v2)
	{
		//var_dump(array($this->name, $v1, $v2));
		return $v1 === $v2;
	}

	/// Import from entire POST/GET array.
	public function Import(&$arr)
	{
		if (isset($_POST[$this->name])) {
			$this->changed = false;
			if (null !== $this->enabled) {
				$this->enabled = isset($arr[$this->name]['_enabled']);
			}
			$this->errors = $this->_Import($arr[$this->name]);
		}
	}

	protected function _Validate()
	{
		return array();
	}

	public function Validate()
	{
		$count = 0;
		if (null === $this->enabled || $this->enabled) {
			$errors = $this->_Validate();
			foreach ($errors as &$error) {
				$this->errors[] = $error;
			}
			$count = count($errors);
		}
		if (null !== $this->changed) {
			if (!$this->_Equal($this->Value(), $this->default)) {
				$this->changed = true;
			}
		}
		return $count;
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
		if (null === $this->enabled || $this->enabled) {
			return $this->value;
		}
		else {
			return null;
		}
	}

	public function Changed()
	{
		return $this->changed;
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
		if ($default !== null) {
			$default = (bool)$default;
		}
		parent::__construct($name, $default, null, $auto);
	}

	protected function _Load()
	{
		?><input	type="checkbox" name="<?php echo("$this->name[val]"); ?>"<?php
			if ($this->value !== null && $this->value) {
				?>	checked="checked"<?php
			}
				?>	/><?php
		?><input	type="hidden" name="<?php echo("$this->name[a]"); ?>"<?php
	}

	protected function _Import(&$arr)
	{
		$this->value = isset($arr['val']);
		return array();
	}
}

/// Text input interface
class InputTextInterface extends InputInterface
{
	protected $max_length = null;
	protected $events = array();

	public function __construct($name, $default = null, $enabled = null, $auto = true)
	{
		parent::__construct($name, $default, $enabled, $auto);
	}

	public function SetMaxLength($max_length)
	{
		$this->max_length = (int)$max_length;
	}

	public function SetEvent($event, $javascript)
	{
		$this->events[$event] = $javascript;
	}

	protected function _Load()
	{
		?><input	type="text"<?php
				?>	name="<?php echo("$this->name[val]"); ?>"<?php
				?>	id="<?php echo($this->name.'__val'); ?>"<?php
				?>	value="<?php echo(xml_escape($this->value)); ?>"<?php
			if ($this->max_length !== null) {
				?>	maxlength="<?php echo($this->max_length); ?>"<?php
			}
			foreach ($this->events as $event => $javascript) {
				?>	<?php echo($event); ?>="<?php echo(xml_escape($javascript)); ?>"<?php
			}
				?>	/><?php
	}

	protected function _Import(&$arr)
	{
		$this->value = $arr['val'];
		return array();
	}

	protected function _Validate()
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

/// Select input interface
class InputSelectInterface extends InputInterface
{
	protected $options = null;
	protected $events = array();
	protected $values = array();

	public function __construct($name, $default = null, $enabled = null, $auto = true)
	{
		parent::__construct($name, $default, $enabled, $auto);
	}

	public function SetOptions($options)
	{
		$this->values = array();
		$this->options = $options;
		foreach ($options as $key => $value) {
			if (is_array($value)) {
				foreach ($value as $key1 => $value1) {
					$this->values[$key1] = true;
				}
			}
			else {
				$this->values[$key] = true;
			}
		}
	}

	public function SetEvent($event, $javascript)
	{
		$this->events[$event] = $javascript;
	}

	public function LoadOptGroup($options, $nested = true)
	{
		foreach ($options as $value => $label) {
			if (is_array($label)) {
				if ($nested) {
					?><optgroup	label="<?php echo(xml_escape($value)); ?>" ><?php
						$this->LoadOptGroup($label, false);
					?></optgroup><?php
				}
			}
			else {
				?><option	value="<?php echo(xml_escape($value)); ?>"<?php
						if ($this->value == $value) {
							?>	selected="selected"<?php
						}
						?>><?php
					echo(xml_escape($label));
				?></option><?php
			}
		}
	}

	protected function _Load()
	{
		?><select	name="<?php echo("$this->name[val]"); ?>"<?php
				?>	id="<?php echo($this->name.'__val'); ?>"<?php
			foreach ($this->events as $event => $javascript) {
				?>	<?php echo($event); ?>="<?php echo(xml_escape($javascript)); ?>"<?php
			}
				?>	><?php
			$this->LoadOptGroup($this->options);
		?></select><?php
	}

	protected function _Import(&$arr)
	{
		$this->value = $arr['val'];
		return array();
	}

	protected function _Validate()
	{
		if (null !== $this->value && !isset($this->values[$this->value])) {
			return array("\"$this->value\" is not a valid value");
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
		if (null !== $default && is_numeric($default) && (int)$default == $default) {
			$default = (int)$default;
		}
		parent::__construct($name, $default, $enabled, $auto);
	}

	public function SetRange($min = null, $max = null)
	{
		$this->min = (int)$min;
		$this->max = (int)$max;
	}

	protected function _Validate()
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
