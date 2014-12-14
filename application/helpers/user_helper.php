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

if (!function_exists('logged_in')) {
  function logged_in() {
    if (isset($_SESSION['user_id'])) {
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
      return $_SESSION['user_id'];
    } else {
      return FALSE;
    }
  }
}

if (!function_exists('rating')) {
  function rating($value = 0) {
    if ($value > 0) {
      return '+' . $value;
    } else {
      return $value;
    }
  }
}

if (!function_exists('question_type')) {
  function question_type($value = 0) {
    if ($value == 0) {
      return 'Дети';
    }
    return 'Родители';
  }
}


if (!function_exists('question_text')) {
  function question_text($value = '') {
    // $value
    if (mb_strlen($value) > 200) {
      return mb_substr($value, 0, 200) . '...';
    }
    return $value;
  }
}

if (!function_exists('answer_type')) {
  function answer_type($value = '') {
    if ($value < 18) {
      return 'Дети';
    }
    return 'Родители';
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


if (!function_exists('logout')) {
  function logout() {
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

    $_SESSION['user_id'] = $user->user_id;
    $_SESSION['email'] = $user->email;
    $_SESSION['age'] = $user->age;
    $_SESSION['sex'] = $user->sex;
    $_SESSION['account_type'] = $user->account_type;
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


  //CI function (system/libraries/Form_validation.php)
if (!function_exists('valid_email')) {
  function valid_email($str) {
    return ( ! preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $str)) ? FALSE : TRUE;
  }
}
