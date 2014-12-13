<div class="block_admin translate">
  <form method="POST" action="/admin/translate/search">
    <input type="text" name="search" placeholder="Поиск по переводам" value="<?php echo ((isset($_POST['search'])) ? ($_POST['search']) : ('')); ?>">
    <input type="submit" value="Поиск">
  </form>
</div>

<style type="text/css">
  .translate pre {
    font-size: 15px;
    border-bottom: 1px dashed gray;
    background-color: #f7f7f7;
  }

  .translate label {
    display: block;
  }

  .translate label:before {
    content: 'Язык: ';
  }

  .translate textarea {
    display: block;
    width: 100%;
    padding: 0;
    height: 80px;
    margin-bottom: 15px;
  }

</style>