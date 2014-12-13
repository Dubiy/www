<?php
  if (isset($add_premium_info) && $add_premium_info != '') {
    echo '<div class="block_admin">' . $add_premium_info . '</div>';
  }

?>
<div class="block_admin">
  Продлить PREMIUM пользователям:
  <form method="POST" action="/admin/user">
    <input type="hidden" name="action" value="add_premium">
    <label>Star: </label>
    <select name="star">
      <option value="-1">All</option>
      <option value="0">Normal</option>
      <option value="1">STAR</option>
    </select><br />
    <label>Продлить на: </label>
    <input type="text" name="days" value="0"> дней<br />
    <input type="submit" value="Продлить">
  </form>

</div>

<style>
  label {
    display: inline-block;
    width: 100px;
  }

  input, select {
    width: 90px;
  }
</style>