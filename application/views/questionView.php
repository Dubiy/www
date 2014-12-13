
<div id="questionShow">
    <?php print_r($question);?>
         <h2><?php echo $question[0]->text; ?></h2>
    <!-- вывод результата из массива -->
<!--<div class="date">--><?php //date("d.m.Y H:i",strtotime( $row->date));?><!--</div>-->
<div class="body"><?php echo $question[0]->text;?></div>
</div>