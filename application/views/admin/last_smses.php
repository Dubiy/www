<div class="block_admin">
<?php
  if (isset($last_smses) && is_array($last_smses) && count($last_smses)) {
    $sms_response_code = $this->config->item('sms_response_code');
    echo '<b>Последние ' . count($last_smses) . ' sms-сообщений</b><br />';
    echo '<table class="last_smses">';
    echo '<tr>';
      echo '<th>Дата</th>';
      echo '<th>Номер</th>';
      echo '<th>Текст</th>';
      echo '<th>Статус</th>';
      echo '<th>sms_id</th>';
    echo '</tr>';
    foreach ($last_smses as $last_sms) {
      echo '<tr class="status_' . $last_sms->status . '">';
        echo '<td>' . date('H:i:s\<\b\r\/\>d.m.y', strtotime($last_sms->datetime)) . '</td>';
        echo '<td>' . $last_sms->number . '</td>';
        echo '<td>' . mb_substr($last_sms->text, 0, 50) . '...</td>';
        echo '<td title="' . $sms_response_code[$last_sms->status] . '">' . $last_sms->status . '</td>';
        echo '<td>' . $last_sms->sms_id . '</td>';
      echo '</tr>';
    }

    echo '</table>';
  } else {
    echo 'Нет последних СМС';
  }
?>
</div>
<style type="text/css">
  .last_smses {
    width: 100%;
  }

  .last_smses td, .last_smses th {
    text-align: center;
  }

  .last_smses .status_103 {
    background-color: #AAFFAA;
  }
</style>