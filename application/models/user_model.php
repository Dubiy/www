<?php

class User_model extends CI_Model {
  function __construct() {
    parent::__construct();
  }

  function quick_search($str = '', $trademates = '') {
    if ($trademates != '') {
      $sql = "SELECT `users`.`user_id`, `users`.`nickname`, `users`.`last_activity`, `users`.`rating`, `users`.`photo`, `user_relationships`.`trademate`, `user_relationships`.`feed`, `user_relationships`.`alerts`, `prognose_alerts`.`user_id` AS `has_alerts` FROM `users` 
                LEFT JOIN `user_relationships` ON `user_relationships`.`friend_id` = `users`.`user_id`
                LEFT JOIN `prognose_alerts` ON `user_relationships`.`user_id` = `prognose_alerts`.`user_id`
                WHERE `users`.`nickname` LIKE '%" . $this->db->escape_like_str($str) . "%' AND `user_relationships`.`trademate` = '1' AND `user_relationships`.`user_id` = '" . logged_in() . "'
                GROUP BY `users`.`user_id`";
      $res = $this->db->query($sql)->result();
      foreach ($res as $k => $rec) {
        $res[$k]->last_activity = mysql2js_date($rec->last_activity);      
      }
      return $res;
    } else {
      $sql = "SELECT `users`.`user_id`, `users`.`nickname` FROM `users`
              WHERE `users`.`nickname` LIKE '%" . $this->db->escape_like_str($str) . "%'
              LIMIT 5";
      return $this->db->query($sql)->result();
    }
  }

  function get_by_ids($ids = '') {
    if (is_array($ids) && count($ids)) {
      $whr = array();
      foreach ($ids as $id) {
        $whr[] = "`users`.`user_id` = '$id'";
      }
      $sql = "SELECT `users`.`user_id`, `users`.`nickname` FROM `users` WHERE " . implode(' OR ', $whr);
      return $this->db->query($sql)->result();
    } else {
      return false;
    }
  }

  /*function get_user($user_id = '') {
    //в цій функції неможна використовувати хелпер logged_in(), бо цей хелпер викликає цю функцію, і виходить мегазациклення. сука!)
    $logged_id = ((isset($_SESSION['user_id'])) ? ($_SESSION['user_id']) : (0));
    if ($user_id == '') {
      $user_id = $logged_id;
    }
    $this->db->select('user.*, user_relationships.trademate, user_relationships.feed, user_relationships.alerts, user_relationships.disable_pm, user_relationships.harassing, user_relationships.ignore')->
               from('user')->
               join('user_relationships', 'user_relationships.friend_id = user.user_id AND user_relationships.user_id = "' . $logged_id . '"', 'left outer')->
               where('user.user_id', $user_id)->
               limit(1);
    $query = $this->db->get();

    return $query->result();
  }*/

  function get_user($user_id = '') {
    //в цій функції неможна використовувати хелпер logged_in(), бо цей хелпер викликає цю функцію, і виходить мегазациклення. сука!)
    $logged_id = ((isset($_SESSION['user_id'])) ? ($_SESSION['user_id']) : (0));
    if ($user_id == '') {
      $user_id = $logged_id;
    }
    $this->db->select('users.*')->
               from('users')->
               where('users.user_id', $user_id)->
               limit(1);
    $query = $this->db->get();
    return $query->result();
  }



