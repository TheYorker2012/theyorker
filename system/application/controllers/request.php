<?php

class Request extends Controller {

    function index()
    {
        $data = array(
            'content_view' => 'request/request'
        );
        $this->load->view('frames/student_frame',$data);
    }
}
?>
