<div id="container">
  <div class="activate_form_container reglogin_form_container">
    <label class="form_message scrambledWriter_login" for="login_form_password_input">
<?php
      $msg_tmp = '';
      $validation_errors = validation_errors();

      if ($validation_errors) {
        $msg_tmp = $validation_errors;
      }
      if (@$error != '')  {
        $msg_tmp .= $error;
      }      

      if (@$msg != '')  {
        $msg_tmp .= $msg;
      }

      if ($msg_tmp != '') {
        echo $msg_tmp;
      } else {
        echo '';
      }
?>  
    </label>
    <a href="/user/register" class="register">REGISTER</a> 
    <a href="/user/login" class="register">LOG IN</a>
  </div>
</div>
