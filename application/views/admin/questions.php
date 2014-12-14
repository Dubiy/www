<div class="block_admin">

  <a href="/admin/questions/all" class="menu_items">Все</a>
  <a href="/admin/questions/no_answer" class="menu_items">Без ответов</a>
  <a href="/admin/questions/answered" class="menu_items">С ответами</a>
  <a href="/admin/questions/psych" class="menu_items">Помощь психолога</a>
</div>

<div class="block_admin">
<?php

  if (isset($questions) && is_array($questions) && count($questions)) {
    foreach ($questions as $question) {
?>
      <div class="question" question_id="<?php echo $question->question_id; ?>">
        <div class="question_left_block">
          <div class="datetime">
            
            <?php echo time_since($question->datetime); ?>
          </div>
          <div class="nickname">
            <?php echo $question->email; ?>
          </div>
          <div class="user_controll">
            <a href="/admin/users/get_by_uid/<?php echo $question->user_id; ?>"></a>
            <div class="delete">
<!--                <a href="/admin/questions/deleteQuestion/--><?php //echo $question->question_id;?><!--"></a>-->
            </div>
          </div>
          <div class="question_text">
              <a href="/question/show/<?php echo $question->question_id;?>"><?php echo $question->text;?></a>
          </div>
        </div>
      </div>
      <div class="clear"></div>
<?php
    }
  } else {
    echo 'Нет вопросов';
  }
?>
</div>