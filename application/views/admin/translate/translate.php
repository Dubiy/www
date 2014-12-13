<div class="block_admin translate">
<?php
  $res = '';

  if (isset($to_translate) && is_array($to_translate) && count($to_translate)) {
    // print_r($to_translate);
    // print_r($translations);

    $res .= '<form method="POST" action="">';
    $res .= 'Перевод строки: <pre>' . $to_translate[0]->line . '</pre>';
    // $languages = $this->config->item('languages');

    foreach ($languages as $lang) {
      $res .= '<div class="lang">
                 <label>' . $lang . '</label>
                 <textarea name="transl_' . $lang . '">' . ((isset($translations_tmp[$lang])) ? ($translations_tmp[$lang]->transl) : ('')) . '</textarea>
               </div>';
    }

    $res .= '<input type="hidden" name="action" value="save_translate">';
    $res .= '<input type="submit" value="Сохранить перевод">';
    $res .= '</form>';
  } else {
    $res .= 'Не найдено<br />';
  }
  echo $res;

?>
</div>

<style type="text/css">
  .translate pre {
    font-size: 15px;
    border-bottom: 1px dashed gray;
    background-color: #f7f7f7;
  }

  .translate label {
    display: block;
  }

  .translate label:before {
    content: 'Язык: ';
  }

  .translate textarea {
    display: block;
    width: 100%;
    padding: 0;
    height: 80px;
    margin-bottom: 15px;
  }

</style>