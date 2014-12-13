<!DOCTYPE HTML>
<html lang="en">
  <head>
    <meta charset=utf-8 />
    <title><?php echo strtoupper($_SERVER['HTTP_HOST']); ?> ADMINISTRATOR</title>
    <link type="text/css" rel="stylesheet" href="/css/admin.css?<?php echo $this->config->item('script_style_version'); ?>"></link>
    <link type="text/css" rel="stylesheet" href="/css/ui/jquery-ui-1.9.2.custom.css"></link>
    <link type="text/css" rel="stylesheet" href="/css/jquery.wysiwyg.css"></link>
    <script type="text/javascript" src="/js/jquery/jquery-1.8.3.min.js"></script>
    <script type="text/javascript" src="/js/jquery/jquery-ui-1.9.2.custom.min.js"></script>
    <script type="text/javascript" src="/js/admin.js?<?php echo $this->config->item('script_style_version'); ?>"></script>
  </head>
  <body>
    <div class="megawrapper">
      <div class="admin_main_menu">
        <ul>
          <li><a href="/admin/users/" <?php echo ((strpos($this->uri->segment(2), 'user') !== FALSE) ? ('class="active"') : ('')); ?>>Користувачі</a></li>
          <li><a href="/admin/questions/no_answer" <?php echo (($this->uri->segment(2) == 'questions') ? ('class="active"') : ('')); ?>>Питання</a></li>
          <li><a href="/admin/pages/" <?php echo (($this->uri->segment(2) == 'pages') ? ('class="active"') : ('')); ?>>Сторінки</a></li>
        </ul>
      </div>
      <div class="wrapper">
        <div class="header_admin">
          <span class="admin_title"><strong><a href="/"><?php echo strtoupper($_SERVER['HTTP_HOST']); ?></a>  ADMINISTRATOR</span>
          <span class="admin_module_title"><?php echo @$MODULE_TITLE; ?></span>
        </div>
<?php
        $validation_errors = validation_errors();
        if (isset($msg) && isset($_GET['sysmsg'])) {
          $msg .= '<br />' . $_GET['sysmsg'];
        } elseif (isset($_GET['sysmsg'])) {
          $msg = $_GET['sysmsg'];
        }
        if ($validation_errors) {
          echo '<div class="header_admin msg">' . $validation_errors . '</div>';
        }
        if (@$error != '')  {
          echo '<div class="header_admin msg">' . $error . '</div>';
        }
        if (@$msg != '')  {
          echo '<div class="header_admin msg">' . $msg . '</div>';
        }
        echo @$CONTENT;
?>
      </div>
    </div>
  </body>
</html>