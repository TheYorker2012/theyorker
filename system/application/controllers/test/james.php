<?php

/// James' Test Controller
class James extends controller
{
	/// Generate random sequences of alphanumeric characters.
	function random()
	{
		if (!CheckPermissions('admin')) return;
		
		$bulk = '
		<FORM CLASS="form" METHOD="POST" ACTION="/test/james/random">
		<FIELDSET>
		<label for="length">Length:</label><input value="8" name="length" /><br />
		<label for="quantity">Quantity:</label><input value="8" name="quantity" /><br />
		<input type="submit" CLASS="button" name="submitter" value="Generate"><br />
		</FIELDSET>
		</FORM>
		';
		
		$length = $this->input->post('length');
		$quantity = $this->input->post('quantity');
		if (is_numeric($length) && is_numeric($quantity)) {
			$length = (int)$length;
			$quantity = (int)$quantity;
			if ($quantity > 100) {
				$quantity = 100;
			}
			$this->load->helper('string');
			$bulk = '';
			for ($i = 0; $i < $quantity; ++$i) {
				$gen = random_string('alnum', $length);
				$bulk .= '<p><b>'.$gen.'</b></p>';
			}
		}
		
		$this->main_frame->SetContent(new SimpleView($bulk));
		$this->main_frame->SetTitle('Random generator');
		$this->main_frame->Load();
	}
}

?>