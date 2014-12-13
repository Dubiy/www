  <div id="container">
    <table class="table_container">
      <tr style="height: 3.6%;">
        <th>
          <div class="empty_place_for_mainmenu"></div>
        </th>
      </tr>
      <tr style="height: 96.4%;">
        <td class="td_container" style="height: inherit;">
          <div id="td_container">
            <div id="question" class="">
              <div class="content">
                <div class="question_head">
                  <table>
                    <tr class="question_title">
                      <td>&nbsp;</td>
                      <td>ASK MY-TRADE A QUESTION</td>
                      <td>&nbsp;</td>
                    </tr>
                    <tr>
                      <td class="td_marginer">&nbsp;</td>
                      <td class="question_text"><textarea name="question_text"></textarea></td>
                      <td class="td_marginer">&nbsp;</td>
                    </tr>
                    <tr class="question_footer">
                      <td>&nbsp;</td>
                      <td><div class="send_a_question">SEND A QUESTION</div></td>
                      <td>&nbsp;</td>
                    </tr>
                  </table>
                </div>
                <div class="content_scroll buttons">
                  <ul class="questions_list">
<?php
                    if (isset($questions) && is_array($questions) && count($questions)) {
                      $i = 0;
                      foreach ($questions as $question) {
                        $i++;
?>
                        <li question_id="<?php echo $question->question_id; ?>" class="<?php echo (($i % 2 == 0) ? ('odd') : ('even')) ?> question_id_<?php echo $question->question_id; ?>">
                          <div class="question">
                            <span class="label">QUESTION:</span><span class="text"><?php echo strip_tags($question->text); ?></span>
                          </div>
                          <div class="answer">
                            <span class="label">ANSWER:</span><span class="text"><?php echo $question->answer; ?></span>
                          </div>
                          <div class="date time_since" datetime="<?php echo mysql2js_date((($question->answer_datetime) ? ($question->answer_datetime) : ($question->datetime))); ?>"><?php echo $question->answer_datetime; ?></div>
                        </li>
<?php
                      }
                    }
?>
                  </ul>
                </div>
              </div>
              <div class="leftopener opener">
                <table>
                  <tr class="top"><td>&nbsp;</td></tr>
                  <tr class="arrow"><td>&nbsp;</td></tr>
                  <tr class="foot"><td>&nbsp;</td></tr>
                </table>
              </div>
            </div>
            <div id="video" class="">
              <div class="content">
                <div class="content_wrapper content_wrapper_archive">
                  <div class="content_scroll">
                    <div class="list">
