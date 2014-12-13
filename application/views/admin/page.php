<script type="text/javascript" src="/js/ckeditor/ckeditor.js"></script>
<?php
  if (isset($page) && is_array($page) && count($page)) {
    $edit = TRUE;
  } else {
    $edit = FALSE;
  }
?>
  <div class="block_admin pages_list page_form_edit <?php echo (($edit) ? ('') : ('page_form_add')); ?>" <?php echo (($edit) ? ('page_id="' . $page[0]->page_id . '"') : ('')); ?>>
    <form action="" method="POST" onsubmit="return false;">
      <div>
        <label>Путь:<span style="float:right;">/page/</span></label><input class="value alias" type="text" name="alias" value="<?php echo (($edit) ? ($page[0]->alias) : ('somealias')); ?>">
      </div>
      <div>
        <label>Заголовок:</label><input class="value title" type="text" name="title" placeholder="Заголовок" value="<?php echo (($edit) ? ($page[0]->title) : ('')); ?>">
      </div>
      <div>
        <label>Текст:</label><div class="textarea" contenteditable="true" id="show_editor_here"><?php echo (($edit) ? ($page[0]->text) : ('текст...')); ?></div>
      </div>      
      <div>
<?php
        if ($edit) {
?>
          <input type="submit" value="Сохранить" onClick="save_page(this, 'edit');"> <input type="submit" value="Сохранить и продолжить" onClick="save_page(this, 'edit', true);"> <button class="page_delete_button" page_id="<?php echo $page[0]->page_id; ?>">Удалить</button> 
<?php
        } else {
?>
          <input type="submit" value="Добавить" onClick="save_page(this, 'add');"> <input type="submit" value="Добавить и продолжить" onClick="save_page(this, 'add', true);">
<?php
        }
?>



        <a href="/admin/pages">Отменить</a>
      </div>
    </form>
  </div>

<style type="text/css">
  .page_form_edit label {
    display: inline-block;
    width: 140px;
  }

  .page_form_edit input.value {
    width: 400px;
  }

  .page_form_edit .textarea {
    width: 730px;
    height: 650px;
    display: inline-block;
    border: 1px solid gray;
    outline: 0;
    overflow: auto;
  }
</style>
<script type="text/javascript">
var editor;
  function save_page(self, action, noredirect) {
    if (typeof noredirect == 'undefined') {
      var noredirect = false;
    }
    editor.destroy();
    var obj = {'action': action,
               'alias': jQuery('.page_form_edit .alias').val(),
               'title': jQuery('.page_form_edit .title').val(),
               'text': jQuery('#show_editor_here').html()};
    if (action == 'edit') {
      obj['page_id'] = jQuery('.page_form_edit').attr('page_id');
    }
    jQuery.post('/admin/page', obj,
      function(data) {
        if (data != '') {
          alert(data);
        } else {
          if ( ! noredirect) {
            window.location.href = '/admin/pages';            
          }
        }
        jQuery(self).animate({'backgroundColor': 'green'}, 500, function() {
          jQuery(self).removeAttr('style');
        });
        editor = CKEDITOR.inline( document.getElementById("show_editor_here") );  
      }
    );
    return false;
  }

  jQuery(document).ready(function() {
    jQuery('.page_form_edit .page_delete_button').click(function() {
      if (confirm('Действительно удалить эту страницу?')) {
        jQuery.post('/admin/page', {'action': 'delete', 'page_id': jQuery(this).attr('page_id')}, function(data) {
          if (data != '') {
            alert(data);
          } else {
            window.location.href = '/admin/pages';
          }
        });
      }
      return false;
    });


    CKEDITOR.disableAutoInline = true;
    editor = CKEDITOR.inline( document.getElementById("show_editor_here") );  

  });
</script>