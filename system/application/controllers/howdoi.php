<?php

class Howdoi extends Controller {

    function index()
    {
        if($this->uri->segment(3) == '') {
          $page = 'howdoi/howdoi';
        } else {
          $page = 'hodoi/howdoidata';
        }

        $data = array(
            'content_view' => $page
        );
        $this->load->view('frames/student_frame',$data);
    }

}
?>
