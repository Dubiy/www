<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {
  function __construct() {
    parent::__construct();
    if (admin()) {
      global $data;
    } else {
      // logout();
      redirect('/main/403', 'location');
      exit;
    }
  }

  public function index() {
    redirect('/admin/questions', 'location');      
    exit;
  }




  public function users($order_by = 'last_activity', $direction = 'desc', $page = 1) {
    global $data;
    if ($order_by == 'account') {
      $order_by = 'premium_to,account_type';
    }
    $data['total_pages'] = 0;
    $data['MODULE_TITLE'] = 'ПОЛЬЗОВАТЕЛИ';
    $data['page'] = $page;
    if (isset($_POST['search'])) {
      $data['users'] = $this->User_model->search_users(array('nickname' => $_POST['search'], 'email' => $_POST['search'], 'phone' => $_POST['search'], 'cash' => $_POST['search']));
    } elseif (isset($_POST['nickname'])) {
      if ($_POST['nickname'] != '') {
        $fields['nickname'] = $_POST['nickname'];
      }
      if ($_POST['email'] != '') {
        $fields['email'] = $_POST['email'];
      }
      if ($_POST['phone'] != '') {
        $fields['phone'] = $_POST['phone'];
      }
      if (!isset($fields)) {
        $fields['nickname'] = '';
      }
      $data['users'] = $this->User_model->search_users($fields, $_POST['account_type']);
    } elseif ($order_by == 'get_by_uid')  {
      $data['users'] = $this->User_model->get_user($direction); // в $direction передається uid
    } else {
      $data['users'] = $this->User_model->get_users($order_by, $direction, $page, $data['total_pages']);
    }

    $data['info_total_info_sum'] = $this->User_model->get_info('total+sum');
    $data['info_online'] = $this->User_model->get_info('online');
    $data['info_premium'] = $this->User_model->get_info('premium');


    $data['CONTENT'] = $this->load->view('admin/users', $data, TRUE);
    $this->load->view('admin/template', $data);
  }

  public function user() {
    if (isset($_POST['action'])) {
      switch ($_POST['action']) {
        case 'edit': {
          //print_r($_POST);
          $user = $this->User_model->get_user($_POST['user_id']);
          if (isset($user) && is_array($user) && count($user)) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('email', 'Email', 'valid_email|callback__email_unique_check_except_this[' . $_POST['user_id'] . ']|required');
            $this->form_validation->set_rules('nickname', 'Nickname');
            $this->form_validation->set_rules('phone', 'Phone');
            if ($this->form_validation->run()) {
              if (isset($_POST['account_type'])) {
                if ($_POST['account_type'] == '1') {
                  $record['premium_to'] = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' -1 day'));
                  $record['account_type'] = '1';
                } elseif ($_POST['account_type'] == '2') {
                  $record['account_type'] = '1';
                  $record['premium_to'] = date('Y-m-d H:i:s', strtotime($_POST['premium_to']));
                }
              }
              $record['email'] = $_POST['email'];
              $record['phone'] = $_POST['phone'];
              $record['cash'] = $_POST['cash'];
              $record['nickname'] = $_POST['nickname'];
              $this->User_model->update_user($_POST['user_id'], $record);
            } else {
              echo strip_tags(validation_errors());
            }
          } else {
            echo 'Пользователь не найден!';
          }
        } break;
        case 'delete': {
          if (isset($_POST['user_id'])) {
            $user = $this->User_model->get_user($_POST['user_id']);
            if (isset($user) && is_array($user) && count($user)) {
              if ($user[0]->account_type == 10) {
                echo 'Нельзя удалить администратора';
              } else {
                $this->User_model->delete($_POST['user_id']);
              }
            }
          }
        } break;
        case 'ban': {
          if (isset($_POST['user_id'])) {
            $user = $this->User_model->get_user($_POST['user_id']);
            if (isset($user) && is_array($user) && count($user)) {
              switch ($_POST['ban']) {
                case 'ok': {
                  $this->User_model->update_user($_POST['user_id'], array('account_type' => '1', 'banned_to' => 0, 'banned_no_questions' => 0));
                  send_email($user[0]->email, $this->config->item('email_subject_unban'), '', 'ban_user_no_ban', (array)$user[0]);
                } break;
                case 'no_questions': {
                  $this->User_model->update_user($_POST['user_id'], array('account_type' => '1', 'banned_to' => 0, 'banned_no_questions' => 1));
                  send_email($user[0]->email, $this->config->item('email_subject_ban_no_questions'), '', 'ban_user_no_questions', (array)$user[0]);
                } break;
                case 'ban1hr': {
                  $this->User_model->update_user($_POST['user_id'], array('account_type' => '-1', 'banned_to' => date('Y-m-d H:i:s', strtotime("+1 Hour", strtotime(date('Y-m-d H:i:s')))), 'session_id' => 'Бугага', 'banned_no_questions' => 0));
                  send_email($user[0]->email, $this->config->item('email_subject_ban_1hr'), '', 'ban_user_1hr', (array)$user[0]);
                } break;
                case 'ban': {
                  $this->User_model->update_user($_POST['user_id'], array('account_type' => '-1', 'banned_to' => date('Y-m-d H:i:s', strtotime("+10 Years", strtotime(date('Y-m-d H:i:s')))), 'session_id' => 'Бугага', 'banned_no_questions' => 0));
                  send_email($user[0]->email, $this->config->item('email_subject_ban_long'), '', 'ban_user_long', (array)$user[0]);
                } break;
              }
            }
          }
        } break;
        case 'add_premium': {
          //YOU ARE HERE
          if (isset($_POST['days'])) {
            if ($_POST['star'] != '-1') {
              $filter = array('star' => $_POST['star']);
            } else {
              $filter = array();
            }
            

            if ((int)$_POST['days'] > 0) {
              $premium_users = $this->User_model->get_premium_users($filter);
              if (is_array($premium_users) && count($premium_users)) {
                foreach ($premium_users as $premium_user) {
                  send_email_queue($premium_user->email, 'Продление премиум аккаунта', '', 'alert_premium_add', array_merge((array)$premium_user, $_POST));
                  $this->db->update('users', array('premium_to' => date('Y-m-d H:i:s', strtotime($premium_user->premium_to . ' +' . (int)$_POST['days'] . ' days'))), array('user_id' => $premium_user->user_id));
                }
              }

              $this->load->model('Options_model', '', TRUE);
              $this->Options_model->set('add_premium', date('Y-m-d H:i:s') . ' продлено на ' . ((int)$_POST['days']) . ' дней, для ' . count($premium_users) . ' пользователей');

              redirect('/admin/user/?action=add_premium&sysmsg=' . urlencode('Премиум продлен на ' . $_POST['days'] . ' дней, для ' . count($premium_users) . ' пользователей. Письма отправлены'), 'location');
            } else {
              redirect('/admin/user/?action=add_premium&sysmsg=' . urlencode('Неверно указано количество дней'), 'location');  
            }

            
          }
        } break;
        case 'toggle_star': {
          if ($this->User_model->toggle_star($_POST['user_id'])) {
            echo 'star';
          } else {
            echo 'normal';
          }
        } break;
        case 'toggle_banned_chat': {
          if ($this->User_model->toggle_banned_chat($_POST['user_id'])) {
            echo 'banned_chat';
          } else {
            echo 'normal';
          }
        } break;
        case 'reset_password': {
          if (isset($_POST['user_id'])) {
            $user = $this->User_model->get_user($_POST['user_id']);
            if (is_array($user) && count($user)) {
              $password = $this->User_model->reset_password($_POST['user_id']);
              send_email($user[0]->email, $this->config->item('email_subject_change_password'), '', 'change_password', array('password' => $password));
              echo 'Пароль сброшен. Пользователю отправлено письмо с новым паролем: ' . $password;
            }
          }
        } break;
        case 'chatadmin': {
          if (isset($_POST['user_id'])) {
            $user = $this->User_model->get_user($_POST['user_id']);
            if (is_array($user) && count($user)) {
              if ($user[0]->account_type == 5 || $user[0]->account_type == 1) {
                $chatadmin = 1;
                if ($_POST['chatadmin'] == 'false') {
                  $chatadmin = 5;
                  echo 'chatadmin';
                }
                $this->User_model->update_user($_POST['user_id'], array('account_type' => $chatadmin));
              } else {
                echo 'Текущая учетная запись имеет больше привилегий';
              }
            }
          }
        } break;
        case 'approve_teamspeak_unique_id': {
          $user = $this->User_model->get_user($_POST['user_id']);



          $teamspeak_db['hostname'] = 'localhost';
          $teamspeak_db['username'] = $this->config->item('teamspeak_db_username');
          $teamspeak_db['password'] = $this->config->item('teamspeak_db_password');
          $teamspeak_db['database'] = $this->config->item('teamspeak_db_database');
          $teamspeak_db['dbdriver'] = 'mysql';
          $teamspeak_db['dbprefix'] = '';
          $teamspeak_db['pconnect'] = TRUE;
          $teamspeak_db['db_debug'] = TRUE;
          $teamspeak_db['cache_on'] = FALSE;
          $teamspeak_db['cachedir'] = '';
          $teamspeak_db['char_set'] = 'utf8';
          $teamspeak_db['dbcollat'] = 'utf8_general_ci';
          $teamspeak_db['swap_pre'] = '';
          $teamspeak_db['autoinit'] = TRUE;
          $teamspeak_db['stricton'] = FALSE;
          $teamspeak_db = $this->load->database($teamspeak_db, TRUE);

          $client = $teamspeak_db->select('*')->from('clients')->where('client_unique_id', $_POST['unique_id'])->get()->result();
          if (is_array($client) && count($client)) {
            //
            $client_first_nickname = explode('#', $client[0]->client_first_nickname);
            if ($client_first_nickname[0] == 'APPROVEDOKAGA') {
              echo 'Unique_ID already approved';
            } else {
              $teamspeak_db->update('clients', array('client_first_nickname' => 'APPROVEDOKAGA#' . $_POST['user_id']), array('client_unique_id' => $_POST['unique_id']));
              //добавляю пользователя в группу PremiumUsers
              $this->load->helper('ts3admin');
              $ts3_ip = $this->config->item('teamspeak_ip');
              $ts3_queryport = $this->config->item('teamspeak_queryport');
              $ts3_user = $this->config->item('teamspeak_user');
              $ts3_pass = $this->config->item('teamspeak_pass');
              $ts3_port = $this->config->item('teamspeak_port');
              $tsAdmin = new ts3admin($ts3_ip, $ts3_queryport);
              if ($tsAdmin->getElement('success', $tsAdmin->connect())) {
                $tsAdmin->login($ts3_user, $ts3_pass);
                $tsAdmin->selectServer($ts3_port);
                $tsAdmin->serverGroupAddClient('10', $client[0]->client_id);
              }
              echo 'ok';
            }
          } else {
            $fields = array(
              'server_id' => '1',
              'client_unique_id' => $_POST['unique_id'],
              'client_nickname' => $user[0]->nickname,
              'client_totalconnections' => '0',
              'client_month_upload' => '0',
              'client_month_download' => '0',
              'client_total_upload' => '0',
              'client_total_download' => '0'
            );
            $teamspeak_db->insert('clients', $fields);
            $teamspeak_db->update('clients', array('client_first_nickname' => 'APPROVEDOKAGA#' . $_POST['user_id']), array('client_unique_id' => $_POST['unique_id']));
            $client_id = $this->db->insert_id();

            $teamspeak_db->insert('client_properties', array('server_id' => '1', 'id' => $client_id, 'ident' => 'client_created', 'value' => strtotime(date('Y-m-d H:i:s'))));
            $teamspeak_db->insert('client_properties', array('server_id' => '1', 'id' => $client_id, 'ident' => 'client_flag_avatar', 'value' => ''));
            $teamspeak_db->insert('client_properties', array('server_id' => '1', 'id' => $client_id, 'ident' => 'client_description', 'value' => ''));
            $teamspeak_db->insert('client_properties', array('server_id' => '1', 'id' => $client_id, 'ident' => 'client_created', 'value' => '0'));

            $this->load->helper('ts3admin');
            $ts3_ip = $this->config->item('teamspeak_ip');
            $ts3_queryport = $this->config->item('teamspeak_queryport');
            $ts3_user = $this->config->item('teamspeak_user');
            $ts3_pass = $this->config->item('teamspeak_pass');
            $ts3_port = $this->config->item('teamspeak_port');
            $tsAdmin = new ts3admin($ts3_ip, $ts3_queryport);
            if ($tsAdmin->getElement('success', $tsAdmin->connect())) {
              $tsAdmin->login($ts3_user, $ts3_pass);
              $tsAdmin->selectServer($ts3_port);
              $tsAdmin->serverGroupAddClient('10', $client_id);
            }

            echo 'Unique_ID added and approved';
          }
          $this->db->close();  

        } break;
      }
    } elseif (isset($_GET['action'])) {
      global $data;
      $data['MODULE_TITLE'] = 'ПОЛЬЗОВАТЕЛИ';
      switch ($_GET['action']) {
        case 'add_premium': {
          $this->load->model('Options_model', '', TRUE);
          $data['add_premium_info'] = $this->Options_model->get('add_premium');
          $data['CONTENT'] = $this->load->view('admin/users_add_premium', $data, TRUE);
        } break;
        default: {
          $data['CONTENT'] = 'unknown action';
        }
        break;
      }
      $this->load->view('admin/template', $data);
    }
  }

  public function questions($action = '') {
    global $data;
    $data['MODULE_TITLE'] = 'ВОПРОСЫ';
    $data['questions'] = $this->Question_model->get_questions($action);
    $data['CONTENT'] = $this->load->view('admin/questions', $data, TRUE);
    $this->load->view('admin/template', $data);
  }

  public function question() {
    if (isset($_POST['action'])) {
      switch ($_POST['action']) {
        case 'delete': {
          if (isset($_POST['question_id'])) {
            $this->Question_model->delete_question($_POST['question_id']);
            echo $this->Question_model->unanswered_count();
            $this->Log_model->add('question', array('action' => 'delete', 'question_id' => $_POST['question_id']));
          }
        } break;
        case 'answer': {
          if (isset($_POST['question_id']) && isset($_POST['answer'])) {
            //var_dump($_POST);
            $this->Question_model->set_answer($_POST['question_id'], $_POST['answer'], $_POST['private']);
            echo $this->Question_model->unanswered_count();

            $question = $this->Question_model->get_question($_POST['question_id']);
            if (is_array($question) && count($question)) {
              if ($_POST['private']) {
                $this->Log_model->add('question', array('action' => 'answer_private', 'question_id' => $_POST['question_id'], 'answer_datetime' => date('Y-m-d H:i:s'), 'answer' => $_POST['answer'], 'text' => $question[0]->text, 'user_id' => $question[0]->user_id), 1);
              } else {
                $this->Log_model->add('question', array('action' => 'answer', 'question_id' => $_POST['question_id'], 'answer_datetime' => date('Y-m-d H:i:s'), 'answer' => $_POST['answer'], 'text' => $question[0]->text, 'user_id' => $question[0]->user_id), 1);
                notify_users_email_and_sms('notify_question_all_reply', 'MyTrade Reply: ' . $question[0]->text . ': ' . $_POST['answer'], (array)$question[0]);
              }

              //notify author by SMS & email
              $user = $this->User_model->get_user($question[0]->user_id);
              if (is_array($user) && count($user)) {
                $notify = notify_decode($user[0]->notify_question_reply);
                if ($notify['sms']) {
                  send_sms($user[0]->phone, 'MyTrade Reply: ' . $question[0]->text . ': ' . $_POST['answer']);
                }
                if ($notify['mail']) {
                  $tmpl_array = array_merge((array)$user[0], (array)$question[0]);
                  send_email_queue($user[0]->email, $this->config->item('email_subject_' . 'notify_question_reply'), 'MyTrade Reply: ' . $question[0]->text . ': ' . $_POST['answer'], 'notify_question_reply', $tmpl_array);
                }
              }
            }
          }
        } break;
        case 'delete_no_answer': {
          echo $this->Question_model->delete_no_answer();
          $this->Log_model->add('question', array('action' => 'delete_no_answer'));
        } break;
        case 'delete_answered_questions_today_yesterday': {
          $this->Question_model->delete_answered_questions_today_yesterday();
          $this->Log_model->add('question', array('action' => 'delete_answered_questions_today_yesterday'));
        } break;
      }
    }
  }

  public function pages($action = '', $page_id = '') {
    global $data;
    $this->load->model('Page_model', '', TRUE);
    $data['MODULE_TITLE'] = 'СТРАНИЦЫ';
    if ($action == '') {
      $data['pages'] = $this->Page_model->get_pages();
      $data['CONTENT'] = $this->load->view('admin/pages', $data, TRUE);
    } elseif ($action == 'edit' && $page_id != '') {
      $data['page'] = $this->Page_model->get_page($page_id);
      $data['CONTENT'] = $this->load->view('admin/page', $data, TRUE);
    } elseif ($action = 'add') {
      $data['CONTENT'] = $this->load->view('admin/page', $data, TRUE);
    }
    $this->load->view('admin/template', $data);
  }

  public function page() {
    global $data;
    $this->load->model('Page_model', '', TRUE);
    if (isset($_POST['action'])) {
      switch ($_POST['action']) {
        case 'edit': {
          if (isset($_POST['page_id'])) {
            $page = $this->Page_model->get_page($_POST['page_id']);
            if (isset($page) && is_array($page) && count($page)) {
              $this->Page_model->update_page($_POST['page_id'], array('alias' => $_POST['alias'], 'title' => $_POST['title'], 'text' => $_POST['text']));
            } else {
              echo 'Страница не найдена';
            }
          } else{
            echo 'Страница не найдена';
          }
        } break;
        case 'add': {
          $this->Page_model->add_page(array('alias' => $_POST['alias'], 'title' => $_POST['title'], 'text' => $_POST['text']));
        } break;
        case 'delete': {
          $this->Page_model->delete_page($_POST['page_id']);
        } break;
      }
    }
  }



  function _email_unique_check_except_this($str, $user_id) {
    $data['user'] = $this->User_model->get_user_by_email($str);
    if (count($data['user']) == 0) {
      return TRUE;
    } elseif ($data['user'][0]->user_id != $user_id) {
      $this->form_validation->set_message('_email_unique_check', 'Пользователь с таким адресом уже существует');
      return FALSE;
    } else {
      return TRUE;
    }
  }

}