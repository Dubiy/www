<?php

class Page_model extends CI_Model {
  function __construct() {
    parent::__construct();
  }

  function get_page($page) {
    if ($page == intval($page) . '') {
      $this->db->select('*')->from('pages')->where('page_id', $page);
    } else {
      $this->db->select('*')->from('pages')->where('alias', $page);
    }
    $query = $this->db->get();
    return $query->result();
  }

  function get_pages() {
    return $this->db->select('*')->from('pages')->get()->result();
  }

  function update_page($page_id = '', $fields = '') {
    $this->db->update('pages', $fields, array('page_id' => $page_id));
  }

  function delete_page($page_id = '') {
    $this->db->delete('pages', array('page_id' => $page_id));
  }

  function add_page($fields = '') {
    $this->db->insert('pages', $fields);
  }
}