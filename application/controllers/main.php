<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {
function __construct() {
    parent::__construct();
    if ( ! logged_in()) {
      redirect('/user/login/', 'location');
      exit;
    }
  }


  public function index($filter = 'all', $category = 'all', $age_start = 0, $age_stop = 0) {


    $data = '';
    $data['filter'] = $filter;
    $data['category'] = $category;
    $data['age_start'] = $age_start;
    $data['age_stop'] = $age_stop;
  
    // echo $filter .'/'. $category . '/' . $age_start .'/'. $age_stop;
    $data['questions'] = $this->Question_site_model->get_questions($filter, $category, $age_start, $age_stop);
    
    $this->load->view('block/site_head', $data);
    $this->load->view('block/header', $data);
    $this->load->view('questions', $data);
    $this->load->view('block/site_foot', $data);
  }

  public function question($question_id = 0) {
    $this->load->model('Answer_model', '', TRUE);
    $data['question'] = $this->Question_site_model->get_question($question_id);
    if (isset($data['question']) && is_array($data['question']) && count($data['question'])) {
      if (isset($_POST['answer'])) {
        $this->Answer_model->add_answer($question_id, $_POST['answer']);
      }

      $data['question'] = $data['question'][0];
    }
    $data['answers'] = $this->Answer_model->get_answers($question_id);
    // $data['answers'] = array();

    $this->load->view('block/site_head', $data);
    $this->load->view('block/header', $data);
    $this->load->view('question', $data);
    $this->load->view('block/site_foot', $data);
  }

  public function add_question() {
    $data = array();



    $this->load->library('form_validation');
    $this->form_validation->set_rules('question', 'Вопрос', 'required');
    $this->form_validation->set_rules('type', 'Тип вопроса', 'required|is_natural');
    if ($this->form_validation->run()) {
      $question_id = $this->Question_site_model->add_question($_POST);
      if ($question_id) {
        redirect('/show/' . $question_id, 'location');
        exit;
      }
    }


    $this->load->view('block/site_head', $data);
    $this->load->view('block/header', $data);
    $this->load->view('add_question', $data);
    $this->load->view('block/site_foot', $data);  
  }


}
