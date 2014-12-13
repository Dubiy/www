<?php
<div id="questionShow">
         <h2><?= $row->title;?></h2> <!-- вывод результата из массива -->
<div class="date"><?= date("d.m.Y H:i",strtotime( $row->date));?></div>
<div class="body"><?= $row->body;?></div>
</div>