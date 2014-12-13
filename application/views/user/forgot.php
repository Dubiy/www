<div id="container">
  <div class="reg_form_container reglogin_form_container restore_password">
    <form method="POST" action="">
      <label class="form_message scrambledWriter_login" for="login_form_email_input">ENTER YOU EMAIL</label>
     
      <div class="regform_inputs"><label for="login_form_email_input">Email: </label><input type="email" name="email" class="input" id="login_form_email_input" value="<?php echo ((isset($_POST['email'])) ? ($_POST['email']) : (''))?>"></div>
      <a href="/user/login" class="register">LOGIN</a>
      <input type="submit" class="submit" value="RESET PASSWORD">
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