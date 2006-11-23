<?php
/**
 * This controller is the default.
 * It currently displays only the prototype homepage, in the prototype student frame
 *
 * \author Nick Evans
 */
class Review extends Controller {

	/**
	* Displays prototype homepage, in the prototype student frame
	*/
	function index()
	{
		$data = array(
			'title' => 'Reviews',
			'food' => 'Food',
			'drink' => 'Drink',
			'culture' => 'Culture',
			'foodtext' => 'This is some text about food. I don\'t know what will go here, but you can have a field day making some',
			'drinktext' => 'I guess this is some drink text. Again, you can have a field day writing this text. Probably not really as much as the food one though',
			'culturetext' => 'Smeg smeg smeg smeg SMEGMANIA! This text is about culture and your mom and your moms mom and her mom too',
		);
		$this->load->view('reviews/review_frame',$data);
	}

}
?>