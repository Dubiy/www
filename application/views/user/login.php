<?php 
  $MSG = '';
  $validation_errors = validation_errors();
  if ($validation_errors) {
    $MSG .= $validation_errors;
  }
  if (@$error != '')  {
    $MSG .= $error;
  }

  if (@$msg != '')  {
    $MSG .= $msg;
  }
?>

<?php
    echo $MSG;
?>
  <div class="reg_login_form">
  <form method="POST" action="">
    <div>
      <label>Email:</label>
      <input type="email" name="email">
    </div>
    <div>
      <label>Пароль:</label>
      <input type="password" name="password">
    </div>
    
    <div>
      <input type="submit" class="submit btn btn-success" value="Вхoд">
    <div class="reg_link">
    
      <a href="/user/register" class="btn btn-primary submit">Регистрация</a>
    </div>
    </div>



  </form>
</div>