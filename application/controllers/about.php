<?php
class About extends CI_Controller {
  public function index() {
    $data = array();
    $this->load->view('block/site_head', $data);
    $this->load->view('block/header', $data);
    $this->load->view('about', $data);
    $this->load->view('block/site_foot', $data);
  }
}
?>
