<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="description" content="Отцы и дети"> 
  <meta name="keywords" content="Тратата"> 
  <link rel="stylesheet" type="text/css" href="/css/ui/jquery-ui-1.9.2.custom.css" />
  <link rel="stylesheet" type="text/css" href="/css/style.css?<?php echo $this->config->item('script_style_version'); ?>" />
  <script type="text/javascript" src="/js/jquery-1.8.3.min.js"></script>
  <script type="text/javascript" src="/js/jquery-ui-1.9.2.custom.min.js"></script>
  <title><?php echo $this->config->item('sitename'); ?></title>
  <script src="/js/site.js?<?php echo $this->config->item('script_style_version'); ?>" type="text/javascript"></script>
</head>
<body >
  <div>
      <a href="/">Все вопросы</a> <br />
      <a href="/parents">Вопросы родителей</a> <br />
      <a href="/children">Вопросы детей</a> <br />
      от <input type="text" name="age_start"> до <input type="text" name="age_stop"> <input type="submit">
      <a href="/about">Про нас</a> <br />
  </div>

  <a href="/add_question">Задать вопрос</a>
 