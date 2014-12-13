<div class="block_admin">
	&nbsp;
  <!-- <a href="/admin/questions/all" class="menu_items">Все</a> -->
  <!-- <a href="/admin/questions/no_answer" class="menu_items">Без ответов</a> -->
  <!-- <a href="/admin/questions/answered" class="menu_items">С ответами</a> -->
  <a href="/admin/user/?action=add_premium" class="menu_items menu_item_right">Продлить премиум</a>
</div>
<div>
  <div class="users_filter">
    <form method="POST" action="/admin/users">
      <input type="text" name="nickname" title="Имя" value="<?php echo ((isset($_POST['nickname'])) ? ($_POST['nickname']) : ('')); ?>">
      <input type="text" name="email" title="Email" value="<?php echo ((isset($_POST['email'])) ? ($_POST['email']) : ('')); ?>">
      <input type="text" name="phone" title="Phone" value="<?php echo ((isset($_POST['phone'])) ? ($_POST['phone']) : ('')); ?>">
      <select name="account_type">
        <option value="0"></option>
        <option value="1" <?php echo ((isset($_POST['account_type']) && $_POST['account_type'] == 1) ? ('selected="selected"') : ('')); ?>>REGULAR</option>
        <option value="2" <?php echo ((isset($_POST['account_type']) && $_POST['account_type'] == 2) ? ('selected="selected"') : ('')); ?>>PREMIUM</option>
      </select>
      <input type="image" class="input_image" src="/img/admin/find.png">
    </form>
  </div>
  <div class="users_find">
    <form method="POST" action="/admin/users">
      <input type="text" name="search" placeholder="Поиск" value="<?php echo ((isset($_POST['search'])) ? ($_POST['search']) : ('')); ?>">
      <input type="image" class="input_image" src="/img/admin/find.png">
    </form>
  </div>
  <div class="users_info">
    <div>ПОЛЬЗОВАТЕЛЕЙ<span><?php echo $info_total_info_sum[0]->count; ?></span></div>
    <div>ONLINE<span><?php echo $info_online[0]->count; ?></span></div>
    <div>PREMIUM<span><?php echo $info_premium[0]->count; ?></span></div>
    <div>REGULAR<span><?php echo $info_total_info_sum[0]->count - $info_premium[0]->count; ?></span></div>
    <div>CASH<span>$ <?php echo $info_total_info_sum[0]->cash; ?></span></div>
  </div>
</div>
<div class="clear"></div>
<div class="block_admin">
  <div class="userlist_header">
    <div class="online"><a href="/admin/users/last_activity/<?php echo (($this->uri->segment(3) == 'last_activity' && $this->uri->segment(4) != 'desc') ? ('desc') : ('asc')); ?>">online<?php echo (($this->uri->segment(3) == 'last_activity') ? ((($this->uri->segment(4) != 'desc') ? ('&uarr;') : ('&darr;'))) : ('')); ?></a></div>
    <div class="nickname"><a href="/admin/users/nickname/<?php echo (($this->uri->segment(3) == 'nickname' && $this->uri->segment(4) != 'desc') ? ('desc') : ('asc')); ?>">nickname<?php echo (($this->uri->segment(3) == 'nickname') ? ((($this->uri->segment(4) != 'desc') ? ('&uarr;') : ('&darr;'))) : ('')); ?></a></div>
    <div class="cash"><a href="/admin/users/cash/<?php echo (($this->uri->segment(3) == 'cash' && $this->uri->segment(4) != 'desc') ? ('desc') : ('asc')); ?>">cash<?php echo (($this->uri->segment(3) == 'cash') ? ((($this->uri->segment(4) != 'desc') ? ('&uarr;') : ('&darr;'))) : ('')); ?></a></div>
    <div class="account"><a href="/admin/users/account/<?php echo (($this->uri->segment(3) == 'account' && $this->uri->segment(4) != 'desc') ? ('desc') : ('asc')); ?>">account<?php echo (($this->uri->segment(3) == 'account') ? ((($this->uri->segment(4) != 'desc') ? ('&uarr;') : ('&darr;'))) : ('')); ?></a></div>
    <div class="ban"><a href="/admin/users/banned_to/<?php echo (($this->uri->segment(3) == 'banned_to' && $this->uri->segment(4) != 'desc') ? ('desc') : ('asc')); ?>">ban<?php echo (($this->uri->segment(3) == 'banned_to') ? ((($this->uri->segment(4) != 'desc') ? ('&uarr;') : ('&darr;'))) : ('')); ?></a></div>
    <div class="controll"><a href="/admin/users/star/<?php echo (($this->uri->segment(3) == 'star' && $this->uri->segment(4) != 'asc') ? ('asc') : ('desc')); ?>">STAR<?php echo (($this->uri->segment(3) == 'star') ? ((($this->uri->segment(4) != 'desc') ? ('&darr;') : ('&uarr;'))) : ('')); ?></a></div>
  </div>
  <div class="userlist_body">
