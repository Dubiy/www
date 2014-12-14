<?php

class Like_model extends CI_Model {
  function __construct() {
    parent::__construct();
  }

  function like($entity = '', $entity_id = '', $type = '') {
    $user_id = logged_in();
    if ($type != 1) {
      $type = -1;
    }
    if ($user_id == FALSE) {
      return false;
    }

    $like = $this->db->select('*')->from('likes')->where('user_id', $user_id)->where('table_name', $entity)->where('table_id', $entity_id)->get()->result();
    if (is_array($like) && count($like)) {
      //nifiga
    } else {

      //insert likes
      $this->db->insert('likes', array('type' => $type, 'table_name' => $entity, 'table_id' => $entity_id, 'user_id' => $user_id, 'datetime' => date('Y-m-d H:i:s')));

      //update rating
      switch ($entity) {
        case 'questions': {
          $this->db->set('rating', 'rating' . (($type > 0) ? ('+') : ('')) . $type, FALSE)->where('question_id', $entity_id)->update($entity);

          $rating = $this->db->select('rating')->from($entity)->where('question_id', $entity_id)->get()->result();
          if (is_array($rating) && count($rating)) {
            return $rating[0]->rating;
          }
        } break;
        case 'answers': {
          $this->db->set('rating', 'rating' . (($type > 0) ? ('+') : ('')) . $type, FALSE)->where('answer_id', $entity_id)->update($entity);

          $rating = $this->db->select('rating')->from($entity)->where('answer_id', $entity_id)->get()->result();
          if (is_array($rating) && count($rating)) {
            return $rating[0]->rating;
          }
        } break;
      }
      return false;
      
    }






  }











  function get_votes($table_name = '', $table_id = '') {
    $table_id = (int)$table_id;
    $allowedTables = array('posts', 'prognoses', 'rooms', 'users');
    if (in_array($table_name, $allowedTables)) {
      $union = '';
      switch ($table_name) {
        case 'users': {
          $union = "UNION
                    SELECT `likes`.*, `users`.`nickname`, `users`.`photo` FROM `posts`
                    LEFT JOIN `likes` ON `likes`.`table_name` = 'posts' AND `likes`.`table_id` = `posts`.`post_id`
                    LEFT JOIN `users` ON `likes`.`user_id` = `users`.`user_id`
                    WHERE `posts`.`author_id` = '$table_id' AND `likes`.`like_id` IS NOT NULL

                    UNION
                    
                    SELECT `likes`.*, `users`.`nickname`, `users`.`photo` FROM `prognoses`
                    LEFT JOIN `likes` ON `likes`.`table_name` = 'prognoses' AND `likes`.`table_id` = `prognoses`.`prognose_id`
                    LEFT JOIN `users` ON `likes`.`user_id` = `users`.`user_id`
                    WHERE `prognoses`.`user_id` = '$table_id' AND `likes`.`like_id` IS NOT NULL

                    UNION 

                    SELECT `likes`.*, `users`.`nickname`, `users`.`photo` FROM `rooms`
                    LEFT JOIN `likes` ON `likes`.`table_name` = 'rooms' AND `likes`.`table_id` = `rooms`.`room_id`
                    LEFT JOIN `users` ON `likes`.`user_id` = `users`.`user_id`
                    WHERE `rooms`.`author_id` = '1' AND `likes`.`like_id` IS NOT NULL";
        } break;
        case 'rooms': {
          $union = "UNION
                    SELECT `likes`.*, `users`.`nickname`, `users`.`photo` FROM `posts`
                    LEFT JOIN `likes` ON `likes`.`table_name` = 'posts' AND `likes`.`table_id` = `posts`.`post_id`
                    LEFT JOIN `users` ON `likes`.`user_id` = `users`.`user_id`
                    WHERE `posts`.`room_id` = '$table_id' AND `likes`.`like_id` IS NOT NULL";
        } break;
        default: {
          $whr = "`table_name` = '$table_name'";
        } break;
      }
      //$this->db->select('*')->from('likes')->where('table_name', $table_name)->where('table_id', $table_id)->
      $sql = "SELECT `likes`.*, `users`.`nickname`, `users`.`photo` FROM `likes`
              LEFT JOIN `users` ON `likes`.`user_id` = `users`.`user_id`
              WHERE `table_name` = '$table_name' AND `table_id` = '$table_id' $union";
      return $this->db->query($sql)->result();
    } else {
      return false;
    }    
  }

