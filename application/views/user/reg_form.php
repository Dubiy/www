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
      <label>Вік:</label>
      <select name="age">
<?php
        $res = '';
        for ($i = 5; $i < 100; $i++) {
          $res .= '<option value="' . $i . '">' . $i . '</option>';
        }
        echo $res;
?>
      </select>
    </div>
    <div>
      <label>Стать:</label>
      <select name="sex">
        <option value="1">М</option>
        <option value="2">Ж</option>
      </select>
    </div>
    <div>
      <input type="submit" value="Реєстрація">
    </div>

  </form>