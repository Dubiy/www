<div class="block_admin deleted">
<?php 
  if (isset($deleted) &&  is_array($deleted) && count($deleted)) {
    foreach ($deleted as $del_msg) {
      // print_r($del_msg);
?>
      <div class="del_msg" deleted_post_id="<?php echo $del_msg->deleted_post_id; ?>">
        <div class="info">
          <a href="/chat#r=<?php echo $del_msg->user_author_nickname; ?>" class="room_title"><?php echo $del_msg->room_title; ?></a>
          <span class="rating"><span class="dislikes"><?php echo rating_dislikes($del_msg->post_rating - $del_msg->post_likes); ?></span> <span class="likes"><?php echo rating_likes($del_msg->post_likes); ?></span></span>
        </div>
        <div class="original">
          <a href="/admin/users/get_by_uid/<?php echo $del_msg->user_author_id; ?>" class="user_link" title="Автор"><?php echo $del_msg->user_author_nickname; ?></a>
          <span class="datetime time_since" title="<?php echo $del_msg->post_datetime; ?>" datetime="<?php echo $del_msg->post_datetime; ?>"><?php echo time_since($del_msg->post_datetime); ?></span>
          <br clear="both" />
          <?php echo $del_msg->original_text; ?>
        </div>
        <div class="modered">
          <a href="/admin/users/get_by_uid/<?php echo $del_msg->user_moder_id; ?>" class="user_link" title="Модератор"><?php echo $del_msg->user_moder_nickname; ?></a>
          <span class="datetime time_since" title="<?php echo $del_msg->delete_datetime; ?>" datetime="<?php echo $del_msg->delete_datetime; ?>"><?php echo time_since($del_msg->delete_datetime); ?></span>
          <br clear="both" />
          <?php echo $del_msg->post_text; ?>
        </div>
        <div class="controll_bar">
          <span class="controll lock" title="Запретить удаление, и вернуть исходное сообщение">&nbsp;</span>
          <span class="controll delete" title="Вернуть исходное сообщение">&nbsp;</span>
          <span class="controll accept" title="Подтвердить удаление">&nbsp;</span>
        </div>
      </div>
<?php
    }
  } else {
    echo 'Нет непроверенных удаленных сообщений';
  }

?>
</div>

<script>
  jQuery(document).ready(function() {
    jQuery('.del_msg .controll_bar .controll').click(function() {
      var self = this;
      var deleted_post_id = jQuery(self).closest('.del_msg').attr('deleted_post_id');
      var action = '';
      if (jQuery(self).hasClass('accept')) {
        action = 'accept';
      }
      if (jQuery(self).hasClass('delete')) {
        action = 'delete';
      }
      if (jQuery(self).hasClass('lock')) {
        action = 'lock';
      }
      jQuery.post('/admin/deleted', {'deleted_post_id': deleted_post_id, 'action': action}, function(data) {
        if (data != '') {
          alert(data);
        } else {
          var start_height = jQuery(self).closest('.del_msg').height();
          jQuery(self).closest('.del_msg').css('height', start_height + 'px').animate({'height': 0}, 500, function() {
            jQuery(this).remove();
          });
        }
      });

    });
  });
</script>