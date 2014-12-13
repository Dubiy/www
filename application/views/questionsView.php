<div id="content">

<?php

print_r($questions);

if(isset($questions) && is_array($questions) && count($questions)) {
    foreach ($questions as $row) {


?>
    <!-- вывод результата из массива -->
<div id="questions">
    <h2><a href="/question/show/<?php echo $row->question_id;?>"><?php echo $row->text;?></a></h2>
<!--    <div class="date">--><?php //echo date("d.m.Y H:i",strtotime( $row['date']));?><!--</div>-->
    <div class="body"><?php echo $row->text;?></div>
<!--    <div class="description">--><?php //echo $row['description'];?><!--</div>-->
<!--    <div class="more"><a href="/blog/show/--><?php //echo $row['id'];?><!--">подробнее...</a></div>-->
</div>
    <?php
    }
    ?>

<?php } else {
    return 'Нет статей в базе';
}?>
</div>