<?php
                      if (isset($videos) && is_array($videos) && count($videos)) {
                        $i = 0;
                        foreach ($videos as $video) {
                          if (($_SESSION['premium_to'] > date('Y-m-d H:i:s')) || (! $video->is_premium) || ($video->is_premium && date('Y-m-d H:i:s', strtotime($video->datetime)) < date('Y-m-d H:i:s', strtotime('-3 days')))) {

?>
                            <div class="video_archive_record <?php echo (($i % 2 == 1) ? ('odd') : ('')); ?>" video_id="<?php echo $video->video_id; ?>" video_code="<?php echo $video->code; ?>">
                              <div class="thumb">
                                <img src="<?php echo $video->thumbnail_url; ?>">
                              </div>
                              <div class="info">
                                <div class="title"><?php echo $video->title; ?></div>
                                <div class="duration"><span>Duration:</span><?php echo date('i:s', $video->length); ?></div>
                                <div class="views"><span>Date:</span><?php echo gmdate('Y-m-d H:i:s', strtotime($video->datetime . (($_SESSION['time_zone'] > 0) ? ('+') : ('')) . $_SESSION['time_zone'] . ' hour')); ?></div>
<?php
                                if ( ! $video->is_premium || $video->is_premium && date('Y-m-d H:i:s', strtotime($video->datetime)) < date('Y-m-d H:i:s', strtotime('-3 days'))) {
?>
                                  <div class="share"><span>Share:</span>
                                    <div class="share_vk" onclick="Share.vkontakte('http://www.youtube.com/watch?v=<?php echo $video->code; ?>','Видео на сайте <?php echo $this->config->item('sitename_onsharebuttons'); ?>','','На сайте <?php echo $this->config->item('sitename_onsharebuttons'); ?> это видео доступное в видео архиве')"></div>
                                    <div class="share_fb" onclick="Share.facebook('http://www.youtube.com/watch?v=<?php echo $video->code; ?>','Видео на сайте <?php echo $this->config->item('sitename_onsharebuttons'); ?>','','На сайте <?php echo $this->config->item('sitename_onsharebuttons'); ?> это видео доступное в видео архиве')"></div>
                                    <div class="share_tw" onclick="Share.twitter('http://www.youtube.com/watch?v=<?php echo $video->code; ?>','Видео на сайте <?php echo $this->config->item('sitename_onsharebuttons'); ?>')"></div>
                                  </div>
<?php
                                }
                                if ($video->is_premium) {
                                  if ($_SESSION['premium_to'] > date('Y-m-d H:i:s')) {
?>
                                    <div class="video_only_for_premium_users">Video only for premium users</div>
<?php
                                  } else {
?>
                                    <div class="premium_video_3_days_delay">Premium video 3 days delay</div>
<?php
                                  }
                                }
?>
                              </div>
                            </div>
<?php
                            $i++;
                          }
                        }
                      }
?>
                    </div>
                  </div>
                </div>
                <div class="content_wrapper content_wrapper_live">
                  <div class="broadcast_player_wrapper">
                    <div id="broadcast_player"></div>
                    <div class="broadcast_player_newwindow_button"></div>
                  </div>
                </div>
                <div class="content_wrapper content_wrapper_videoplayer">
                  <div id="content_wrapper_videoplayer">
                    video player here
                  </div>
                </div>
                <div id="video_sidebar">
                  <div class="videoarchive active">
                    <div></div>
                  </div>
                  <div class="broadcasting">
                    <div></div>
                  </div>
                </div>
              </div>
              <div class="rightopener opener">
                <table>
                  <tr class="top"><td>&nbsp;</td></tr>
                  <tr class="arrow"><td>&nbsp;</td></tr>
                  <tr class="foot"><td>&nbsp;</td></tr>
                </table>
              </div>
            </div>

            <div id="main_page_alert" class="<?php echo (($main_page_alert['text'] != '') ? ('active') : ('')); ?>">
              <div class="wrapper">
                <div class="time_since" datetime="<?php echo mysql2js_date($main_page_alert['datetime']); ?>">&nbsp;</div>
                <div class="label">MY-TRADE ALERT</div>
                <div class="text content_scroll">
                <?php
                  echo $main_page_alert['text'];
                ?>

                </div>
              </div>
            </div>

            <div id="table" class="">
              <div class="content">
                <div>
                  <div class="tickers_table">
                    <div class="tickers_table_header" style="height: 30px; background: url('/images/tickers_header_bg.png') repeat;">
                      <div class="cheker">&nbsp;</div>
                      <div class="ticker">Futures</div>
                      <div class="short">Short-term</div>
                      <div class="middle">Middle-term <?php echo (($_SESSION['premium_to'] && $_SESSION['premium_to'] > date('Y-m-d H:i:s')) ? ('<div class="realtime_data">Realtime data</div>') : ('<div class="all_data_is_4_hours_delayed">all data is ' . (int)$this->config->item('regular_delay_table') . ' hours delayed</div>')); ?></div>
                    </div>
                    <div class="content_scroll" style="height: 130px;">
