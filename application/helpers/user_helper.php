<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('uprofiler')) {
  function uprofiler($file = '', $line = '', $comment = '') {
    global $uprofiler;
    $uprofiler[] = array('t' => microtime(true), 'f' => $file, 'l' => $line, 'c' => $comment);
    //echo sprintf("Elapsed:  %f", $now-$then);
  }
}

if (!function_exists('uprofiler_report')) {
  function uprofiler_report() {
    global $uprofiler;
    $res = '';
    if (isset($uprofiler) && is_array($uprofiler) && count($uprofiler)) {
      $prev = $uprofiler[0]['t'];
      foreach ($uprofiler as $rec) {
        $res .= $rec['t'] . ' ' . ($rec['t'] - $prev) . '. ' . $rec['c'] . ' (' . $rec['f'] . ':' . $rec['l'] . ")\n";
      }
    }
    echo "<!-- \n" . $res . "\n-->";
  }
}

if (!function_exists('userpic')) {
    function userpic($filename = '') {
        if ($filename) {
            return '/upload/userpic/thumbs/' . $filename;
        } else {
            return '/img/userpic_default.png';
        }
    }
}

if (!function_exists('premium')) {
    function premium() {
        if (isset($_SESSION['user_id']) && $_SESSION['premium_to'] > date('Y-m-d H:i:s')) {
            return true;
        }
        return false;
    }
}

if (!function_exists('logged_in')) {
  function logged_in() {
    static $first_execution;
    if (isset($_SESSION['user_id'])) {
      if ($first_execution == 0) {
        $CI =& get_instance();
        $user = $CI->User_model->get_user($_SESSION['user_id']);
        $_SESSION['teamspeak_token'] = $user[0]->teamspeak_token;

        // $_SESSION['undercover'] - адмін зайшов під чиїмось логіном, і тут зберігається сесія адміна
        if (! isset($_SESSION['undercover']) && (! admin()) && $user[0]->session_id != session_id()) {
          logout();
          redirect('/', 'location');
          return FALSE;
        }

        //update session info
        $_SESSION['cash'] = $user[0]->cash;
        $_SESSION['premium_to'] = $user[0]->premium_to;
        $_SESSION['banned_chat'] = $user[0]->banned_chat;

        if (! isset($_SESSION['undercover'])) {
          $CI->User_model->update_last_activity($_SESSION['user_id']);
        }
        $first_execution++;

      }
      return $_SESSION['user_id'];
    } else {
      return FALSE;
    }
  }
}



if (!function_exists('chatadmin')) {
  function chatadmin() {
    $CI =& get_instance();
    $account_type = $CI->config->item('account_type');
    if ((isset($_SESSION['user_id'])) && ($account_type[$_SESSION['account_type']] == 'chatadmin')) {
      $CI->User_model->update_last_activity($_SESSION['user_id']);
      return $_SESSION['user_id'];
    } else {
      return FALSE;
    }
  }
}


if (!function_exists('admin')) {
  function admin() {
    $CI =& get_instance();
    $account_type = $CI->config->item('account_type');
    if ((isset($_SESSION['user_id'])) && ($account_type[$_SESSION['account_type']] == 'admin')) {
      $CI->User_model->update_last_activity($_SESSION['user_id']);
      return $_SESSION['user_id'];
    } else {
      return FALSE;
    }
  }
}

if (!function_exists('human_size')) {
    function human_size($bytes, $precision = 2) { 
        $units = array('B', 'KB', 'MB', 'GB', 'TB'); 
        $bytes = max($bytes, 0); 
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
        $pow = min($pow, count($units) - 1); 

        // Uncomment one of the following alternatives
        $bytes /= pow(1024, $pow);
        // $bytes /= (1 << (10 * $pow)); 

        return round($bytes, $precision) . ' ' . $units[$pow]; 
    }
}

