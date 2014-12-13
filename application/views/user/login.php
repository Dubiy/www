<div id="container">

<h1>Тестовый пароль: xHymtFSsoR</h1>

<?php 
  $MSG = '';
  $validation_errors = validation_errors();
  if ($validation_errors) {
    $MSG .= $validation_errors;
  }
  if (@$error != '')  {
    $MSG .= $error;
  }

  if (@$msg != '')  {
    $MSG .= $msg;
  }
?>

<script type="text/javascript">
  function get_cookie(cookie_name) {
    var results = document.cookie.match('(^|;) ?' + cookie_name + '=([^;]*)(;|$)');
    if (results) {
      return (unescape(results[2]));
    } else {
      return null;
    }
  }

  function recalc_player_position() {
    if (jQuery('#video_on_login_player:visible').length) {
      var player = 'video_on_login_player';
    } else {
      var player = 'video_on_login_player_info';
    }
    if (document.getElementById(player).paused) {
      jQuery('#video_on_login .play_pause').addClass('paused');
    } else {
      jQuery('#video_on_login .play_pause').removeClass('paused');
    }
    


    jQuery('#video_on_login_player').css('margin-top', Math.ceil((jQuery('#video_on_login').height() - jQuery('#video_on_login_player').height()) / 2) + 'px');
    jQuery('#video_on_login_player_info').css('margin-top', Math.ceil((jQuery('#video_on_login').height() - jQuery('#video_on_login_player_info').height()) / 2) + 'px');
  }



  function hide_video_player() {
    jQuery('#video_on_login').fadeOut(500, function() {
      document.getElementById('video_on_login_player').pause();
      document.getElementById('video_on_login_player_info').pause();
    });
    animate_login_form();
  }

  function onplay() {
    jQuery('#video_on_login_player').css('margin-top', Math.ceil((jQuery('#video_on_login').height() - jQuery('#video_on_login_player').height()) / 2) + 'px');
    recalc_player_position();
    jQuery('#video_on_login .play_pause').removeClass('paused');
  }

  function onpause() {
  	jQuery('#video_on_login .play_pause').addClass('paused');

  }

  function show_video_player_info() {
    jQuery('#video_on_login').fadeIn(500, function() {
      jQuery('#video_on_login .controll .never_show_again').html('HIDE VIDEO');
      jQuery('#video_on_login_player_info').css('display', 'block');
      jQuery('#video_on_login_player').css('display', 'none');
      document.getElementById('video_on_login_player_info').load();
      document.getElementById('video_on_login_player_info').play();
    });
  }

  jQuery(document).ready(function() {
    window.setInterval(function () {
      recalc_player_position();
      //console.log('recalc');
    }, 500);

    if (get_cookie("never_show_intro_video") <?php echo (($MSG != '') ? ('|| true') : ('')); //воводжу відео, лише коли немає повідомлень чи помилок. Типу якщо неправильний пароль, щоб не крутилося відео ?>) {
      document.getElementById('video_on_login_player').pause();
      jQuery('#video_on_login').hide(0);
      animate_login_form();
    }

    jQuery('#video_on_login .controll .never_show_again').click(function() {
      hide_video_player();
      document.cookie="never_show_intro_video=never_show_intro_video; path=/; expires=Mon, 01-Jun-2020 00:00:00 GMT";
    });

    jQuery('#video_on_login .play_pause').click(function() {
      var self = this;
      if (jQuery('#video_on_login_player:visible').length) {
        var player = 'video_on_login_player';
      } else {
        var player = 'video_on_login_player_info';
      }
      if (jQuery(self).hasClass('paused')) {
	    document.getElementById(player).play();
	    jQuery(self).removeClass('paused');
      } else {
        document.getElementById(player).pause();
        jQuery(self).addClass('paused');
      }
    })

    jQuery(window).resize(function(){
      window.clearTimeout(b);
      b = window.setTimeout(function () {
        recalc_player_position();
      }, 100);
    });

    jQuery('#login_pupup_window .window .content .close').click(function() {
      jQuery('#login_pupup_window').animate({'opacity': 0}, 500, function() {
        jQuery(this).css('display', 'none');
      });
    });

    jQuery('.reglogin_form_container .info_buttons .text').click(function() {
      jQuery('#login_pupup_window').css('display', 'block').animate({'opacity': 1}, 500);
    });

    jQuery('.reglogin_form_container .info_buttons .video').click(function() {
      //jQuery('#video_on_login').show(0);
      show_video_player_info();
    });
  });


</script>

  <div id="video_on_login">
    <video autoplay="autoplay" id="video_on_login_player" onended="hide_video_player()" onplaying="onplay()" onpause="onpause()">
      <source src="/video/PixmediaHomepageVideo.webm" type="video/webm;">
      <source src="/video/PixmediaHomepageVideo.mp4" type="video/mp4;"/>
      <source src="/video/PixmediaHomepageVideo.ogg" type="video/ogg" />
    </video>
    <video id="video_on_login_player_info" onended="hide_video_player()" onplaying="onplay()" onpause="onpause()">
      <source src="/video/PixmediaHomepageVideo2.webm" type="video/webm;">
      <source src="/video/PixmediaHomepageVideo2.mp4" type="video/mp4;"/>
    </video>

    <div class="controll">
      <div class="never_show_again">DON`T SHOW BEFORE LOGOUT</div>
      <div class="play_pause"></div>
      <!-- <div class="skip_video">SKIP VIDEO</div> -->
    </div>
  </div>

  <div class="login_form_container reglogin_form_container">
    <form method="POST" action="">
      <label class="form_message scrambledWriter_login" for="login_form_containerrm_password_input">ENTER PASSWORD OR REGISTER</label>
      <input type="password" name="password" id="login_form_password_input" class="password input">
      <a href="/user/register" class="register">REGISTER</a>
      <input type="submit" class="submit" value="LOG IN">
    </form>
    <div class="popup_message <?php echo ((! isset($no_scrambledWriter_on_popup_message)) ? ('scrambledWriter') : (''))?>">
<?php
    echo $MSG;
?>
    </div>
    <div class="info_buttons">
      <div class="video"><div class="label">START VIDEO PRESENTATION</div></div>
      <div class="text"><div class="label">INFORMATION</div></div>
    </div>

  </div>

  <div id="login_pupup_window">
    <div class="window">
      <div class="content">
        <div class="close"></div>
        <div class="header">
          <div class="header_underline">
            <?php echo $text_page[0]->title; ?>
          </div>
        </div>
        <div class="text_container">
          <div class="text content_scroll">
<?php
            echo $text_page[0]->text;
            if (isset($relatedlinks) && is_array($relatedlinks) && count($relatedlinks)) {
?>
              <div class="relatedlinks">
                <h2><?php echo $relatedlinks[0]->title; ?></h2>
                <?php echo $relatedlinks[0]->text; ?>
              </div>
<?php
            }
?>
          </div>
        </div>
      </div>
    </div>

  </div>


<style type="text/css">
#login_pupup_window {
  position: absolute;
  opacity: 0;
  display: none;
  z-index: 1;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  background-color: rgba(0,0,0,0.35);
}

#login_pupup_window .window {
  position: absolute;
  width: 768px;
  top: 8%;
  left: 50%;
  margin-left: -384px;
  height: 84%;
  background-color: rgba(158,158,158,0.8);
  padding: 10px;
}

#login_pupup_window .window .content {
  width: 100%;
  height: 100%;
  background-color: rgba(255,255,255,0.25);
  position: relative;
}

#login_pupup_window .window .content .close {
  position: absolute;
  top: 11px;
  right: 9px;
  width: 23px;
  height: 23px;
  background: url('/images/login_close_popup.png');
  cursor: pointer;
}

#login_pupup_window .window .content .header {
  font-size: 18px;
  font-weight: bold;
  color: #ffffff;
  height: 9.55%;
  padding-left: 34px;
  padding-right: 40px;
  overflow: hidden;
  /*padding-top: 4.05%;


  padding-bottom: 1.68%;*/
}

#login_pupup_window .window .content .header .header_underline {
  padding-top: 4.05%;
  padding-bottom: 1.68%;

  border-bottom: 1px solid white;
}

#login_pupup_window .window .content .text_container {
  width: 725px;
  padding-left: 33px;

  height: 88.76%;
}

#login_pupup_window a {
  color: white;
}

#login_pupup_window a:visited {
  color: gray;
}

#login_pupup_window .window .content .text_container .mCustomScrollbar .mCSB_scrollTools .mCSB_dragger_bar {
  background: rgba(255, 255, 255, 0.35);
}

#login_pupup_window .window .content .text_container .mCustomScrollbar .mCSB_scrollTools .mCSB_dragger_bar:hover {
  background: rgba(255, 255, 255, 0.5);
}
#login_pupup_window .window .content .text_container .text {
  width: 100%;
  height: 100%;
  overflow: hidden;
  margin-bottom: 50%;
}

#login_pupup_window .window .content .text_container .text p {
  margin-bottom: 5px;
}

#video_on_login_player_info {
  display: none;
  width: 100%;
}

.reglogin_form_container .info_buttons {
  width: 255px;
  height: 95px;
  margin: 0 auto;
  position: relative;
}

.reglogin_form_container .info_buttons .video {
  float: left;
  width: 91px;
  height: 56px;
  background: url('/images/login_video_icon.png');
  margin-left: 36px;
  opacity: 0.5;
}

.reglogin_form_container .info_buttons .text {
  float: right;
  width: 45px;
  height: 56px;
  background: url('/images/login_text_icon.png');
  margin-right: 36px;
  opacity: 0.5;
}

.reglogin_form_container .info_buttons .video:hover, .reglogin_form_container .info_buttons .text:hover {
  opacity: 1;
  cursor: pointer;
}

.reglogin_form_container .info_buttons .video:hover .label, .reglogin_form_container .info_buttons .text:hover .label {
  display: block;
}

.reglogin_form_container .info_buttons .video .label, .reglogin_form_container .info_buttons .text .label {
  position: absolute;
  display: none;
  bottom: 0;
  text-align: center;
  width: 100%;
  left: 0;
  font-size: 15px;
  font-weight: bold;
}
</style>

 

  <div class="login_connect_with">
    <div class="connect_with">CONNECT WITH</div>
    <div class="facepalm" url="https://www.facebook.com/dialog/oauth?client_id=<?php echo $this->config->item('fb_client_id'); ?>&redirect_uri=<?php echo $this->config->item('fb_redirect_uri'); ?>&response_type=code&scope=email"></div>
    <div class="vkontakte" url="http://oauth.vk.com/authorize?client_id=<?php echo $this->config->item('vk_client_id'); ?>&redirect_uri=<?php echo $this->config->item('vk_redirect_uri'); ?>&scope=notifications,notify,status,offline&response_type=code"></div>
    <div class="google" url="<?php echo 'https://accounts.google.com/o/oauth2/auth?redirect_uri=' .  urlencode($this->config->item('google_redirect_uris')) . '&response_type=code&client_id=' . $this->config->item('google_client_id') . '&scope=https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.email+https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fuserinfo.profile'; ?>"></div>
  </div>
</div>
