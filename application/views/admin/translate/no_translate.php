<?php
  $res = '';

  if (isset($no_translate) && is_array($no_translate) && count($no_translate)) {
    $res .= '<table id="no_translate_table" style="border-collapse: collapse;">';
    $res .= '<tr><th>Язык</th><th>Текст</th><th>Путь</th><th class="date_col">Дата</th></tr>';
    foreach ($no_translate as $record) {
      $res .= '<tr>';
        $res .= '<td>' . $record->lang . '</td>';
        $res .= '<td><a href="/admin/translate/translate/' . $record->translation_fail_id . '">' . $record->line . '</a></td>';
        $res .= '<td>' . $record->uri . '</td>';
        $res .= '<td title="' . date('H:i:s', strtotime($record->datetime)) . '">' . date('Y-m-d', strtotime($record->datetime)) . '</td>';
      $res .= '</tr>';
    }
    $res .= '</table>';
  } else {
    $res .= 'Нет записей<br />';
  }
  echo $res;

?>

<style type="text/css">
  #no_translate_table {
    border-collapse: collapse;
    width: 100%;
  }

  #no_translate_table, #no_translate_table th, #no_translate_table td {
    border: 1px solid #b3b3b3;
  }

  #no_translate_table th.date_col {
    width: 80px;
  }

</style>