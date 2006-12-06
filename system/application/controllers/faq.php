<?php

class Faq extends Controller {

    function index()
    {
        $data = array(
            'content_view' => 'faq/faq'
        );
        $this->load->view('frames/student_frame',$data);
    }
}
?>
