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
			if ($interface[1]->Changed() === true) {
				return true;
			}
		}
		return false;
	}

	public function UpdatedValues()
	{
		$values = array();
		foreach ($this->interfaces as $id => &$interface) {
			if ($interface[1]->Changed() !== null) {
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

	public function Reset()
	{
		foreach ($this->interfaces as $name => &$interface) {
			$interface[1]->Reset();
		}
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
					$ci->main_frame->includeJs('javascript/css_classes.js');
					$ci->main_frame->includeJs('javascript/input.js');
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
				$warnings = $interface[1]->Warnings();
				if (count($warnings) > 0) {
					$ci->main_frame->includeJs('javascript/css_classes.js');
					$ci->main_frame->includeJs('javascript/input.js');
					$enter_js = 'input_error_mouse('.js_literalise($name).', true);';
					$exit_js = 'input_error_mouse('.js_literalise($name).', false);';
					$ci->messages->AddMessage('warning',
						'<a href="#'.$name.'"'.
							' onmouseover="'.xml_escape($enter_js).'"'.
							' onmouseout="'.xml_escape($exit_js).'"'.
							'>'.
							'Warnings for "'.xml_escape($interface[0]).'"'.
						'</a>'.
						xml_escape(': '.join(', ', $warnings).'.')
					);
				}
			}
		}
		return $error_count;
	}

	public function Load()
	{
		foreach ($this->interfaces as $name => &$interface) {
			$id = $interface[1]->Id();
			$error = (count($interface[1]->Errors()) > 0);
			if ($error) {
				?><div id="<?php echo($id.'__error'); ?>" class="input_error"><?php
			}
			?><label for="<?php echo($id); ?>"><?php
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

/// Text validator class
abstract class InputValidator
{
	public abstract function Validate(&$value, &$errors, &$warnings);
}

/// Input interface object.
abstract class InputInterface
{
	protected $name;
	protected $id;
	protected $default;
	protected $value;
	protected $enabled;
	protected $required = false;
	protected $validators = array();
	protected $changed = null;
	protected $errors = array();
	protected $warnings = array();
	protected $div_classes = array();

	/// Construct and initialise.
	public function __construct($name, $default = null, $enabled = null, $auto = true)
	{
		$this->name = $name;
		$this->id = str_replace(array(']','['),
								array('_','_'),
								$name);
		$this->value = $default;
		$this->enabled = $enabled;
		if ($this->enabled !== null && $this->enabled && $default === null) {
			$this->enabled = false;
		}
		$this->default = $this->Value();
		if (null !== $enabled) {
			get_instance()->main_frame->includeJs('javascript/css_classes.js');
			get_instance()->main_frame->includeJs('javascript/input.js');
		}
		if ($auto) {
			$this->Import($_POST);
		}
	}

	/// Attach a validator object to this interface.
	public function AddValidator(&$validator)
	{
		$this->validators[] = &$validator;
	}

	/// Set whether this input is required.
	public function SetRequired($required)
	{
		$this->required = $required;
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
					?>	/><?php
		}
		?><div	id="<?php echo($this->id); ?>"<?php
		$classes = $this->div_classes;
		$classes[] = 'input_encase';
		if (!empty($classes)) {
			?>	class="<?php echo(xml_escape(join(' ',$classes))); ?>"<?php
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

	protected function _ValueChanged()
	{
	}

	public function Reset()
	{
		$this->value = $this->default;
		$this->changed = null;
		$this->_ValueChanged();
	}

	protected function _Validate(&$value, &$errors, &$warnings)
	{
		return true;
	}

	public function Validate()
	{
		$count = 0;
		if (null !== $this->changed) {
			if (null === $this->enabled || $this->enabled) {
				if ($this->_Validate($this->value, $this->errors, $this->warnings)) {
					foreach ($this->validators as &$validator) {
						if (!$validator->Validate($this->value, $this->errors, $this->warnings)) {
							break;
						}
					}
				}
				$count = count($this->errors);
			}
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
	public function Id()
	{
		return $this->id;
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

	/// Get any warning messages.
	public function Warnings()
	{
		return $this->warnings;
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
				?>	/><?php
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
	protected $multiline = false;

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

	public function SetMultiline($multiline)
	{
		$this->multiline = $multiline;
	}

	protected function _Load()
	{
		if (!$this->multiline) {
			?><input	type="text"<?php
					?>	name="<?php echo("$this->name[val]"); ?>"<?php
					?>	id="<?php echo($this->id.'__val'); ?>"<?php
					?>	value="<?php echo(xml_escape($this->value)); ?>"<?php
				if ($this->max_length !== null) {
					?>	maxlength="<?php echo($this->max_length); ?>"<?php
				}
				foreach ($this->events as $event => $javascript) {
					?>	<?php echo($event); ?>="<?php echo(xml_escape($javascript)); ?>"<?php
				}
					?>	/><?php
		}
		else {
			?><textarea	name="<?php echo("$this->name[val]"); ?>"<?php
					?>	id="<?php echo($this->id.'__val'); ?>"<?php
				if ($this->max_length !== null) {
					?>	maxlength="<?php echo($this->max_length); ?>"<?php
				}
				foreach ($this->events as $event => $javascript) {
					?>	<?php echo($event); ?>="<?php echo(xml_escape($javascript)); ?>"<?php
				}
					?>><?php
			echo(xml_escape($this->value));
			?></textarea><?php
		}
	}

	protected function _Import(&$arr)
	{
		$this->value = $arr['val'];
		return array();
	}

	protected function _Validate(&$value, &$errors, &$warnings)
	{
		$ok = true;
		$len = strlen($value);
		if ($this->required && $len <= 0) {
			$errors[] = "this field is required";
			$ok = false;
		}
		elseif ($this->max_length !== null) {
			if ($len > $this->max_length) {
				$errors[] = "length of $len exceeds maximum of $this->max_length";
				$ok = false;
			}
		}
		return $ok;
	}
}

/// String validator, must be at least a certain number of chars long.
class InputTextValidatorMinLength extends InputValidator
{
	private $min_length;

	public function __construct($min_length)
	{
		$this->min_length = $min_length;
	}

	public function Validate(&$value, &$errors, &$warnings)
	{
		if (strlen($value) < $this->min_length) {
			$len = strlen($value);
			$errors[] = "length of $len does not reach minimum of $this->min_length";
			return false;
		}
		return true;
	}
}

/// Select input interface
class InputSelectInterface extends InputInterface
{
	protected $options = null;
	protected $events = array();
	protected $values = array();

	// Default to array() for multiselect
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

	public function Multiple()
	{
		return is_array($this->default);
	}

	public function AddedValues()
	{
		return array_diff($this->value, $this->default);
	}
	public function RemovedValues()
	{
		return array_diff($this->default, $this->value);
	}

	public function IsSelected($value)
	{
		return ($this->Multiple() ? in_array($value, $this->value) : ($value == $this->value));
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
						if ($this->IsSelected($value)) {
							?>	selected="selected"<?php
						}
						?>><?php
					echo(xml_escape($label));
				?></option><?php
			}
		}
	}

	protected function _Equal($v1, $v2)
	{
		if ($this->Multiple()) {
			$d1 = array_diff($v1,$v2);
			$d2 = array_diff($v2,$v1);
			return empty($d1) && empty($d2);
		}
		else {
			return parent::_Equal($v1, $v2);
		}
	}

	protected function _Load()
	{
		?><select	name="<?php echo($this->Multiple()?"$this->name[vals][]":"$this->name[val]"); ?>"<?php
				?>	id="<?php echo($this->id.'__val'); ?>"<?php
			if ($this->Multiple()) {
				?>	multiple="multiple"<?php
			}
			foreach ($this->events as $event => $javascript) {
				?>	<?php echo($event); ?>="<?php echo(xml_escape($javascript)); ?>"<?php
			}
				?>	><?php
			$this->LoadOptGroup($this->options);
		?></select><?php
		// So we can detect blank
		if ($this->Multiple()) {
			?><input	type="hidden" name="<?php echo("$this->name[a]"); ?>"<?php
					?>	/><?php
		}
	}

	protected function _Import(&$arr)
	{
		if ($this->Multiple()) {
			if (!isset($arr['vals'])) {
				$this->value = array();
			}
			else {
				$this->value = $arr['vals'];
			}
		}
		else {
			$this->value = $arr['val'];
		}
		return array();
	}

	protected function _Validate(&$value, &$errors, &$warnings)
	{
		$ok = true;
		if ($this->Multiple()) {
			if (null !== $value) {
				foreach ($value as $val) {
					if (!isset($this->values[$val])) {
						$errors[] = "\"$val\" is not a valid value";
						$ok = false;
					}
				}
			}
		}
		else {
			if (null !== $value && !isset($this->values[$value])) {
				$errors[] = "\"$value\" is not a valid value";
				$ok = false;
			}
		}
		return $ok;
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

	protected function _Validate(&$value, &$errors, &$warnings)
	{
		$ok = parent::_Validate($value, $errors, $warnings);
		if (!is_numeric($value)) {
			$errors[] = "not a number";
			$ok = false;
		}
		else {
			$value = (int)$value;
			if ($this->min !== null && $value < $this->min) {
				$errors[] = "below the lower limit of $this->min";
				$ok = false;
			}
			else if ($this->max !== null && $value > $this->max) {
				$errors[] = "above the upper limit of $this->max";
				$ok = false;
			}
		}
		return $ok;
	}
}

?>
