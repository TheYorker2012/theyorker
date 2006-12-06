<?php

class Request extends Controller {

    function index()
    {
        $data = array(
            'content_view' => 'request/request'
        );
        $this->load->view('frames/student_frame',$data);
    }

    function upload()
    {
        $data = array(
            'content_view' => 'request/upload'
        );
        $this->load->view('frames/student_frame',$data);
    }

    function crop()
    {
        $data = array(
            'content_view' => 'request/crop'
        );
        $this->load->view('frames/student_frame',$data);
    }

}
?>
