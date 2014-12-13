<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="description" content="My-trade.pro - ОНЛАЙН ТОРГОВЛЯ СКАНДАЛЬНОГО ТРЕЙДЕРА!"> 
  <meta name="keywords" content="my-trade.pro, trade, trader, traders, trading, guru, CME, usa, трейдинг, гуру, профи, профит, фьючерс, futures, рынок, market, stock market, спекуляции, технический анализ, психология трейдинга, трейдер, трейдеры, грааль, стратегия, деньги, заработок, Futures (magazine), GPB, USD, EUR, JPY, smart-lab, smart-lab.ru, Мартьянов, майтрейд, Euro (Currency), Price Action Trading"> 
  <link rel="stylesheet" type="text/css" href="/css/ui/jquery-ui-1.9.2.custom.css" />
  <link rel="stylesheet" type="text/css" href="/css/style.css?<?php echo $this->config->item('script_style_version'); ?>" />
  <link rel="stylesheet" type="text/css" href="/css/jquery.mCustomScrollbar.css?<?php echo $this->config->item('script_style_version'); ?>" />
  <script type="text/javascript" src="/js/jquery-1.8.3.min.js"></script>
  <script type="text/javascript" src="/js/jquery-ui-1.9.2.custom.min.js"></script>
  <?php //<script src="/js/jquery.mousewheel.min.js"></script>
        //<script src="/js/jquery.mCustomScrollbar.js"></script> ?>
  <script src="/js/jquery.mCustomScrollbar.concat.min.js"></script>
  <script src="/js/sound.js"></script>
  <script src="/js/jquery.text-effects.js"></script>
  <script type="text/javascript" src="/js/jwplayer.js"></script>
  <title><?php echo site_title(); ?></title>











  <script type="text/javascript">
    var USER_ID = <?php echo ((isset($_SESSION['user_id']) && ($_SESSION['user_id'] > 0)) ? ($_SESSION['user_id']) : (0)); ?>;
    var SERVER_TIME = new Date('<?php echo date('D, d M y H:i:s O'); ?>');
    var TIME_ZONE = '<?php echo ((isset($_SESSION['time_zone'])) ? ($_SESSION['time_zone']) : (0)); ?>';
    var max_datetime = '<?php echo date('Y-m-d H:i:s'); ?>';
    var recomendations = [<?php echo "'" . implode("', '", $this->config->item('recomendations')) . "'"; ?>];
    var sitename_onsharebuttons = '<?php echo $this->config->item('sitename_onsharebuttons'); ?>';
    var NotifyOptions = {<?php
          foreach ($this->config->item('notification_types') as $notification_type => $v) {
            $tmp = notify_decode(((isset($_SESSION[$notification_type])) ? ($_SESSION[$notification_type]) : (0)) );
            echo "'" . $notification_type . "': " . $tmp['sound'] . ', ';
          }
        ?>};
  </script>











<?php
        $LONGPOLLING = $this->config->item('sitelongpolling');
        if ($LONGPOLLING) {
?>
          <script type="text/javascript" src="http://kitty.<?=$_SERVER['HTTP_HOST']?>/?identifier=SCRIPT&<?=0*time()?>"></script>
<?php
        }
?>
        <script type="text/javascript">
          var LONGPOLLING = <?php echo (($LONGPOLLING) ? ('true') : ('false')); ?>;
          var MYCHANNEL = '<?php echo (($LONGPOLLING) ? (secret_longpolling_channel(logged_in())) : ('')); ?>';
<?php
          if ($LONGPOLLING) {
?>
            var realplexor = new Dklab_Realplexor(
                "http://kitty.<?=$_SERVER['HTTP_HOST']?>/?<?=0*time()?>",
                "demo_"
            );
<?php
          }
?>
     



    jQuery(document).ready(function() {
      setInterval('calc_time_since()', <?php echo $this->config->item('calc_time_since_interval'); ?>);
<?php
      if ( !$LONGPOLLING && isset($run_js_ping_function) && $run_js_ping_function) {
?>
        setInterval('ping()', <?php echo $this->config->item('ping_interval'); ?>);
<?php
      }
?>
    });
  </script>
  <script src="/js/site.js?<?php echo $this->config->item('script_style_version'); ?>" type="text/javascript"></script>
</head>
<body class="thisisnotblog">
  <div class="background_bgback">
    <div class="background_bgtop">
    </div>
  </div>
