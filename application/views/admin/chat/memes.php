<div class="block_admin add_meme">
  <form method="POST" enctype="multipart/form-data" action="">

    Добавить мем: 
    <input type="text" placeholder="Название" name="title">
    <input type="file" name="photoimg" class="file photoimg ">
    <input type="submit" value="Добавить">
  </form>
</div>

<div class="block_admin memes">
<?php 
  if (isset($memes) &&  is_array($memes) && count($memes)) {
    foreach ($memes as $meme) {
      // print_r($meme);
?>
      <div class="meme" meme_id="<?php echo $meme->meme_id; ?>">
        <div class="title"><?php echo $meme->title; ?></div>
        <div class="delete">[удалить]</div>
        <img src="/upload/chat/memes/<?php echo $meme->src; ?>" >  
      </div>
<?php
    }
  } else {
    echo 'Нет мемов?';
  }

?>
</div>

<style type="text/css">
  .block_admin.memes .meme {
    transition:All 0.5s ease;
    -webkit-transition:All 0.5s ease;
    height: 142px;
    position: relative;
  }

  .block_admin.memes .meme:hover {
    background-color: #f0f0f0;
  }

  .block_admin.memes .meme .title {
    float: left;
  }

  .block_admin.memes .meme .delete {
    margin-left: 10px;
    cursor: pointer;
    float: left;
  }


  .block_admin.memes .meme img {
    transition:All 0.5s ease;
    -webkit-transition:All 0.5s ease;
    position: absolute;
    right: 5px;
    top: 5px;
    height: 120px;
    padding: 5px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: white;
    display: block;
    z-index: 1;
  }

  .block_admin.memes .meme img:hover {
    height: 400px;
    z-index: 1000;
  }

</style>

<script>
  jQuery(document).ready(function() {
    jQuery('.block_admin.memes .meme .delete').click(function() {
      var self = this;
      if (confirm('Действительно удалить?')) {
        var meme_id = jQuery(self).closest('.meme').attr('meme_id');
        jQuery.post('/admin/memes', {'delete': 'meme', 'meme_id': meme_id}, function() {
          jQuery(self).closest('.meme').css('overflow', 'hidden').animate({'height': '0'}, 500);
        });
      }
    });
  });
</script>