<?
  //поиск румов????

?>
<div class="block_admin rooms">
  <form method="POST" action="">
    Выберите комнату в которую нужно перенести сообщения: <br />
    <select name="room_id">
<?php
      foreach ($rooms as $room_record) {
        echo '<option value="' . $room_record->room_id . '">' . $room_record->title . '</option>';
        
      }
?>
    </select>
    <br />
    После переноса сообщений, комната "<?echo @$room[0]->title; ?>" удалится<br />
    <input type="submit" value="Перенести">
  </form>
</div>