<?php
                      $recomendation_no_index_constant = $this->config->item('recomendation_no_index_constant');
                      if (isset($tickers) && is_array($tickers) && count($tickers)) {
                        $i = 0;
                        for ($j = 0; $j < 2; $j++) {
                          echo '<div class="' . (($j == 0) ? ('') : ('un')) . 'holded_tickers">';
                          foreach ($tickers as $ticker) {
                            if ($j == 0 && !$ticker->holded || $j == 1 && $ticker->holded) {
                              continue;
                            }
                            $i++;
?>
                            <div class="tickers_table_record ticker_id_<?php echo $ticker->ticker_id . ' '; echo (($i % 2 == 0) ? ('odd') : ('even')); ?>" ticker_id="<?php echo $ticker->ticker_id; ?>">
                              <div class="cheker ticker_circle"></div>
                              <div class="ticker" title="<?php echo $ticker->title; ?>"><?php echo $ticker->name; ?></div>
                              <div class="short recomendation_<?php echo (($ticker->type_s) ? ($ticker->type_s) : ('0')) . ' recomendation_id_' . $ticker->recomendation_id_s . (($ticker->image_s) ? (' has_image') : ('')); ?>">
                                <div class="icon recomendation_type_<?php echo (($ticker->type_s) ? ($ticker->type_s) : ('0')); ?>"></div>
                                <div class="date"><div class="time_since addbr" title="<?php echo (($ticker->datetime_s != '') ? (gmdate('Y-m-d H:i:s', strtotime($ticker->datetime_s . (($_SESSION['time_zone'] > 0) ? ('+') : ('')) . $_SESSION['time_zone'] . ' hour'))) : ('')); ?>" datetime="<?php echo mysql2js_date($ticker->datetime_s); ?>"><?php echo $ticker->datetime_s; ?></div></div>
                                <div class="info">
                                  <span class="recomendation_title"><?php echo $RECOMENDATIONS[(($ticker->type_s) ? ($ticker->type_s) : ('0'))]; ?></span>
                                  <span class="recomendation_index"><?php echo (($ticker->type_s > 0 && $ticker->index_s != $recomendation_no_index_constant) ? ('(Target ' . $ticker->index_s . ')') : ('')); ?></span>
                                  <div class="recomendation_comment"><?php echo $ticker->text_s; ?></div>
                                </div>
                              </div>
                              <div class="middle recomendation_<?php echo (($ticker->type_l) ? ($ticker->type_l) : ('0')) . ' recomendation_id_' . $ticker->recomendation_id_l . (($ticker->image_l) ? (' has_image') : ('')); ?>">
                                <div class="icon recomendation_type_<?php echo (($ticker->type_l) ? ($ticker->type_l) : ('0')); ?>"></div>
                                <div class="date"><div class="time_since addbr" title="<?php echo (($ticker->datetime_l != '') ? (gmdate('Y-m-d H:i:s', strtotime($ticker->datetime_l . (($_SESSION['time_zone'] > 0) ? ('+') : ('')) . $_SESSION['time_zone'] . ' hour'))) : ('')); ?>" datetime="<?php echo mysql2js_date($ticker->datetime_l); ?>"><?php echo $ticker->datetime_l; ?></div></div>
                                <div class="info">
                                  <span class="recomendation_title"><?php echo $RECOMENDATIONS[(($ticker->type_l) ? ($ticker->type_l) : ('0'))]; ?></span>
                                  <span class="recomendation_index"><?php echo (($ticker->type_l > 0 && $ticker->index_l != $recomendation_no_index_constant) ? ('(Target ' . $ticker->index_l . ')') : ('')); ?></span>
                                  <div class="recomendation_comment"><?php echo $ticker->text_l; ?></div>
                                </div>
                              </div>
                            </div>
<?php
                          }
                          echo '</div>';
                        }
                      }