if (!function_exists('transliterate1')) {
  function transliterate1($string) {
    $rus = array('ё','ж','ц','ч','ш','щ','ю','я','Ё','Ж','Ц','Ч','Ш','Щ','Ю','Я', 'А', 'Б', 'В', 'Г', 'Д', 'Е', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ъ', 'Ы', 'Ь', 'Э', 'а', 'б', 'в', 'г', 'д', 'е', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ъ', 'ы', 'ь', 'э', ' ', 'і', 'ї', 'є', 'І', 'Ї', 'Є');
    $lat = array('yo','zh','tc','ch','sh','sh','yu','ya','YO','ZH','TC','CH','SH','SH','YU','YA', 'A', 'B', 'V', 'G', 'D', 'E', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', '', 'I', '', 'E', 'a', 'b', 'v', 'g', 'd', 'e', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', '', 'i', '', 'e', '_', 'i', 'yi', 'ye', 'I', 'Yi', 'Ye');
    $string = str_replace($rus,$lat,$string);
    return($string);
  }
}



if (!function_exists('send_email')) {
  function send_email($email = '', $subject = '', $text = '', $template = '', $array = '') {
    $CI =& get_instance();
    $CI->load->library('email');
    $CI->email->from($CI->config->item('email_from'), $CI->config->item('email_from_name'));
    $CI->email->to($email);
    $CI->email->subject($subject);
    if ($template == '') {
      $CI->email->message($text);
    } else {
      $array['text'] = $text;
      $CI->email->message($CI->load->view('mail_tpl/' . $template, $array, TRUE));
    }
    $CI->email->send();
  }
}


if (!function_exists('send_email_queue')) {
  function send_email_queue($email = '', $subject = '', $text = '', $template = '', $array = '', $send_datetime = '') {
    $CI =& get_instance();

    $CI->load->model('Email_model', '', TRUE);
    if ($template != '') {
      if (isset($array['text'])) {
      	$array['texte'] = $text;
      } else {
      	$array['text'] = $text;
      }
      
      $text = $CI->load->view('mail_tpl/' . $template, $array, TRUE);
    }
    $CI->Email_model->push($CI->config->item('email_from'), $CI->config->item('email_from_name'), $email, $subject, $text, $send_datetime);
  }
}


if (!function_exists('send_sms')) {
  function send_sms($number = '', $text = '') {
    $CI =& get_instance();
    $CI->load->model('Sms_model', '', TRUE);
    if (valid_phone($number)) {
      $CI->Sms_model->send($number, $text);
      //file_get_contents('http://sms.ru/sms/send?api_id=' . $CI->config->item('sms_api_id') . '&to=' . urlencode($number) . '&text=' . urlencode($text) . '&partner_id=' . ((rand(0, 1)) ? ('10051') : ('14762')));
      return TRUE;
    } else {
      return FALSE;
    }
  }
}

if (!function_exists('site_title')) {
  function site_title() {
    $CI =& get_instance();
    $site_title_array = $CI->config->item('site_title_array');
    $res = ((isset($site_title_array[$CI->uri->segment(1)]['index'])) ? ($site_title_array[$CI->uri->segment(1)]['index']) : (''));
    $res = ((isset($site_title_array[$CI->uri->segment(1)][$CI->uri->segment(2)])) ? ($site_title_array[$CI->uri->segment(1)][$CI->uri->segment(2)]) : ($res));
    return (($res != '') ? ($res . ' | ') : ('')) . $CI->config->item('sitename');
  }
}

if (!function_exists('valid_phone')) {
  function valid_phone($number = '') {
    if ($number != '' && (strlen($number) > 8)) {
      return TRUE;
    } else {
      return FALSE;
    }
  }
}