  function post($post_id = '', $vote = '') {
    if ( ! logged_in()) {
      return false;
    }
    if ($vote >= 0) {
      $vote = 1;
    } else {
      $vote = -1;
    }
    $data['status'] = 'no';
    //check if liked 
    //if !liked OR type != vote { ok }
    $user_id = logged_in();
    $like = $this->db->select('*')->from('likes')->where('user_id', $user_id)->where('table_name', 'posts')->where('table_id', $post_id)->get()->result();
    if (is_array($like) && count($like) && $like[0]->type != $vote || !is_array($like) || !count($like)) {
      $post = $this->db->select('*')->from('posts')->where('post_id', $post_id)->get()->result();
      if (is_array($post) && count($post)) {
        if ($this->config->item('longpolling')) {
          global $mpl;
          if ( ! isset($mpl) || ! $mpl) {
              $this->load->helper('realplexor');
              $mpl = new Dklab_Realplexor("127.0.0.1", "10010", "demo_");
          }
        }

        if (is_array($like) && count($like)) {
          //Якщо вже лайкали запис
          //то рейтинг змінюється на 2
          $rating_inc = 2;
          //змінна $likes_inc використовується лише при dislike. коли лайк, завжди +1
          $likes_inc = 1;
        } else {
          $rating_inc = 1;
          $likes_inc = 0;
        }
        if ($vote > 0) {
          //post
          $this->db->set('likes', 'likes+1', FALSE)->set('rating', 'rating+'.$rating_inc, FALSE)->where('post_id', $post_id)->update('posts');
          if ($this->config->item('longpolling')) {
            $rp_data['action'] = 'like_post';
            $rp_data['data'] = array('post_id' => $post_id, 'rating' => ($post[0]->rating + $rating_inc), 'likes' => ($post[0]->likes + 1));
            $mpl->send('room_id_' . private_room_longpolling_channel($post[0]->room_id), json_encode($rp_data));            
          }
          if ($post[0]->parent_id != '0') {
            //parent_post
            $this->db->set('likes', 'likes+1', FALSE)->set('rating', 'rating+'.$rating_inc, FALSE)->where('post_id', $post[0]->parent_id)->update('posts');
          }
          //room
          $this->db->set('likes', 'likes+1', FALSE)->set('rating', 'rating+'.$rating_inc, FALSE)->where('room_id', $post[0]->room_id)->update('rooms');
          //author
          $this->db->set('likes', 'likes+1', FALSE)->set('rating', 'rating+'.$rating_inc, FALSE)->where('user_id', $post[0]->author_id)->update('users');
        } else {
          //post
          $this->db->set('likes', 'likes-'.$likes_inc, FALSE)->set('rating', 'rating-'.$rating_inc, FALSE)->where('post_id', $post_id)->update('posts');
          if ($this->config->item('longpolling')) {
            $rp_data['action'] = 'like_post';
            $rp_data['data'] = array('post_id' => $post_id, 'rating' => ($post[0]->rating - $rating_inc), 'likes' => ($post[0]->likes - $likes_inc));
            $mpl->send('room_id_' . private_room_longpolling_channel($post[0]->room_id), json_encode($rp_data));            
          }
          if ($post[0]->parent_id != '0') {
            //parent_post
            $this->db->set('likes', 'likes-'.$likes_inc, FALSE)->set('rating', 'rating-'.$rating_inc, FALSE)->where('post_id', $post[0]->parent_id)->update('posts');
          }
          //room
          $this->db->set('likes', 'likes-'.$likes_inc, FALSE)->set('rating', 'rating-'.$rating_inc, FALSE)->where('room_id', $post[0]->room_id)->update('rooms');
          //author
          $this->db->set('likes', 'likes-'.$likes_inc, FALSE)->set('rating', 'rating-'.$rating_inc, FALSE)->where('user_id', $post[0]->author_id)->update('users');
        }
        
        //insetr into likes table
        if (is_array($like) && count($like)) {
          $this->db->update('likes', array('type' => $vote, 'datetime' => date('Y-m-d H:i:s')), array('like_id' => $like[0]->like_id));
          $data['status'] = 'update';
        } else {
          $this->db->insert('likes', array('type' => $vote, 'table_name' => 'posts', 'table_id' => $post_id, 'user_id' => $user_id, 'datetime' => date('Y-m-d H:i:s')));
          $data['status'] = 'ok';
        }
      } else {
        //post not exists((
      }
    } else {
      // already hava that vote
    }
    echo json_encode($data);
  }  

  function room($room_id = '', $vote = '') {
    if ( ! logged_in()) {
      return false;
    }    
    if ($vote >= 0) {
      $vote = 1;
    } else {
      $vote = -1;
    }
    $data['status'] = 'no';
    //check if liked 
    //if !liked OR type != vote { ok }
    $user_id = logged_in();
    $like = $this->db->select('*')->from('likes')->where('user_id', $user_id)->where('table_name', 'rooms')->where('table_id', $room_id)->get()->result();
    if (is_array($like) && count($like) && $like[0]->type != $vote || !is_array($like) || !count($like)) {
      $room = $this->db->select('*')->from('rooms')->where('room_id', $room_id)->get()->result();
      if (is_array($room) && count($room)) {
        if ($this->config->item('longpolling')) {
          global $mpl;
          if ( ! isset($mpl) || ! $mpl) {
              $this->load->helper('realplexor');
              $mpl = new Dklab_Realplexor("127.0.0.1", "10010", "demo_");
          }
        }
        if (is_array($like) && count($like)) {
          //Якщо вже лайкали кімнату
          //то рейтинг змінюється на 2
          $rating_inc = 2;
          //змінна $likes_inc використовується лише при dislike. коли лайк, завжди +1
          $likes_inc = 1;
        } else {
          $rating_inc = 1;
          $likes_inc = 0;
        }
        if ($vote > 0) {
          //room
          $this->db->set('likes', 'likes+1', FALSE)->set('rating', 'rating+'.$rating_inc, FALSE)->where('room_id', $room_id)->update('rooms');

          if ($this->config->item('longpolling')) {
            $rp_data['action'] = 'like_room';
            $rp_data['data'] = array('room_id' => $room_id, 'rating' => ($room[0]->rating + $rating_inc), 'likes' => ($room[0]->likes + 1));
            $mpl->send('roombox', json_encode($rp_data));
          }

          //author
          $this->db->set('likes', 'likes+1', FALSE)->set('rating', 'rating+'.$rating_inc, FALSE)->where('user_id', $room[0]->author_id)->update('users');
        } else {
          //room
          $this->db->set('likes', 'likes-'.$likes_inc, FALSE)->set('rating', 'rating-'.$rating_inc, FALSE)->where('room_id', $room_id)->update('rooms');

          if ($this->config->item('longpolling')) {
            $rp_data['action'] = 'like_room';
            $rp_data['data'] = array('room_id' => $room_id, 'rating' => ($room[0]->rating - $rating_inc), 'likes' => ($room[0]->likes - $likes_inc));
            $mpl->send('roombox', json_encode($rp_data));
          }

          //author
          $this->db->set('likes', 'likes-'.$likes_inc, FALSE)->set('rating', 'rating-'.$rating_inc, FALSE)->where('user_id', $room[0]->author_id)->update('users');
        }
        
        //insetr into likes table
        if (is_array($like) && count($like)) {
          $this->db->update('likes', array('type' => $vote, 'datetime' => date('Y-m-d H:i:s')), array('like_id' => $like[0]->like_id));
          $data['status'] = 'update';
        } else {
          $this->db->insert('likes', array('type' => $vote, 'table_name' => 'rooms', 'table_id' => $room_id, 'user_id' => $user_id, 'datetime' => date('Y-m-d H:i:s')));
          $data['status'] = 'ok';
        }
      } else {
        //post not exists((
      }
    } else {
      // already hava that vote
    }
    echo json_encode($data);
  }    


  function prognose($prognose_id = '', $vote = '') {
    if ( ! logged_in()) {
      return false;
    }
    if ($vote >= 0) {
      $vote = 1;
    } else {
      $vote = -1;
    }
    $data['status'] = 'no';
    $user_id = logged_in();
    $like = $this->db->select('*')->from('likes')->where('user_id', $user_id)->where('table_name', 'prognoses')->where('table_id', $prognose_id)->get()->result();
    if (is_array($like) && count($like) && $like[0]->type != $vote || !is_array($like) || !count($like)) {
      $prognose = $this->db->select('*')->from('prognoses')->where('prognose_id', $prognose_id)->get()->result();
      if (is_array($prognose) && count($prognose)) {
        if ($this->config->item('longpolling')) {
          global $mpl;
          if ( ! isset($mpl) || ! $mpl) {
              $this->load->helper('realplexor');
              $mpl = new Dklab_Realplexor("127.0.0.1", "10010", "demo_");
          }
        }
        if (is_array($like) && count($like)) {
          //Якщо вже лайкали прогноз
          //то рейтинг змінюється на 2
          $rating_inc = 2;
          //змінна $likes_inc використовується лише при dislike. коли лайк, завжди +1
          $likes_inc = 1;
        } else {
          $rating_inc = 1;
          $likes_inc = 0;
        }
        if ($vote > 0) {
          //prognose
          $this->db->set('likes', 'likes+1', FALSE)->set('rating', 'rating+'.$rating_inc, FALSE)->where('prognose_id', $prognose_id)->update('prognoses');
          if ($this->config->item('longpolling')) {
            $rp_data['action'] = 'like_prognose';
            $rp_data['data'] = array('user_id' => $prognose[0]->user_id ,'prognose_id' => $prognose[0]->prognose_id, 'rating' => ($prognose[0]->rating + $rating_inc), 'likes' => ($prognose[0]->likes + 1));
            $mpl->send('user_id_' . $prognose[0]->user_id, json_encode($rp_data));            
          }
          //author
          $this->db->set('likes', 'likes+1', FALSE)->set('rating', 'rating+'.$rating_inc, FALSE)->where('user_id', $prognose[0]->user_id)->update('users');
        } else {
          //prognose
          $this->db->set('likes', 'likes-'.$likes_inc, FALSE)->set('rating', 'rating-'.$rating_inc, FALSE)->where('prognose_id', $prognose_id)->update('prognoses');
          if ($this->config->item('longpolling')) {
            $rp_data['action'] = 'like_prognose';
            $rp_data['data'] = array('user_id' => $prognose[0]->user_id, 'prognose_id' => $prognose[0]->prognose_id, 'rating' => ($prognose[0]->rating - $rating_inc), 'likes' => ($prognose[0]->likes - $likes_inc));
            $mpl->send('user_id_' . $prognose[0]->user_id, json_encode($rp_data));            
          }
          //author
          $this->db->set('likes', 'likes-'.$likes_inc, FALSE)->set('rating', 'rating-'.$rating_inc, FALSE)->where('user_id', $prognose[0]->user_id)->update('users');
        }
        
        //insetr into likes table
        if (is_array($like) && count($like)) {
          $this->db->update('likes', array('type' => $vote, 'datetime' => date('Y-m-d H:i:s')), array('like_id' => $like[0]->like_id));
          $data['status'] = 'update';
        } else {
          $this->db->insert('likes', array('type' => $vote, 'table_name' => 'prognoses', 'table_id' => $prognose_id, 'user_id' => $user_id, 'datetime' => date('Y-m-d H:i:s')));
          $data['status'] = 'ok';
        }
      } else {
        //post not exists((
      }
    } else {
      // already hava that vote
    }
    echo json_encode($data);
  } 

  function user($vote_user_id = '', $vote = '') {
    if ( ! logged_in()) {
      return false;
    }
    if ($vote >= 0) {
      $vote = 1;
    } else {
      $vote = -1;
    }
    $data['status'] = 'no';
    $user_id = logged_in();
    $like = $this->db->select('*')->from('likes')->where('user_id', $user_id)->where('table_name', 'users')->where('table_id', $vote_user_id)->get()->result();
    if (is_array($like) && count($like) && $like[0]->type != $vote || !is_array($like) || !count($like)) {
      $user = $this->db->select('*')->from('users')->where('user_id', $vote_user_id)->get()->result();
      if (is_array($user) && count($user)) {
        if ($this->config->item('longpolling')) {
          global $mpl;
          if ( ! isset($mpl) || ! $mpl) {
              $this->load->helper('realplexor');
              $mpl = new Dklab_Realplexor("127.0.0.1", "10010", "demo_");
          }
        }
        if (is_array($like) && count($like)) {
          //Якщо вже лайкали прогноз
          //то рейтинг змінюється на 2
          $rating_inc = 2;
          //змінна $likes_inc використовується лише при dislike. коли лайк, завжди +1
          $likes_inc = 1;
        } else {
          $rating_inc = 1;
          $likes_inc = 0;
        }
        if ($vote > 0) {
          $this->db->set('likes', 'likes+1', FALSE)->set('rating', 'rating+'.$rating_inc, FALSE)->where('user_id', $vote_user_id)->update('users');
          if ($this->config->item('longpolling')) {
            $rp_data['action'] = 'like_user';
            $rp_data['data'] = array('user_id' => $user[0]->user_id, 'rating' => ($user[0]->rating + $rating_inc), 'likes' => ($user[0]->likes + 1));
            $mpl->send('user_id_' . $user[0]->user_id, json_encode($rp_data));            
          }
        } else {
          $this->db->set('likes', 'likes-'.$likes_inc, FALSE)->set('rating', 'rating-'.$rating_inc, FALSE)->where('user_id', $vote_user_id)->update('users');
          if ($this->config->item('longpolling')) {
            $rp_data['action'] = 'like_user';
            $rp_data['data'] = array('user_id' => $user[0]->user_id, 'rating' => ($user[0]->rating - $rating_inc), 'likes' => ($user[0]->likes - $likes_inc));
            $mpl->send('user_id_' . $user[0]->user_id, json_encode($rp_data));            
          }
        }
        
        //insetr into likes table
        if (is_array($like) && count($like)) {
          $this->db->update('likes', array('type' => $vote, 'datetime' => date('Y-m-d H:i:s')), array('like_id' => $like[0]->like_id));
          $data['status'] = 'update';
        } else {
          $this->db->insert('likes', array('type' => $vote, 'table_name' => 'users', 'table_id' => $vote_user_id, 'user_id' => $user_id, 'datetime' => date('Y-m-d H:i:s')));
          $data['status'] = 'ok';
        }
      } else {
        //post not exists((
      }
    } else {
      // already hava that vote
    }
    echo json_encode($data);
  } 
}