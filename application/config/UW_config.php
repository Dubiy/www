<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


  // title сайта
  $config['sitename'] = 'Поделись';
  // название сайта в письмах
  $config['sitename_for_letters'] = 'Поделись';
  // название сайта социальных сетях при розшаривании
  $config['sitename_onsharebuttons'] = 'Поделись';
  
  $config['script_style_version'] = '1';

  $config['account_type'] = array(
      0 => 'user',
      10 => 'admin'
    );


  //поддерживаемые языки
  $config['languages'] = array('en', 'ru');

  // частина тексту генерування хешу для назви персонального каналу
  // формат md5($secret_longpolling_key . $user_id) //. date('Y-m-d')
  $config['secret_longpolling_key'] = 'Вышел Гарри из тумана. Вынул ножик из кармана.';


  // почта админа. Сюда приходят уведомления о подозрительной активности пользователей
  $config['admin_email'] = 'netzver@gmail.com';
  // число авторизаций за сутки одного пользователя, когда уведомляется администратор
  $config['admin_alert_after_logins_count'] = 10;


 