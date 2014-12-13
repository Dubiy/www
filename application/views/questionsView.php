<?php
<div id="content">
<?php foreach ($questions as $row)
      if(isset($row) && is_array($row) && count($row)>=0)
        {;?>
    <!-- вывод результата из массива -->
<div id="questions">
    <h2><a href="/question/show/<?=$row['id'];?>"><?= $row['question'];?></a></h2>
    <div class="date"><?= date("d.m.Y H:i",strtotime( $row['date']));?></div>
    <div class="body"><?= $row['body'];?></div>
    <div class="description"><?=$row['description'];?></div>
    <div class="more"><a href="/blog/show/<?=$row['id'];?>">подробнее...</a></div>
    <?php };?>
</div>
<?php } else {
    return 'Нет статей в базе';
};?>
</div>