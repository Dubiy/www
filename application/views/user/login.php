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
      <input type="submit" value="Вхід">
    </div>

  </form>