<?php
  if (isset($users) && is_array($users) && count($users)) {
    $account_type = $this->config->item('account_type');
    foreach ($users as $user) {
?>
      <div class="user" user_id="<?php echo $user->user_id; ?>" chatadmin="<?php echo (($user->account_type == $account_type['chatadmin']) ? ('1') : ('0'));?>">
        <div class="online"><?php $between = 0; $time_since = time_since($user->last_activity, $between); echo (($user->last_activity) ? ( (($between < $this->config->item('online_time')) ? ('online') : ($time_since))) : ('never')); ?></div>
        <div class="nickname"><?php echo (($user->nickname) ? ($user->nickname) : ($user->email)); ?></div>
        <div class="email"><?php echo $user->email; ?></div>
        <div class="phone"><?php echo $user->phone; ?></div>
        <div class="premium_to"><?php echo $user->premium_to; ?></div>
        <div class="banned_to"><?php echo $user->banned_to; ?></div>
        <div class="cash">$ <?php echo $user->cash; ?></div>
        <div class="account" is_premium="<?php echo ((strtotime($user->premium_to) > strtotime(date('Y-m-d H:i:s'))) ? ('1') : ('0')); ?>" account_type="<?php echo $user->account_type;?>"><?php echo ((strtotime($user->premium_to) > strtotime(date('Y-m-d H:i:s'))) ? ('premium') : ($account_type['h'][$user->account_type])); ?></div>
        <div class="ban">
<?php
          if ((strtotime($user->banned_to) < strtotime(date('Y-m-d H:i:s'))) && ($user->banned_no_questions == 0)) {
            $ban = 'ok';
          } elseif ((strtotime($user->banned_to) < strtotime(date('Y-m-d H:i:s'))) && ($user->banned_no_questions == 1)) {
            $ban = 'no_questions';
          } elseif (strtotime($user->banned_to) > strtotime("+1 Hour", strtotime(date('Y-m-d H:i:s')))) {
            $ban = 'ban';
          } else {
            $ban = 'ban1hr';
          }
?>
          <select class="lets_ban_this_user">
            <option value="ok" <?php echo (($ban == 'ok') ? ('selected') : ('')); ?>>OK</option>
            <option value="no_questions" <?php echo (($ban == 'no_questions') ? ('selected') : ('')); ?>>NO QUESTIONS</option>
            <option value="ban1hr" <?php echo (($ban == 'ban1hr') ? ('selected') : ('')); ?>>BAN 1 HR</option>
            <option value="ban" <?php echo (($ban == 'ban') ? ('selected') : ('')); ?>>BAN</option>
          </select>
        </div>
        <div class="controll">
          <div class="star <?php echo (($user->star) ? ('active') : ('')); ?>"></div>
          <div class="banned_chat <?php echo (($user->banned_chat) ? ('active') : ('')); ?>" title="Забанить в чате"></div>

          <div class="edit"></div>
          <div class="delete"></div>

<?php
          if ($user->vk_profileid != '') {
            echo '<div class="vk_profile"><a href="http://vk.com/id' . $user->vk_profileid . '" target="_blank"><img src="/img/admin/vk.png"></a></div>';
          }
          if ($user->fb_profileid != '') {
            echo '<div class="fb_profile"><a href="http://facebook.com/' . $user->fb_profileid . '" target="_blank"><img src="/img/admin/fb.png"></a></div>';
          }
?>
        </div>
      </div>
<?php
    }
  } else {
    echo 'Пользователи не найдены';
  }
?>
  </div>
  <div class="clear"></div>
</div>
<?php
  if ($total_pages > 1) {
?>
    <div class="block_admin pagination">
<?php
      if ($page > $total_pages) {
        $page = $total_pages;
      } elseif ($page < 1) {
        $page = 1;
      }
      $pager = '';
      $url = '/' . $this->uri->segment(1) . '/' . $this->uri->segment(2) . '/' . (($this->uri->segment(3)) ? ($this->uri->segment(3)) : ('last_activity')) . '/' . (($this->uri->segment(4)) ? ($this->uri->segment(4)) : ('desc')) . '/';
      for ($i = 1; $i <= $total_pages; $i++) {
        $pager .= '<a ' . (($i == $page) ? ('class="active"') : ('')) . ' href="' . $url . $i . '">' . $i . '</a>';
      }
      echo $pager;
?>
    </div>
<?php
  }
?>

<style>
  .editcurrent_user > div {
    display: block !important;
    clear: both;

  }

  .editcurrent_user {
    float: none;
    width: 100%;
  }

  .approve_teamspeak_unique_id {
    position: absolute;
    top: 0;
    right: 0;
    text-align: right;
  }

  .set_chatadmin_permission {
    position: absolute;
    bottom: 0;
    right: 0;
    text-align: right;
  }

  .set_chatadmin_permission .chatadmin {
    transition:All 0.5s ease;
    -webkit-transition:All 0.5s ease;
    width: 32px;
    height: 32px;
    display: inline-block;
    background: url(/img/admin/crown.png) no-repeat center center;
    cursor: pointer;
    opacity: 0.2;
  }

  .set_chatadmin_permission .chatadmin.active, .set_chatadmin_permission .chatadmin:hover {
    opacity: 1;
  }
</style>