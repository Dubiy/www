<div class="block_admin translate">
<?php
  $res = '';

  if (isset($results) && is_array($results) && count($results)) {
    $res .= '<table id="no_translate_table" style="border-collapse: collapse;">';
    $res .= '<tr><th>Язык</th><th>Строка</th><th>Перевод</th></tr>';
    foreach ($results as $record) {
      $res .= '<tr>';
        $res .= '<td>' . $record->lang . '</td>';
        $res .= '<td><a href="/admin/translate/translate/' . $record->translation_id . '/update">' . $record->line . '</a></td>';
        $res .= '<td>' . $record->transl . '</td>';
      $res .= '</tr>';
    }
    $res .= '</table>';
  } else {
    $res .= 'Нет записей<br />';
  }
  echo $res;

?>






</div>

<style type="text/css">
  #no_translate_table {
    border-collapse: collapse;
    width: 100%;
  }

  #no_translate_table, #no_translate_table th, #no_translate_table td {
    border: 1px solid #b3b3b3;
  }
</style>