  function get_friends($type = 'pm', $skip = '0', $filter = 'all') {
    if ( ! logged_in()) {
      return false;
    }
    $logged_id = logged_in();
    if ($type == 'all') {
      $type = 'pm';
    }

    switch ($filter) {
      case 'all': {
        //do nifiga
        $whr = '';
      } break;
      case 'mates_feed': {
        $whr = "AND `user_relationships`.`feed` = '1'";
      } break;
      case 'trade_alerts': {
        $whr = "AND `user_relationships`.`alerts` = '1'";
      } break;
    }

    switch ($type) {
      case 'pm': {
        $sql = "SELECT `users`.`user_id`, `users`.`nickname`, `users`.`last_activity`, `users`.`rating`, `users`.`photo`, `user_relationships`.`trademate`, SUM(`messages`.`status` = '0' AND `messages`.`sender_id` = `users`.`user_id`) AS `unread_msgs` FROM `users`
                LEFT JOIN `messages` ON (`messages`.`sender_id` = `users`.`user_id` OR `messages`.`receiver_id` = `users`.`user_id`) AND (`messages`.`sender_id` = '$logged_id' OR `messages`.`receiver_id` = '$logged_id')
                LEFT JOIN `user_relationships` ON `user_relationships`.`friend_id` = `users`.`user_id` AND (`user_relationships`.`user_id` = `messages`.`receiver_id` OR `user_relationships`.`user_id` = `messages`.`sender_id`)
                WHERE `messages`.`datetime` IS NOT NULL AND `users`.`user_id` <> '$logged_id'
                GROUP BY `users`.`user_id`
                ORDER BY `unread_msgs` DESC
                LIMIT $skip, 10";
      } break;
      case 'trademates': {
        $sql = "SELECT `users`.`user_id`, `users`.`nickname`, `users`.`last_activity`, `users`.`rating`, `users`.`photo`, `user_relationships`.`trademate`, `user_relationships`.`feed`, `user_relationships`.`alerts`, `prognose_alerts`.`user_id` AS `has_alerts` FROM `users` 
                LEFT JOIN `user_relationships` ON `user_relationships`.`friend_id` = `users`.`user_id`
                LEFT JOIN `prognose_alerts` ON `user_relationships`.`user_id` = `prognose_alerts`.`user_id`
                WHERE `user_relationships`.`trademate` = '1' AND `user_relationships`.`user_id` = '" . logged_in() . "' $whr
                GROUP BY `users`.`user_id`
                LIMIT $skip, 10";
      } break;
      default: {
        echo 'WTF, dude!?';
        $sql = '';
      } break;
    }

    $res = $this->db->query($sql)->result();
    foreach ($res as $k => $rec) {
      $res[$k]->last_activity = mysql2js_date($rec->last_activity);      
    }
    return $res;
  }

  function get_user_permissions($user_id = '') {
    $logged_id = ((isset($_SESSION['user_id'])) ? ($_SESSION['user_id']) : (0));
    //використовується, коли я пишу повідомлення користувачу, і перевіряю, чи він не заблокував мене, наприклад
    $this->db->select('*')->
               from('user_relationships')->
               where('user_id', $user_id)->
               where('friend_id', $logged_id)->
               limit(1);
    $query = $this->db->get();

    return $query->result();
  }

  function get_users($order_by = 'last_activity', $direction = 'DESC', $page = 1, &$total_pages = 0) {
    $per_page = $this->config->item('users_per_page');
    $this->db->select('count(*) as count')->from('users')->order_by($order_by, $direction);
    $query = $this->db->get();
    $records = $query->result();
    $records = $records[0]->count;
    $total_pages = ceil($records / $per_page);
    if ($page > $total_pages) {
      $page = $total_pages;
    } elseif ($page < 1) {
      $page = 1;
    }
    $this->db->select('users.*')->from('users')->order_by($order_by, $direction)->limit($per_page, $per_page * $page - $per_page);
    $query = $this->db->get();
    return $query->result();
  }