if (!function_exists('notify_users_email_and_sms')) {
  function notify_users_email_and_sms($notify_type = '', $text = '', $variables = '', $regular_users_delay = '') {
    $CI =& get_instance();
    $variables['regular_users_delay'] = $regular_users_delay;


    if ($notify_type == 'notify_question_all_reply' && isset($variables['user_id'])) {
      //Все пользователи, кроме $variables['user_id']
      $users = $CI->User_model->get_user_emails($variables['user_id']);
    } else {
      //Все пользователи
      $users = $CI->User_model->get_user_emails();
    }
    
    if ($notify_type == 'notify_new_trade_idea') {
      $CI->load->model('Table_model', '', TRUE);
      $ticker_holds = $CI->Table_model->get_ticker_holds($variables['ticker_id']);
      if (is_array($ticker_holds) && count($ticker_holds)) {
        foreach ($ticker_holds as $hold) {
          $user_holds[] = $hold->user_id;
        }
      }
    }

    if (isset($users) && is_array($users) && count($users)) {
      foreach ($users as $user) {

        //file_put_contents('filenamek2', print_r($users, true));

        if ($user->premium_to > date('Y-m-d H:i:s')) {
          //premium
          $send_datetime = '';
        } elseif ($regular_users_delay != '') {
          //regular + затримка
          $send_datetime = date('Y-m-d H:i:s', strtotime($regular_users_delay));
        } else {
          //regular і не вказана затримка
          $send_datetime = '';
        }

        $notify = notify_decode($user->$notify_type);
        if (($notify_type == 'notify_new_trade_idea') && ($notify['mail'] || $notify['sms'])) {
          if ( ! in_array($user->user_id, $user_holds)) {
            $notify['mail'] = 0;
            $notify['sms'] = 0;
          }
        }

        //не уведомлять про ответ по СМС никого кроме автора вопроса
        if ($notify_type == 'notify_question_reply') {
          $notify['sms'] = 0;
        }

        if ($notify['mail']) {
          if (is_array($variables) && count($variables)) {
            $tmpl_array = array_merge((array)$user, $variables);
          } else {
            $tmpl_array = (array)$user;
          }
          //file_put_contents('filenamek2', print_r($tmpl_array, true));
          send_email_queue($user->email, $CI->config->item('email_subject_' . $notify_type), $text, $notify_type, $tmpl_array, $send_datetime);
          //send_email($user->email, $CI->config->item('email_subject_' . $notify_type), $text, $notify_type, $tmpl_array);
        }

        if ($notify['sms']) {
          send_sms($user->phone, $text);
        }
      }
    }
  }
}


if (!function_exists('logout')) {
  function logout() {
    setcookie("never_show_intro_video", '', time()-1000, '/');
    $keys = array_keys($_SESSION);
    for ($i = 0; $i < count($keys); $i++) {
      unset($_SESSION[$keys[$i]]);
    }
  }
}


