<?php
if (!empty($boxes)) {
	foreach ($boxes as $box) {
		$this->load->view('flexibox/' . $box['type'], $box);
	}
}
?>