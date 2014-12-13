<?
  //поиск румов????

?>
<div class="block_admin rooms">
  <form method="GET" action="">
    <input type="text" name="search" placeholder="Введите название" value="<?php echo ((isset($_GET['search'])) ? (htmlspecialchars($_GET['search'])) : ('')); ?>">
    <input type="submit" value="Поиск">
    <a href="/admin/rooms/deleted/" class="menu_items menu_item_right">Запросы на удаление комнат</a>
    <a href="/admin/rooms/" class="menu_items menu_item_right">Все комнаты</a>
  </form>




</div>



<div class="block_admin rooms">
<?php
  if (isset($_GET['skip'])) {
    $skip = (int) $_GET['skip'];
  } else {
    $skip = 0;
  }

  if (isset($rooms) && is_array($rooms) && count($rooms)) {
    foreach ($rooms as $room) {
?>
      <div class="room room_id_<?php echo $room->room_id; ?>" room_id="<?php echo $room->room_id; ?>">
        <span class="title"><?php echo $room->title; ?></span>
        <span class="author" title="Автор"><a href="/admin/users/get_by_uid/<?php echo $room->author_id; ?>"><?php echo $room->nickname; ?></a></span>
        <span class="rating" title="Рейтинг"><?php echo rating($room->rating) . ' (' . rating_dislikes($room->rating - $room->likes) . ' ' . rating_likes($room->likes); ?>)</span>
        <span class="delete" title="Удалить">&nbsp;</span>
        <span class="edit" title="Войти в комнату">&nbsp;</span>
        <span class="move" title="Перенести сообщения в другую комнату">&nbsp;</span>
        <span class="fix <?php echo (($room->fixed) ? ('active') : ('')); ?>" title="Закрепить">&nbsp;</span>
      </div>
<?   
      // print_r($room);

    }
    if (isset($_GET['search']) && $_GET['search'] != '') {

    } else {
      echo '<a href="?skip=' . (($skip - $this->config->item('rooms_per_page') > 0) ? ($skip - $this->config->item('rooms_per_page')) : (0)) . '">← сюда</a> ';
      echo '<a href="?skip=' . ($skip + $this->config->item('rooms_per_page')) . '">туда →</a>';      
    }
  } else {
    if (isset($_GET['search']) && $_GET['search'] != '') {

    } else {
      echo 'Нет комнат<br /><a href="?skip=' . (($skip - $this->config->item('rooms_per_page') > 0) ? ($skip - $this->config->item('rooms_per_page')) : (0)) . '">← сюда</a> ';
    }
  }
?>
</div>

<script>
  jQuery(document).ready(function() {
    jQuery('.rooms .room .fix').click(function() {
      var self = this;
      var room_id = jQuery(self).closest('.room').attr('room_id');
      var status = jQuery(self).hasClass('active');
      jQuery.post('/admin/room/' + room_id + '/fix', {'status': status}, function(data) {
        if (status) {
          jQuery(self).removeClass('active');
        } else {
          jQuery(self).addClass('active');
        }
      });
    });

    jQuery('.rooms .room .move').click(function() {
      var self = this;
      var room_id = jQuery(self).closest('.room').attr('room_id');
      document.location.href = '/admin/movemsgs/' + room_id;
    });

    jQuery('.rooms .room .edit').click(function() {
      var room_id = jQuery(this).closest('.room').attr('room_id');
      document.location.href = '/chat#r=' + room_id;
    });

    jQuery('.rooms .room .delete').click(function() {
      var room_id = jQuery(this).closest('.room').attr('room_id');
      var title = jQuery(this).closest('.room').find('.title').html()
      if (confirm('Удалить комнату "' + title + '"?')) {
        if (confirm('Будут удалены все сообщения, комментарии, файлы. Продолжить?')) {
          if (confirm('Дальнейшее восстановление невозможно. Продолжить?')) {
            jQuery.post('/admin/room/' + room_id + '/delete', {'confirm': 'aga'}, function(data) {
              if (data != '') {
                alert(data);
              } else {
                jQuery('.rooms .room.room_id_' + room_id).animate({'height': 0}, 500, function() {
                  jQuery(this).remove();
                })
              }
            });
          }
        }
      }

      //  document.location.href = '/admin/room/' + room_id;
    });



  });
</script>