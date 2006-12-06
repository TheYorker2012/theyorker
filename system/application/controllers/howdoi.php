<?php

class Howdoi extends Controller {

    function index()
    {
        $data = array(
            'content_view' => 'howdoi/howdoi'
        );
        $this->load->view('frames/student_frame',$data);
    }

    function view()
    {
        $data = array(
            'content_view' => 'howdoi/view'
        );
        $this->load->view('frames/student_frame',$data);
    }

}
?>