  function get_user_emails($skip_user_id = '0', $filter = 'all') {
    $skip_user_id = (int)$skip_user_id;
    switch ($filter) {
      case 'all': {
        $whr = '';
      } break;
      case 'star': {
        $whr = " AND `star` = '1'";
      } break;
      case 'premium': {
        $whr = " AND `premium_to` > '" . date('Y-m-d H:i:s') . "'";
      } break;
      case 'regular': {
        $whr = " AND (`premium_to` < '" . date('Y-m-d H:i:s') . "' OR `premium_to` IS NULL)";
      } break;
      case 'online': {
        $whr = " AND `last_activity` BETWEEN STR_TO_DATE('" . date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s')) - $this->config->item('online_time')) . "', '%Y-%m-%d %H:%i:%s') AND STR_TO_DATE('" . date('Y-m-d H:i:s') . "', '%Y-%m-%d %H:%i:%s')";
      } break;
      case 'onlineprem': {
        $whr = " AND `last_activity` BETWEEN STR_TO_DATE('" . date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s')) - $this->config->item('online_time')) . "', '%Y-%m-%d %H:%i:%s') AND STR_TO_DATE('" . date('Y-m-d H:i:s') . "', '%Y-%m-%d %H:%i:%s')  AND `premium_to` > '" . date('Y-m-d H:i:s') . "'";
      } break;
    }

    $sql = "SELECT `user_id`, `email`, `nickname`, `premium_to`, `phone`, `notify_new_trade_note`, `notify_live_broadcasting_start`, `notify_new_archive_video`, `notify_new_trade_idea`, `notify_question_reply`, `notify_question_all_reply`, `notify_new_post_in_blog`
            FROM `users` 
            WHERE `users`.`user_id` != '$skip_user_id' " . $whr;
    return $this->db->query($sql)->result();


    /*$this->db->select('user_id, email, nickname, premium_to, phone, notify_new_trade_note, notify_live_broadcasting_start, notify_new_archive_video, notify_new_trade_idea, notify_question_reply, notify_question_all_reply, notify_new_post_in_blog')->from('user')->where('user_id !=', $skip_user_id);
    $query = $this->db->get();
    return $query->result();*/
  }

  function get_user_phones() {
    $this->db->select('user_id, phone')->from('users')->where('phone <>', '');
    $query = $this->db->get();
    return $query->result();
  }

  function search_users($fields, $account_type = '-1') {
    $this->db->select('users.*')->from('users');
    if ($account_type != '-1') {
      $this->db->like($fields);
      if ($account_type == '1') {
        $this->db->where("((`premium_to` < '" . date('Y-m-d H:i:s') . "') OR ((`premium_to` IS NULL) AND (`account_type` = '1')))");
      } elseif ($account_type == '2') {
        $this->db->where("premium_to > '" . date('Y-m-d H:i:s') . "'");
      }
    } else {
      $this->db->or_like($fields);
    }
    $query = $this->db->get();
    //echo $this->db->last_query();
    return $query->result();
  }

  function get_info($about = 'total+sum') {
    if ($about == 'total+sum') {
      $this->db->select('count(*) as `count`, sum(`cash`) as `cash`')->from('users');
    } elseif ($about == 'online') {
      $this->db->select('count(*) as `count`')->from('users')->where("`last_activity` BETWEEN STR_TO_DATE('" . date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s')) - $this->config->item('online_time')) . "', '%Y-%m-%d %H:%i:%s') AND STR_TO_DATE('" . date('Y-m-d H:i:s') . "', '%Y-%m-%d %H:%i:%s');");
    } elseif ($about == 'premium') {
      $this->db->select('count(*) as `count`')->from('users')->where("`premium_to` > STR_TO_DATE('" . date('Y-m-d H:i:s') . "', '%Y-%m-%d %H:%i:%s')");
    }
    $query = $this->db->get();
    return $query->result();
  }

  function set_relationship($friend_id = 0, $type = 0, $set_status = 0) {
    if ( ! logged_in()) {
      return false;
    }
    $allowed_fields = array('trademate', 'feed', 'alerts', 'disable_pm', 'harassing', 'ignore');
    if (in_array($type, $allowed_fields)) {
      $sql = "SELECT `user_relationships`.*
              FROM `user_relationships` 
              WHERE `user_relationships`.`friend_id` = '$friend_id' AND `user_relationships`.`user_id` = '" . logged_in() . "'
              LIMIT 1";
      $relationship = $this->db->query($sql)->result();
      
      if (is_array($relationship) && count($relationship)) {
        //update
        $fields = array($type => $set_status);
        if ($type == 'trademate') {
          if ($set_status == '0') {
            $fields['feed'] = 0;
            $fields['alerts'] = 0;
          } else {
            $fields['feed'] = 1;
          }
        } 
        $this->db->update('user_relationships', $fields, array('relationship_id' => $relationship[0]->relationship_id));
      } else {
        //insert
        $record = array(
          'user_id' => logged_in(),
          'friend_id' => $friend_id,
          'trademate' => 0,
          'feed' => 0,
          'alerts' => 0,
          'disable_pm' => 0,
          'harassing' => 0,
          'ignore' => 0,
        );
        $record[$type] = $set_status;
        if ($type == 'trademate' && $set_status == '1') {
          $fields['feed'] = 1;
        } 
        $this->db->insert('user_relationships', $record);
      }
    } else {
      return false;
    }
  }

  function toggle_star($user_id = '') {
    $user = $this->db->select('*')->from('users')->where('user_id', $user_id)->get()->result();
    if (is_array($user) && count($user)) {
      $this->db->update('users', array('star' => abs($user[0]->star - 1)), array('user_id' => $user_id));
      if (abs($user[0]->star - 1)) {
        return TRUE;
      } else {
        return FALSE;
      }
    } else {
      return FALSE;
    }
  }

  function toggle_banned_chat($user_id = '') {
    $user = $this->db->select('*')->from('users')->where('user_id', $user_id)->get()->result();
    if (is_array($user) && count($user)) {
      $this->db->update('users', array('banned_chat' => abs($user[0]->banned_chat - 1)), array('user_id' => $user_id));
      if (abs($user[0]->banned_chat - 1)) {
        return TRUE;
      } else {
        return FALSE;
      }
    } else {
      return FALSE;
    }
  }

  function get_premium_users($filter = '') {
    if (is_array($filter) && count($filter)) {
      return $this->db->select('users.*')->from('users')->where($filter)->where("premium_to > STR_TO_DATE('" . date('Y-m-d H:i:s')  . "', '%Y-%m-%d %H:%i:%s')")->get()->result();
    } else {
      return $this->db->select('users.*')->from('users')->where("premium_to > STR_TO_DATE('" . date('Y-m-d H:i:s')  . "', '%Y-%m-%d %H:%i:%s')")->get()->result();
    }
    
  }

  function get_premium_users_3day_before_end() {
    return $this->db->select('users.*')->from('users')->where("premium_to BETWEEN STR_TO_DATE('" . date('Y-m-d', strtotime('+3 days'))  . " 00:00:00', '%Y-%m-%d %H:%i:%s') AND STR_TO_DATE('" . date('Y-m-d', strtotime('+3 days')) . " 23:59:59', '%Y-%m-%d %H:%i:%s')")->get()->result();
  }

  function get_expired_premium_users() {
    //return $this->db->select('users.*')->from('users')->where("premium_to BETWEEN STR_TO_DATE('" . date('Y-m-d', strtotime('-1 day')) . " 00:00:00', '%Y-%m-%d %H:%i:%s') AND STR_TO_DATE('" . date('Y-m-d') . " 23:59:59', '%Y-%m-%d %H:%i:%s')")->get()->result();
    return $this->db->select('users.*')->from('users')->where("premium_to BETWEEN STR_TO_DATE('" . date('Y-m-d') . " 00:00:00', '%Y-%m-%d %H:%i:%s') AND STR_TO_DATE('" . date('Y-m-d') . " 23:59:59', '%Y-%m-%d %H:%i:%s')")->get()->result();
  }

  function get_user_by_email($email = '') {
    $this->db->select('users.*')->from('users')->where('users.email', $email)->limit(1);
    $query = $this->db->get();
    return $query->result();
  }

  function get_user_by_vkid($vkid = '') {
    $this->db->select('users.*')->from('users')->where('users.vk_profileid', $vkid)->limit(1);
    $query = $this->db->get();
    return $query->result();
  }

  function get_user_by_password($password = '') { /*_*/
    $this->db->select('users.*')->from('users')->where('users.password', $password)->limit(1);
    $query = $this->db->get();
    return $query->result();
  }

  function get_user_by_nick($str = '') {
    $this->db->select('users.*')->from('users')->where('users.nickname', $str)->limit(1);
    $query = $this->db->get();
    return $query->result();
  }

  function update_last_activity($user_id = '') {
    $this->db->update('users', array('last_activity' => date('Y-m-d H:i:s'), 'ip' => $_SERVER['REMOTE_ADDR']), array('user_id' => $user_id));
  }

  function update_session_id($user_id = '') {
    $this->db->update('users', array('session_id' => session_id()), array('user_id' => $user_id));
  }

  function update_user($user_id = '', $fields = '') {
    $this->db->update('users', $fields, array('user_id' => $user_id));
  }

  function update_photo($user_id = '', $photo = '') {
    $user_photo = $this->db->select('photo')->
                            from('users')->
                            where('user_id', $user_id)->
                            get()->
                            result();
    if (is_array($user_photo) && count($user_photo)) {
      if ($user_photo[0]->photo != '') {
        $path = $this->config->item('avatars_upload_path');
        $path_thumbs = $this->config->item('avatars_upload_path_thumbs');
        @unlink($path . $user_photo[0]->photo);
        @unlink($path_thumbs . $user_photo[0]->photo);
      }
    }
    $fields = array('photo' => $photo);
    $this->db->update('users', $fields, array('user_id' => $user_id));
  }


  function activate($user_id = '') {
    $account_type = $this->config->item('account_type');
    $this->db->update('users', array('account_type' => $account_type['activated']), array('user_id' => $user_id));
  }


  function register($array = '') {
    //$password = md5($array['email'] . date('Y-m-d H:i:s') . rand(1, 100500));

    do {
      $password = generatePassword(10);
      $passwords = $this->db->select('*')->
                            from('users')->
                            where('password', md5($password))->
                            get()->
                            result();
    } while (is_array($passwords) && count($passwords));

    $record = array('email' => $array['email'],
                    'password' => md5($password),
                    'nickname' => $array['nickname'],
                    'phone' => $array['phone'],
                    'pass' => $password  //unhashed password, would be deleted after user activation
    );
    if (isset($array['vk_profileid'])) {
      $record['vk_profileid'] = $array['vk_profileid'];
    }
    if (isset($array['fb_profileid'])) {
      $record['fb_profileid'] = $array['fb_profileid'];
    }

    $this->db->insert('users', $record);
    $record['user_id'] = $this->db->insert_id();

    //включаем новому пользователю все тикеры.
    $tickers = $this->db->select('*')->from('tickers')->get()->result();
    if (is_array($tickers) && count($tickers)) {
      foreach ($tickers as $ticker) {
        $this->db->insert('ticker_holds', array('ticker_id' => $ticker->ticker_id, 'user_id' => $record['user_id']));
      }
    }

    $record['activation_code'] = md5(md5($record['password']));
    $record['password'] = $password;
    return $record;
  }

  function reset_password($user_id = '') {
    if ($user_id == '') {
      $user_id = logged_in();
    }

    do {
      $password = generatePassword(10);
      $passwords = $this->db->select('*')->
                            from('users')->
                            where('password', md5($password))->
                            get()->
                            result();
    } while (is_array($passwords) && count($passwords));

    $this->db->update('users', array('password' => md5($password), 'pass' => $password), array('user_id' => $user_id));
    return $password;
  }

  function delete($user_id = '0') {
    $this->db->delete('users', array('user_id' => $user_id));
  }


}