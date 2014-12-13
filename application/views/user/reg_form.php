<div id="container">
  <div class="reg_form_container reglogin_form_container">
    <form method="POST" action="">
      <label class="form_message scrambledWriter_login" for="login_form_email_input">ENTER YOU EMAIL AND NICK TO REGISTER</label>
     
      <div class="regform_inputs"><label for="login_form_email_input">Email: </label><input type="email" name="email" class="input" id="login_form_email_input" value="<?php echo ((isset($_POST['email'])) ? ($_POST['email']) : (''))?>"></div>
      <div class="regform_inputs"><label for="login_form_nickname_input">Nick:</label><input type="text" name="nickname" class="input" id="login_form_nickname_input" value="<?php echo ((isset($_POST['nickname'])) ? ($_POST['nickname']) : (''))?>"></div>
      <div class="regform_inputs"><label for="login_form_phone_input">Phone:</label><input type="text" name="phone" class="input" id="login_form_phone_input" value="<?php echo ((isset($_POST['phone'])) ? ($_POST['phone']) : (''))?>"></div>
      <a href="/user/login" class="register">BACK</a>
      <input type="submit" class="submit" value="REGISTER">
    </form>
    <div class="popup_message scrambledWriter">
<?php
      $validation_errors = validation_errors();
      if ($validation_errors) {
        echo $validation_errors;
      }
      if (@$error != '')  {
        echo $error;
      }      

      if (@$msg != '')  {
        echo $msg;
      }
?>  
    </div>        
<?php
  if (isset($redirect)) {
?>
    <script type="text/javascript">
      jQuery(document).ready(function() {
        setTimeout(function() {
          location.href = '<?php echo $redirect; ?>';
        }, <?php echo $redirect_time; ?>);
      });
    </script>
<?php
  }
?>    
  </div>
</div>