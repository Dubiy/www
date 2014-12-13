<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {



  public function index() {
    if ( ! logged_in()) {
      redirect('/user/login/', 'location');
      exit;
    }
  
    $data = '';
    
      $this->load->view('block/site_head', $data);
      $this->load->view('block/header', $data);
      $this->load->view('content', $data);
      $this->load->view('block/site_foot', $data);
 
  }




}
