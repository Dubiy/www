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
      <input type="email" name="email" value="<? echo ((isset($_POST['email'])) ? ($_POST['email']) : ('')); ?>">
    </div>
    <div>
      <label>Пароль:</label>
      <input type="password" name="password">
    </div>
    <div>
      <label>Возраст:</label>
      <select name="age">
<?php
        $res = '';
        $age =  ((isset($_POST['age'])) ? ($_POST['age']) : (10)); 
        for ($i = 5; $i < 100; $i++) {
          $res .= '<option value="' . $i . '" ' . (($age == $i) ? ('selected="selected"') : ('')) . '>' . $i . '</option>';
        }
        echo $res;
?>
      </select>
    </div>
    <div>
      <label>Пол:</label>
      <select name="sex">
        <option value="1" <? echo ((isset($_POST['sex']) && $_POST['sex'] == 1) ? ('selected="selected"') : ('')) ?>>М</option>
        <option value="2" <? echo ((isset($_POST['sex']) && $_POST['sex'] == 2) ? ('selected="selected"') : ('')) ?>>Ж</option>
      </select>
    </div>
    <div>
      <input type="submit" class="submit btn btn-success" value="Регистрация">
        <div class="reg_link">
         <a href="/user/login" class="btn btn-primary submit">Логин</a>
        </div>
    </div>

  </form>
  </div>
