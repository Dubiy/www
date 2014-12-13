<div class="block_admin pages_list">
  <div class="page_record header">
    <div class="alias">Адрес</div>
    <div class="title">Заголовок</div>
    <div class="clear"></div>
  </div>
<?php
  if (isset($pages) && is_array($pages) && count($pages)) {
    foreach ($pages as $page) {
?>
      <div class="page_record">
        <div class="alias"><a href="/page/<?php echo $page->alias; ?>" target="_blank">/page/<b><?php echo $page->alias; ?></b></a></div>
        <div class="title"><a href="/admin/pages/edit/<?php echo $page->page_id; ?>"><span><?php echo $page->title; ?></span> <img src="/images/admin/edit_user.png"></a></div>
        <div class="clear"></div>
      </div>
<?php
    }
  }
?>
  <a href="/admin/pages/add" class="btn">Добавить страницу</a>
</div>

<style type="text/css">
  .page_record {
    margin-bottom: 5px;
    background-color: rgba(0,0,0,0.1);
    line-height: 24px;
  }

  .page_record img {
    float: right;
  }

  .page_record.header {
    font-weight: bold;
  }

  .page_record .alias {
    float: left;
    width: 250px;
  }

  .page_record .title {
    float: left;
    width: 600px;
  }

  .page_record .controll {
    float: left;
    width: 100px;
  }
</style>