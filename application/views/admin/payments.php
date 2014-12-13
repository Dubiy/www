<?php
  if (isset($_GET['skip'])) {
    $skip = (int) $_GET['skip'];
  } else {
    $skip = 0;
  }

  $res = '';

  if (isset($payments) && is_array($payments) && count($payments)) {
    $res .= '<table id="payments_table" style="border-collapse: collapse;">';
    $res .= '<tr><th>Дата</th><th>Пользователь</th><th>Статус</th><th>Сумма</th><th>Инфо</th></tr>';
    foreach ($payments as $payment) {
      $res .= '<tr>';
        $res .= '<td>' . $payment->datetime . '</td>';
        $res .= '<td><a href="/admin/users/get_by_uid/' . $payment->user_id . '">' . $payment->nickname . '</a></td>';
        $res .= '<td>' . (($payment->payed) ? ('OK') : ('Fail')) . '</td>';
        $res .= '<td>' . $payment->sum . '</td>';
        $res .= '<td><pre>' . print_r(unserialize($payment->array), TRUE) . '</pre>&nbsp;</td>';
      $res .= '</tr>';
    }
    $res .= '</table>';
    $res .= '<div class="block_admin nav_bar">';
    $res .= '<a href="?skip=' . (($skip - $this->config->item('rooms_per_page') > 0) ? ($skip - $this->config->item('rooms_per_page')) : (0)) . '">← сюда</a> ';
    $res .= '<a href="?skip=' . ($skip + $this->config->item('rooms_per_page')) . '">туда →</a>';      
    $res .= '</div>';
  } else {
    $res .= 'Нет записей<br />';
    $res .= '<div class="block_admin nav_bar">';
    $res .= '<a href="?skip=' . (($skip - $this->config->item('rooms_per_page') > 0) ? ($skip - $this->config->item('rooms_per_page')) : (0)) . '">← сюда</a> ';
    $res .= '</div>';
  }
  echo $res;

?>

<style type="text/css">
  .block_admin.nav_bar {
    margin-top: 10px;
  }

  #payments_table {
    border-collapse: collapse;
    width: 100%;
  }

  #payments_table, #payments_table th, #payments_table td {
    border: 1px solid #b3b3b3;
  }
</style>