<?php

class questionModel{

    public function getQuestion()
    {
        $this->db->order_by('id', 'desc');

        $query = $this->db->getQuestion('question', $num, $str);
        return $query->result_array();
    }
}