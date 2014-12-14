<?php

class Question_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function get_question($question_id = '')
    {
        $this->db->select('questions.*, users.*')->from('questions')->where('questions.question_id', $question_id)->
        join('users', 'users.user_id = questions.user_id', 'left')->limit(1);
        $query = $this->db->get();
        return $query->result();
    }

    function add_question($text)
    {
        $this->db->insert('questions', array('user_id' => logged_in(), 'datetime' => date('Y-m-d H:i:s'), 'text' => $text));
        send_sms($this->config->item('my_trade_phone_number'), 'Q: ' . mb_substr($text, 0, 67));
        // send_sms('+380937444376', 'Q: ' . mb_substr($text, 0, 67));
        return $this->db->insert_id();
    }
        /*public function get_questions($type = 'all'){
            return $type;
        }*/

    function get_questions($type = 'all_questions')
    {   //$type=$this->get_questions();
        switch($type){
        case "all_questions":
            $this->db->select('questions.*, users.*')->from('questions')->join('users', 'users.user_id = questions.user_id', 'left');
            $query = $this->db->get();
            return $query->result();
            break;
        case "no_answer":
            $sql = "SELECT questions.*, users.*, COUNT(`answers`.`answer_id`) AS `answers_count` FROM `questions`
LEFT JOIN `answers` ON `answers`.`question_id` = `questions`.question_id
LEFT JOIN `users` ON `users`.`user_id` = `questions`.user_id
GROUP BY `questions`.`question_id`";
            $arr = $this->db->query($sql)->result();
            if (is_array($arr) && count($arr)) {
                foreach ($arr as $key => $record) {
                    if ($arr[$key]->answers_count > 0) {
                        unset($arr[$key]);
                        //да, ми знаэмо що це лайно. проблема вирішена в файлі Question_site_model.php
                    }
                }
            }
            return $arr;

            break;
        case "answered":
            $sql = "SELECT questions.*, users.*, COUNT(`answers`.`answer_id`) AS `answers_count` FROM `questions`
LEFT JOIN `answers` ON `answers`.`question_id` = `questions`.question_id
LEFT JOIN `users` ON `users`.`user_id` = `questions`.user_id
GROUP BY `questions`.`question_id`";
            $arr = $this->db->query($sql)->result();
            if (is_array($arr) && count($arr)) {
                foreach ($arr as $key => $record) {
                    if ($arr[$key]->answers_count == 0) {
                        unset($arr[$key]);
                        //да, ми знаэмо що це лайно. проблема вирішена в файлі Question_site_model.php
                    }
                }
            }
            return $arr;

            break;
        case "psych":
            $this->db->select('questions.*, users.*')->from('questions')->join('users', 'users.user_id = questions.user_id', 'left')->where('psych','1');
            $query = $this->db->get();

            return $query->result();
            break;

        }



    }


    function get_user_questions($user_id = '0', $start_id = 0)
    {
        $this->db->select('questions.*, users.*')->
        from('questions')->
        join('users', 'users.user_id = questions.user_id', 'left')->
        //where('((`questions`.`answer` IS NOT NULL) AND (`questions`.`answer` != \'\') OR (`questions`.`user_id` = \'' . $user_id . '\')) AND (`questions`.`question_id` > ' . $start_id . ')')->
        where('(`questions`.`answer` IS NOT NULL) AND (`questions`.`answer` != \'\') AND ((`questions`.`private` = \'0\') OR (`questions`.`private` = \'1\') AND (`questions`.`user_id` = \'' . $user_id . '\')) AND (`questions`.`question_id` > ' . $start_id . ')')->
        order_by('questions.answer_datetime', 'desc')->
        limit(80);
        $query = $this->db->get();
        return $query->result();
    }

    function get_user_questions_nu($user_id = '0', $start_id = 0)
    {
        $start_id = (int)$start_id;
        $user_id = (int)$user_id;
        $sql = "SELECT `questions`.*, `author`.`user_id` AS author_user_id, `author`.`nickname` AS author_nickname, `author`.`photo` AS author_photo, `admin`.`user_id` AS admin_user_id, `admin`.`nickname` AS admin_nickname, `admin`.`photo` AS admin_photo
                FROM (`questions`)
                LEFT JOIN `users` AS author ON `author`.`user_id` = `questions`.`user_id`
                LEFT JOIN `users` AS admin ON `admin`.`user_id` = `questions`.`admin_id`
                WHERE (`questions`.`answer` IS NOT NULL) AND (`questions`.`answer` != '') AND ((`questions`.`private` = '0') OR (`questions`.`private` = '1') AND (`questions`.`user_id` = '$user_id')) AND (`questions`.`question_id` > '$start_id')
                ORDER BY `questions`.`answer_datetime` DESC
                LIMIT 20";
        return $this->db->query($sql)->result();
    }

    /*function unanswered_count()
    {
        $this->db->select('COUNT(*) as `count`')->from('questions')->where('(`questions`.`answer` IS NULL) OR (`questions`.`answer` = \'\')');
        $query = $this->db->get();
        $return = $query->result();
        return $return[0]->count;
    }*/

    function delete_question($question_id)
    {
        $this->db->delete('questions', array('questions.question_id' => $question_id));
    }

    function set_answer($question_id = '', $answer = '', $private = '0')
    {
        $this->db->update('questions', array('answer' => $answer, 'answer_datetime' => date('Y-m-d H:i:s'), 'private' => $private), array('question_id' => $question_id));
    }

    public function get_answer($question_id='')
    {
        // $this
    }

    function delete_no_answer()
    {
        $this->db->delete('questions', '(`questions`.`answer` IS NULL) OR (`questions`.`answer` = \'\')');
    }

    function delete_answered_questions_today_yesterday()
    {
        $sql = "DELETE FROM `questions` WHERE `answer` IS NOT NULL AND `answer` != '' AND (DATE(`answer_datetime`) = DATE('" . date('Y-m-d', strtotime('-1 day')) . "') OR DATE(`answer_datetime`) = DATE('" . date('Y-m-d', strtotime('-2 day')) . "'))";
        return $this->db->query($sql);
    }


}