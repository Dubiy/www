<div class="block_admin stylecustomizr_block">
<?php
    $properties = array();
    if (isset($block) && is_array($block)  && count($block)) {
      foreach ($block as $block_propery) {
        $properties[] = $block_propery->property;
      }
    }

    echo '<form method="POST" action="">
      <h3>' . ((isset($block) && is_array($block)  && count($block)) ? ('Настройки блока') : ('Новый блок')) . '</h3>

      <div>
        <label>Заголовок</label>
        <input type="text" name="title" value="' . ((isset($block[0]->title)) ? ($block[0]->title) : (''))  . '">
      </div>
      <div>
        <label>Путь CSS</label>
        <input type="text" name="path" value="' . ((isset($block[0]->path)) ? ($block[0]->path) : ('')) . '">
      </div>
      <div>
        <label>Свойства CSS</label>
        <input type="text" name="properties" value="' . implode(', ', $properties) . '">
      </div>
      <input type="submit" value="Сохранить">
      ' . ((isset($block) && is_array($block)  && count($block)) ? ('<a href="#" class="delete_block" path="' . $block[0]->path . '">Удалить</a>') : ('')) . '
      
    </form>';

    // echo '<pre>'. print_r($block, true) . '</pre>';
 

?>
</div>

<style type="text/css">
  .stylecustomizr_block form label {
    display: block;
  }

  .stylecustomizr_block form input[type=text] {
    display: block;
    width: 30%;
  }

  .stylecustomizr_block form div {
    margin-bottom: 10px;
  }
</style>

<script type="text/javascript">
  jQuery(document).ready(function() {
    jQuery('a.delete_block').click(function() {
      var path = jQuery(this).attr('path');
      if (confirm('Действительно удалить блок?')) {
        jQuery.post('/admin/stylecustomizr/delete_block', {'path': path}, function(data) {
          document.location = '/admin/stylecustomizr/blocks';
        })
      }
      return false;
    });

  });
  
</script>