?>
                    </div>
                  </div>
                  <div class="ticker_details">ticker_details</div>
                </div>
              </div>
              <div class="rightopener opener">
                <table>
                  <tr class="top"><td>&nbsp;</td></tr>
                  <tr class="arrow"><td>&nbsp;</td></tr>
                  <tr class="foot"><td>&nbsp;</td></tr>
                </table>
              </div>
            </div>

            <div id="notification" class="<?php echo (($this->agent->is_mobile()) ? ('is_mobile') : ('')); ?>">
              <div class="headr">
                <table>
                  <tr>
                    <td class="opener">
                      <table>
                      <tr>
                        <td class="left">&nbsp;</td>
                        <td class="center">
                          <div class="icon"></div>
                          <div class="text"></div>
                        </td>
                        <td class="right">&nbsp;</td>
                      </tr>
                      </table>
                    </td>
                  </tr>
                </table>
              </div>
              <div class="content">
                <div class="content_scroll">
                  <div class="notify_records">
                    <table class="notify_records_table">
<?php
                      if (isset($notifications) && is_array($notifications) && count($notifications)) {
                        $i = 0;
                        foreach ($notifications as $notification) {
                          $notification->datetime = mysql2js_date($notification->datetime);
                          $tmp_arr = unserialize($notification->array);
                          if ($notification->type == 'table') {
?>
                            <tr class="notify_record ideas <?php echo (($i % 2 == 0) ? ('odd') : ('')); ?>">
                              <td class="td_space">&nbsp;</td>
                              <td class="td_icon icon ideas recomendation_type_<?php echo $tmp_arr['type'] . ' ' . $notification->type; ?>">&nbsp;</td>
                              <td class="td_text text"><div class="title">TRADE IDEAS: </div> <?php echo $tmp_arr['ticker_name'] . ', ' . (($tmp_arr['long']) ? ('Middle-term') : ('Short-term')) . ', ' . $RECOMENDATIONS[$tmp_arr['type']] . (($tmp_arr['type'] != '0') ? (' (aim ' . $tmp_arr['index'] . '),') : ('')) . '<br /> ' . $tmp_arr['text'] ; ?></td>
                              <td class="td_time time time_since addbr" title="<?php echo gmdate('Y-m-d H:i:s', strtotime($notification->datetime . (($_SESSION['time_zone'] > 0) ? ('+') : ('')) . $_SESSION['time_zone'] . ' hour')); ?>" datetime="<?php echo $notification->datetime; ?>">&nbsp;</td>
                            </tr>
<?php
                          } elseif ($notification->type == 'notification') {
?>
                            <tr class="notify_record mytrade <?php echo $notification->type . ' ' . (($i % 2 == 0) ? ('odd') : ('')); ?>">
                              <td class="td_space">&nbsp;</td>
                              <td class="td_icon icon <?php echo $notification->type; ?>">&nbsp;</td>
                              <td class="td_text text"><div class="title">MY-TRADE: </div><?php echo $tmp_arr['text']; ?></td>
                              <td class="td_time time time_since addbr" title="<?php echo gmdate('Y-m-d H:i:s', strtotime($notification->datetime . (($_SESSION['time_zone'] > 0) ? ('+') : ('')) . $_SESSION['time_zone'] . ' hour')); ?>" datetime="<?php echo $notification->datetime; ?>">&nbsp;</td>
                            </tr>
<?php
                          } elseif ($notification->type == 'archive') {
                            if ($tmp_arr['action'] != 'delete_video') {
?>
                              <tr class="notify_record <?php echo $notification->type . ' ' . (($i % 2 == 0) ? ('odd') : ('')); ?>">
                                <td class="td_space">&nbsp;</td>
                                <td class="td_icon icon <?php echo $notification->type; ?>">&nbsp;</td>
                                <td class="td_text text"><div class="title">ARCHIVE: </div><?php echo $tmp_arr['title']; ?>
                                </td>
                                <td class="td_time time time_since addbr" title="<?php echo gmdate('Y-m-d H:i:s', strtotime($notification->datetime . (($_SESSION['time_zone'] > 0) ? ('+') : ('')) . $_SESSION['time_zone'] . ' hour')); ?>" datetime="<?php echo $notification->datetime; ?>">&nbsp;</td>
                              </tr>
<?php
                            }
                          } elseif ($notification->type == 'blog') {
?>
                            <tr class="notify_record <?php echo $notification->type . ' ' . (($i % 2 == 0) ? ('odd') : ('')); ?>">
                              <td class="td_space">&nbsp;</td>
                              <td class="td_icon icon <?php echo $notification->type; ?>">&nbsp;</td>
                              <td class="td_text text"><div class="title">BLOG: </div><?php echo $tmp_arr['title']; ?></td>
                              <td class="td_time time time_since addbr" title="<?php echo gmdate('Y-m-d H:i:s', strtotime($notification->datetime . (($_SESSION['time_zone'] > 0) ? ('+') : ('')) . $_SESSION['time_zone'] . ' hour')); ?>" datetime="<?php echo $notification->datetime; ?>">&nbsp;</td>
                            </tr>
<?php
                          } elseif ($notification->type == 'question') {
?>
                            <tr class="notify_record <?php echo $notification->type . ' ' . (($i % 2 == 0) ? ('odd') : ('')); ?>">
                              <td class="td_space">&nbsp;</td>
                              <td class="td_icon icon <?php echo $notification->type; ?>">&nbsp;</td>
                              <td class="td_text text"><div class="title">QUESTION REPLY: </div><?php echo $tmp_arr['text']; ?></td>
                              <td class="td_time time time_since addbr" title="<?php echo gmdate('Y-m-d H:i:s', strtotime($notification->datetime . (($_SESSION['time_zone'] > 0) ? ('+') : ('')) . $_SESSION['time_zone'] . ' hour')); ?>" datetime="<?php echo $notification->datetime; ?>">&nbsp;</td>
                            </tr>
<?php
                          } elseif ($notification->type == 'live') {
?>
                            <tr class="notify_record <?php echo $notification->type . ' ' . (($i % 2 == 0) ? ('odd') : ('')); ?>">
                              <td class="td_space">&nbsp;</td>
                              <td class="td_icon icon <?php echo $notification->type; ?>">&nbsp;</td>
                              <td class="td_text text"><div class="title">LIVE BROADCASTING: </div><?php echo $tmp_arr['title']; ?></td>
                              <td class="td_time time time_since addbr" title="<?php echo gmdate('Y-m-d H:i:s', strtotime($notification->datetime . (($_SESSION['time_zone'] > 0) ? ('+') : ('')) . $_SESSION['time_zone'] . ' hour')); ?>" datetime="<?php echo $notification->datetime; ?>">&nbsp;</td>
                            </tr>
<?php
                          } elseif ($notification->type == 'alert') {
                            //do nothing
                          } else {
?>
                            <tr class="notify_record <?php echo $notification->type . ' ' . (($i % 2 == 0) ? ('odd') : ('')); ?>">
                              <td class="td_space">&nbsp;</td>
                              <td class="td_icon icon mytrade">&nbsp;</td>
                              <td class="td_text text"><div class="title">OTHER: </div><?php echo $tmp_arr['text']; ?></td>
                              <td class="td_time time time_since addbr" title="<?php echo gmdate('Y-m-d H:i:s', strtotime($notification->datetime . (($_SESSION['time_zone'] > 0) ? ('+') : ('')) . $_SESSION['time_zone'] . ' hour')); ?>" datetime="<?php echo $notification->datetime; ?>">&nbsp;</td>
                            </tr>
<?php
                          }
                          $i++;
                        }
                      } else {
                        echo 'Нет событий';
                      }
