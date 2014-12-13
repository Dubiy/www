<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {
  public function player_test() {
    $this->load->view('player_test');
  }

  public function scroll_test() {
    $this->load->view('scroll_test'); 
  }

  public function index() {
    // uprofiler(__FILE__, __LINE__, 'start');
    if ( ! logged_in()) {
      redirect('/user/login/', 'location');
      exit;
    }
    // uprofiler(__FILE__, __LINE__);

    $data = '';
    
      $this->load->view('block/site_head', $data);
      $this->load->view('block/header', $data);
      $this->load->view('content', $data);
      $this->load->view('block/site_foot', $data);
 
  }

  public function archive() {
    if ( ! logged_in()) {
      redirect('/user/login/', 'location');
      exit;
    }
    $this->load->library('user_agent');
    $data['is_mobile'] = $this->agent->is_mobile();
    $this->load->model('Archive_model', '', TRUE);
    $data['videos'] = $this->Archive_model->get_videos();
    if ($this->agent->is_mobile() && ( ! isset($_SESSION['no_mobile'])) || isset($_GET['mobile'])) {
      $this->load->view('mobile/block/site_head', $data);
      $this->load->view('mobile/archive', $data);
      $this->load->view('mobile/block/site_foot', $data);
    } else {
      redirect('/', 'location');
    }
  }

  public function question() {
    if ( ! logged_in()) {
      redirect('/user/login/', 'location');
      exit;
    }

    if (isset($_POST['text']) && $_POST['text'] != '') {
      $user = $this->User_model->get_user(logged_in());
      if (is_array($user) && count($user) && $user[0]->banned_no_questions == 0) {
        if ($user[0]->premium_to > date('Y-m-d H:i:s')) {
          $this->load->model('Log_model', '', TRUE);
          $this->load->model('Question_model', '', TRUE);
          $question_id = $this->Question_model->add_question($_POST['text']);
          $this->Log_model->add('question', array('action' => 'add', 'user_id' => logged_in(), 'question_id' => $question_id, 'text' => $_POST['text']));
        } else {
          //Only premium users allowed to send questions
        }
      } else {
        //You are banned from asking questions
      }
    }

    $this->load->library('user_agent');
    $data['is_mobile'] = $this->agent->is_mobile();
    $this->load->model('Question_model', '', TRUE);
    $data['questions'] = $this->Question_model->get_user_questions(logged_in());
    if ($this->agent->is_mobile() && ( ! isset($_SESSION['no_mobile'])) || isset($_GET['mobile'])) {
      $this->load->view('mobile/block/site_head', $data);
      $this->load->view('mobile/question', $data);
      $this->load->view('mobile/block/site_foot', $data);
    } else {
      redirect('/', 'location');
    }
  }

  public function table($ticker_id = 0) {
    if ( ! logged_in()) {
      redirect('/user/login/', 'location');
      exit;
    }
    $this->load->library('user_agent');
    $data['is_mobile'] = $this->agent->is_mobile();
    $this->load->model('Table_model', '', TRUE);

    if ($ticker_id != 0) {
      $data['ticker'] = $this->Table_model->get_ticker($ticker_id);
      if (is_array($data['ticker']) && count($data['ticker'])) {
        $data['recomendation_short'] = $this->Table_model->last_recomendations_frontend($ticker_id, 0, 1);
        $data['recomendations_long'] = $this->Table_model->last_recomendations_frontend($ticker_id, 1, 1);
        if ($this->agent->is_mobile() && ( ! isset($_SESSION['no_mobile'])) || isset($_GET['mobile'])) {
          $this->load->view('mobile/block/site_head', $data);
          $this->load->view('mobile/table_ticker', $data);
          $this->load->view('mobile/block/site_foot', $data);
        } else {
          redirect('/', 'location');
        }
      } else {
        echo 'Ticker not found';
      }

    } else {
      $data['tickers'] = $this->Table_model->get_tickers();
      $data['RECOMENDATIONS'] = $this->config->item('recomendations');
      if ($this->agent->is_mobile() && ( ! isset($_SESSION['no_mobile'])) || isset($_GET['mobile'])) {
        $this->load->view('mobile/block/site_head', $data);
        $this->load->view('mobile/table', $data);
        $this->load->view('mobile/block/site_foot', $data);
      } else {
        redirect('/', 'location');
      }
    }
  }

  public function notifications() {
    if ( ! logged_in()) {
      redirect('/user/login/', 'location');
      exit;
    }
    $this->load->library('user_agent');
    $data['is_mobile'] = $this->agent->is_mobile();
    $this->load->model('Log_model', '', TRUE);
    $data['RECOMENDATIONS'] = $this->config->item('recomendations');
    $data['notifications'] = $this->Log_model->get_last_notifications();
    if ($this->agent->is_mobile() && ( ! isset($_SESSION['no_mobile'])) || isset($_GET['mobile'])) {
      $this->load->view('mobile/block/site_head', $data);
      $this->load->view('mobile/notifications', $data);
      $this->load->view('mobile/block/site_foot', $data);
    } else {
      redirect('/', 'location');
    }
  }

  public function page_403() {
    $data = array();
    $this->load->view('pages/header', $data);
    $this->load->view('pages/403', $data);
    $this->load->view('pages/footer', $data);
  }

  public function blog($post_id = -1000, $direction = '') {
    if ( ! logged_in()) {
      redirect('/user/login/', 'location');
      exit;
    }
    $this->load->library('user_agent');
    $data['is_mobile'] = $this->agent->is_mobile();
    $this->load->model('Blog_model', '', TRUE);
    if ($post_id != -1000) {
      $data['post_id_in_url'] = $post_id;
      $data['post'] = $this->Blog_model->get_post($post_id, $direction);
      if ($this->agent->is_mobile() && ( ! isset($_SESSION['no_mobile'])) || isset($_GET['mobile'])) {
        $this->load->view('mobile/block/site_head', $data);
        $this->load->view('mobile/post', $data);
        $this->load->view('mobile/block/site_foot', $data);
      } else {
        redirect('/', 'location');
      }
    } else {
      $data['blogs'] = $this->Blog_model->get_blogs(date("Y-m-d H:i:s"), 0, 20);
      if ($this->agent->is_mobile() && ( ! isset($_SESSION['no_mobile'])) || isset($_GET['mobile'])) {
        $this->load->view('mobile/block/site_head', $data);
        $this->load->view('mobile/blog', $data);
        $this->load->view('mobile/block/site_foot', $data);
      } else {
        redirect('/', 'location');
      }
    }
  }

  public function settings() {
    if ( ! logged_in()) {
      redirect('/user/login/', 'location');
      exit;
    }
    $this->load->library('user_agent');
    $data['is_mobile'] = $this->agent->is_mobile();
    $this->load->model('User_model', '', TRUE);
    $data['user'] = $this->User_model->get_user(logged_in());

    if ($this->agent->is_mobile() && ( ! isset($_SESSION['no_mobile'])) || isset($_GET['mobile'])) {
      $this->load->view('mobile/block/site_head', $data);
      $this->load->view('mobile/settings', $data);
      $this->load->view('mobile/block/site_foot', $data);
    } else {
      redirect('/', 'location');
    }
  }


  public function live() {
    if ( ! logged_in()) {
      redirect('/user/login/', 'location');
      exit;
    }
    $this->load->library('user_agent');
    $data['is_mobile'] = $this->agent->is_mobile();
    $this->load->model('User_model', '', TRUE);
    $data['user'] = $this->User_model->get_user(logged_in());

    if ($this->agent->is_mobile() && ( ! isset($_SESSION['no_mobile'])) || isset($_GET['mobile'])) {
      $this->load->view('mobile/block/site_head', $data);
      $this->load->view('mobile/live', $data);
      $this->load->view('mobile/block/site_foot', $data);
    } else {
      redirect('/', 'location');
    }
  }

}
