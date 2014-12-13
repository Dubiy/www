<div class="block_admin">
  <a href="/admin/questions/all" class="menu_items">Все</a>
  <a href="/admin/questions/no_answer" class="menu_items">Без ответов</a>
  <a href="/admin/questions/answered" class="menu_items">С ответами</a>
  <a href="/admin/questions/" class="menu_items menu_item_right delete_questions_without_answers">Удалить все без ответа</a>
  <a href="/admin/questions/" class="menu_items menu_item_right delete_answered_questions_today_yesterday">Удалить отвеченные за вчера и позавчера</a>
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
            <?php echo $question->nickname; ?>
          </div>
          <div class="user_controll">
            <a href="/admin/users/get_by_uid/<?php echo $question->user_id; ?>"></a>
            <div class="delete"></div>
          </div>
          <div class="question_text">
            <?php echo $question->text; ?>
          </div>
        </div>
        <div class="question_right_block">
          <textarea class="answer" <?php echo (($question->answer) ? ('disabled') : ('')); ?>><?php echo (($question->answer) ? ($question->answer) : ('')); ?></textarea>
          <div class="question_controll">
          	<div class="silent"><label for="cb_silent_' . $question->question_id . '"><input type="checkbox" <?php echo (($question->answer) ? ('disabled') : ('')); ?> <?php echo (($question->private) ? ('checked="checked"') : ('')); ?> id="cb_silent_' . $question->question_id . '">private</label></div>
            <?php echo (($question->answer) ? ('') : ('<div class="accept" title="Ctrl+Enter"></div>')); ?>
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