?>
                    </table>
                  </div>
                </div>
              </div>
              <div class="footer">
                <div class="mytrade active" notify_type="mytrade">MY-TRADE<span>|</span></div>
                <div class="live active" notify_type="live">LIVE<span>|</span></div>
                <div class="archive active" notify_type="archive">ARCHIVE<span>|</span></div>
                <div class="blog active" notify_type="blog">BLOG<span>|</span></div>
                <div class="question active" notify_type="question">QUESTION REPLY<span>|</span></div>
                <div class="ideas active" notify_type="ideas">TRADE IDEAS</div>
              </div>
            </div>
          </div>
        </td>
      </tr>
    </table>
    <div id="utlogo"></div>
<?php
    if (isset($_SESSION['undercover'])) {
?>
      <div class="unundercover">
        <a href="/ajax/unundercover">Admin-panel</a> &bull; <a href="/user/logout">Log out</a>
      </div>

      <style type="text/css">
        .unundercover {
          position: absolute;
          top: 1px;
          z-index: 1;
          left: 260px;
          padding: 10px;
          background-color: rgba(0,0,0,0.3);
          border-radius: 10px;
          font-size: 14px;
        }

        .unundercover a {
          color: white;
        }

      </style>
<?php
    }
?>

    <div id="settings" class="">
      <div class="settings_wrapper">
        <div class="settings_block">
          <div class="settings_block_personal">
            <div class="title">PERSONAL SETTINGS</div>
            <div class="option_line">
              <div class="name">NICKNAME</div>
              <div class="value"><?php echo $_SESSION['nickname']; ?></div>
              <div class="buttons">
              </div>
            </div>
            <div class="option_line option_line_password">
              <div class="name">PASSWORD</div>
              <div class="value cursor_pointer">********</div>
              <div class="buttons">
                <div class="button_arrows"></div>
              </div>
              <div class="hidden">
                <div class="name">&nbsp;</div>
                <div class="value">
                  <div class="div_button">RESET PASSWORD</div>
                </div>
                <div class="buttons"></div>
              </div>
            </div>
            <div class="option_line option_line_email">
              <div class="name">E-MAIL</div>
              <div class="value cursor_pointer"><?php echo $_SESSION['email']; ?></div>
              <div class="buttons">
                <div class="button_arrows"></div>
              </div>
              <div class="hidden">
                <div class="name">CHANGE E-MAIL</div>
                <div class="value"><input type="text"></div>
                <div class="buttons">
                  <div class="button_commit"></div>
                </div>
              </div>
            </div>
            <div class="option_line option_line_phone">
              <div class="name">PHONE</div>
              <div class="value cursor_pointer"><?php echo $_SESSION['phone']; ?></div>
              <div class="buttons">
                <div class="button_arrows"></div>
              </div>
              <div class="hidden">
                <div class="name">CHANGE PHONE</div>
                <div class="value"><input type="text"></div>
                <div class="buttons">
                  <div class="button_commit"></div>
                </div>
              </div>
            </div>
            <div class="option_line option_line_gmt">
              <div class="name">TIME ZONE</div>
              <div class="value cursor_pointer">UTC <?php echo (($_SESSION['time_zone'] > 0) ? ('+') : ('')) . $_SESSION['time_zone']; ?></div>
              <div class="buttons">
                <div class="button_arrows"></div>
              </div>
              <div class="hidden">
                <div class="name">CHANGE TIME ZONE</div>
                <div class="value">
                  <select>
<?php
                  for ($i = -12; $i <= 13; $i++) {
                    echo '<option value="' . $i . '" ' . (($i == $_SESSION['time_zone']) ? ('selected="selected"') : ('')) . '>UTC ' . (($i > 0) ? ('+') : ('')) . $i . '</option>';
                  }
?>
                  </select>
                </div>
                <div class="buttons">
                  <div class="button_commit"></div>
                </div>
              </div>
            </div>
            <div class="option_line option_line_tsuid">
              <div class="name">TEAMSPEAK ID</div>
              <div class="value"><input type="text"></div>
              <div class="buttons">
                <div class="button_add">+</div>
              </div>
            </div>
          </div>
          <div class="settings_block_notifications">
            <div class="title">NOTIFICATIONS</div>
<?php
              $notification_types = $this->config->item('notification_types');
              foreach ($notification_types as $notify_type_tmp => $notify_name_tmp) {
                $notify_tmp = notify_decode(@$_SESSION[$notify_type_tmp]);
?>
                <div class="option_line option_line_<?php echo $notify_type_tmp; ?>" notification_type="<?php echo $notify_type_tmp; ?>">
                  <div class="name"><?php echo $notify_name_tmp; ?></div>
                  <div class="buttons">
<?php
                    if ($notify_type_tmp != 'notify_question_all_reply') {
?>
                      <div button_name="sms" title="SMS" class="button_phone <?php echo (($notify_tmp['sms']) ? ('active') : ('')); ?>"></div>
<?php
                    } else {
?>
                      <div button_name="" class="spacer"></div>
<?php
                    }
?>                      
                    <div  button_name="mail" title="Email" class="button_mail <?php echo (($notify_tmp['mail']) ? ('active') : ('')); ?>"></div>
                    <div  button_name="sound" title="Sound" class="button_sound <?php echo (($notify_tmp['sound']) ? ('active') : ('')); ?>"></div>
                  </div>
                </div>
<?php
              }
