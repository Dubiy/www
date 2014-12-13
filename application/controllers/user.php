<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User extends CI_Controller {

    public function index() {
        if (logged_in()) {
            redirect('/', 'location');
            exit;
        } else {
            redirect('/user/login', 'location');
        }
    }
    
    public function quick_search() {
        if (isset($_POST['str'])) {
            $res['users'] = $this->User_model->quick_search($_POST['str'], ((isset($_POST['trademates'])) ? ($_POST['trademates']) : ('')));
            echo json_encode($res);
        }
    }

    public function get_user() {
        if (isset($_POST['user_id'])) {
            $user = $this->User_model->get_user($_POST['user_id']);
            if (is_array($user) && count($user)) {
                $data['user'] = array(
                    'user_id' => $user[0]->user_id,
                    'nickname' => $user[0]->nickname,
                    'premium_to' => $user[0]->premium_to,
                    'account_type' => (($user[0]->premium_to > date('Y-m-d H:i:s')) ? ('PREMIUM') : ('REGULAR')),
                    'last_activity' => $user[0]->last_activity,
                    'likes' => $user[0]->likes,
                    'rating' => $user[0]->rating,
                    'trading_experiance' => (($user[0]->trading_experiance != '0000-00-00 00:00:00') ? (time_since($user[0]->trading_experiance)) : ('NONE')),
                    'markets' => (($user[0]->markets) ? ($user[0]->markets) : ('NONE')),
                    'trading_style' => (($user[0]->trading_style) ? ($user[0]->trading_style) : ('NONE')),
                    'status' => $user[0]->status,
                    'photo' => $user[0]->photo,
                    'trademate' => $user[0]->trademate,
                    'feed' => $user[0]->feed,
                    'alerts' => $user[0]->alerts,
                    'disable_pm' => $user[0]->disable_pm,
                    'harassing' => $user[0]->harassing,
                    'ignore' => $user[0]->ignore,
                );
                $this->load->model('Prognose_model', '', TRUE);
                $data['prognoses'] = $this->Prognose_model->get_prognoses($_POST['user_id']);
                $data['prognoses_count_archive'] = $this->Prognose_model->get_prognoses($_POST['user_id'], 0);
                if (is_array($data['prognoses_count_archive']) && count($data['prognoses_count_archive'])) {
                    $data['prognoses_count_archive'] = count($data['prognoses_count_archive']);
                } else {
                    $data['prognoses_count_archive'] = 0;
                }
                $data['alerts_count'] = $this->Prognose_model->alerts_count();

                //GET PROGNOSE
                //GET RELATIONSHIP
                //GET BLOCK STATUS

                echo json_encode($data);
            } else {
                echo '._.'; // Не знайдений
            }
        } else {
            echo 'o_O';
        }
    }

    function prompt_login() {
        $data = array();
        $this->load->view('chat/popup_header', $data);
        
        if (logged_in()) {

        }

        if (isset($_POST['password'])) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('password', 'Password', 'required');
            // ну ніфіга ж собі форма авторизації)))
            if ($this->form_validation->run()) {
                $data['user'] = $this->User_model->get_user_by_password(md5($_POST['password']));
                if (is_array($data['user']) && count($data['user'])) {
                    $status = login($data['user'][0]);
                    if ($status === TRUE) {
                        $data['msg'] = t('SUCCESSFUL AUTH').'<script>parent.document.location = "/chat"</script>';
                        if (isset($_GET['r'])) {
                            // redirect($_GET['r'], 'location');
                        } else {
                            // redirect('/', 'location');
                        }
                    } else {
                        $data['msg'] = $status;
                    }
                } else {
                    $data['msg'] = t('WRONG PASSWORD');
                    $data['no_scrambledWriter_on_popup_message'] = 1;
                }
            }
        }


        $this->load->view('chat/prompt_login', $data);
        
        $this->load->view('chat/popup_footer', $data);
    }



    public function seng_msg() {
        $this->load->model('Message_model', '', TRUE);
        $data = '';
        if (isset($_POST['ajax'])) {
            //вибираю юзера, і дивлюсь, чи можна йому писать
            $user_permissions = $this->User_model->get_user_permissions($_POST['user_id']);
            if (is_array($user_permissions) && count($user_permissions) && $user_permissions[0]->disable_pm) {
                $data['msg'] = t('User block personal messages from you');
            } else {
                //ok
                $text = nl2br(strip_tags($_POST['text']));
                $data['msg_id'] = $this->Message_model->add_msg($_POST['user_id'], $_POST['form_id'], $_POST['text'], $_POST['meme_id']);
            }          
        }
        echo json_encode($data);
    }

    public function get_msgs() {
        $this->load->model('Message_model', '', TRUE);
        $data = '';
        if (isset($_POST['auth_user_id'])) {
            if ($_POST['auth_user_id'] != logged_in()) {
                echo "Привет! Я, разработчик этого сайта, очень рад что им кто-то интерисуется.\nСудя по всему, сейчас ты пытаешся найти дыры в коде. Если что-то найдешь, большая просьба, напиши мне на email: netzver@gmail.com\nСпасибо!\n(и даже если не найдешь ничего, всеравно напиши мне, ведь неспроста читаешь этот текст :-)";
                return;
            }
            if (isset($_POST['user_id'])) {
                $data['msgs'] = $this->Message_model->get_msgs($_POST['user_id'], ((isset($_POST['search'])) ? ($_POST['search']) : ('')));
                // $data['blocked'] = rand(0, 1);
                $permissions = $this->User_model->get_user_permissions($_POST['user_id']);
                if (is_array($permissions) && count($permissions)) {
                    $data['blocked'] = $permissions[0]->disable_pm;
                } else {
                    $data['blocked'] = 0;
                }
                $data['unread_msgs'] = $this->Message_model->unread_count();
            } else {
                $data['error'] = t('Missing some variable');
            }
        }
        echo json_encode($data);
    }

    public function get_friends() {
        if (isset($_POST['ajax'])) {
            if (isset($_POST['mode']) && $_POST['mode'] == 'trademates') {
                $this->load->model('Prognose_model', '', TRUE);
                $data['alerts_count'] = $this->Prognose_model->alerts_count();
                $data['users'] = $this->User_model->get_friends('trademates', $_POST['skip'], $_POST['filter']);
            } else {
                $data['users'] = $this->User_model->get_friends($_POST['filter'], $_POST['skip']);
                $this->load->model('Message_model', '', TRUE);
                $data['unread_msgs'] = $this->Message_model->unread_count();
            }
            echo json_encode($data);
        }
    }

    public function save_profile() {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'set_status': {
                        $this->User_model->update_user(logged_in(), array('status' => strip_tags(htmlspecialchars_decode($_POST['status']))));
                } break;
                case 'nickname': {
                        $this->User_model->update_user(logged_in(), array('nickname' => strip_tags(htmlspecialchars_decode($_POST['nickname']))));
                } break;
                case 'language': {
                    if (in_array($_POST['language'], $this->config->item('languages'))) {
                        $this->User_model->update_user(logged_in(), array('language' => $_POST['language']));
                        $_SESSION['language'] = $_POST['language'];
                    }
                } break;
                case 'trading_style':
                case 'markets':{
                        $this->User_model->update_user(logged_in(), array($_POST['action'] => strip_tags(htmlspecialchars_decode($_POST['value']))));
                } break;

                case 'trading_experiance': {
                    if (strtotime($_POST['trading_experiance']) < strtotime(date('Y-m-d H:i:s'))) {
                        $this->User_model->update_user(logged_in(), array('trading_experiance' => date('Y-m-d H:i:s', strtotime($_POST['trading_experiance']))));
                        echo time_since(date('Y-m-d H:i:s', strtotime($_POST['trading_experiance'])));
                    }
                } break;

                case 'set_relationship': {
                    $this->User_model->set_relationship($_POST['friend_id'], $_POST['type'], $_POST['set_status']);
                } break;
                case 'unlink_site': {
                    if (in_array($_POST['socnetwork'], array('tw', 'fb', 'vk', 'gplus'))) {
                        if ($_POST['socnetwork'] == 'gplus') {
                            $_POST['socnetwork'] = 'g';
                        }
                        $this->User_model->update_user(logged_in(), array($_POST['socnetwork'] . '_profileid' => '', $_POST['socnetwork'] . '_username' => ''));
                    } else {
                        echo 'hack you, facker)';
                    }

                } break;
            }
        }
    }

    public function profile($user_id = 0, $action = '') {
        $user_id = (int)$user_id;
        $data['avatars_upload_path'] = $this->config->item('avatars_upload_path');
        $data['avatars_upload_path_thumbs'] = $this->config->item('avatars_upload_path_thumbs');
        if ($user_id == 0) {
            $user_id = logged_in();
        }
        $data['user'] = $this->User_model->get_user($user_id);
        switch ($action) {
            case 'avatar': {
                if (is_array($data['user']) && count($data['user'])) {
                    $this->load->view('chat/popup_header', $data);
                    $this->load->view('chat/profile_avatar', $data);
                    $this->load->view('chat/popup_footer', $data);
                }
            } break;
            case 'edit_avatar': {
                if (is_array($data['user']) && count($data['user']) && $user_id == logged_in()) {
                    $this->load->helper('chat'); //тут функція create_thumb()
                    $path = $this->config->item('avatars_upload_path');
                    $path_thumbs = $this->config->item('avatars_upload_path_thumbs');
                    $path_thumbs_big = $this->config->item('avatars_upload_path_thumbs_big');

                    if (isset($_POST['action']) && $_POST['action'] == 'rethumb') {
                        print_r($_POST);
                        $filename = $data['user'][0]->photo;
                        create_thumb($path, $filename, $path_thumbs, $filename, 49, 49, false, $_POST['x1'], $_POST['y1'], $_POST['width'], $_POST['height']);
                        create_thumb($path, $filename, $path_thumbs_big, $filename, 133, 133, false, $_POST['x1'], $_POST['y1'], $_POST['width'], $_POST['height']);
                    }


                    if (isset($_POST['action']) && $_POST['action'] == 'update_avatar') {

                        $valid_formats = array("jpg", "png", "jpeg");
                        if (isset($_POST['url']) && $_POST['url'] != '') {
                            $ext = pathinfo($_POST['url'], PATHINFO_EXTENSION);
                            if (in_array(strtolower($ext), $valid_formats)) {
                                $filename = 'uid_' . logged_in() . '_' . date('Y-m-d_H-i-s') . '.' . $ext;
                                @file_put_contents($path . $filename, file_get_contents($_POST['url']));
                                if (create_thumb($path, $filename, $path_thumbs, $filename, 49, 49)) {
                                    $this->User_model->update_photo(logged_in(), $filename);
                                    $data['user'] = $this->User_model->get_user($user_id);
                                    $data['thumb_url'] = '/' . $path_thumbs . $filename;
                                    $data['filename'] = $filename;
                                    $data['msg'] = 'ok';
                                    echo '<script>document.domain = document.domain;</script>';
                                } else {
                                    $data['error'] = t('Something went wrong');
                                }
                            } else {
                                $data['error'] = t('Wrong image format');
                            }
                        } else {
                            $name = $_FILES['photoimg']['name'];
                            $filesize = $_FILES['photoimg']['size'];
                            $ext = pathinfo($name, PATHINFO_EXTENSION);
                            if ($name != '') {
                                if (in_array(strtolower($ext), $valid_formats)) {
                                    $tmp = $_FILES['photoimg']['tmp_name'];
                                    $filename = 'uid_' . logged_in() . '_' . date('Y-m-d_H-i-s') . '.' . $ext;
                                    if (move_uploaded_file($tmp, $path . $filename)) {
                                        if (create_thumb($path, $filename, $path_thumbs, $filename, 49, 49)) {
                                            $this->User_model->update_photo(logged_in(), $filename);
                                            $data['user'] = $this->User_model->get_user($user_id);
                                            $data['thumb_url'] = '/' . $path_thumbs . $filename;
                                            $data['filename'] = $filename;
                                            $data['msg'] = 'ok';
                                            echo '<script>document.domain = document.domain;</script>';
                                        } else {
                                            $data['error'] = t('Something went wrong');
                                        }
                                    } else {
                                        $data['error'] = t('image upload failed :(');
                                    }
                                } else {
                                    $data['error'] = t('Wrong image format');
                                }
                            } else {
                                $data['error'] = t('Empty image');
                            }
                        }


                        // echo json_encode($data);
                    }

                    $data['avatars_upload_path'] = $this->config->item('avatars_upload_path');
                    $this->load->view('chat/popup_header', $data);
                    $this->load->view('chat/edit_avatar', $data);
                    $this->load->view('chat/popup_footer', $data);
                }
            } break;
            default: {
                $this->load->model('Attachment_model', '', TRUE);
                $data['total_upload_size'] = $this->Attachment_model->user_info(logged_in());
                if (is_array($data['user']) && count($data['user']) && $user_id == logged_in()) {
                    $data['path_thumbs'] = $this->config->item('avatars_upload_path_thumbs');
                    $this->load->view('chat/popup_header', $data);
                    $this->load->view('chat/edit_profile', $data);
                    $this->load->view('chat/popup_footer', $data);
                }
            } break;
        }
        
    }



    public function attach_fb() {
        if (isset($_GET['code'])) {
            //echo $_GET['code'];

            $ch = curl_init();
            $go_url = 'https://graph.facebook.com/oauth/access_token?client_id=' . $this->config->item('fb_client_id') . '&redirect_uri=' . $this->config->item('fb_redirect_uri_attach') . '&client_secret=' . $this->config->item('fb_app_secret') . '&code=' . $_GET['code'];
            curl_setopt($ch, CURLOPT_URL, $go_url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($ch, CURLOPT_USERAGENT, 'PHP Bot (https://graph.facebook.com)');
            $data = curl_exec($ch);
            //exit;
            $split_parameters = explode('&', $data);
            for ($i = 0; $i < count($split_parameters); $i++) {
                $final_split = explode('=', $split_parameters[$i]);
                $split_complete[$final_split[0]] = $final_split[1];
            }

            //https://graph.facebook.com/me?access_token={access_token}

            $go_url = 'https://graph.facebook.com/me?fields=id,name,email&access_token=' . $split_complete['access_token'] . '&scope=email';
            curl_setopt($ch, CURLOPT_URL, $go_url);
            $user_info = curl_exec($ch);
            curl_close($ch);
            $user_info = json_decode($user_info);
            
            $arr = array('fb_profileid' => $user_info->id, 'fb_username' => $user_info->name);
            $this->User_model->update_user(logged_in(), $arr);
            redirect('/chat#u=/user/profile', 'location');
            exit;
        } elseif (isset($_GET['error'])) {
            echo $_GET['error'] . ': ' . $_GET['error_description'];
        } else {
            echo t('wrong request');
        }        
    }




    public function attach_vk() {
        if (isset($_GET['code'])) {
            $ch = curl_init();
            $go_url = 'https://api.vk.com/oauth/access_token?client_id=' . $this->config->item('vk_client_id') . '&client_secret=' . $this->config->item('vk_privatekey') . '&code=' . $_GET['code'] . '&redirect_uri=' . $this->config->item('vk_redirect_uri_attach') . '&';
            curl_setopt($ch, CURLOPT_URL, $go_url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($ch, CURLOPT_USERAGENT, 'PHP Bot (https://oauth.vk.com)');
            $data = curl_exec($ch);
            $data = json_decode($data);

            $go_url = 'https://api.vk.com/method/getProfiles?uid=' . $data->user_id . '&access_token=' . $data->access_token;
            curl_setopt($ch, CURLOPT_URL, $go_url);
            $user_info = curl_exec($ch);
            curl_close($ch);
            $user_info = json_decode($user_info);

            $arr = array('vk_profileid' => $user_info->response[0]->uid, 'vk_username' => $user_info->response[0]->first_name . ' ' . $user_info->response[0]->last_name);
            $this->User_model->update_user(logged_in(), $arr);
            redirect('/chat#u=/user/profile', 'location');
            exit;

        } elseif (isset($_GET['error'])) {
            echo $_GET['error'] . ': ' . $_GET['error_description'];
        } else {
            echo t('wrong request');
        }
    }

    public function attach_tw() {
        $auth = array_key_exists('auth', $_GET) ? true : false;
        $denied = array_key_exists('denied', $_GET) ? true : false;

        if ($auth && !$denied) {
            //include 'TwitterAuth.php';
            $this->load->helper('twitter_auth');
            $TWAuth = new TwitterAuth($this->config->item('twitter_consumer_key'), $this->config->item('twitter_consumer_secret'), $this->config->item('twitter_url_callback'));
            //$TWAuth->text_support(true);

            $oauth_token = array_key_exists('oauth_token', $_GET) ? $_GET['oauth_token'] : false;
            $oauth_verifier = array_key_exists('oauth_verifier', $_GET) ? $_GET['oauth_verifier'] : false;


            if (!$oauth_token && !$oauth_verifier) {
                $TWAuth->request_token();
                $TWAuth->authorize();
            } else {
                // access_token и user_id
                $TWAuth->access_token($oauth_token, $oauth_verifier);

                // JSON-версия
                $user_data = $TWAuth->user_data();
                $user_data = json_decode($user_data);

                $arr = array('tw_profileid' => $user_data->id, 'tw_username' => $user_data->name);
                $this->User_model->update_user(logged_in(), $arr);
                redirect('/chat#u=/user/profile', 'location');
                exit;
            }
        } else {
            if ($denied) {
                //redirect('/user/profile?error=twitter', 'location');
                echo '<p><strong>Было отказано в доверии приложению</strong></p>';
            }
        }
    }

    public function vklogin() {
        if (isset($_GET['code'])) {
            $ch = curl_init();
            $go_url = 'https://api.vk.com/oauth/access_token?client_id=' . $this->config->item('vk_client_id') . '&client_secret=' . $this->config->item('vk_privatekey') . '&code=' . $_GET['code'] . '&redirect_uri=' . $this->config->item('vk_redirect_uri') . '&';
            curl_setopt($ch, CURLOPT_URL, $go_url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($ch, CURLOPT_USERAGENT, 'PHP Bot (https://oauth.vk.com)');
            $data = curl_exec($ch);
            $data = json_decode($data);

            $go_url = 'https://api.vk.com/method/getProfiles?uid=' . $data->user_id . '&access_token=' . $data->access_token;
            curl_setopt($ch, CURLOPT_URL, $go_url);
            $user_info = curl_exec($ch);
            curl_close($ch);
            $user_info = json_decode($user_info);

            $user_vk = $this->User_model->get_user_by_vkid($data->user_id);
            if (isset($user_vk) && is_array($user_vk) && count($user_vk)) {
                $status = login($user_vk[0]);
                if ($status === TRUE) {
                    redirect('/', 'location');
                } else {
                    redirect('/user/login?msg=' . $status, 'location');
                }
            } else {
                redirect('/user/register?vkname=' . $user_info->response[0]->first_name . ' ' . $user_info->response[0]->last_name . '&vkid=' . $data->user_id, 'location');
            }
        } elseif (isset($_GET['error'])) {
            redirect('/user/register', 'location');
            echo $_GET['error'] . ': ' . $_GET['error_description'];
        } else {
            echo t('wrong request');
        }
    }

    public function fblogin() {
        if (isset($_GET['code'])) {
            //echo $_GET['code'];

            $ch = curl_init();
            $go_url = 'https://graph.facebook.com/oauth/access_token?client_id=' . $this->config->item('fb_client_id') . '&redirect_uri=' . $this->config->item('fb_redirect_uri') . '&client_secret=' . $this->config->item('fb_app_secret') . '&code=' . $_GET['code'];
            curl_setopt($ch, CURLOPT_URL, $go_url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($ch, CURLOPT_USERAGENT, 'PHP Bot (https://graph.facebook.com)');
            $data = curl_exec($ch);
            //exit;
            $split_parameters = explode('&', $data);
            for ($i = 0; $i < count($split_parameters); $i++) {
                $final_split = explode('=', $split_parameters[$i]);
                $split_complete[$final_split[0]] = $final_split[1];
            }
            //https://graph.facebook.com/me?access_token={access_token}

            $go_url = 'https://graph.facebook.com/me?fields=id,name,email&access_token=' . $split_complete['access_token'] . '&scope=email';
            curl_setopt($ch, CURLOPT_URL, $go_url);
            $user_info = curl_exec($ch);
            curl_close($ch);
            $user_info = json_decode($user_info);

            $user = $this->User_model->get_user_by_email($user_info->email);
            if (isset($user) && is_array($user) && count($user)) {
                $status = login($user[0]);
                $this->User_model->update_user($user[0]->user_id, array('fb_profileid' => $user_info->id));
                if ($status === TRUE) {
                    redirect('/', 'location');
                } else {
                    redirect('/user/login?msg=' . $status, 'location');
                }
            } else {
                $info = $this->User_model->register(array('email' => $user_info->email, 'nickname' => $user_info->name, 'phone' => '', 'fb_profileid' => $user_info->id));
                $this->User_model->activate($info['user_id']);
                $new_user = $this->User_model->get_user($info['user_id']);
                send_email($new_user[0]->email, $this->config->item('email_subject_activation_ok'), '', 'register', (array) $new_user[0]);
                $status = login($new_user[0]);
                if ($status === TRUE) {
                    redirect('/', 'location');
                } else {
                    redirect('/user/login?msg=' . $status, 'location');
                }
            }
        } elseif (isset($_GET['error'])) {
            redirect('/user/register', 'location');
            echo $_GET['error'] . ': ' . $_GET['error_description'];
        } else {
            echo t('wrong request');
        }
    }

    public function googlelogin($goto = '') {
        switch ($goto) {
            case 'gotochat': {
                $goto = '/chat/#u=/user/profile';
            } break;
            default: {
                $goto = '/';
            } break;
        }


        if (isset($_GET['code'])) {
            $arr = array(
                'code' => $_GET['code'],
                'client_id' => $this->config->item('google_client_id'),
                'client_secret' => $this->config->item('google_client_secret'),
                'redirect_uri' => $this->config->item('google_redirect_uris'),
                "grant_type" => "authorization_code"
            );
            $headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8'; 
            $ch = curl_init('https://accounts.google.com/o/oauth2/token');
            curl_setopt( $ch, CURLOPT_POST, 1);
            curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers); 
            curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query($arr));
            curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt( $ch, CURLOPT_HEADER, 0);
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
            $response = curl_exec( $ch );
            $response = json_decode($response);
 
            if (isset($response->access_token)) {
                $response = file_get_contents('https://www.googleapis.com/oauth2/v1/userinfo?access_token=' . $response->access_token);
                $response = json_decode($response);
                if (isset($response->email)) {
                    /*echo $response->id;
                    echo $response->email;
                    echo $response->name;
                    echo $response->email;
                    echo $response->picture;*/
                    
                    $user = $this->User_model->get_user_by_email($response->email);
                    if (isset($user) && is_array($user) && count($user)) {
                        $status = login($user[0]);
                        $this->User_model->update_user($user[0]->user_id, array('g_profileid' => $response->id, 'g_username' => $response->name));
                        if ($status === TRUE) {
                            redirect($goto, 'location');
                        } else {
                            redirect('/user/login?msg=' . $status, 'location');
                        }
                    } else {
                        $info = $this->User_model->register(array('email' => $response->email, 'nickname' => $response->name, 'phone' => '', 'g_profileid' => $response->id, 'g_username' => $response->name));
                        $this->User_model->activate($info['user_id']);
                        $new_user = $this->User_model->get_user($info['user_id']);
                        send_email($new_user[0]->email, $this->config->item('email_subject_activation_ok'), '', 'register', (array) $new_user[0]);
                        $status = login($new_user[0]);
                        if ($status === TRUE) {
                            redirect($goto, 'location');
                        } else {
                            redirect('/user/login?msg=' . $status, 'location');
                        }
                    }


                }
            } else {
                redirect($goto, 'location');
            }
        } else {
            redirect($goto, 'location');
        }

    }

    public function twlogin() {
        //echo 'tweettor';
        $auth = array_key_exists('auth', $_GET) ? true : false;
        $denied = array_key_exists('denied', $_GET) ? true : false;

        if ($auth && !$denied) {
            define('TWITTER_CONSUMER_KEY', 'k0hHHyvc5xpJRzbMu4Zng');
            define('TWITTER_CONSUMER_SECRET', 'PEMD06xzoj6OoDtwzsJAGYT40xfseyYGHYoACcYss');
            define('TWITTER_URL_CALLBACK', 'http://my-trade.pro/user/twlogin?auth=1');

            //include 'TwitterAuth.php';
            $this->load->helper('twitter_auth');

            $TWAuth = new TwitterAuth(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET, TWITTER_URL_CALLBACK);
            //$TWAuth->text_support(true);

            $oauth_token = array_key_exists('oauth_token', $_GET) ? $_GET['oauth_token'] : false;
            $oauth_verifier = array_key_exists('oauth_verifier', $_GET) ? $_GET['oauth_verifier'] : false;


            if (!$oauth_token && !$oauth_verifier) {
                $TWAuth->request_token();
                $TWAuth->authorize();
            } else {
                // access_token и user_id
                $TWAuth->access_token($oauth_token, $oauth_verifier);

                // JSON-версия
                $user_data = $TWAuth->user_data();
                $user_data = json_decode($user_data);

                echo '<pre>User data<br>';
                print_r($user_data);
                echo '</pre>';

                // XML-версия
                // $user_data = $TWAuth->user_data('xml');
            }
        } else {
            if ($denied) {
                echo '<p><strong>Было отказано в доверии приложению</strong></p>';
            }

            echo '<p><a href="twitter_auth.php?auth=1">Начать авторизацию через Твиттер</a></p>';
            echo '<p>Скачать архив: <a href="TwitterAuth.rar">TwitterAuth.rar</a></p>';
        }
    }


    public function login() {



        $data = array();
        if (!logged_in()) {
            if (isset($_GET['msg'])) {
                $data['msg'] = $_GET['msg'];
            }
            if (isset($_POST['password'])) {
                $this->load->library('form_validation');
                $this->form_validation->set_rules('password', 'Password', 'required');
                // ну ніфіга ж собі форма авторизації)))
                if ($this->form_validation->run()) {
                    $data['user'] = $this->User_model->get_user_by_password(md5($_POST['password']));
                    if (is_array($data['user']) && count($data['user'])) {
                        $status = login($data['user'][0]);
                        if ($status === TRUE) {
                            $data['msg'] = 'SUCCESSFUL AUTH';
                            if (isset($_GET['r'])) {
                                redirect($_GET['r'], 'location');
                            } else {
                                redirect('/', 'location');
                            }
                        } else {
                            $data['error'] = $status;
                        }
                    } else {
                        $data['error'] = '<span class="scrambledWriter">' . t('WRONG PASSWORD.') . '</span><br /><a href="/user/forgot" class="scrambledWriter">' . t('FORGOT YOUR PASSWORD?') . '</a>';
                        $data['no_scrambledWriter_on_popup_message'] = 1;
                    }
                }
            }

            
            $this->load->view('block/site_head', $data);
            $this->load->model('Page_model', '', TRUE);
            $this->load->view('user/login', $data);
            $this->load->view('block/site_foot', $data);
        
        } else {
            redirect('/', 'location');
        }
    }

    public function forgot($user_id = '', $code = '') {
        $data = array();
        if ($user_id != '' && $code != '') {
            $data['user'] = $this->User_model->get_user($user_id);
            if (is_array($data['user']) && count($data['user'])) {
                if (md5($data['user'][0]->password . $data['user'][0]->email) == $code) {
                    //ok
                    $new_password = $this->User_model->reset_password($user_id);
                    send_email($data['user'][0]->email, $this->config->item('email_subject_change_password'), '', 'change_password', array('password' => $new_password));

                    $data['msg'] = t('Password reseted, and sent to your e-mail');
                } else {
                    $data['error'] = t('BAD RESET LINK');
                }
            } else {
                $data['error'] = t('USER NOT FOUND');
            }
            $this->load->view('block/site_head', $data);
            $this->load->view('user/activate', $data);
            $this->load->view('block/site_foot', $data);
        } else {
            if (isset($_POST['email'])) {
                $data['user'] = $this->User_model->get_user_by_email($_POST['email']);
                if (is_array($data['user']) && count($data['user'])) {
                    $link = 'http://' . $_SERVER['HTTP_HOST'] . '/user/forgot/' . $data['user'][0]->user_id . '/' . md5($data['user'][0]->password . $data['user'][0]->email);
                    send_email($data['user'][0]->email, $this->config->item('email_subject_reset_password_link'), $link, 'reset_link', (array) $data['user'][0]);
                    $data['msg'] = t('Check your mailbox for password reset link');
                } else {
                    $data['error'] = t('USER NOT FOUND');
                }
            }
            $this->load->view('block/site_head', $data);
            $this->load->view('user/forgot', $data);
            $this->load->view('block/site_foot', $data);
        }
    }

    public function activate($user_id = '', $code = '') {
        $data = array();
        $data['user'] = $this->User_model->get_user($user_id);
        if (is_array($data['user']) && count($data['user'])) {
            $account_type = $this->config->item('account_type');
            if ($data['user'][0]->account_type == $account_type['registred']) {
                if (md5(md5($data['user'][0]->password)) == $code) {
                    $this->User_model->activate($user_id);
                    $data['msg'] = t('User activated. A message with the password sent to your e-mail');
                    send_email($data['user'][0]->email, $this->config->item('email_subject_activation_ok'), '', 'register', (array) $data['user'][0]);
                } else {
                    $data['error'] = t('Wrong activation code');
                }
            } else {
                $data['error'] = t('Code is already in use');
            }
        } else {
            $data['error'] = t('USER Not found');
        }

        $this->load->library('user_agent');
        if ($this->agent->is_mobile() && (!isset($_SESSION['no_mobile'])) || isset($_GET['mobile'])) {
            $this->load->view('mobile/block/site_head', $data);
            $this->load->view('mobile/user/activate', $data);
            $this->load->view('mobile/block/site_foot', $data);
        } else {
            $this->load->view('block/site_head', $data);
            $this->load->view('user/activate', $data);
            $this->load->view('block/site_foot', $data);
        }
    }

    public function register() {
        $data = array();
        if (!logged_in()) {
            if (isset($_GET['vkname']) && !isset($_POST['nickname'])) {
                $_POST['nickname'] = $_GET['vkname'];
                $data['msg'] = t('To proceed, enter your Email');
            }
            if (isset($_GET['vkid'])) {
                $_POST['vk_profileid'] = $_GET['vkid'];
            }
            if (isset($_POST['email'])) {
                $this->load->library('form_validation');
                $this->form_validation->set_rules('email', t('Email'), 'valid_email|callback__email_unique_check|required');
                $this->form_validation->set_rules('nickname', t('Nickname'));
                $this->form_validation->set_rules('phone', t('Phone'));
                if ($this->form_validation->run()) {
                    $user_info = $this->User_model->register($_POST);

                    //comment if activation required
                    $this->User_model->activate($user_info['user_id']);
                    $data['msg'] = t('User registered. A message with the password sent to your e-mail');
                    send_email($user_info['email'], $this->config->item('email_subject_activation_ok'), '', 'register', (array) $user_info);
                    $data['redirect'] = '/user/login';
                    $data['redirect_time'] = '10000'; //ms

                    /*
                      // uncomment if activation required
                      $data['msg'] = 'Successful registration. Sent to your email activation code';
                      $data['redirect'] = '/user/login';
                      $data['redirect_time'] = '10000'; //ms
                      send_email($user_info['email'], $this->config->item('email_subject_activation'), '', 'activate', $user_info);
                     */
                }
            }

            $this->load->library('user_agent');
            if ($this->agent->is_mobile() && (!isset($_SESSION['no_mobile'])) || isset($_GET['mobile'])) {
                $this->load->view('mobile/block/site_head', $data);
                $this->load->view('mobile/user/reg_form', $data);
                $this->load->view('mobile/block/site_foot', $data);
            } else {
                $this->load->view('block/site_head', $data);
                $this->load->view('user/reg_form', $data);
                $this->load->view('block/site_foot', $data);
            }
        } else {
            redirect('/', 'location');
        }
    }

    public function logout() {
        logout();
        redirect('/', 'location');
    }

    function _email_unique_check($str) {
        $data['user'] = $this->User_model->get_user_by_email($str);
        if (count($data['user']) == 0) {
            return TRUE;
        } else {
            $this->form_validation->set_message('_email_unique_check', t('User with that email address already exists'));
            return FALSE;
        }
    }

    function _nickname_unique_check($str) {
        $data['user'] = $this->User_model->get_user_by_nick($str);
        if (count($data['user']) == 0) {
            return TRUE;
        } else {
            $this->form_validation->set_message('_nickname_unique_check', t('User with such a nickname already exists'));
            return FALSE;
        }
    }

}