if (!function_exists('login')) {
  function login($user = '') {
    $CI = & get_instance();
    $account_type = $CI->config->item('account_type');
    if ($account_type[$user->account_type] == 'registred') {
      return 'Учетная запись не активирована';
    }
    if ($account_type[$user->account_type] == 'banned') {
      return 'Учетная запись заблокирована до ' . $user->banned_to;
    }

    if (date('Y-m-d', strtotime($user->last_login)) != date('Y-m-d')) {
      $CI->User_model->update_user($user->user_id, array('last_login' => date('Y-m-d H:i:s'), 'today_auth_count' => 0));
    } else {
      $CI->User_model->update_user($user->user_id, array('today_auth_count' => ($user->today_auth_count + 1)));
      if ($user->today_auth_count + 1 == $CI->config->item('admin_alert_after_logins_count')) {
        send_email($CI->config->item('admin_email'), 'Превышен дневной лимит авторизаций пользователя', '', 'admin_too_many_logins', (array)$user);
      }
    }

    $_SESSION['user_id'] = $user->user_id;
    $_SESSION['email'] = $user->email;
    $_SESSION['nickname'] = $user->nickname;
    $_SESSION['phone'] = $user->phone;
    $_SESSION['cash'] = $user->cash;
    $_SESSION['account_type'] = $user->account_type;
    $_SESSION['premium_to'] = $user->premium_to;
    $_SESSION['frozen_days'] = $user->frozen_days;
    $_SESSION['settlement'] = $user->settlement;
    $_SESSION['automatic_payment'] = $user->automatic_payment;
    $_SESSION['time_zone'] = $user->time_zone;
    $_SESSION['banned_chat'] = $user->banned_chat;
    $_SESSION['language'] = $user->language;

    $_SESSION['notify_announcement_update'] = $user->notify_announcement_update;
    $_SESSION['notify_new_trade_idea'] = $user->notify_new_trade_idea;
    $_SESSION['notify_live_broadcasting_start'] = $user->notify_live_broadcasting_start;
    $_SESSION['notify_new_archive_video'] = $user->notify_new_archive_video;
    $_SESSION['notify_question_all_reply'] = $user->notify_question_all_reply;
    $_SESSION['notify_question_reply'] = $user->notify_question_reply;
    $_SESSION['notify_activity_update'] = $user->notify_activity_update;
    $_SESSION['notify_chats_update'] = $user->notify_chats_update;

    $_SESSION['teamspeak_token'] = $user->teamspeak_token;

    if (! isset($_SESSION['undercover'])) {
      $CI->User_model->update_session_id($user->user_id);
    }
    return TRUE;
  }
}

if (!function_exists('time_since')) {
  function time_since($since, &$between_ = 0, $addbr = FALSE) {
    $since = strtotime(date('Y-m-d H:i:s')) - strtotime($since);
    $sign = '';
    if ($since < 0) {
      $sign = '-';
      $since = abs($since);
    }
    $between_ = $since;
    $chunks = array(
        array(60 * 60 * 24 * 365 , 'YR'),
        array(60 * 60 * 24 * 30 , 'MNT'),
        array(60 * 60 * 24 * 7, 'WK'),
        array(60 * 60 * 24 , 'DY'),
        array(60 * 60 , 'HR'),
        array(60 , 'MIN'),
        array(1 , 'SEC')
    );
    for ($i = 0, $j = count($chunks); $i < $j; $i++) {
        $seconds = $chunks[$i][0];
        $name = $chunks[$i][1];
        if (($count = floor($since / $seconds)) != 0) {
            break;
        }
    }
    $print = $sign . $count . (($addbr) ? ('<br />') : ('')) . $name;
    return $print;
  }
}

if (!function_exists('notify_decode')) {
  function notify_decode($code = 0) {
    if ($code >= 4) {
      $res['sound'] = 1;
      $code -= 4;
    } else {
      $res['sound'] = 0;
    }
    if ($code >= 2) {
      $res['mail'] = 1;
      $code -= 2;
    } else {
      $res['mail'] = 0;
    }
    if ($code >= 1) {
      $res['sms'] = 1;
    } else {
      $res['sms'] = 0;
    }
    return $res;
  }
}

if (!function_exists('notify_encode')) {
  function notify_encode($arr) {
    return $arr['sound'] * 4 + $arr['mail'] * 2 + $arr['sms'];
  }
}

if (!function_exists('generatePassword')) {
  function generatePassword($length = 10) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $count = mb_strlen($chars);
    for ($i = 0, $result = ''; $i < $length; $i++) {
      $index = rand(0, $count - 1);
      $result .= mb_substr($chars, $index, 1);
    }
    return $result;
  }
}

  //CI function (system/libraries/Form_validation.php)
if (!function_exists('valid_email')) {
  function valid_email($str) {
    return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
  }
}

if (!function_exists('mysql2js_date')) {
  function mysql2js_date($str = '') {
    return (($str) ? (date('D M d Y H:i:s O', strtotime($str))) : (''));
  }
}

/* End of file user_helper.php */
/* Location: ./system/helpers/user_helper.php */