?>
          </div>
          <div class="settings_block_account">
            <div class="title">ACCOUNT & PAYMENT</div>
<?php
            if ($_SESSION['premium_to'] && $_SESSION['premium_to'] > date('Y-m-d H:i:s')) {
              $account_type = 'PREMIUM';
            } else {
              $account_type = 'REGULAR';
            }
?>
            <div class="option_line option_line_account_type" account_type="<?php echo $account_type; ?>">
              <div class="button_info" url="/ajax/page/accounts_popup"></div>
              <div class="name">ACCOUNT TYPE</div>
              <div class="value cursor_pointer"><?php echo $account_type; ?></div>
              <div class="buttons">
                <div class="button_arrows"></div>

              </div>
              <div class="hidden">
                <div class="button_spacer"></div>
                <div class="name"><?php echo (($account_type == 'PREMIUM') ? ('DOWN') : ('UP')); ?>GRADE TO</div>
                <div class="value"><?php echo (($account_type == 'PREMIUM') ? ('REGULAR') : ('PREMIUM')); ?></div>
                <div class="buttons">
                  <div class="button_commit"></div>
                </div>
              </div>
            </div>
<?php
            $settlement = $this->config->item('settlement');
?>
            <div class="option_line option_line_settlement" settlement="<?php echo $_SESSION['settlement']; ?>">
              <div class="button_info" url="/ajax/page/settlement_popup"></div>
              <div class="name">SETTLEMENT</div>
              <div class="value cursor_pointer"><?php echo $settlement[$_SESSION['settlement']]; ?></div>
              <div class="buttons">
                <div class="button_arrows"></div>

              </div>
              <div class="hidden">
                <div class="button_spacer"></div>
                <div class="name">UPGRADE TO</div>
                <div class="value"><?php echo $settlement[abs($_SESSION['settlement'] - 1)]; ?></div>
                <div class="buttons">
                  <div class="button_commit"></div>
                </div>
              </div>
            </div>

            <div class="option_line option_line_cash">
              <div class="button_spacer"></div>
              <div class="name">CASH</div>
              <div class="value">$<?php echo $_SESSION['cash']; ?></div>
              <div class="buttons">
              </div>
            </div>
