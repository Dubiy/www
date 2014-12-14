<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="stylesheet" type="text/css" href="/css/ui/jquery-ui-1.9.2.custom.css" />
  <script type="text/javascript" src="/js/jquery/jquery-1.8.3.min.js"></script>
  <script type="text/javascript" src="/js/jquery/jquery-ui-1.9.2.custom.min.js"></script>
  <title><?php echo $this->config->item('sitename'); ?></title>

  <!-- Bootstrap Core CSS -->
  <link href="/css/bootstrap.min.css" rel="stylesheet">

  <!-- Custom CSS -->
  <link href="/css/simple-sidebar.css" rel="stylesheet">
  <link href="/css/styles.css" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="/css/style.css?<?php echo $this->config->item('script_style_version'); ?>" />
  <script src="/js/site.js?<?php echo $this->config->item('script_style_version'); ?>" type="text/javascript"></script>

</head>

<body>
    <a href="/add_question" class="btn btn-success take_question_btn">Задать вопрос</a>
  <nav class="navbar navbar-default" role="navigation">
    <h1 class="sitetitle">Поделись</h1>
    <div class="container-fluid">
      <div class="navbar-header">
        <a class="navbar-brand" href="/">
          <h1><img alt="Brand" src="/img/lg.png"></h1>
        </a>
      </div>
    </div>
  </nav>
<div id="wrapper">
  <!-- Sidebar -->
  <div id="sidebar-wrapper">
    <ul class="sidebar-nav">
      <li class="dropdown-header">
        <a class="btn btn-primary" href="/">Все вопросы</a>
      </li>
      <li class="dropdown-header">
        <a class="btn btn-info" href="/parents">Вопросы родителей</a>
      </li>
      <li class="dropdown-header">
        <a class="btn btn-warning" href="/children">Вопросы детей</a>
      </li>
      <li>
      <div class="age_selector" filter="<? echo @$filter; ?>">
        <span>Возрастная категория</span><br />


        от <input type="text" name="age_start" class="age_start" value="<?php echo @$age_start; ?>"> до <input type="text" name="age_stop" class="age_stop" value="<?php echo @$age_stop; ?>"> <input type="submit" class="submit">
      </div>
      </li>
      <li class="dropdown-header">
        <a class="btn btn-danger" href="/about">Про нас</a>
      </li>
     
    </ul>
  </div>
  <!-- /#sidebar-wrapper -->

  <!-- Page Content -->
  <div id="page-content-wrapper">



