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