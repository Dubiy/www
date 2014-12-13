  if (typeof SERVER_TIME != 'undefined') {
    var now = new Date();
    var SERVER_min_diff = ((((SERVER_TIME.getTime() - now.getTime()) / 1000) / 60) % 60)|0;
    var SERVER_hour_diff = (((SERVER_TIME.getTime() - now.getTime()) / 1000) / 3600)|0;
  } else {
    var SERVER_min_diff = 0;
    var SERVER_hour_diff = 0;
  }
  
  var edit_user_form = '<div class="editcurrent_user">' +
          '<div class="edit_nickname"><input type="text"></div>' +
          '<div class="edit_email"><input type="text"></div>' +
          '<div class="edit_phone"><input type="text"><div class="write_sms" title="Отправить СМС"></div></div>' +
          '<div class="edit_cash" title="Баланс"><input type="text"></div>' +
          '<div class="edit_status">' +
            '<select>' +
              '<option value="1">REGULAR</option>' +
              '<option value="2">PREMIUM</option>' +
            '</select>' +
            '<div class="controll">' +
              '<div class="accept"></div>' +
              '<div class="cancel"></div>' +
              '<div class="login_as_user">LOGIN AS</div>' +
            '</div>' +
          '</div>' +
          '<div class="hidden_calendar">' +
            '<input type="text" class="jcalendar" value="Показать календарь">' +
          '</div>' +
          '<div class="reset_this_user_password">Reset password</div>' +
          '<div class="approve_teamspeak_unique_id">' +
            'Teamspeak: добавить Unique_ID <br />' +
            '<input type="text"><button>Add</button>' + 
          '</div>' +
          '<div class="set_chatadmin_permission">' +
            'Дать права ChatAdmin <br />' +
            '<div class="chatadmin"></div>' + 
          '</div>' +
        '</div>';

  function calc_time_since() {
    jQuery('.time_since').each(function() {
      jQuery(this).html(time_since(jQuery(this).attr('datetime')));
    });
  }

  function current_datetime() {
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth()+1;

    var yyyy = today.getFullYear();
    if(dd<10) {dd='0'+dd} if(mm<10) {mm='0'+mm} today = yyyy+'-'+mm+'-'+dd+' '+today.getHours()+':'+today.getMinutes()+':'+today.getSeconds();
    return today;
  }

  function delete_video_from_archive(this_) {
    if (confirm('Действительно удалить видео?')) {
      jQuery.post('/admin/archive_handler', {'action': 'delete', 'video_id': jQuery(this_).parent().attr('video_id')}, function(data) {
        jQuery(this_).parent().fadeOut(500, function() {
          jQuery(this).remove();
        });
      });
    }
  }

  function time_since(datetime, addbr) {
    if (datetime == '') {
      return 'NON';
    }
    var now = new Date();
    var new_hour = (now.getHours() + SERVER_hour_diff) % 24;
    if (new_hour < 0) {
      new_hour = 24 + new_hour;
    }
    var new_min = now.getMinutes() + SERVER_min_diff % 60;
    if (new_min < 0) {
      new_min = 60 + new_min;
    }
    now.setHours(new_hour);
    now.setMinutes(new_min);

    var datetime_ = new Date(datetime);
    var since = (now.getTime() - datetime_.getTime())/1000;
    var chunks = new Array(
        new Array(60 * 60 * 24 * 365 , 'YR'),
        new Array(60 * 60 * 24 * 30 , 'MTH'),
        new Array(60 * 60 * 24 * 7, 'WK'),
        new Array(60 * 60 * 24 , 'DY'),
        new Array(60 * 60 , 'HR'),
        new Array(60 , 'MIN'),
        new Array(1 , 'SEC')
    );
    for (i = 0, j = chunks.length; i < j; i++) {
        seconds = chunks[i][0];
        name = chunks[i][1];
        if ((count = Math.floor(since / seconds)) != 0) {
            break;
        }
    }
    if (count < 0) {
      return 'NOW';
    }

    if (count == 1) {
      if (addbr) {
        return '1<br />' + name;
      } else {
        return '1 ' + name;
      }
    } else {
      if (addbr) {
        return count + '<br />' + name; // + 'S';
      } else {
        return count + ' ' + name; // + 'S';
      }
    }
  }

  jQuery(document).ready(function() {
    setInterval('calc_time_since()', 5000);
    calc_time_since();

    jQuery('.table_records .ticker_fields select').change(function() {
      var self = this;
      jQuery(self).closest('.ticker_fields').find('div.wysiwyg:not(.comment)').remove();//;
      jQuery(self).closest('.ticker_fields').find('.wysiwyg').replaceWith('<textarea class="comment wysiwyg" name="text"></textarea>').wysiwyg({rmUnusedControls: true,
        controls: {
          bold: { visible : true },
          italic: { visible : true },
          strikeThrough: { visible : true },
          removeFormat: { visible : true }
        }});
    });

    jQuery('.block_admin.add_video .youtubelink').change(function() {
      jQuery.post('/ajax/youtuber', {'link': jQuery(this).val()}, function(data) {
        //alert(data.storyboard_spec);
        jQuery('.preview_add_video_form h2').html(data.title);
        jQuery('.add_video_form textarea').val(data.title);
        jQuery('.preview_add_video_form .time span').html(data.length_seconds);
        jQuery('.preview_add_video_form .views span').html(data.view_count);
        jQuery('.preview_add_video_form .thumbnail').html('<img src="' + data.thumbnail_url + '"/>');
      }, 'json');
    });

    jQuery('.block_admin.add_video .accept').click(function() {
      if (jQuery('#checkboxforpremium').attr('checked') == 'checked') {
        var is_premium = '1';
      } else {
        var is_premium = '0';
      }
      jQuery.post('/admin/archive_handler', {'action': 'add', 'link': jQuery('.add_video_form .youtubelink').val(), 'is_premium': is_premium, 'title': jQuery('.add_video_form textarea').val()}, function(data) {
        if (typeof data.err == "undefined") {
          if (is_premium == '1') {
            var is_premium_text = 'YES';
          } else {
            var is_premium_text = 'NO';
          }
          jQuery('.block_videoarchive_list').prepend(
            '<div class="video_record_line video_id_' + data.video_id + '" video_id="' + data.video_id + '" style="display:none;">' +
              '<h2>' + data.title + '</h2>' +
              '<div class="time">Длина: <span>' + data.length + '</span></div>' +
              '<div class="views">Просмотров: <span>' + data.view_count + '</span></div>' +
              '<div class="is_premium">PREMIUM: <span>' + is_premium_text + '</span></div>' +
              '<div class="thumbnail">' +
                '<img width="120" src="' + data.thumbnail_url + '" />' +
              '</div>' +
              '<div class="time_added">Добавлено: <span class="time_since" datetime="' + data.datetime + '">0 SECS</span></div>' +
              '<div class="delete"></div>' +
            '</div>'
          ).find('.video_id_' + data.video_id).fadeIn(500).find('.delete').click(function() {
            delete_video_from_archive(this);
          });
        } else {
          alert(data.err);
        }
        jQuery('.preview_add_video_form h2').html('');
        jQuery('.add_video_form textarea').val('');
        jQuery('.add_video_form .youtubelink').val('');
        jQuery('.preview_add_video_form .time span').html('');
        jQuery('.preview_add_video_form .views span').html('');
        jQuery('.preview_add_video_form .thumbnail').html('');
      }, 'json');
    });

    jQuery('.video_record_line .delete').click(function() {
      delete_video_from_archive(this);
    });

    jQuery('.ticker_fields select').change(function() {
      var self = this;
      if (jQuery(self).val() == '0') {
        jQuery(self).parent().find('.checkbox').attr('checked', 'checked');
      }
    });

    jQuery('.lets_ban_this_user').change(function() {
      var this_ = this;
      jQuery.post('/admin/user', {'action': 'ban', 'user_id': jQuery(this_).parent().parent().attr('user_id'), 'ban': jQuery(this_).val()}, function(data) {
        if (data != '') {
          alert(data);
        } else {
          jQuery(this_).css('background-color', '#00aa00').animate({ backgroundColor: "#eaeaea" }, 1000);
        }
      });
    });

    /*$("input[type=text]:not(.nodefault)").focus(function(){
      if(this.value == this.defaultValue) this.value = '';
    }).focusout(function(){
      if(this.value == "") this.value = this.defaultValue;
    });*/

    jQuery('.userlist_body .user .delete').click(function() {
      var user = jQuery(this).parent().parent();
      var user_id = jQuery(user).attr('user_id');
      if (confirm('Действительно хотите удалить пользователя?')) {
        jQuery.post('/admin/user', {'action': 'delete', 'user_id': user_id}, function (data) {
          if (data != '') {
            alert(data);
          } else {
            jQuery(user).hide(500, function() {
              jQuery(this).remove();
            });
          }
        });
      }
    });


    jQuery('.userlist_body .user .star').click(function() {
      var this_ = this;
      var curr_user = jQuery(this_).parent().parent().attr('user_id');
      jQuery.post('/admin/user', {'action': 'toggle_star', 'user_id': curr_user}, function(data) {
        if (data == 'star') {
          jQuery(this_).addClass('active');
        } else {
          jQuery(this_).removeClass('active');
        }
      });
    });

    jQuery('.userlist_body .user .banned_chat').click(function() {
      var this_ = this;
      var curr_user = jQuery(this_).parent().parent().attr('user_id');
      jQuery.post('/admin/user', {'action': 'toggle_banned_chat', 'user_id': curr_user}, function(data) {
        if (data == 'banned_chat') {
          jQuery(this_).addClass('active');
        } else {
          jQuery(this_).removeClass('active');
        }
      });
    });

    jQuery('.userlist_body .user .edit').click(function() {
      var this_ = this;
      jQuery('.userlist_body .user .editcurrent_user').remove();
      if (jQuery(this_).parent().parent().hasClass('editing')) {
        jQuery('.userlist_body .user').removeClass('editing');
      } else {
        jQuery('.userlist_body .user').removeClass('editing');
        var curr_user = jQuery(this_).parent().parent();
        var edit_user = jQuery(this_).parent().parent().addClass('editing').append(edit_user_form).find('.editcurrent_user');
        jQuery(edit_user).find('.edit_nickname input').attr('value', jQuery(curr_user).find('.nickname').text());
        jQuery(edit_user).find('.edit_email input').attr('value', jQuery(curr_user).find('.email').text());
        jQuery(edit_user).find('.edit_phone input').attr('value', jQuery(curr_user).find('.phone').text());
        jQuery(edit_user).find('.edit_cash input').attr('value', jQuery(curr_user).find('.cash').text().slice(2));
        if (jQuery(curr_user).attr('chatadmin') == '1') {
          jQuery(edit_user).find('.set_chatadmin_permission .chatadmin').addClass('active');
        }
        jQuery(edit_user).find('.set_chatadmin_permission .chatadmin').click(function() {
          var self = this;
          var user_id = jQuery(this).closest('.user').attr('user_id');
          jQuery.post('/admin/user/', {'action': 'chatadmin', 'user_id': user_id, 'chatadmin': jQuery(edit_user).find('.set_chatadmin_permission .chatadmin').hasClass('active')}, function(data) {
            if (data == 'chatadmin') {
              jQuery(self).addClass('active');
            } else if (data == 'regular') {
              jQuery(self).removeClass('active');
            } else {
              alert(data);
            }
          });
        });
        jQuery(edit_user).find('.edit_phone .write_sms').click(function() {
          var self = this;
          var message=prompt('Текст сообщения', '');
          if (message != null && message != '') {
            jQuery.post('/admin/send_sms', {'number': jQuery(curr_user).find('.phone').text(), 'message': message}, function() {
              jQuery(self).css('background-color', '#00FF00').animate({'backgroundColor': '#FFFFFF'}, 1000);
            });
          }
        });

        jQuery(edit_user).find('.approve_teamspeak_unique_id button').click(function() {
          var user_id = jQuery(this).closest('.user').attr('user_id');
          var unique_id = jQuery(edit_user).find('.approve_teamspeak_unique_id input').val();
          jQuery.post('/admin/user/', {'action': 'approve_teamspeak_unique_id', 'user_id': user_id, 'unique_id': unique_id}, function(data) {
            if (data == 'ok') {
              jQuery(edit_user).find('.approve_teamspeak_unique_id input').css('background-color', '#00FF00').animate({'backgroundColor': '#FFFFFF'}, 500).val('');
            } else {
              jQuery(edit_user).find('.approve_teamspeak_unique_id input').css('background-color', '#FF0000').animate({'backgroundColor': '#FFFFFF'}, 500).val(data);
            }
          });
        });

        jQuery('.editcurrent_user .login_as_user').click(function() {
          var self = this;
          var user_id = jQuery(self).closest('.user').attr('user_id');
          //alert(user_id);
          jQuery.post('/admin/login_as/', {'user_id': user_id}, function(data) {
            if (data != '') {
              alert(data);
            } else {
              location.href = '/';
            }
            
          });
        });

        jQuery(edit_user).find('.reset_this_user_password').click(function() {
          if (confirm('Сбросить пароль?')) {
            jQuery.post('/admin/user', {'action': 'reset_password', 'user_id': jQuery(curr_user).attr('user_id')}, function(data) {
              alert(data);
            });
          }
        })
        //jQuery(edit_user).find('.edit_nickname input').attr('value', jQuery(curr_user).find('.account').attr('account_type'));
        if (jQuery(curr_user).find('.account').attr('account_type') == '10') {
          jQuery(edit_user).find(".edit_status select").remove();
        }
        jQuery(edit_user).find(".edit_status select [value='" + (parseInt(jQuery(curr_user).find('.account').attr('is_premium')) + 1) + "']").attr("selected", "selected").parent().change(function() {
          show_hide_calendar(this);
        });
        show_hide_calendar(jQuery(edit_user).find(".edit_status select"));
        jQuery(edit_user).find('.jcalendar').attr('value', jQuery(curr_user).find('.premium_to').text());

        //.find('.edit_nickname input').attr('value', 'lolo');
        jQuery(edit_user).find('.accept').click(function() {
          edit_accept_click(this);
        });

        jQuery(edit_user).find('.cancel').click(function() {
          jQuery('.userlist_body .user').removeClass('editing');
          jQuery('.userlist_body .user .editcurrent_user').remove();
        });
      }
    });


    jQuery('.add_notification .accept').click(function() {
      if (jQuery('.add_notification textarea').val() != '') {
        jQuery.post('/admin/notification', {'action': 'add', 'text': jQuery('.add_notification textarea').val()}, function(data) {
          jQuery('.notifications').prepend('<div class="notification" notification_id="' + data + '"><div class="datetime time_since" datetime="' + current_datetime() + '">0 SEC</div><div class="notification_text">' + jQuery('.add_notification textarea').val() + '</div><div class="delete"></div></div><div class="clear">');
          jQuery('.add_notification textarea').val('');
          jQuery('.notification .delete:first').click(function() {
            notification_delete(this);
          });
        });
      }
    });

    jQuery('.notification .delete').click(function() {
      notification_delete(this);
    });

    jQuery('.question .delete').click(function() {
      var this_ = this;
      //if (confirm('Действительно хотите удалить вопрос?')) {
        jQuery.post('/admin/question', {'action': 'delete', 'question_id': jQuery(this).parent().parent().parent().attr('question_id')}, function(data) {
          jQuery('.unanswered_count').html(data);
          jQuery(this_).parent().parent().parent().hide(500, function() {
            jQuery(this).remove();
          });
        });
      //}
    });

    jQuery('.question .answer').keydown(function (e) {
      var self = this;
      if (e.ctrlKey && e.keyCode == 13) {
        jQuery(self).parent().find('.question_controll .accept').click();
      }
    });


    jQuery('.question_controll .accept').click(function() {
      var this_ = this;
      var silent = jQuery(this_).parent().find(':checked').length;
      jQuery.post('/admin/question', {'action': 'answer', 'question_id': jQuery(this).parent().parent().parent().attr('question_id'), 'answer': jQuery(this).parent().parent().find('.answer').val(), 'private': silent}, function(data) {
        jQuery('.unanswered_count').html(data);
        jQuery(this_).parent().parent().find('.answer').attr("disabled", "disabled");
        jQuery(this_).parent().find('input').attr("disabled", "disabled");
        jQuery(this_).hide(500);
       });
    });

    jQuery('.delete_questions_without_answers').click(function() {
      if (confirm('Действительно хотите удалить все вопросы без ответов?')) {
        jQuery.post('/admin/question', {'action': 'delete_no_answer'}, function(data) {
          if (data != '') {
            alert(data);
          } else {
            jQuery('.unanswered_count').html('0');
            jQuery('.question').each(function() {
              if (jQuery(this).find('.answer').val() == '') {
                jQuery(this).hide(500, function() {
                  jQuery(this).remove();
                })
              }
            });
          }
        });
      }
      return false;
    });

    jQuery('.delete_answered_questions_today_yesterday').click(function() {
      if (confirm('Действительно хотите удалить отвеченные вопросы за вчера и позавчера?')) {
        jQuery.post('/admin/question', {'action': 'delete_answered_questions_today_yesterday'}, function(data) {
          if (data != '') {
            alert(data);
          } else {
            location.reload();
          }
        });
      }
      return false;
    });

    jQuery('.href_strong_cash_for_all, .href_no_recomendation_for_all').click(function() {
      if (jQuery(this).hasClass('href_strong_cash_for_all')) {
        var type = 'STRONG CASH';
      } else {
        var type = 'NO RECOMENDATION';
      }
      if (confirm('Уверенны, что хотите обновить все тикеры?')) {
        jQuery.post('/admin/table_handler', {'action': 'update_all_tickers', 'type': type}, function(data) {
          if (data != '') {
            alert(data);
          } else {
            location.reload();
          }
        });
      }
    });

    jQuery('.ticker .controll .trash').click(function () {
      var this_ = this;
      if (confirm('Удалить тикер? Будет удалена вся история для этого тикера')) {
        jQuery.post('/admin/table_handler', {'action': 'delete_ticker', 'ticker_id': jQuery(this_).parent().parent().attr('ticker_id')}, function(data) {
          if (data != '') {
            alert(data);
          } else {
            jQuery(this_).parent().parent().hide(500, function() {
              jQuery(this).remove();
            })
          }
        });
      }
    });

    jQuery('.ticker .controll .refresh').click(function () {
      var this_ = this;
      if (jQuery(this_).hasClass('opened')) {
        jQuery(this_).removeClass('opened');
        jQuery(this_).parent().parent().find('.short_term .history').html('').parent().parent().find('.long_term .history').html('');
      } else {
        jQuery(this_).addClass('opened');
        jQuery.post('/admin/table_handler', {'action': 'last_recomendations', 'count': '5', 'ticker_id': jQuery(this).parent().parent().attr('ticker_id')}, function(data) {
          jQuery(this_).parent().parent().find('.short_term .history').html(data.short).parent().parent().find('.long_term .history').html(data.long);
          jQuery('.ticker .record select').change(function() {
            change_prognose(this);
          });

          jQuery('.ticker .record .delete').click(function() {
            delete_recomendation(this);
          });
          jQuery('.load_all_recomendations').click(function() {
            load_all_recomendation(this);
          });
        }, 'json');
      }

    });

    jQuery('.ticker .form .ticker_fields .accept').click(function() {
      var this_ = this;
      jQuery(this_).parent().parent().parent().parent().find('form').ajaxForm({target: '#preview', success: function() {
        jQuery(this_).parent().parent().parent().parent().find('.time').html('NOW').attr('title', current_datetime()).attr('datetime', current_datetime());
        jQuery(this_).parent().parent().parent().parent().find('form').parent().parent().css('background-color', '#00FF00').animate({'backgroundColor': '#FFFFFF'}, 500);

      }}).submit();
    

      //image uploader
      /*
        1 upload
        2 rename
        3 resize
        4 ticker update
      */


      
      /*
      jQuery.post('/admin/table_handler', {'action': 'add_recomendation',
                                           'ticker_id': jQuery(this_).parent().parent().parent().parent().parent().attr('ticker_id'),
                                           'type': jQuery(this_).parent().parent().find('select').val(),
                                           'index': jQuery(this_).parent().parent().find('input').attr('value'),
                                           'text': jQuery(this_).parent().parent().find('textarea').val(),
                                           'long': long_term}, function(data) {
        if (data != '') {
          alert(data);
        } else {
          jQuery(this_).parent().parent().parent().find('.time').html('NOW').attr('title', current_datetime()).attr('datetime', current_datetime());
        }
      });
      */
    });

		jQuery('.wysiwyg').wysiwyg({rmUnusedControls: true,
			controls: {
				bold: { visible : true },
				italic: { visible : true },
				strikeThrough: { visible : true },
				removeFormat: { visible : true }
			}}
    );

  });

  function edit_accept_click(this_) {
    var edit_form = jQuery(this_).parent().parent().parent();
    var user_id = jQuery(edit_form).parent().attr('user_id');
    jQuery.post('/admin/user', {'action': 'edit',
                                'user_id': user_id,
                                'nickname': jQuery(edit_form).find('.edit_nickname input').attr('value'),
                                'email': jQuery(edit_form).find('.edit_email input').attr('value'),
                                'phone': jQuery(edit_form).find('.edit_phone input').attr('value'),
                                'cash': jQuery(edit_form).find('.edit_cash input').attr('value'),
                                'account_type': jQuery(edit_form).find('.edit_status select').val(),
                                'premium_to': jQuery(edit_form).find('.hidden_calendar input').attr('value')},
      function(data) {
        if (data != '') {
          //шось не так
          alert(data);
        } else {
          jQuery(edit_form).parent().find('.nickname').html(jQuery(edit_form).find('.edit_nickname input').attr('value'));

          var now = new Date();
          var to = new Date(jQuery(edit_form).find('.hidden_calendar input').attr('value'));
          if ((jQuery(edit_form).find('.edit_status select').val() == 2) && (to.getTime()-now.getTime() > 0)) {
            jQuery(edit_form).parent().find('.account').html('premium').attr('is_premium', '1');
          } else {
            jQuery(edit_form).parent().find('.account').html('regular').attr('is_premium', '0');
          }
          jQuery('.userlist_body .user').removeClass('editing').find('.editcurrent_user').remove();
        }
      }
    );
  }

  function addZero(i) {
    return (i < 10)? "0" + i: i;
  }

  function show_hide_calendar(this_) {
    if (jQuery(this_).val() == 2) {
      jQuery('.hidden_calendar').show();
      if (jQuery('.jcalendar').val() == '') {
        var today = new Date();
        date_str = today.getFullYear() + '-' + addZero(today.getMonth() + 1) + '-' +  addZero(today.getDate());
        jQuery('.jcalendar').val(date_str);
      }
      jQuery('.jcalendar').datepicker({ altFormat: "yy-mm-dd", dateFormat: "yy-mm-dd"});
    } else {
      jQuery('.hidden_calendar').hide();
    }
  }

  function notification_delete(that_) {
    var this_ = that_;
    jQuery.post('/admin/notification', {'action': 'delete', 'notification_id': jQuery(this_).parent().attr('notification_id')}, function(data) {
      if (data != '') {
        alert(data);
      } else {
        jQuery(this_).parent().hide(500, function() {
          jQuery(this).remove();
        })
      }
    });
  }

  function delete_recomendation(this_) {
    var recomendation = jQuery(this_).parent().parent();
    if (confirm('Действительно удалить?')) {
      jQuery.post('/admin/table_handler', {'action': 'delete_recomendation', 'recomendation_id': jQuery(recomendation).attr('recomendation_id')}, function(data) {
        if (data != '') {
          alert(data);
        } else {
          jQuery(recomendation).hide(500, function() {
            jQuery(this).remove();
          })
        }
      })
    }
  }

  function load_all_recomendation(this_) {
    var ticker_id = jQuery(this_).parent().parent().parent().attr('ticker_id');
    jQuery.post('/admin/table_handler', {'action': 'last_recomendations', 'count': '5000', 'ticker_id': ticker_id}, function(data) {
      jQuery(this_).parent().parent().parent().find('.short_term .history').html(data.short).parent().parent().find('.long_term .history').html(data.long);
      jQuery('.ticker .record select').change(function() {
        change_prognose(this);
      });

      jQuery('.ticker .record .delete').click(function() {
        delete_recomendation(this);
      });
      jQuery('.load_all_recomendations').click(function() {
        load_all_recomendation(this);
      });
    }, 'json');
  }

  function change_prognose(self) {
    prognose = jQuery(self).find(':selected').attr('value');
    recomendation_id = jQuery(self).parent().parent().parent().attr('recomendation_id');
    jQuery.post('/admin/table_handler', {'action': 'set_prognose', 'recomendation_id': recomendation_id, 'prognose': prognose}, function(data) {
      jQuery(self).css('background-color', 'green').animate({backgroundColor: '#ffffff'}, 500);
      if (data != '') {
        alert(data);
      }
    });
  }

  function add_ticker_on_submit(this_) {
    jQuery.post('/admin/table_handler', {'action': 'add_ticker', 'name': jQuery(this_).find('.ticker_name').attr('value'), 'title': jQuery(this_).find('.ticker_title').attr('value')}, function(data) {
      if (data != '') {
        alert(data);
      } else {
        location.reload();
      }
    });
    return false;
  }

function print_r(obj) {
  str = '';
  for (myKey in obj){
    str = str + "obj["+myKey +"] = "+obj[myKey] + "\n";
  }
  return str;
}
