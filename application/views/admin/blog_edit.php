<script type="text/javascript" src="/js/ckeditor/ckeditor.js"></script>
<?php
  if (isset($post) && is_array($post) && count($post)) {
?>
  <div class="block_admin pages_list page_form_edit" post_id="<?php echo $post[0]->id; ?>">
    <form action="" method="POST" onsubmit="return save_post()">
      <div>
        <label>Заголовок:</label><input class="value title" type="text" name="title" value="<?php echo $post[0]->title; ?>">
      </div>
      <div>
        <label>Датавремя:</label><input class="value timestamp" type="text" name="timestamp" value="<?php echo $post[0]->timestamp; ?>">
      </div>
      <div>
        <label>Текст:</label><div class="textarea" contenteditable="true" id="show_editor_here"><?php echo $post[0]->text; ?></div>
      </div> 
<?php
  if ($post[0]->preview) {
?>  
      <div>
        <label for="publishPost" class="checkbox"><input type="checkbox" id="publishPost"> Опубликовать запись (после отправки формы, пользователи будут уведомлены, и этот пункт пропадет из формы)</label>
      </div>
<?php
  }
?>    
      <div>
        <input type="submit" value="Сохранить"> <a href="/blog">Отменить, и вернутся в блог</a>
      </div>
    </form>
  </div>
<?php
  } else {
?>
  <div class="block_admin">
    Пост не найден
  </div>
<?php
  }
?>  

<style type="text/css">
  .page_form_edit label {
    display: inline-block;
    width: 140px;
  }

  label.checkbox {
    width: auto;
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
  function save_post() {
    if (jQuery('#publishPost').length && jQuery('#publishPost').is(':checked')) {
      var publish = 1;
    } else {
      var publish = 0;
    }

    


    jQuery.post('/admin/blog/save', {'post_id': jQuery('.page_form_edit').attr('post_id'),
                                     'title': jQuery('.page_form_edit .title').val(),
                                     'timestamp': jQuery('.page_form_edit .timestamp').val(),
                                     'text': jQuery('#show_editor_here').html(),
                                     'publish': publish},
      function(data) {
        if (data != '') {
          alert(data);
        } else {
          window.location.reload();
        }
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
    var editor = CKEDITOR.inline( document.getElementById("show_editor_here") );  
  });
</script>