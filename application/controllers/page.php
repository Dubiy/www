<?php
class Page extends CI_Controller {
  public function index($pageId_or_Alias = '', $open_in_chat = FALSE) {
    $this->load->model('Page_model', '', TRUE);
    
    if ($pageId_or_Alias == 'badbrowser') {
    	$data['page'][0]->title = 'Вы используете устаревший браузер';
    	$data['page'][0]->text = $this->load->view('badbrowser', '', true);
    } elseif($pageId_or_Alias == 'some_other_page') {

    } else {
    	$data['page'] = $this->Page_model->get_page($pageId_or_Alias);
    }



    if ($open_in_chat) {
        $data['relatedlinks'] = $this->Page_model->get_page('chatrelatedlinks');
        $this->load->view('chat/popup_header', $data);
        $this->load->view('chat/pages', $data);
        $this->load->view('chat/popup_footer', $data);
    } else {
        $data['relatedlinks'] = $this->Page_model->get_page('relatedlinks');
        $this->load->view('block/site_head', $data);
        $this->load->view('pages', $data);
        $this->load->view('block/site_foot', $data);
    }
  }
}
?>
