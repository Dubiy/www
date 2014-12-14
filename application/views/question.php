<div class="content_wrapper">
<?php
  if (isset($question) && is_object($question)) {
?>
    <h2>Вопрос</h2>
    <div class="questions single_page">
      <div class="question type_<?php echo $question->type; ?>" question_id="<?php echo $question->question_id; ?>">
        <div class="question_left">
          <div class="rating">
            <div class="plus">+</div>
            <div class="value"><?php echo rating($question->rating); ?></div>
            <div class="minus">&dash;</div>
          </div>
          <div class="type"><?php echo question_type($question->type); ?></div>
        </div>
        <div class="text"><a href="/show/<?php echo $question->question_id; ?>"><?php echo question_text($question->text); ?></a></div>
        <div class="answers_count"><a href="/show/<?php echo $question->question_id; ?>#answers">Ответов: <?php echo count($answers); ?></a></div>
        <br clear="both" />
      </div>
    </div>
    <h2>Ответы</h2>
    <div class="answers">
      <a name="answers"></a>
<?php
      if (isset($answers) && is_array($answers) && count($answers)) {
        foreach ($answers as $i => $answer) {
?>
          <div class="answer <?php echo (($i % 2 != 0) ? ('odd') : ('')); ?> type_<?php echo answer_type($answer->age, $answer->account_type, TRUE); ?>" answer_id="<?php echo $answer->answer_id; ?>">
            <div class="answer_left">
              <div class="rating">
                <div class="plus">+</div>
                <div class="value"><?php echo rating($answer->rating); ?></div>
                <div class="minus">&dash;</div>
              </div>
              <div class="type"><?php echo answer_type($answer->age, $answer->account_type); ?></div>
            </div>
            <div class="text"><?php echo $answer->text; ?></a></div>
            <br clear="both" />
          </div>
<?php
        }
      } else {
        echo '<h3>Нет ответов</h3>';
      }
?>
    </div>

    <div class="leave_answer">
      <form method="POST" action="">
        <h3>Ответить</h3>
        <textarea name="answer"></textarea>
        <input type="submit" class="btn btn-success submit" value="Отправить">
      </form>
    </div>
<?php
  } else {
    echo '<h3>Вопрос не найдены</h3>';
  }





?>
</div>

