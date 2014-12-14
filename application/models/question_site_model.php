<?php

class Question_site_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }


    function get_questions($filter = 'all', $category = 'all', $age_start = 0, $age_stop = 0) {

        $allowed_filters = array('all', 'top', 'unanswered', 'psych');
        if (! in_array($filter, $allowed_filters)) {
            $filter = 'all';
        }
        $allowed_categories = array('all', 'parents', 'children', 'age', 'best');
        if (! in_array($category, $allowed_categories)) {
            $category = 'all';
        }
        $age_start = (int)$age_start;
        $age_stop = (int)$age_stop;
        if ($age_start > $age_stop) {
            $tmp = $age_start;
            $age_start = $age_stop;
            $age_stop = $tmp;
        }



        $WHERE = array();
        $ORDER = '';
        $in_select = false;

        switch ($filter) {
            case 'top': {
                $ORDER = "ORDER BY `questions`.`rating` DESC";
            } break;
            case 'unanswered': {
                $in_select = true;
            } break;
            case 'psych': {
                $WHERE[] = "`questions`.`psych` = '1'";
            } break;
            default: {
                // WTF
            } break;
        }

        switch ($category) {
            case 'parents': {
                $WHERE[] = "`questions`.`type` = '1'";
            } break;
            case 'children': {
                $WHERE[] = "`questions`.`type` = '0'";
            } break;
            case 'best': {
                $WHERE[] = "`questions`.`best` = '1'";
            } break;
            case 'age': {
                if ($age_start) {
                    $WHERE[] = "`users`.`age` >= '$age_start'";
                    if ($age_stop) {
                        $WHERE[] = "`users`.`age` <= '$age_stop'";
                    }
                }
            } break;
            default: {
                // WTF
            } break;
        }

        if (is_array($WHERE) && count($WHERE)) {
            $WHERE = 'WHERE (' . implode(') AND (', $WHERE) . ')';

        } else {
            $WHERE = '';
        }

        //(($in_select) ? ('') : (''))
        $sql = (($in_select) ? ("SELECT * FROM (") : ('')) .
            "SELECT `questions`.*, `users`.`age`, `users`.`account_type`, `users`.`sex`, `answers`.`answer_id`, COUNT(`answers`.`answer_id`) AS `answers_count` FROM `questions`
                LEFT JOIN `answers` ON `answers`.`question_id` = `questions`.question_id
                LEFT JOIN `users` ON `users`.`user_id` = `questions`.`user_id`
                $WHERE
                GROUP BY `questions`.`question_id`
                $ORDER " .
                (($in_select) ? (") AS `res_table` WHERE `answers_count` = '0'") : (''));
        return $this->db->query($sql)->result();


    }



    function get_question($question_id = '')
    {
        $this->db->select('questions.*, users.*')->from('questions')->where('questions.question_id', $question_id)->
        join('users', 'users.user_id = questions.user_id', 'left')->limit(1);
        $query = $this->db->get();
        return $query->result();
    }

    function add_question($array = '')
    {
        if (! logged_in()) {
            return false;
        }
        $record = array(
            'user_id' => logged_in(),
            'text' => $array['question'],
            'rating' => 0,
            'datetime' => date('Y-m-d H:i:s'),
            'psych' => ((isset($array['psych']) && $array['psych'] == 1) ? (1) : (0)),
            'type' => $array['type'],
            'best' => 0
        );
        $this->db->insert('questions', $record);
        return $this->db->insert_id();
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

    function unanswered_count()
    {
        $this->db->select('COUNT(*) as `count`')->from('questions')->where('(`questions`.`answer` IS NULL) OR (`questions`.`answer` = \'\')');
        $query = $this->db->get();
        $return = $query->result();
        return $return[0]->count;
    }

    function delete_question($question_id)
    {
        $this->db->delete('questions', array('questions.question_id' => $question_id));
    }

    function set_answer($question_id = '', $answer = '', $private = '0')
    {
        $this->db->update('questions', array('answer' => $answer, 'answer_datetime' => date('Y-m-d H:i:s'), 'private' => $private), array('question_id' => $question_id));
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