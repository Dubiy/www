<!DOCTYPE HTML>
<html lang="en">
  <head>
    <meta charset=utf-8 />
    <title><?php echo strtoupper($_SERVER['HTTP_HOST']); ?> ADMINISTRATOR</title>
    <link type="text/css" rel="stylesheet" href="/css/admin.css?<?php echo $this->config->item('script_style_version'); ?>"></link>
    <link type="text/css" rel="stylesheet" href="/css/ui/jquery-ui-1.9.2.custom.css"></link>
    <link type="text/css" rel="stylesheet" href="/css/jquery.wysiwyg.css"></link>
    <script type="text/javascript" src="/js/jquery-1.8.3.min.js"></script>
    <script type="text/javascript" src="/js/jquery-ui-1.9.2.custom.min.js"></script>
    <script type="text/javascript" src="/js/admin.js?<?php echo $this->config->item('script_style_version'); ?>"></script>
    <script type="text/javascript" src="/js/jquery.wysiwyg.js"></script>
    <script type="text/javascript">
      var SERVER_TIME = new Date('<?php echo date('D, d M y H:i:s O'); ?>');
      var TIME_ZONE = '<?php echo ((isset($_SESSION['time_zone'])) ? ($_SESSION['time_zone']) : (0)); ?>';
    </script>
  </head>
  <body>
    <div class="megawrapper">
      <div class="admin_main_menu">
        <ul>
          <li><a href="/admin/notifications/" <?php echo (($this->uri->segment(2) == 'notifications') ? ('class="active"') : ('')); ?>>Оповещения</a></li>
          <li><a href="/admin/users/" <?php echo ((strpos($this->uri->segment(2), 'user') !== FALSE) ? ('class="active"') : ('')); ?>>Пользователи</a></li>
          <li><a href="/admin/questions/no_answer" <?php echo (($this->uri->segment(2) == 'questions') ? ('class="active"') : ('')); ?>>Вопросы (<span class="unanswered_count"><?php echo $UNANSWERED_QUESTIONS_COUNT; ?></span>)</a></li>
          <li><a href="/admin/table/" <?php echo (($this->uri->segment(2) == 'table') ? ('class="active"') : ('')); ?>>Таблица</a></li>
          <li><a href="/admin/archive/" <?php echo (($this->uri->segment(2) == 'archive') ? ('class="active"') : ('')); ?>>Архив и запись</a></li>
          <li><a href="/admin/mailer/" <?php echo (($this->uri->segment(2) == 'mailer') ? ('class="active"') : ('')); ?>>Рассылка</a></li>
          <li><a href="/admin/pages/" <?php echo (($this->uri->segment(2) == 'pages') ? ('class="active"') : ('')); ?>>Страницы</a></li>
          <li><a href="/admin/sms/" <?php echo (($this->uri->segment(2) == 'sms') ? ('class="active"') : ('')); ?>>SMS</a></li>
          <li><a href="/admin/payments/" <?php echo (($this->uri->segment(2) == 'payments') ? ('class="active"') : ('')); ?>>Платежи</a></li>
          <li><a href="/admin/translate/" <?php echo (($this->uri->segment(2) == 'translate') ? ('class="active"') : ('')); ?>>Переводы</a></li>
          <li><a href="/admin/stylecustomizr/" <?php echo (($this->uri->segment(2) == 'stylecustomizr') ? ('class="active"') : ('')); ?>>Стили</a></li>
          <li class="separator"><a href="/admin/rooms/" <?php echo (($this->uri->segment(2) == 'rooms') ? ('class="active"') : ('')); ?>>Rooms</a></li>
          <li><a href="/admin/deleted/" <?php echo (($this->uri->segment(2) == 'deleted') ? ('class="active"') : ('')); ?>>Deleted msgs</a></li>
          <li><a href="/admin/memes/" <?php echo (($this->uri->segment(2) == 'memes') ? ('class="active"') : ('')); ?>>MEMES</a></li>
          <li><a href="/admin/uploader/" <?php echo (($this->uri->segment(2) == 'uploader') ? ('class="active"') : ('')); ?>>Uploader</a></li>
        </ul>
      </div>
      <div class="wrapper">
        <div class="header_admin">
          <span class="admin_title"><strong><a href="/"><?php echo strtoupper($_SERVER['HTTP_HOST']); ?></a> &amp; <a href="/blog">BLOG</a> &amp; <a href="/chat">CHAT</a></strong> ADMINISTRATOR</span>
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