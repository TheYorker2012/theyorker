<?php

class Faq extends Controller {

    function index()
    {
        $data = array(
            'content_view' => 'faq/faq'
        );
        $this->load->view('frames/student_frame',$data);
    }

    function howdoi()
    {
        if($this->uri->segment(3) == '') {
          $page = 'faq/howdoi';
        } else {
          $page = 'faq/howdoidata';
        }

        $data = array(
            'content_view' => $page
        );
        $this->load->view('frames/student_frame',$data);
    }

}
?>
