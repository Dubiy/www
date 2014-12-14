<div class="filter">
  <a href="/all/<?php echo $category; echo (($age_start != 0) ? ('/' . $age_start) : ('')) . (($age_stop != 0) ? ('/' . $age_stop) : ('')); ?>">Все</a>
  <a href="/top/<?php echo $category; echo (($age_start != 0) ? ('/' . $age_start) : ('')) . (($age_stop != 0) ? ('/' . $age_stop) : ('')); ?>">Популярные</a>
  <a href="/unanswered/<?php echo $category; echo (($age_start != 0) ? ('/' . $age_start) : ('')) . (($age_stop != 0) ? ('/' . $age_stop) : ('')); ?>">Без ответов</a>
  <a href="/psych/<?php echo $category; echo (($age_start != 0) ? ('/' . $age_start) : ('')) . (($age_stop != 0) ? ('/' . $age_stop) : ('')); ?>">С участием психолога</a>
</div>

<div class="content_wrapper">
<?php
  if (isset($questions) && is_array($questions) && count($questions)) {
    echo '<div class="questions">';
    foreach ($questions as $question) {
?>
      <div class="question" question_id="<?php echo $question->question_id; ?>">
        <div class="question_left">
          <div class="rating">
            <div class="plus">+</div>
            <div class="value"><?php echo rating($question->rating); ?></div>
            <div class="minus">&dash;</div>
          </div>
          <div class="type"><?php echo question_type($question->type); ?></div>
        </div>
        <div class="text"><a href="/show/<?php echo $question->question_id; ?>"><?php echo question_text($question->text); ?></a></div>
        <div class="answers_count"><a href="/show/<?php echo $question->question_id; ?>#answers">Ответов: <?php echo $question->answers_count; ?></a></div>
        <br clear="both" />
      </div>
<?php
    }
    echo '</div>';
  } else {
    echo '<h3>Вопросы не найдены</h3>';
  }





?>
</div>

<style type="text/css">
.questions .question {
  clear: both;
  margin-bottom: 20px;
}

.questions .question .question_left {
  float: left;
  width: 100px;
  text-align: center;
}
.questions .question .answers_count {
  float: right;
}

.questions .question .question_left .rating > div {
  display: inline-block;
}

.questions .question .question_left .rating {
}

</style>