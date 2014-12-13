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
    
    public function login() {

        $data = array();
        if (!logged_in()) {
            if (isset($_POST['email'], $_POST['password'])) {
                $this->load->library('form_validation');
                $this->form_validation->set_rules('password', 'Пароль', 'required');
                $this->form_validation->set_rules('email', 'Email', 'required');
                if ($this->form_validation->run()) {
                    $data['user'] = $this->User_model->get_user_by_email($_POST['email']);
                    if (is_array($data['user']) && count($data['user']) && $_POST['password'] == $data['user'][0]->password) {
                   
                        $status = login($data['user'][0]);
                        if ($status === TRUE) {
                            $data['msg'] = 'Успішний логін';
                            if (isset($_GET['r'])) {
                                redirect($_GET['r'], 'location');
                            } else {
                                redirect('/', 'location');
                            }
                        } else {
                            $data['error'] = $status;
                        }
                    } else {
                        $data['error'] = 'Невірний пароль';
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