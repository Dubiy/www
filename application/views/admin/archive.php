<div class="clear"></div>

<div class="block_admin add_video">
<?php
  if (isset($video) && is_array($video) && count($video)) {
?>
    <form method="POST" action="">
<?php
  }
?>
      <div class="add_video_form">
        <input type="text" name="link" title="Youtube Link" class="youtubelink" value="<? echo ((isset($video[0]->code)) ? ('http://www.youtube.com/watch?v=' . $video[0]->code) : ('')); ?>">
        <textarea name="title"><? echo ((isset($video[0]->title)) ? ($video[0]->title) : ('Название записи архива')); ?></textarea>
        <label for="checkboxforpremium" class="checkboxforpremium"><input type="checkbox" name="is_premium" id="checkboxforpremium" <? echo ((isset($video[0]->is_premium) && $video[0]->is_premium == 1) ? ('checked="checked"') : ('')); ?>>&nbsp;PREMIUM</label>
        <? echo ((isset($video[0]->title)) ? ('<input type="submit" value="Сохранить">') : ('<div class="accept"></div>')); ?>
      </div>
<?php
  if (isset($video) && is_array($video) && count($video)) {
?>
    </form>
<?php
  }
?>
  <div class="preview_add_video_form" <? echo ((isset($video) && is_array($video) && count($video)) ? ('style="display:none;"') : ('')); ?>>
    <h2>Без названия</h2>
    <div class="time">Длина: <span>00:00</span></div>
    <div class="views">Просмотров: <span>0</span></div>
    <div class="thumbnail"></div>
  </div>
</div>
<div class="clear"></div>

<div class="block_admin block_videoarchive_list">
<?php
  if (isset($videos) && is_array($videos) && count($videos)) {
    foreach ($videos as $video) {
?>
      <div class="video_record_line video_id_<?php echo $video->video_id; ?>" video_id="<?php echo $video->video_id; ?>">
        <h2><a href="/admin/archive/edit/<?php echo $video->video_id; ?>"><?php echo $video->title; ?></a></h2>
        <div class="time">Длина: <span><?php echo date('i:s', $video->length); ?></span></div>
        <div class="views">Просмотров: <span><?php echo $video->view_count; ?></span></div>
        <div class="is_premium">PREMIUM: <span><?php echo (($video->is_premium) ? ('YES') : ('NO')); ?></span></div>
        <div class="thumbnail">
          <img width="120" src="<?php echo $video->thumbnail_url; ?>" />
        </div>
        <div class="time_added">Добавлено: <span class="time_since" datetime="<?php echo mysql2js_date($video->datetime); ?>"><?php echo time_since($video->datetime); ?></span></div>
        <div class="delete"></div>
      </div>
<?php
    }
  } else {
    echo 'Нет оповещений';
  }
?>
</div>

<style type="text/css">
  .checkboxforpremium input {
    width: auto;
  }
</style>

