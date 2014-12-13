<div class="block_admin">
<?php
  if (isset($public_theme_ids) && is_array($public_theme_ids)  && count($public_theme_ids)) {
    foreach ($public_theme_ids as $theme_id) {
      echo '<div class="theme"><span>' . t('recomended_theme' . $theme_id) . '</span><div class="delete">X</div></div>';
    }
  }

?>
</div>