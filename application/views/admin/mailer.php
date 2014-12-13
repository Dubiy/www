<div class="block_admin add_mailer">
  <form method="POST" action="">
    <div>
      Введите тему сообщения, которое отправится всем пользователям на email
    </div>
    <input type="text" name="subject" class="subject">
    <div>
      Кому:
      <select name="filter">
        <option value="all">ALL</option>
        <option value="star">STAR</option>
        <option value="premium">PREMIUM</option>
        <option value="regular">REGULAR ONLY</option>
        <option value="online">ONLINE</option>
        <option value="onlineprem">ONLINE + PREMIUM</option>
      </select>
      <label><input type="checkbox" name="sms_only"> Только SMS</label>
    </div>

    <div>
      Введите текст сообщения
    </div>
    <textarea name="text"></textarea>    
    <input type="submit" value="Отправить">
  </form>
  </div>

<div class="block_admin add_mailer">
  <form method="POST" action="">
    <div>
      Введите текст сообщения, которое отправится всем пользователям по SMS
    </div>
    <textarea name="text_sms" class="sms"></textarea>
    <input type="submit" value="Отправить">
  </form>
  </div>
</div>

</div>


<style type="text/css">
  .add_mailer textarea {
    width: 100%;
    height: 200px;
  }

  .add_mailer input.subject {
    width: 100%;
  }

  .add_mailer textarea.sms {
    width: 100%;
    height: 100px;
  }
</style>