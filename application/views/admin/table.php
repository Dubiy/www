<script type="text/javascript" src="/js/jquery.form.js"></script>
<div class="block_admin add_ticker">
  <form method="POST" action="/admin/table" onSubmit="return add_ticker_on_submit(this);">
    <input type="text" class="ticker_name" maxlength="7" placeholder="ТИКЕР">
    <input type="text" class="ticker_title" placeholder="Название">
    <input type="image" class="add_ticker_accept" src="/images/admin/accept.png">
  </form>
  <a class="href_strong_cash_for_all" href="#">STRONG CASH ДЛЯ ВСЕХ</a>
  <a class="href_no_recomendation_for_all" href="#">NO RECOMENDATION ДЛЯ ВСЕХ</a>
  
  <div class="clear"></div>
</div>

<?php
  $recomendations = $this->config->item('recomendations');
?>

<div class="block_admin table_records">
<div id="preview"></div>
<div class="tickers_header">
  <div class="tickers_header_ticker">Ticker</div>
  <div class="tickers_header_short">Short-term</div>
  <div class="tickers_header_long">Long-term</div>
</div>
<?php
  if (isset($tickers) && is_array($tickers) && count($tickers)) {
    $NULL_val = $this->config->item('recomendation_no_index_constant');
    foreach ($tickers as $ticker) {
?>
      <div class="ticker" ticker_id="<?php echo $ticker->ticker_id; ?>">
        <div class="ticker_name"><?php echo $ticker->name; ?></div>
        <div class="short_term">
          <div class="form">
            <form id="imageform_tid<?php echo $ticker->ticker_id; ?>_short" method="POST" enctype="multipart/form-data" action="/admin/table_handler/">
              <input type="hidden" name="action" value="add_recomendation">
              <input type="hidden" name="ticker_id" value="<?php echo $ticker->ticker_id; ?>">
              <input type="hidden" name="long" value="0">
<?php
              $time_since = time_since($ticker->datetime_s, $between);
?>          
              <div class="time time_since" datetime="<?php echo $ticker->datetime_s; ?>" title="<?php echo (($between < 1300000000) ? (date('d.m.Y H:i:s', strtotime($ticker->datetime_s))) : ('')); ?>"><?php echo (($between < 1300000000) ? ($time_since) : ('-----')); ?></div>
              <div class="ticker_fields">
                <select name="type">
<?php
                  $return = '';
                  foreach($recomendations as $k => $v) {
                    $return .= '<option value="' . $k . '" ' . (($k == $ticker->type_s) ? ('selected="selected"') : ('')) . '>' . $v . '</option>';
                  }
                  echo $return;
?>          
                </select>
                <input class="index" name="index" value="<?php echo (($NULL_val == $ticker->index_s) ? ('') : ($ticker->index_s)); ?>">
                <textarea class="comment wysiwyg" name="text"><?php echo $ticker->text_s; ?></textarea>
                <div class="additional">
                  <input type="checkbox" class="checkbox" id="silent_tid<?php echo $ticker->ticker_id; ?>_short" name="silent"><label for="silent_tid<?php echo $ticker->ticker_id; ?>_short" class="checkboxlabel"> silent</label>
                  <div class="accept"></div>
                </div>
              
              </div>
            </form>
          </div>
          <div class="history"></div>
        </div>




























        <div class="long_term">
          <div class="form">
            <form id="imageform_tid<?php echo $ticker->ticker_id; ?>_long" method="POST" enctype="multipart/form-data" action="/admin/table_handler/">
              <input type="hidden" name="action" value="add_recomendation">
              <input type="hidden" name="ticker_id" value="<?php echo $ticker->ticker_id; ?>">
              <input type="hidden" name="long" value="1">
<?php
              $time_since = time_since($ticker->datetime_l, $between);
?>          
              <div class="time time_since" datetime="<?php echo $ticker->datetime_l; ?>" title="<?php echo (($between < 1300000000) ? (date('d.m.Y H:i:s', strtotime($ticker->datetime_l))) : ('')); ?>"><?php echo (($between < 1300000000) ? ($time_since) : ('-----')); ?></div>
              <div class="ticker_fields">
                <select name="type">
<?php
                  $return = '';
                  foreach($recomendations as $k => $v) {
                    $return .= '<option value="' . $k . '" ' . (($k == $ticker->type_l) ? ('selected="selected"') : ('')) . '>' . $v . '</option>';
                  }
                  echo $return;
?>          
                </select>
                <input class="index" name="index" value="<?php echo (($NULL_val == $ticker->index_l) ? ('') : ($ticker->index_l)); ?>">
                <textarea class="comment wysiwyg" name="text"><?php echo $ticker->text_l; ?></textarea>
                <div class="additional">
                  <input type="checkbox" class="checkbox" id="silent_tid<?php echo $ticker->ticker_id; ?>_short" name="silent"><label for="silent_tid<?php echo $ticker->ticker_id; ?>_short" class="checkboxlabel"> silent</label>
                  <div class="accept"></div>
                </div>
              
              </div>
            </form>
          </div>
          <div class="history"></div>
        </div>


        <div class="controll">
          <div class="refresh"></div>
          <div class="trash"></div>
        </div>
      </div>
      <div class="clear"></div>
<?php    
    }
  } else {
    echo 'Нет тикеров';
  }
?>
</div>




<script>


	jQuery(document).ready(function() {
    /*jQuery('#photoimg').live('change', function() { 
      jQuery("#preview").html('');
      jQuery("#preview").html('<img src="loader.gif" alt="Uploading...."/>');
      jQuery("#imageform").ajaxForm({target: '#preview'}).submit();
    });*/

    jQuery('.photoimg').live('change', function() {
      var self = this;
      var rid = jQuery(this).parent().parent().parent().parent().attr('recomendation_id');
      jQuery(this).parent().ajaxForm({target: '.recomendation_id_' + rid + ' .form_preview', success: function() {
        jQuery(self).addClass('hidden');
      }}).submit();
    });

    jQuery('button.remove_image').live('click', function() {
      var self = this;
      var rid = jQuery(self).attr('recomendation_id');
      if (confirm('Удалить изображение?')) {
        //delete
        jQuery.post('/admin/table_handler/', {'action': 'remove_image', 'recomendation_id': rid}, function() {
          jQuery(self).parent().parent().find('.photoimg').removeClass('hidden');
          jQuery(self).parent().parent().find('.form_preview').html('');
        });        
      }
      return false;
    });

	});  
</script>

