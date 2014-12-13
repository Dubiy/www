<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {
  function __construct() {
    parent::__construct();
    $allowed_actions = array('', 'index', 'rooms', 'room', 'deleted', 'movemsgs');
    if (admin() || chatadmin() && in_array($this->uri->segment(2), $allowed_actions)) {
      global $data;
      $this->load->model('Question_model', '', TRUE);
      $data['UNANSWERED_QUESTIONS_COUNT'] = $this->Question_model->unanswered_count();
      $this->load->model('Log_model', '', TRUE);
    } else {
      // logout();
      redirect('/main/403', 'location');
      exit;
    }
  }

  public function index() {
    if (chatadmin()) {
      redirect('/admin/rooms', 'location');      
    } else {
      //admin
      redirect('/admin/notifications', 'location');
    }
    exit;
  }

  public function uploader() {
    global $data;
    $this->load->helper('chat');

    if (isset($_FILES['photoimg'])) {
      $path = $this->config->item('files_upload_path');
      $name = $_FILES['photoimg']['name'];
      $ext = pathinfo($name, PATHINFO_EXTENSION);
      if ($name != '') {
          $tmp = $_FILES['photoimg']['tmp_name'];
          $filename = md5(date('Y-m-d_H-i-s')) . '.' . $ext;
          if (move_uploaded_file($tmp, $path . $filename)) {
              $data['msg'] = 'Файл загружен: http://'. $_SERVER['HTTP_HOST'] .'/' . $path . $filename;
          } else {
              $data['error'] = 'upload failed :(';
          }
      } else {
          $data['error'] = 'Empty file';
      }
    } 

    $data['MODULE_TITLE'] = 'Загрузчик файлов';
    $data['CONTENT'] = $this->load->view('admin/chat/uploader', $data, TRUE);
    $this->load->view('admin/template', $data);

  }

  public function stylecustomizr($action = '', $id = '') {
    global $data;
    $data['MODULE_TITLE'] = 'Стилизатор';
    $data['CONTENT'] = '';
    $data['CONTENT'] .= $this->load->view('admin/stylecustomizr/bar', $data, TRUE);
    $this->load->model('Customizr_model', '', TRUE);

    if ($action == '') {
      $data['public_theme_ids'] = $this->Customizr_model->get_public_themes();
      $data['CONTENT'] .= $this->load->view('admin/stylecustomizr/list', $data, TRUE);
    } elseif ($action == 'blocks') {
      $data['blocks'] = $this->Customizr_model->get_blocks();
      if ($id != '') {
        $data['add_empty_group'] = 'add_empty_group';
      }
      $data['CONTENT'] .= $this->load->view('admin/stylecustomizr/blocks', $data, TRUE);
    } elseif ($action == 'block') {
      if (isset($_POST['title'])) {
        $this->Customizr_model->save_block($_POST);
        redirect('/admin/stylecustomizr/blocks', 'location');
      }
      $data['block'] = $this->Customizr_model->get_block(base64_decode(urldecode($id)));        
      $data['CONTENT'] .= $this->load->view('admin/stylecustomizr/block', $data, TRUE);
    } elseif ($action == 'change_group') {
      if (isset($_POST['path'], $_POST['group'])) {
        $this->Customizr_model->move_block($_POST['path'], $_POST['group']);
      }
    } elseif ($action == 'delete_block') {
      if (isset($_POST['path'])) {
        $this->Customizr_model->delete_block($_POST['path']);
      }
    }








    $this->load->view('admin/template', $data);
  }

  public function translate($action = 'no_translate', $id = 0, $mode = '') {
    global $data;
    $data['MODULE_TITLE'] = 'Переводы';
    $data['CONTENT'] = '';
    $data['CONTENT'] .= $this->load->view('admin/translate/translate_bar', $data, TRUE);
    $this->load->model('Translate_model', '', TRUE);

    switch ($action) {
      case 'search': {
        if (isset($_POST['search'])) {
          $data['results'] = $this->Translate_model->search($_POST['search']);
          $data['CONTENT'] .= $this->load->view('admin/translate/results', $data, TRUE);        
        } else {
          redirect('/admin/translate', 'location');
        }
      } break;
      case 'no_translate': {
        $data['no_translate'] = $this->Translate_model->get_no_translate();
        $data['CONTENT'] .= $this->load->view('admin/translate/no_translate', $data, TRUE);
      } break;
      case 'translate': {
        $data['languages'] = $this->config->item('languages');
        if ($mode == 'update') {
          $data['to_translate'] = $this->Translate_model->get_translation_by_id($id);
        } else {
          $data['to_translate'] = $this->Translate_model->get_no_translate($id);
        }

        if (isset($data['to_translate']) && is_array($data['to_translate']) && count($data['to_translate'])) {
          $data['translations'] = $this->Translate_model->get_translations($data['to_translate'][0]->line);
          if (isset($data['translations']) && is_array($data['translations']) && count($data['translations'])) {
            foreach ($data['translations'] as $translation) {
              $data['translations_tmp'][$translation->lang] = $translation;
            }
          }

          if (isset($_POST['action']) && $_POST['action'] == 'save_translate') {
            foreach ($data['languages'] as $lang) {
              if (isset($_POST['transl_' . $lang]) && $_POST['transl_' . $lang] != '') {
                if (isset($data['translations_tmp'][$lang])) {
                  //update
                  $this->Translate_model->update_translate($data['to_translate'][0]->line, $lang, $_POST['transl_' . $lang]);
                } else {
                  //insert
                  $this->Translate_model->add_translate($data['to_translate'][0]->line, $lang, $_POST['transl_' . $lang]);
                }
              }
            }
            $this->Translate_model->delete_fail($data['to_translate'][0]->line);
            $this->Translate_model->regenerate_translations();

            redirect('/admin/translate/no_translate', 'location');
          } else {
            $data['CONTENT'] .= $this->load->view('admin/translate/translate', $data, TRUE);
          }
        } else {
          $data['CONTENT'] .= 'Не найдено';  
        }
      } break;
      default: {
        $data['CONTENT'] .= 'unknown action';
      } break;
    }

    
    $this->load->view('admin/template', $data);
  }

  public function payments() {
    global $data;
    $data['MODULE_TITLE'] = 'Платежи';
    $this->load->model('Pay_model', '', TRUE);

    if (isset($_GET['skip'])) {
      $skip = (int) $_GET['skip'];
      if ($skip < 0) {
        $skip = 0;
      }
    } else {
      $skip = 0;
    }

    $data['payments'] = $this->Pay_model->get_payments($skip);
    $data['CONTENT'] = $this->load->view('admin/payments', $data, TRUE);
    $this->load->view('admin/template', $data);
  }

  public function movemsgs($room_id = 0) {
    global $data;
    $data['MODULE_TITLE'] = 'Перенос сообщений';
    $this->load->model('Room_model', '', TRUE);
    if (isset($_POST['room_id'])) {
      $this->Room_model->move_room($room_id, $_POST['room_id']); 
      $data['msg'] = 'Сообщения перенесены';
      $data['CONTENT'] = '';
    } else {
      $data['rooms'] = $this->Room_model->get_rooms('', 0, 1000);
      $data['room'] = $this->Room_model->get_room($room_id);
      $data['CONTENT'] = $this->load->view('admin/chat/move', $data, TRUE);
    }
    $this->load->view('admin/template', $data);
  }

  public function memes() {
    global $data;
    $this->load->helper('chat');
    $this->load->model('Meme_model', '', TRUE);

    if (isset($_POST['delete'])) {
      $this->Meme_model->delete($_POST['meme_id']);
    }


    if (isset($_POST['title'])) {
      if ($_POST['title'] != '') {
        $path = $this->config->item('memes_upload_path');
        $path_thumbs = $this->config->item('memes_upload_path_thumbs');
        $valid_formats = array("jpg", "png", "jpeg");
        $name = $_FILES['photoimg']['name'];
        $filesize = $_FILES['photoimg']['size'];
        $ext = pathinfo($name, PATHINFO_EXTENSION);
        if ($name != '') {
            if (in_array(strtolower($ext), $valid_formats)) {
                $tmp = $_FILES['photoimg']['tmp_name'];
                $filename = md5(date('Y-m-d_H-i-s')) . '.' . $ext;
                if (move_uploaded_file($tmp, $path . $filename)) {
                    $this->Meme_model->add($_POST['title'], $filename);
                    create_thumb($path, $filename, $path_thumbs, $filename, 49, 49);
                    $data['msg'] = 'Мем добавлен';
                } else {
                    $data['error'] = 'image upload failed :(';
                }
            } else {
                $data['error'] = 'Wrong image format';
            }
        } else {
            $data['error'] = 'Empty image';
        }
      } else {
        $data['error'] = 'Пустое название';
      }
    } 

    $data['MODULE_TITLE'] = 'Управление мемами';
    $data['memes'] = $this->Meme_model->get_memes();
    $data['CONTENT'] = $this->load->view('admin/chat/memes', $data, TRUE);
    $this->load->view('admin/template', $data);
       
  }

  public function deleted() {
    global $data;
    $this->load->helper('chat');
    $this->load->model('Post_model', '', TRUE);

    if (isset($_POST['action']) && isset($_POST['deleted_post_id'])) {
      $status = $this->Post_model->confirm_delete($_POST['deleted_post_id'], $_POST['action']);
      if ($status !== TRUE) {
        echo $status;
      }
    } else {
      $data['MODULE_TITLE'] = 'Лента удаленных сообщений';
      $data['deleted'] = $this->Post_model->deleted_posts();
      $data['CONTENT'] = $this->load->view('admin/chat/deleted', $data, TRUE);
      $this->load->view('admin/template', $data);
    }
  }

  public function rooms($filter = '') {
    global $data;
    $this->load->helper('chat');
    $this->load->model('Room_model', '', TRUE);
    $data['MODULE_TITLE'] = 'Список комнат';

    if (isset($_GET['skip'])) {
      $skip = (int) $_GET['skip'];
      if ($skip < 0) {
        $skip = 0;
      }
    } else {
      $skip = 0;
    }

    if (isset($_GET['search']) && $_GET['search'] != '') {
      $data['rooms'] = $this->Room_model->search($_GET['search'], '', '', 'rooms');
    } else {
      $data['rooms'] = $this->Room_model->get_rooms('0', $skip, $this->config->item('rooms_per_page'), (($filter == 'deleted') ? (array('deleted' => 1)) : (''))  );
    }
    $data['CONTENT'] = $this->load->view('admin/chat/rooms', $data, TRUE);
    $this->load->view('admin/template', $data);
  }

  public function room($room_id = '', $action = '') {
    global $data;
    $this->load->helper('chat');
    $this->load->model('Room_model', '', TRUE);
    $data['room'] = $this->Room_model->get_room($room_id);
    switch ($action) {
      case 'fix': {
        if (isset($_POST['status']) && $_POST['status'] == 'true')  {
          $fixed = 0;
        } else {
          $fixed = 1;
        }
        $fields = array('fixed' => $fixed);
        $this->Room_model->update($room_id, $fields);
      } break;
      case 'delete': {
        if (isset($_POST['confirm'])) {
          $this->Room_model->delete($room_id);
        } else {
          echo 'WTF, dude?!';
        }
      } break;
      default: {
        //no action, just show room

        if (isset($_POST['title']) && strip_tags($_POST['title']) != '') {
          $fields = array('title' => $_POST['title']);
          $this->Room_model->update($room_id, $fields);
          $data['room'] = $this->Room_model->get_room($room_id);
        }

        $data['MODULE_TITLE'] = 'Редактирование комнаты';
        $data['moders'] = $this->Room_model->get_moderators($room_id);
        $data['CONTENT'] = $this->load->view('admin/chat/room', $data, TRUE);
        $this->load->view('admin/template', $data);
      } break;
    }
  }

  public function send_sms() {
    if (isset($_POST['number'])) {
      send_sms($_POST['number'], $_POST['message']);
    }
  }

  public function login_as() {
    if (isset($_POST['user_id'])) {
      //login as
      $_SESSION['undercover'] = serialize($_SESSION);
      $user = $this->User_model->get_user($_POST['user_id']);
      login($user[0]);
    } else {
      echo 'wrong id';
    }
  }

  public function make_unactivated_people_happy() {
    //return TRUE;
    $users = $this->db->select('*')->from('users')->where('account_type', '0')->get()->result();
    if (is_array($users) && count($users)) {
      foreach ($users as $user) {
        //update user + mark
        $fields = array('description' => 'Not activated users by make_unactivated_people_happy()',
                        'account_type' => '1',
                        'premium_to' => date('Y-m-d H:i:s', strtotime('+14 days')));
        $this->db->update('users', $fields, array('user_id' => $user->user_id));

        //update password
        $password = $this->User_model->reset_password($user->user_id);

        //send email
        $text = '<h2>Здравствуйте' . (($user->nickname) ? (', ' . $user->nickname) : ('')) . '</h2>
                 Ваша учетная запись активированна.<br />
                 Ваш пароль: <b>' . $password . '</b><br />
                 Приносим извинения за неудобства и дарим Вам 2-х недельный премиум аккаунт. Заходите, будет интересно!
                 <br />
                 Если у вас возникли вопросы по использованию сайта, вы можете обратиться к <a href="http://' . $_SERVER['HTTP_HOST'] . '/page/userguide">инструкции пользователя</a>.<br />
                 <br />
                 С уважением,<br />
                 администрация <a href="http://' . $_SERVER['HTTP_HOST'] . '/">' . strtoupper($_SERVER['HTTP_HOST']) . '</a>';

        send_email_queue($user->email, 'Активация учетной записи на сайте My-trade.pro', $text);
      }
    }
    echo 'Обработано ' . count($users) . ' учетных записей';

    /*
    Три Закони користування бородою
      1. Борода не може заподіяти шкоду людині, або своєю бездіяльністю дозволити, щоб людині була заподіяна шкода;
      2. Борода повинна підкорятися наказам людини, за винятком тих, котрі суперечать першому Закону;
      3. Борода повинна захищати себе, якщо тільки її дії не суперечать першому і другому Закону.
    */
  }

  public function notifications() {
    global $data;
    $this->load->model('Notification_model', '', TRUE);
    $data['MODULE_TITLE'] = 'ОПОВЕЩЕНИЯ';
    $data['notifications'] = $this->Notification_model->get_notifications();
    $data['main_page_alert'] = $this->Log_model->get_last_alert();
    $data['CONTENT'] = $this->load->view('admin/notifications', $data, TRUE);
    $this->load->view('admin/template', $data);
  }

  public function notification() {
    global $data;
    $this->load->model('Notification_model', '', TRUE);
    if (isset($_POST['action'])) {
      switch ($_POST['action']) {
        case 'add': {
          if (isset($_POST['text'])) {
            echo $notification_id = $this->Notification_model->add($_POST['text']);
            $this->Log_model->add('notification', array('action' => 'add', 'notification_id' => $notification_id, 'text' => $_POST['text']), 1);
          }

          notify_users_email_and_sms('notify_new_trade_note', 'MyTrade: ' . $_POST['text']);

        } break;
        case 'add_alert': {
          if (isset($_POST['text'])) {
            $this->Log_model->add('alert', array('text' => $_POST['text'], 'datetime' => date('Y-m-d H:i:s')), 1);
          }
        } break;
        case 'delete': {
          if (isset($_POST['notification_id'])) {
            $this->Notification_model->delete_notification($_POST['notification_id']);
          }
        } break;
      }
    }
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

  public function table() {
    global $data;
    $this->load->model('Table_model', '', TRUE);
    $data['MODULE_TITLE'] = 'ТАБЛИЦА';
    $data['tickers'] = $this->Table_model->get_tickers_admin();
    //print_r($data['tickers']);
    $data['CONTENT'] = $this->load->view('admin/table', $data, TRUE);
    $this->load->view('admin/template', $data);
  }

  public function table_handler() {
    $this->load->model('Table_model', '', TRUE);
    if (isset($_POST['action'])) {
      switch ($_POST['action']) {
        case 'ticker_image_upload': {
          if (isset($_POST['recomendation_id'])) {
            $path = $this->config->item('recomendations_upload_path');
            $valid_formats = array("jpg", "png", "jpeg");
            $name = $_FILES['photoimg']['name'];
            $size = $_FILES['photoimg']['size'];
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            if ($name != '') {
              if (in_array($ext, $valid_formats)) {
                $tmp = $_FILES['photoimg']['tmp_name'];
                $filename = date('Y-m-d_H-i-s') . '_tid_' . $_POST['ticker_id'] . '.' . $ext;
                if (move_uploaded_file($tmp, $path.$filename)) {
                  $this->Table_model->update_recomendation($_POST['recomendation_id'], array('image' => $filename));
                  echo '<a href="/' . $path . $filename . '" target="_blank">Image</a> <button class="remove_image" recomendation_id="' . $_POST['recomendation_id'] . '">X</button>';
                  $this->Log_model->add('table_photo', array('action' => 'ticker_image_upload', 'recomendation_id' => $_POST['recomendation_id']), 1, $this->config->item('regular_delay_table'));
                } else {
                  echo 'image upload failed :(';
                }            
              } else {
                echo 'Wrong image format';
              }
            } else {
              echo 'empty image';
            } 
          } else {
            echo 'wrong recomendation id';
          }
        } break;
        case 'remove_image': {
          if (isset($_POST['recomendation_id'])) {
            $this->Table_model->remove_image($_POST['recomendation_id']);
            $this->Log_model->add('table_photo', array('action' => 'remove_image', 'recomendation_id' => $_POST['recomendation_id']), 1, $this->config->item('regular_delay_table'));
          }
        } break;
        case 'add_ticker': {
          $ticker = $this->Table_model->get_ticker_by_name($_POST['name']);
          if (isset($ticker) && is_array($ticker) && count($ticker)) {
            echo 'Такой тикер уже существует';
          } else {
            $ticker_id = $this->Table_model->add_ticker($_POST['name'], $_POST['title']);
            $this->Log_model->add('table', array('action' => 'add_ticker', 'ticker_id' => $ticker_id, 'name' => $_POST['name'], 'title' => $_POST['title']), 0, $this->config->item('regular_delay_table'));
          }
        } break;
        case 'add_recomendation': {
          if (isset($_POST['ticker_id'])) {
            $ticker_tmp = $this->Table_model->get_ticker($_POST['ticker_id']);
            $fields = array('ticker_id' => $_POST['ticker_id'],
                            'type' => $_POST['type'],
                            'index' => (($_POST['index'] == '') ? ((float)$this->config->item('recomendation_no_index_constant')) : ($_POST['index'])),
                            'text' => $_POST['text'],
                            'long' => $_POST['long']);
            $ticker = $this->Table_model->add_recomendation($fields);
            $this->Log_model->add('table', array('action' => 'add_recomendation', 'ticker_id' => $_POST['ticker_id'], 'recomendation_id' => $ticker, 'ticker_name' => $ticker_tmp[0]->name, 'datetime' => date('Y-m-d H:i:s'), 'type' => $_POST['type'], 'index' => $_POST['index'], 'text' => $_POST['text'], 'long' => $_POST['long']), 1, $this->config->item('regular_delay_table'));
            $recomendations = $this->config->item('recomendations');
            if (! isset($_POST['silent'])) {
              
              $tmp_array = $_POST;
              $tmp_array['recomendations'] = $recomendations;
              $tmp_array['name'] = $ticker_tmp[0]->name;

              notify_users_email_and_sms('notify_new_trade_idea', $ticker_tmp[0]->name . ': ' . $recomendations[$_POST['type']] . (($_POST['index'] != '') ? ('(' . $_POST['index'] . ').') : ('')) . (($_POST['text']) ? (' "' . $_POST['text'] . '"') : ('')), $tmp_array, $this->config->item('regular_delay_table'));

            }
            
          }
        } break;
        case 'set_prognose': {
          if (isset($_POST['recomendation_id'], $_POST['prognose'])) {
            echo $this->Table_model->set_prognose($_POST['recomendation_id'], $_POST['prognose']);
          } else {
            echo 'missing required fields';
          }
        } break;



        case 'last_recomendations': {
          if (isset($_POST['ticker_id'])) {
            $recomendations = $this->Table_model->last_recomendations($_POST['ticker_id'], $_POST['count']);
            $recomendation_type = $this->config->item('recomendations');
            $array['long'] = $array['short'] = '';
            $prognose_status = $this->config->item('prognose_status');
            $path = $this->config->item('recomendations_upload_path');
            $NULL_val = $this->config->item('recomendation_no_index_constant');
            foreach ($recomendations as $recomendation) {
              $tmp = '<div class="record recomendation_id_' . $recomendation->recomendation_id . '" recomendation_id="' . $recomendation->recomendation_id . '">' .
                        '<div class="time">' . time_since($recomendation->datetime) . '</div>' .
                        '<div class="info_fields">' .
                          '<div class="type">' . $recomendation_type[$recomendation->type] . '</div>' .
                          '<div class="delete"></div>' .
                          '<div class="index">' . (($recomendation->index != $NULL_val) ? ($recomendation->index) : ('-----')) . '</div>' .
                          '<div class="text">' . (($recomendation->text) ? ($recomendation->text) : ('-----')) . '</div>' .
                          '<div class="prognose">Прогноз: ' . $this->_return_select_prognose($recomendation->status, $prognose_status) . '</div>' .
                          '<div class="picture">' .
                          '<form method="POST" enctype="multipart/form-data" action="/admin/table_handler/">' .
                            '<input type="hidden" name="action" value="ticker_image_upload">' .
                            '<input type="hidden" name="recomendation_id" value="' . $recomendation->recomendation_id . '">' .
                            '<input type="hidden" name="ticker_id" value="' . $_POST['ticker_id'] . '">' .
                            '<input type="file" name="photoimg" class="file photoimg ' . (($recomendation->image != '') ? ('hidden') :('')) . '"/></div>' .
                            '<div class="form_preview">' . (($recomendation->image != '') ? ('<a href="/' . $path . $recomendation->image . '" target="_blank">Image</a> <button class="remove_image" recomendation_id="' . $recomendation->recomendation_id . '">X</button>') :('')) . '</div>' .
                          '</form>' .
                        '</div>' .
                      '</div>' .
                      '<div class="clear"></div>';
              if ($recomendation->long) {
                $array['long'] .= $tmp;
              } else {
                $array['short'] .= $tmp;
              }
            }
            $array['long'] .= '<div class="load_all_recomendations" ticker_id="' . $_POST['ticker_id'] . '">ЗАГРУЗИТЬ ВСЮ ИСТОРИЮ</div>';
            echo json_encode($array);
          }
        } break;
        case 'delete_recomendation': {
          if (isset($_POST['recomendation_id'])) {
            $this->Table_model->del_recomendation($_POST['recomendation_id']);
          }
        } break;
        case 'delete_ticker': {
          if (isset($_POST['ticker_id'])) {
            $this->Table_model->del_ticker($_POST['ticker_id']);
            $this->Log_model->add('table', array('action' => 'delete_ticker', 'ticker_id' => $_POST['ticker_id']), 0, $this->config->item('regular_delay_table'));
          }
        } break;
        case 'update_all_tickers': {
          $this->Table_model->update_all_tickers($_POST['type']);
        } break;
      }
    }
  }

  public function archive($action = '', $id = '') {
    global $data;
    $this->load->model('Archive_model', '', TRUE);
    $data['MODULE_TITLE'] = 'АРХИВ И ЗАПИСЬ';
    if ($action == 'edit') {
      if (isset($_POST['title'])) {
        $video = $this->Archive_model->add($_POST['link'], ((isset($_POST['is_premium'])) ? (1) : (0)), $_POST['title'], $id);
      }
      $data['video'] = $this->Archive_model->get_video($id);
    }
    $data['videos'] = $this->Archive_model->get_videos();
    $data['CONTENT'] = $this->load->view('admin/archive', $data, TRUE);
    $this->load->view('admin/template', $data);
  }

  public function archive_handler() {
    global $data;
    $this->load->model('Archive_model', '', TRUE);
    if (isset($_POST['action'])) {
      switch ($_POST['action']) {
        case 'add': {
          if (isset($_POST['link'])) {
            $video = $this->Archive_model->add($_POST['link'], $_POST['is_premium'], $_POST['title']);
            if ($video !== FALSE) {
              echo json_encode($video);
              $video['action'] = 'add';

              $this->Log_model->add('archive', $video, 1, (($_POST['is_premium'] == 1) ? ($this->config->item('regular_delay_archive')) : ('')));

              notify_users_email_and_sms('notify_new_archive_video', 'На сайте MyTrade появилось новое архивное видео: ' . $video['title']);
            } else {
              echo json_encode(array('err' => 'Video not found'));
            }
          }
        } break;
        case 'delete': {
          if (isset($_POST['video_id'])) {
            $this->Archive_model->delete_video($_POST['video_id']);
            $this->Log_model->add('archive', array('action' => 'delete_video', 'video_id' => $_POST['video_id']), 1);
          }
        } break;
        case 'start_broadcast': {
          $this->Log_model->add('live', array('action' => 'add', 'title' => 'Broadcast started'), 1);
          notify_users_email_and_sms('notify_live_broadcasting_start', 'На сайте MyTrade началась видеотрансляция');
          print_r($_POST);
          $this->load->model('Options_model', '', TRUE);
          $this->Options_model->set('broadcast_start_time', $_POST['start_time']);



        } break;
      }
    }
  }

  function mailer() {
    global $data;
    $data['MODULE_TITLE'] = 'РАССЫЛКА';

    if (isset($_POST['text']) && $_POST['text'] != '') {
      $users = $this->User_model->get_user_emails(0, $_POST['filter']);
      if (isset($_POST['subject']) && $_POST['subject']) {
        $subject = $_POST['subject'];
      } else {
        $subject = $this->config->item('email_subject_admin_mailer'); 
      }

      //send_sms($_POST['number'], $_POST['message']);

      if (is_array($users) && count($users)) {
        if (isset($_POST['sms_only'])) {
          $count = 0;
          foreach ($users as $user) {
            if ($user->phone) {
              send_sms($user->phone, $_POST['text']);
              $count ++;
            }
          }
          $data['msg'] = 'Отправлено ' . $count . ' SMS';
        } else {
          foreach ($users as $user) {
            send_email_queue($user->email, $subject, $_POST['text'], 'admin_mailer', (array)$user);
          }
          $data['msg'] = 'Отправлено ' . count($users) . ' писем';
        }
        
      }
    } elseif (isset($_POST['text'])) {
      $data['msg'] = 'Нельзя отправлять пустые письма';
    }

    if (isset($_POST['text_sms']) && $_POST['text_sms'] != '') {
      $user_phones = $this->User_model->get_user_phones();
      if (is_array($user_phones) && count($user_phones)) {
        foreach ($user_phones as $user) {
          send_sms($user->phone, $_POST['text_sms']);
        }
        $data['msg'] = 'Отправлено ' . count($user_phones) . ' сообщений';
      }
    } elseif (isset($_POST['text_sms'])) {
      $data['msg'] = 'Нельзя отправлять пустые сообщения';
    }
    $data['CONTENT'] = $this->load->view('admin/mailer', $data, TRUE);
    $this->load->view('admin/template', $data);
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

  public function sms() {
    global $data;
    $this->load->model('Log_model', '', TRUE);
    $this->load->model('Sms_model', '', TRUE);
    $data['MODULE_TITLE'] = 'SMS БАЛАНС';
    $sms_ballance = file_get_contents('http://sms.ru/my/balance?api_id=' . $this->config->item('sms_api_id'));
    $sms_ballance = explode("\n", $sms_ballance);
    $data['CONTENT'] = '<div class="block_admin">' . 'На вашем счету: ' . $sms_ballance[1] . ' рублей' . '</div>';
    $data['last_smses'] = $this->Sms_model->last_smses(1000);
    $data['CONTENT'] .= $this->load->view('admin/last_smses', $data, true);
    $this->load->view('admin/template', $data);
  }

  public function blog($action = '', $id = '') {
    global $data;
    $this->load->model('Blog_model', '', TRUE);
    switch ($action) {
      case 'edit': {
        $data['MODULE_TITLE'] = 'Редактирование поста';
        $data['post'] = $this->Blog_model->get_post($id);
        $data['CONTENT'] = $this->load->view('admin/blog_edit', $data, true);
        $this->load->view('admin/template', $data);
      } break;
      case 'save': {
        if (isset($_POST['post_id'])) {
          $post = $this->Blog_model->get_post($_POST['post_id']);
          if (is_array($post) && count($post)) {
            if ($_POST['publish'] == 1) {
              $this->load->model('Log_model', '', TRUE);
              $this->Log_model->add('blog', array('action' => 'add', 'blog_id' => $_POST['post_id'], 'title' => $_POST['title']), 1);
              notify_users_email_and_sms('notify_new_post_in_blog', 'MyTrade Blog: ' . $_POST['title'], array('href' => '/blog'));
            }
            $this->Blog_model->update_post($_POST['post_id'], $_POST['title'], $_POST['text'], $_POST['timestamp'], $_POST['publish']);
          } else {
            echo 'Пост не найден';
          }
        } else {
          echo 'Wrong request';
        }
      } break;        
      default:
        echo 'Unknown action';
      break;
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

  function _return_select_prognose($status = 0, $prognose_status = '') {
    $res = '<select>';
    foreach ($prognose_status as $key => $value) {
      $res .= '<option value="' . $key . '" ' . (($key == $status) ? ('selected="selected"') : ('')) . '>' . $value . '</option>';
    }
    return $res . '</select>';
  }
}