<div class="content_wrapper">
  <div class="add_question_form">
  <form method="POST" action="">
    <h2>Задать вопрос</h2>
    <div>
      <textarea name="question" placeholder="Текст вопроса"></textarea>
    </div>
    <div style="display:none;">
      <label>Теги: </label>
      <input name="tags" placeholder="Tags">
    </div>
    <div>
      <label>Тип вопроса: </label>
      <select name="type">
        <option value="0">Детский вопрос</option>
        <option value="1">Родительский вопрос</option>
      </select>
    </div>
    <div>
      <label><input type="checkbox" value="1" name="psych"> Нужна помощь психолога</label>
    </div>
    <div>
      <input type="submit" class="btn btn-success submit" value="Отправить">
    </div>

  </form>
  </div>
</div>

