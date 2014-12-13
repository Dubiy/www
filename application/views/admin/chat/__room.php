<div class="block_admin room">
<form method="POST" action="">
  <div class="field">  
    <label>Название рума</label>
    <input type="text" name="title" value="<?php echo htmlspecialchars($room[0]->title); ?>">
  </div>
  <input type="submit" value="Сохранить">
</form>
  <div class="moderators field">  
    <label>Модераторы рума</label>
<?php
    if (isset($moders) && is_array($moders) && count($moders)) {
      foreach ($moders as $moder) {
        echo '<span class="moder" moder_id="' . $moder->user_id . '">' . $moder->nickname . '</span> ';
      }
    } else {
      echo 'Нет модераторов';
    }
?>
  </div>


<?php
// print_r($room);
?>
</div>

<style type="text/css">
  .block_admin.room label {
    display: block;
  }

  .block_admin.room .field {
    border-bottom: 10px;
  }

  .moderators .moder {
    background-color: gray;
    border-radius: 4px;
    padding: 4px;
    margin-top: 4px;
    display: inline-block;
  }
</style>