<? /*
            <div class="option_line option_line_date_of_payment">
              <div class="button_info" url="/ajax/page/date_of_payment_popup"></div>
              <div class="name">DATE OF PAYMENT</div>
              <div class="value"><?php echo (($_SESSION['premium_to']) ? (date('Y-m-d', strtotime($_SESSION['premium_to']))) : ('NONE')); ?></div>
              <div class="buttons">
              </div>
            </div>
*/ ?>
			<div class="option_line option_line_date_of_payment">
              <div class="button_info" url="/ajax/page/date_of_payment_popup"></div>
              <div class="name">PREMIUM TO</div>
              <div class="value cursor_pointer"><?php echo (($_SESSION['premium_to']) ? (date('Y-m-d', strtotime($_SESSION['premium_to']))) : ('NONE')); ?></div>
              <div class="buttons">
                <div class="button_arrows"></div>
              </div>
              <div class="hidden">
                <div class="button_spacer"></div>
                <div class="name">FREEZE PREMIUM</div>
                <div class="value"><div class="freeze_button <?php echo ((isset($_SESSION['frozen_days']) && $_SESSION['frozen_days'] > 0 && $_SESSION['frozen_days'] < 366) ? ('unfreeze') : ('')); ?>"></div></div>
                <div class="buttons">
                  <!-- <div class="button_commit"></div> -->
                </div>
              </div>
            </div>



<?php
            $automatic_payment = $this->config->item('automatic_payment');
?>

            <div class="option_line option_line_automatic_payment" automatic_payment="<?php echo $_SESSION['automatic_payment']; ?>">
              <div class="button_info" url="/ajax/page/automatic_payment_popup"></div>
              <div class="name">AUTOMATIC PAYMENT</div>
              <div class="value cursor_pointer"><?php echo (($_SESSION['automatic_payment']) ? ('YES') : ('NO')); ?></div>
              <div class="buttons">
                <div class="button_arrows"></div>
              </div>
              <div class="hidden">
                <div class="button_spacer"></div>
                <div class="name">SET TO</div>
                <div class="value"><?php echo $automatic_payment[abs($_SESSION['automatic_payment'] - 1)]; ?></div>
                <div class="buttons">
                  <div class="button_commit"></div>
                </div>
              </div>
            </div>

            <div class="option_line option_line_fund_account">
              <div class="button_info" url="/ajax/page/fund_account_popup"></div>
              <div class="name">FUND YOUR ACCOUNT</div>
              <div class="value"><input type="text"></div>
              <div class="buttons">
                <div class="button_fund"></div>
              </div>
            </div>
          </div>
          <div class="settings_block_site">
            <a href="/user/logout" class="logoutbutton">&nbsp;</a>
            <a href="/page/terms" class="rulesbutton">&nbsp;</a>
          </div>
        </div>

        <div id="mainmenu">
          <div class="sitelogo">MY-TRADE<span>.PRO</span><sup>beta</sup></div>
<?
                  if (true || admin()) {
                    if ($_SESSION['premium_to'] > date('Y-m-d H:i:s')) {
                      $nickname = urlencode(transliterate1($_SESSION['nickname']) . '_#' . $_SESSION['user_id'] . '.');
                      $token = urlencode($_SESSION['teamspeak_token']);
                      //echo '<a class="teamspeak_link" href="ts3server://' . $_SERVER['HTTP_HOST'] . '?nickname=' . $nickname . '&token=' . $token . '&addbookmark=' . urlencode($_SERVER['HTTP_HOST']) . '">Join to Teamspeak conversation</a>';
                      echo '<a class="teamspeak_link" href="ts3server://' . $_SERVER['HTTP_HOST'] . '?nickname=' . $nickname . '&token=' . $token . '&addbookmark=' . urlencode($_SERVER['HTTP_HOST']) . '" title="Join to Teamspeak conversation">&nbsp;</a>';
                    }
                  }
?>
          <div class="menu">
            <ul>
              <li class="video" menu_name="video">MY-TRADE LIVE</li>
              <li class="table" menu_name="table">TRADE IDEAS</li>
              <li class="question" menu_name="question">ASK A QUESTION</li>
              <li class="blog" menu_name="blog">MY-TRADE BLOG</li>
            </ul>
          </div>
          <div class="options"></div>
        </div>
      </div>
      <div class="clickme_Ill_close_bar"></div>
    </div>