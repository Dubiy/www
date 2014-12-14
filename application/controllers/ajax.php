<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Ajax extends CI_Controller {

    function __construct() {
        parent::__construct();
    }

    public function vote() {
        if (isset($_POST['entity'])) {
            $this->load->model('Like_model', '', TRUE);    
            $rating = $this->Like_model->like($_POST['entity'], $_POST['entity_id'], $_POST['action']);
            if ($rating) {
                echo rating($rating);
                return;
            }
        }
        echo 'false';
        return;
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */