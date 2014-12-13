<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Ajax extends CI_Controller {

    function __construct() {
        parent::__construct();
    }

    public function index() {
        echo 'Hello) This powerfull site was created by <a href="http://garik.pp.ua" target="_blank">Ihor Dubij</a> & <a href="http://uw-t.com" target="_blank">Sergey Sikach</a>';
    }

    public function unundercover() {
        if (isset($_SESSION['undercover'])) {
            $undercover = $_SESSION['undercover'];
            foreach ($_SESSION as $key => $value) {
                unset($_SESSION[$key]);
            }
            $_SESSION = unserialize($undercover);
            redirect('/admin', 'location');
        }
    }

    function test2() {
        //SELECT count(*) from posts GROUP by 
        print_r($this->db->select('author_id')->from('posts')->where('room_id', 1)->group_by('author_id ')->get()->result());
    }

    public function test() {
        $this->load->library('email');
        $this->email->from('trader@my-trade.pro', 'My-trade');
        $this->email->to('osben@fxmail.ru');
        $this->email->subject('Тест бля');
        $this->email->message('Мыло, где же ты?');
        var_dump($this->email->send());

        $this->email->from('trader@my-trade.pro', 'My-trade');
        $this->email->to('netzver@gmail.com');
        $this->email->subject('Тест бля');
        $this->email->message('Мыло, где же ты?');
        var_dump($this->email->send());

        //var_dump(mail ('netzver@gmail.com',' string $subject ',' string $message ['));
    }

    function sms_status_callback() {
        if (isset($_POST['data']) && is_array($_POST['data']) && count($_POST['data'])) {
            $this->load->model('Sms_model', '', TRUE);

            foreach ($_POST['data'] as $sms_status) {
                $lines = explode("\n", $sms_status);
                if ($lines[0] == "sms_status") {
                    $sms_id = $lines[1];
                    $sms_status = $lines[2];
                    $this->Sms_model->update_status($sms_id, $sms_status);
                }
            }

            //ну вообще піздецкрутапровєрка чи немає в нас сбоя)
            echo '100';
        } else {
            echo 'Ты кто такой? Давай до-свидание!';
        }
    }

    function cron_send_email_queue() {
        //запускається щохвилини. виконується до 55 секунд (щоб виконання не вібувалося параленьно). якщо робота не закінчена - завершається примусово
        $max_time = 55;
        $start_time = time();

        $this->load->model('Email_model', '', TRUE);
        $queue = $this->Email_model->get_queue();
        if (is_array($queue) && count($queue)) {
            $this->load->library('email');

            foreach ($queue as $letter) {
                if (time() > $start_time + $max_time) {
                    exit;
                }

                //send
                $this->email->from($letter->email_from, $letter->email_from_name);
                $this->email->to($letter->email_to);
                $this->email->subject($letter->subject);
                $this->email->message($letter->text);
                $this->email->send();

                //pop
                $this->Email_model->pop($letter->email_queue_id);
            }
        }
    }

    function token_use() {
        if (isset($_POST['action'])) {
            $this->db->update('users', array('teamspeak_token_used' => date('Y-m-d H:i:s')), array('user_id' => logged_in()));
        }
    }

    function cron_teamspeak() {
        if (!$this->config->item('teamspeak_status')) {
            echo 'Teamspeak disabled';
            return false;
        }

        $premium_users_tmp = $this->db->select('users.*')->from('users')->where("premium_to > STR_TO_DATE('" . date('Y-m-d H:i:s') . "', '%Y-%m-%d %H:%i:%s')")->get()->result();
        //$use
        foreach ($premium_users_tmp as $premium_user) {
            $premium_users[$premium_user->user_id] = $premium_user;
        }

        //kickban
        //UPDATE USER TOKENS
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

        //get tokens
        $token_list = $teamspeak_db->select('*')->from('tokens')->where('token_id1', '10')->get()->result();
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if (count($token_list) < 1000) {
            //generate_tokens(100);
            for ($i = 0; $i < 100; $i++) {
                $randomString = '';
                for ($j = 0; $j < 40; $j++) {
                    $randomString .= $characters[rand(0, strlen($characters) - 1)];
                }
                $teamspeak_db->insert('tokens', array('server_id' => '1', 'token_key' => $randomString, 'token_type' => 0, 'token_id1' => 10, 'token_id2' => 0, 'token_created' => strtotime('NOW'), 'token_description' => '', 'token_customset' => ''));
            }
        }

        //get clients
        $client_list_tmp = $teamspeak_db->select('*')->from('clients')->order_by('client_first_nickname', 'DESC')->order_by('client_lastconnected', 'DESC')->get()->result();
        $tmp_client_first_nickname = '';
        
 
        

        //print_r($client_list_tmp);

        if (is_array($client_list_tmp) && count($client_list_tmp)) {
            foreach ($client_list_tmp as $k => $client) {
                $client_list[$client->client_id] = $client;
            }
 
            $teamspeak_admin_unique_ids = $this->config->item('teamspeak_admin_unique_ids');
            foreach ($client_list as $k => $client) {
                if (in_array($client->client_unique_id, $teamspeak_admin_unique_ids)) {
                    //пропустить server admin
                    unset($client_list[$k]);
                    continue;
                }
            }
        }
     
        if (true) {
            //получить список клієнтів в чаті
            $this->load->helper('ts3admin');

            /* -------SETTINGS------- */
            $ts3_ip = $this->config->item('teamspeak_ip');
            $ts3_queryport = $this->config->item('teamspeak_queryport');
            $ts3_user = $this->config->item('teamspeak_user');
            $ts3_pass = $this->config->item('teamspeak_pass');
            $ts3_port = $this->config->item('teamspeak_port');

            /* ---------------------- */


            #build a new ts3admin object
            $tsAdmin = new ts3admin($ts3_ip, $ts3_queryport);



            if ($tsAdmin->getElement('success', $tsAdmin->connect())) {
                #login as serveradmin
                $tsAdmin->login($ts3_user, $ts3_pass);
                #select teamspeakserver
                $tsAdmin->selectServer($ts3_port);


                #get clientlist
                $clients_in_chat = $tsAdmin->clientList();
                $approve = array();
                $clients_ids_in_chat = array();

                //print_r($tsAdmin->serverGroupAddClient('10', '320'));

                 //print_r($clients_in_chat['data']);
                foreach ($clients_in_chat['data'] as $client) {
                    //print_r($client_list[$client['client_database_id']]);
                    if (isset($client_list[$client['client_database_id']])) {
                        $client_record = $client_list[$client['client_database_id']];

                        $client_first_nickname = explode('#', $client_record->client_first_nickname);
                        $uid = (int)$client_first_nickname[count($client_first_nickname) - 1];
                        if (isset($premium_users[$uid]) && $premium_users[$uid]->premium_to > date('Y-m-d H:i:s')) {
                            if ($client_first_nickname[0] == 'APPROVEDOKAGA') {
                                //ok. check premium
                                $clients_ids_in_chat[$uid][] = array('clid' => $client['clid'], 'client_lastconnected' => $client_list[$client['client_database_id']]->client_lastconnected);
                            } else {
                                //try to approve OR KICK))
                                $seconds_after_token_use = strtotime('NOW') - strtotime($premium_users[$uid]->teamspeak_token_used);
                                if ($seconds_after_token_use > 0 && $seconds_after_token_use < 90) {
                                    //approve
                                    $approve[$uid] = $client['client_database_id'];
                                    $clients_ids_in_chat[$uid][] = array('clid' => $client['clid'], 'client_lastconnected' => $client_list[$client['client_database_id']]->client_lastconnected);
                                } else {
                                    //kick
                                    $tsAdmin->clientKick($client['clid'], 'server', 'Error: 573. It\'s OK for you ;)');
                                    echo 'KICK' . $client['client_nickname'] . '<hr />';
                                }
                            }
                        } else {
                            //kick
                            $tsAdmin->clientKick($client['clid'], 'server', 'Error: 391. It\'s OK for you ;)');
                            echo 'KICK' . $client['client_nickname'] . '<hr />';
                        }
                    }
                }

                //print_r($clients_ids_in_chat);
                // print_r($approve);

                //банимо людей клонів у чаті
                if (is_array($clients_ids_in_chat) && count($clients_ids_in_chat)) {
                    
                    foreach ($clients_ids_in_chat as $uid => $clients) {
                        if (is_array($clients) && count($clients) > 1) {
                            //пошук того хто останній підключився. його не чіпаєм
                            $max_i = 0;
                            $max_val = 0;
                            foreach ($clients as $i => $client) {
                                if ($client['client_lastconnected'] > $max_val) {
                                    $max_val = $client['client_lastconnected'];
                                    $max_i = $i;
                                }
                            }
                            unset($clients[$max_i]);
                            //всіх іншик кікаємо))))
                            foreach ($clients as $i => $client) {
                                $tsAdmin->clientKick($client['clid'], 'server', 'Error: 251. It\'s OK for you ;)');
                                echo 'KICK uid=' . $uid . 'old clones';
                            }

                        }
                    }
                }

                if (is_array($approve) && count($approve)) {
                    foreach ($approve as $uid => $client_id) {
                        $teamspeak_db->update('clients', array('client_first_nickname' => 'APPROVEDOKAGA#' . $uid), array('client_id' => $client_id));
                    }
                }

                #print client count
                //echo count($clients_in_chat['data']) . ' clients_in_chat on selected server<br><br>';
                #print clients_in_chat to browser
                // foreach ($clients_in_chat['data'] as $client) {
                //     echo '<a href="clientkick.php?id=' . $client['clid'] . '">' . $client['client_nickname'] . '</a><br>';
                // }





            } else {
                echo 'Teamspeak: Connection could not be established.';
            }

            /**
             * This code retuns all errors from the debugLog
             */
            if (count($tsAdmin->getDebugLog()) > 0) {
                foreach ($tsAdmin->getDebugLog() as $logEntry) {
                    echo '<script>alert("' . $logEntry . '");</script>';
                }
            }


        }
        $this->db->close();

        $tokens = array();
        foreach ($token_list as $token) {
            $tokens[] = $token->token_key;
        }

        //print_r($premium_users);
        //remove active tokens from array, and users with active tokens
        foreach ($premium_users as $k => $premium_user) {
            if (in_array($premium_user->teamspeak_token, $tokens)) {
                $premium_user->user_id;
                unset($tokens[array_search($premium_user->teamspeak_token, $tokens)]);
                unset($premium_users[$k]);
            }
        }

        //update user tokens
        foreach ($premium_users as $premium_user) {
            if (!in_array($premium_user->teamspeak_token, $tokens)) {
                $this->db->update('users', array('teamspeak_token' => array_pop($tokens)), array('user_id' => $premium_user->user_id));
            }
        }


//    print_r($this->db->select('*')->from('users')->get()->result());     
        echo 'РАСХОДИТЕСЬ ВСЕ, НЕ НА ЧТО ТУТ СМОТРЕТЬ.';
    }

    function approve_teamspeak_unique_id() {
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
              $teamspeak_db->update('clients', array('client_first_nickname' => 'APPROVEDOKAGA#' . $_SESSION['user_id']), array('client_unique_id' => $_POST['unique_id']));
              //$teamspeak_db->insert('group_server_to_client', array('group_id' => '10', 'server_id' => '1', 'id1' => $client[0]->client_id, 'id2' => '0'));
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
              echo 'Unique_ID approved sucessfully';
            }
          } else {
            echo 'Unique_ID not found';
          }
          $this->db->close();  
    } 

    function cron($secret = '') {
        if ($secret != $this->config->item('cron_secret')) {
            echo 'This is CROOONN!!!!!!111';
        } else {
            $xml = new DOMDocument();
            $url = 'http://www.cbr.ru/scripts/XML_daily.asp?date_req=' . date('d.m.Y');
            if (@$xml->load($url)) {
                $list = array(); 

                $root = $xml->documentElement;
                $items = $root->getElementsByTagName('Valute');
     
                foreach ($items as $item) {
                    $code = $item->getElementsByTagName('CharCode')->item(0)->nodeValue;
                    $curs = $item->getElementsByTagName('Value')->item(0)->nodeValue;
                    $list[$code] = floatval(str_replace(',', '.', $curs));
                }
                // print_r($list);
                if (is_array($list) && count($list)) {
                    $this->load->model('Options_model', '', TRUE);
                    $this->Options_model->set('curs_usd', $list['USD']);
                }
            } 

            //убивашка SMS (тимчасова, поки не пофікситься те що після return 'бля';)
            $sql = "SELECT * FROM `users` WHERE `premium_to` < NOW() AND `premium_to` > '" . date('Y-m-d', strtotime(date('Y-m-d') . ' -30 days')) . "' ORDER BY `premium_to` DESC";
            // $sql = "SELECT * FROM `users` WHERE `premium_to` < NOW()";
            $users = $this->db->query($sql)->result();
            $notifies = array('notify_new_trade_note', 'notify_live_broadcasting_start', 'notify_new_archive_video', 'notify_new_trade_idea', 'notify_question_reply', 'notify_new_post_in_blog');
            $count = 0;
            foreach ($users as $user) {
                $user_upd_arr = array();
                foreach ($notifies as $notify) {
                    $tmp = notify_decode($user->$notify);
                    $tmp['sms'] = 0;
                    $user_upd_arr[$notify] = notify_encode($tmp);
                    $this->User_model->update_user($user->user_id, $user_upd_arr);
                }
                $count++;
            }
            echo $count;




            //продовження акаунтів, розсилка мил, і т.д.

            $notification_types = $this->config->item('notification_types');
            $settlement = $this->config->item('settlement');
            $users = $this->User_model->get_premium_users_3day_before_end();

            //print_r($users);
            if (is_array($users) && count($users)) {
                foreach ($users as $user) {
                    if ($user->cash < $settlement['cost'][$user->settlement]) {
                        send_email_queue($user->email, $this->config->item('email_subject_low_cash'), $text = '', 'low_cash', (array) $user);
                        echo '<br />uid ' . $user->user_id . ": недостаточно денег. До окончания PREMIUM\n";
                    }
                }
            }

            // echo 'Мої криві руки підвели, тимчасово відключаю цей глюкокод' . __LINE__;
            // return 'бля';

            $users = $this->User_model->get_expired_premium_users();
            // print_r($users);
            if (is_array($users) && count($users)) {
                foreach ($users as $user) {
                    if ($user->cash >= $settlement['cost'][$user->settlement] && $user->automatic_payment == 1) {
                        $premium_to = date('Y-m-d', strtotime($settlement['period'][$user->settlement])) . ' 23:59:58';
                        $user->premium_to = $premium_to;
                        $this->User_model->update_user($user->user_id, array('premium_to' => $premium_to, 'cash' => ($user->cash - $settlement['cost'][$user->settlement])));
                        send_email_queue($user->email, $this->config->item('email_subject_go_premium'), $text = '', 'go_premium', (array) $user);
                        echo 'uid ' . $user->user_id . ': продлен премиум. $' . $settlement['cost'][$user->settlement] . "\n";
                    } elseif ($user->automatic_payment == 1) {
                        $fields = '';
                        foreach ($notification_types as $notification_type => $notification_type_human) {
                            $notify = notify_decode($user->$notification_type);
                            $notify['sms'] = 0;
                            $fields[$notification_type] = notify_encode($notify);
                        }
                        $fields['automatic_payment'] = 0;
                        // $fields['premium_to'] = date('Y-m-d H:i:s', strtotime('-10 year'));
                        $this->User_model->update_user($user->user_id, $fields);
                        send_email_queue($user->email, $this->config->item('email_subject_low_cash_go_regular'), $text = '', 'low_cash_go_regular', (array) $user);
                        echo 'uid ' . $user->user_id . ': переведен в регуляр. Недостаточно денег. На счете $' . $user->cash . "\n";
                    } else {
                        $fields = '';
                        foreach ($notification_types as $notification_type => $notification_type_human) {
                            $notify = notify_decode($user->$notification_type);
                            $notify['sms'] = 0;
                            $fields[$notification_type] = notify_encode($notify);
                        }
                        $fields['automatic_payment'] = 0;
                        $fields['premium_to'] = date('Y-m-d H:i:s', strtotime('-10 year'));
                        $this->User_model->update_user($user->user_id, $fields);
                        send_email_queue($user->email, $this->config->item('email_subject_go_regular'), $text = '', 'go_regular', (array) $user);
                        echo 'uid ' . $user->user_id . ': переведен в регуляр. На счете $' . $user->cash . "\n";
                    }
                }
            }
        }
    }

    function allow_video() {
        //echo $query = 'user_id=4';
        if (isset($_SERVER['HTTP_X_CDN_QUERY'])) {
            $query = $_SERVER['HTTP_X_CDN_QUERY'];
        }

        $status = '';
        $query = explode('=', $query);
        $user = $this->User_model->get_user($query[1]);
        if (isset($user) && is_array($user) && count($user)) {
            if (($user[0]->premium_to > date('Y-m-d H:i:s') && ($user[0]->ip == $_SERVER['HTTP_X_CDN_CLIENT_IP']))) {
                header('x-cdn-status-int: 0');
                header('x-cdn-status-text: OK');
                $status = 'OK';
            } elseif ($user[0]->premium_to < date('Y-m-d H:i:s')) {
                header('x-cdn-status-int: 1');
                header('x-cdn-status-text: Access forbidden, user not premium');
                $status = 'FAIL user not premium';
            } elseif ($user[0]->ip != $_SERVER['HTTP_X_CDN_CLIENT_IP']) {
                header('x-cdn-status-int: 1');
                header('x-cdn-status-text: Access forbidden, wrong IP (session ip: ' . $user[0]->ip . ' != cdn client ip: ' . $_SERVER['HTTP_X_CDN_CLIENT_IP'] . ')');
                $status = 'FAIL wrong IP';
            } else {
                header('x-cdn-status-int: 1');
                header('x-cdn-status-text: Access forbidden, other problem? Wierd...');
                $status = 'FAIL other problem? Wierd...';
            }
        } else {
            header('x-cdn-status-int: 1');
            header('x-cdn-status-text: Access forbidden, user not exist.');
            $status = 'FAIL user not exist';
        }
/*
        if ($query[1] == '239') {
            $f = fopen('uid239_' . date('Y-m-d_H-i-s') . '.headers', 'w+');
            fputs($f, print_r($_SERVER, TRUE));
            fputs($f, 'Status = ' . $status);
            fclose($f);
        }
*/

        /*
          [HTTP_X_CDN_URI]
          [HTTP_X_CDN_CLIENT_IP]
          [HTTP_X_CDN_METHOD]
          [HTTP_X_CDN_QUERY]
          [HTTP_X_CDN_REFERRER]
          [HTTP_X_CDN_STREAM_NAME]
          [HTTP_X_CDN_USER_AGENT]
          [HTTP_X_CDN_PAGE_URL]
          [HTTP_X_CDN_SESSION_ID]
          [HTTP_X_CDN_EVENT]
         */
    }

    function broadcast_player() {
        if (logged_in()) {
            $data = '';
            $this->load->view('block/site_head', $data);
            echo '<div id="broadcast_player" class="new_window"></div>';
            $this->load->view('block/site_foot', $data);
        } else {
            echo 'not authorised';
        }
    }

    function page($alias = '') {
        $this->load->model('Page_model', '', TRUE);
        $page = $this->Page_model->get_page($alias);
        if (is_array($page) && count($page)) {
            $title = $page[0]->title;
            $text = $page[0]->text;
        } else {
            $title = 'Error';
            $text = '404: Not found';
        }
        echo json_encode(array('title' => $title, 'text' => $text));
    }

    public function ping() {
        $this->load->model('Log_model', '', TRUE);
        if (isset($_POST['max_datetime'])) {
            if (logged_in()) {
                $return_max_datetime = '1970-01-01';
                $records = $this->Log_model->get_by_max_id($_POST['max_datetime'], $return_max_datetime);
                if (is_array($records) && count($records)) {
                    foreach ($records as $record) {
                        $tmp_arr = unserialize($record->array);
                        $tmp_arr['datetime'] = mysql2js_date($record->datetime);
                        $tmp_arr['gmtime'] = gmdate('Y-m-d H:i:s', strtotime($record->datetime . (($_SESSION['time_zone'] > 0) ? ('+') : ('')) . $_SESSION['time_zone'] . ' hour'));
                        if ($record->type == 'question' && $tmp_arr['action'] == 'add' && $tmp_arr['user_id'] != logged_in()) {
                            //skip
                        } else {
                            $result['records'][$record->type][] = $tmp_arr;
                        }
                    }
                    $result['max_datetime'] = $return_max_datetime;
                } else {
                    $result['max_datetime'] = $_POST['max_datetime'];
                }
                $result['msg'] = 'ok';
                $result['user_cash'] = $_SESSION['cash'];

                $result['premium'] = (($_SESSION['premium_to'] > date('Y-m-d H:i:s')) ? (true) : (false));
            } else {
                $result['max_datetime'] = $_POST['max_datetime'];
                $result['msg'] = 'not logged in';
            }
            echo json_encode($result);
        } else {
            echo json_encode(array('msg' => 'wrong request'));
        }
    }






    function video_url_parse($link = '') {
        //нащадок function youtuber();
        if (isset($_POST['link'])) {
            $link = $_POST['link'];
        }
        $this->load->helper('chat');
        echo json_encode(video_url_parse_helper($link));
    }

    function youtuber($video_code = '', $all_info = false) {
        if ($video_code == '') {
            $parts = explode('?', trim($_POST['link']));
            if (count($parts) > 1) {
                $parts = explode('&', $parts[1]);
                foreach ($parts as $part) {
                    $block = explode('=', $part);
                    if ($block[0] == 'v') {
                        $video_code = $block[1];
                    }
                }
            } else {
                $parts = explode('youtu.be/', trim($_POST['link']));
                if (isset($parts[1])) {
                    $video_code = $parts[1];
                } else {
                    $video_code = '';
                }
            }
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://www.youtube.com/get_video_info?video_id=' . $video_code);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $links = curl_exec($ch);
        curl_close($ch);
        parse_str($links, $info);
            // print_r($info);
            // print_r(urldecode($links));
        
        if ($all_info) {
            $arr = explode('&', urldecode($links));

            foreach ($arr as $a) {
                //print_r(explode('=', $a));
            }
        }

        $arr = array(
            'video_code' => $video_code,
            'title' => @$info['title'],
            'view_count' => @$info['view_count'],
            'thumbnail_url' => @$info['thumbnail_url'],
            'length_seconds' => date('i:s', @$info['length_seconds']),
        );
        echo json_encode($arr);
    }

    function hold_ticker() {
        if (isset($_POST['ticker_id'])) {
            if (logged_in()) {
                $this->load->model('Table_model', '', TRUE);
                $this->Table_model->hold_unhold_ticker($_POST['ticker_id'], $_POST['action']);
            } else {
                echo json_encode(array('msg' => 'not authorised'));
            }
        }
    }

    function notification() {
        if (logged_in()) {
            $user = $this->User_model->get_user(logged_in());
            if (is_array($user) && count($user)) {
                $notification_types = $this->config->item('notification_types');
                if (in_array($_POST['notification_type'], array_keys($this->config->item('notification_types')))) {
                    $notify_tmp = notify_decode($user[0]->$_POST['notification_type']);
                    $notify_tmp[$_POST['button_name']] = $_POST['set_status'];
                    if ($_POST['button_name'] == 'cancel') {
                        $value = 0;
                    } elseif ($user[0]->premium_to < date('Y-m-d H:i:s') && $_POST['button_name'] == 'sms' && $_POST['set_status']) {
                        echo 'Regular users not allowed to use SMS notifications';
                        $notify_tmp['sms'] = 0;
                        $value = notify_encode($notify_tmp);
                    } elseif ($_POST['notification_type'] == 'notify_question_all_reply') {
                        $notify_tmp['sms'] = 0;
                        $value = notify_encode($notify_tmp);
                    } else {
                        $value = notify_encode($notify_tmp);
                    }
                    $_SESSION[$_POST['notification_type']] = $value;
                    $this->User_model->update_user(logged_in(), array($_POST['notification_type'] => $value));
                } else {
                    echo 'wrong field';
                }
            }
        } else {
            echo 'not authorised';
        }
    }

    function settings() {
        $res['title'] = 'Error';
        $res['fail'] = '';
        if (logged_in()) {
            if (isset($_POST['action'])) {
                switch ($_POST['action']) {
                    case 'email': {
                            $res['title'] = 'Изменение Email';
                            $user = $this->User_model->get_user();
                            if (valid_email($_POST['value'])) {
                                $tmp_user = $this->User_model->get_user_by_email($_POST['value']);
                                if (is_array($tmp_user) && count($tmp_user)) {
                                    $res['msg'] = 'email already exists';
                                } else {
                                    $this->User_model->update_user(logged_in(), array('email' => $_POST['value']));
                                    //send email
                                    send_email($_POST['value'], $this->config->item('email_subject_change_email'), $text = '', 'change_email', array('email' => $_POST['value']));
                                    $_SESSION['email'] = $_POST['value'];
                                    $res['email'] = $_POST['value'];
                                    $res['msg'] = 'Ваш email успешно изменен';
                                    unset($res['fail']);
                                }
                            } else {
                                $res['msg'] = 'wrong email';
                            }
                        } break;
                    case 'time_zone': {
                            $res['title'] = 'Изменение временной зоны';
                            $user = $this->User_model->get_user();
                            $this->User_model->update_user(logged_in(), array('time_zone' => $_POST['value']));
                            $_SESSION['time_zone'] = $_POST['value'];
                            $res['time_zone'] = 'UTC ' . (($_POST['value'] > 0) ? ('+') : ('')) . $_POST['value'];
                            $res['msg'] = 'Ваша временная зона успешно изменена';
                            unset($res['fail']);
                        } break;
                    case 'phone': {
                            $res['title'] = 'Изменение телефона';
                            $user = $this->User_model->get_user();
                            $this->User_model->update_user(logged_in(), array('phone' => $_POST['value']));
                            //send email
                            send_email($_SESSION['email'], $this->config->item('email_subject_change_phone'), $text = '', 'change_phone', array('phone' => $_POST['value']));
                            $_SESSION['phone'] = $_POST['value'];
                            $res['phone'] = $_POST['value'];
                            $res['msg'] = 'Ваш телефон успешно изменен';
                            unset($res['fail']);
                        } break;
                    case 'password': {
                            $res['title'] = 'Изменение пароля';
                            $password = $this->User_model->reset_password();

                            //send email
                            send_email($_SESSION['email'], $this->config->item('email_subject_change_password'), '', 'change_password', array('password' => $password));
                            $res['password'] = $password;
                            $res['msg'] = 'Ваш пароль успешно изменен и отправлен Вам на почту';
                            unset($res['fail']);
                        } break;
                    case 'freeze': {
                            $res['title'] = 'Заморозка аккаунта';
                            $user = $this->User_model->get_user();
                            if (is_array($user) && count($user)) {
                                if ($_POST['set_status'] == 'unfreeze') {
                                    if ($user[0]->premium_to < date('Y-m-d H:i:s')) {
                                        if ($user[0]->frozen_days > 0 && $user[0]->frozen_days <= 366) { // проверка. не может быть больше года
                                            $premium_to = date('Y-m-d', strtotime($user[0]->premium_to . ' +' . $user[0]->frozen_days . ' days'));
                                            $frozen_days = strtotime(date('Y-m-d H:i:s')); // дата разморозки
                                            //update user
                                            $user = $this->User_model->update_user(logged_in(), array('premium_to' => $premium_to, 'frozen_days' => $frozen_days));
                                            //update session
                                            $_SESSION['premium_to'] = $premium_to;
                                            $_SESSION['frozen_days'] = $frozen_days;
                                        }
                                    }

                                    $res['premium_to'] = date('Y-m-d', strtotime($_SESSION['premium_to']));
                                    $res['msg'] = 'Аккаунт разморожен';
                                    unset($res['fail']);
                                } elseif ($_POST['set_status'] == 'freeze') {
                                    if ($user[0]->premium_to > date('Y-m-d H:i:s')) {
                                        if ((time() - $user[0]->frozen_days) / (3600 * 24) > 30) {
                                            //ok
                                            $frozen_days = ceil((strtotime($user[0]->premium_to) - time()) / (3600 * 24));
                                            $premium_to = date('Y-m-d');
                                            $_SESSION['premium_to'] = $premium_to;
                                            $_SESSION['frozen_days'] = $frozen_days;

                                            $user_upd_arr = array();
                                            $notifies = array('notify_new_trade_note', 'notify_live_broadcasting_start', 'notify_new_archive_video', 'notify_new_trade_idea', 'notify_question_reply', 'notify_new_post_in_blog');
                                            foreach ($notifies as $notify) {
                                                $tmp = notify_decode($user[0]->$notify);
                                                $tmp['sms'] = 0;
                                                $user_upd_arr[$notify] = notify_encode($tmp);
                                                $_SESSION[$notify] = $user_upd_arr[$notify];
                                            }
                                            $user_upd_arr['premium_to'] = $premium_to;
                                            $user_upd_arr['frozen_days'] = $frozen_days;

                                            $user = $this->User_model->update_user(logged_in(), $user_upd_arr);
                                            $res['msg'] = 'Аккаунт заморожен';
                                            $res['premium_to'] = date('Y-m-d', strtotime($_SESSION['premium_to']));
                                            unset($res['fail']);
                                        } else {
                                            $res['msg'] = 'Нельзя морозить аккаунт чаще одного раза на месяц';
                                        }
                                    } else {
                                        $res['msg'] = 'REGULAR аккаунты не морозим ;)';
                                    }
                                }
                            } else {
                                $res['msg'] = 'Пользователь не найден. Странно';
                            }

                            //send email
                        } break;
                    case 'settlement': {
                            $res['title'] = 'Change settlement';
                            $user = $this->User_model->get_user();
                            if ($_POST['value'] == 0 || $_POST['value'] == 1) {
                                $this->User_model->update_user(logged_in(), array('settlement' => abs($_POST['value'] - 1)));  // 0 or 1 only
                                $_SESSION['settlement'] = abs($_POST['value'] - 1);
                                $settlement = $this->config->item('settlement');
                                $res['current_settlement'] = $settlement[abs($_POST['value'] - 1)];
                                $res['set_settlement'] = $settlement[$_POST['value']];
                                $res['settlement'] = abs($_POST['value'] - 1);
                                $res['msg'] = 'Settlement changed sucessfully';
                                unset($res['fail']);
                            } else {
                                $res['msg'] = 'wrong value';
                            }
                        } break;
                    case 'automatic_payment': {
                            $res['title'] = 'Change automatic payment';
                            $user = $this->User_model->get_user();
                            if ($_POST['value'] == 0 || $_POST['value'] == 1) {
                                // приходить поточний стан
                                if ($_POST['value'] == 0 && $_SESSION['premium_to'] < date('Y-m-d H:i:s')) {
                                    $res['msg'] = 'Automatic payment allowed only to PREMIUM ACCOUNT';
                                } else {
                                    $this->User_model->update_user(logged_in(), array('automatic_payment' => abs($_POST['value'] - 1)));  // 0 or 1 only
                                    $_SESSION['automatic_payment'] = abs($_POST['value'] - 1);
                                    $automatic_payment = $this->config->item('automatic_payment');
                                    $res['current_automatic_payment'] = ((abs($_POST['value'] - 1)) ? ('YES') : ('NO'));
                                    $res['set_automatic_payment'] = $automatic_payment[$_POST['value']];
                                    $res['automatic_payment'] = abs($_POST['value'] - 1);
                                    $res['msg'] = 'Automatic payment changed sucessfully';
                                    unset($res['fail']);
                                }
                            } else {
                                $res['msg'] = 'wrong value';
                            }
                        } break;
                    case 'account_type': {
                            $res['title'] = 'Change account type';
                            $user = $this->User_model->get_user();
                            if ($_POST['value'] == 'PREMIUM' || $_POST['value'] == 'REGULAR') {
                                if ($_POST['value'] == 'PREMIUM') {
                                    $set_to = 'REGULAR';
                                    $_SESSION['premium_to'] = date('Y-m-d H:i:s');
                                    $res['msg'] = "Поздравляем! У Вас теперь REGULAR ACCOUNT";
                                    $notification_types = $this->config->item('notification_types');
                                    $fields = '';
                                    foreach ($notification_types as $notification_type => $notification_type_human) {
                                        $notify = notify_decode($user[0]->$notification_type);
                                        $notify['sms'] = 0;
                                        $fields[$notification_type] = notify_encode($notify);
                                        $_SESSION[$notification_type] = notify_encode($notify);
                                    }
                                    $fields['premium_to'] = $_SESSION['premium_to'];
                                    $this->User_model->update_user(logged_in(), $fields);
                                    unset($res['fail']);
                                } else {
                                    $set_to = 'PREMIUM';
                                    if (isset($user) && is_array($user) && count($user)) {
                                        if ($user[0]->frozen_days > 0 && $user[0]->frozen_days < 366) {
                                            $res['msg'] = 'Ваш аккаунт заморожен. Разморозте его, и активируются PREMIUM возможности'; // . print_r($user, TRUE);
                                        } else {
                                            if (!$user[0]->premium_to || $user[0]->premium_to < date('Y-m-d H:i:s')) {
                                                $settlement = $this->config->item('settlement');
                                                if ($user[0]->cash >= $settlement['cost'][$user[0]->settlement]) {
                                                    $cash = $user[0]->cash - $settlement['cost'][$user[0]->settlement];
                                                    $premium_to = date('Y-m-d', strtotime($settlement['period'][$user[0]->settlement]));
                                                    $_SESSION['premium_to'] = $premium_to;
                                                    $_SESSION['cash'] = $cash;
                                                    $this->User_model->update_user(logged_in(), array('premium_to' => $premium_to, 'cash' => $cash));
                                                    $res['msg'] = "Поздравляем! У Вас теперь PREMIUM ACCOUNT до " . $_SESSION['premium_to'];
                                                    unset($res['fail']);
                                                } else {
                                                    $res['msg'] = 'Low cash. Please refill the account';
                                                }
                                            } else {
                                                $res['msg'] = 'Your account already PREMIUM. Nothing changed'; // . print_r($user, TRUE);
                                            }
                                        }
                                    }
                                }

                                //$this->User_model->update_user(logged_in(), array('automatic_payment' => abs($_POST['value'] - 1)));  // 0 or 1 only
                                //$_SESSION['automatic_payment'] = abs($_POST['value'] - 1);
                                //$automatic_payment = $this->config->item('automatic_payment');
                                $res['current_account_type'] = $set_to;
                                $res['set_account_type'] = $_POST['value'];
                                $res['account_type'] = $set_to;
                                $res['cash'] = $_SESSION['cash'];
                                $res['premium_to'] = date('Y-m-d', strtotime($_SESSION['premium_to']));
                            } else {
                                $res['msg'] = 'wrong value';
                            }
                        } break;
                }
            } else {
                $res['msg'] = 'wrong query';
            }
        } else {
            $res['msg'] = 'not authorised';
        }
        echo json_encode($res);
    }

    function recomendations() {
        if (logged_in()) {
            $this->load->model('Table_model', '', TRUE);
            if ($_POST['shortlong'] == 'short') {
                $shortlong = 0;
            } else {
                $shortlong = 1;
            }
            $data['ticker'] = $this->Table_model->get_ticker($_POST['ticker_id']);
            if (is_array($data['ticker']) && count($data['ticker'])) {
                $data['recomendations'] = $this->Table_model->last_recomendations_frontend($_POST['ticker_id'], $shortlong);
                $this->load->view('ticker_recomendations', $data);
            } else {
                echo 'Ticker not found';
            }
        } else {
            echo 'not authorised';
        }
    }

    function add_question() {
        if (logged_in()) {
            //if not banned!!?!???!?
            $user = $this->User_model->get_user(logged_in());
            if (is_array($user) && count($user) && $user[0]->banned_no_questions == 0) {
                if ($user[0]->premium_to > date('Y-m-d H:i:s')) {
                    $this->load->model('Log_model', '', TRUE);
                    $this->load->model('Question_model', '', TRUE);
                    $question_id = $this->Question_model->add_question($_POST['text']);
                    //$this->Log_model->add('question', array('action' => 'add', 'user_id' => logged_in(), 'question_id' => $question_id, 'text' => $_POST['text']));
                    echo json_encode(array());
                } else {
                    echo json_encode(array('msg' => 'Only premium users allowed to send questions', 'title' => 'Question', 'redirect' => '/page/accounts'));
                }
            } else {
                echo json_encode(array('msg' => 'You are banned from asking questions', 'title' => 'Question'));
            }
        } else {
            echo json_encode(array('msg' => 'not logged in', 'title' => 'Question'));
        }
    }

    /**
      Сохранение блога
     * */
    function save_blog() {
        $this->load->helper('blog');
        $data = json_decode($_POST['data']);
        $result = "";
        $sql = "SELECT `AUTO_INCREMENT` inc FROM `information_schema`.`TABLES` WHERE (`TABLE_NAME`='blog')";
        $last_id = $this->db->query($sql)->row()->inc;
        $npp = 0;
        foreach ($data as $value) {
            foreach ($value as $key => $block) {
                if ($key == "title") {
                    $title = $block;
                } elseif ($key == "file") {//Если это блок файлов
                    if (count($block) > 0) {
                        $result .= '<div class="images_block">';
                        foreach ($block as $key_img => $row_img) {//перебераем строки
                            if (count($block) > 0) {
                                $result .= '<div class="img_row">';
                                foreach ($row_img as $image) {//перебераем картинки в строках
                                    $result .= '<img src="/thumb.php?src=' . save_img($image->src, $last_id, $npp) . '&h=' . $image->height . '&w=' . $image->width . '&zc=1" />';
                                    $npp++;
                                    //print_r($image->height);
                                }
                                $result .="</div>";
                            }
                        }
                    }
                    $result .= '</div>';
                    //print_r($block);
                } elseif ($key == "video") {//Если это блок Видео
                    if (count($block) > 0) {
                        $result .= '<div class="video_row">';
                        foreach ($block as $video) {
                            $result .= '<iframe width="540" height="290" frameborder="0" allowfullscreen="" src="' . $video . '"></iframe>';
                        }
                    }
                    $result .= '</div>';
                } elseif ($key == "text") {//Если это блок текст
                    $result .= '<div class="text_block">';
                    $result .= $block;
                    $result .= '</div>';
                }
            }
        }
        $this->db->insert("blog", array('title' => $title, 'text' => $result, 'width' => $_POST['width'], 'preview' => $_POST['preview']));

        if ($_POST['preview'] == 0) {
            $this->load->model('Log_model', '', TRUE);
            $this->Log_model->add('blog', array('action' => 'add', 'blog_id' => $this->db->insert_id(), 'title' => $title), 1);
            notify_users_email_and_sms('notify_new_post_in_blog', 'MyTrade Blog: ' . $title, array('href' => '/blog'));
        }

        echo "Сохранено";
    }

    /**
      Удаление блога
     * */
    function delete_post($id) {
        $this->db->delete("blog", array("id" => $id));
        $dirname = $_SERVER['DOCUMENT_ROOT'] . '/upload/blog/' . $id;
        if (is_dir($dirname))
            $dir_handle = opendir($dirname);
        if (!$dir_handle)
            return false;
        while ($file = readdir($dir_handle)) {
            if ($file != "." && $file != "..") {
                if (!is_dir($dirname . "/" . $file))
                    unlink($dirname . "/" . $file);
                else
                    delete_directory($dirname . '/' . $file);
            }
        }
        closedir($dir_handle);
        rmdir($dirname);
        return true;
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */