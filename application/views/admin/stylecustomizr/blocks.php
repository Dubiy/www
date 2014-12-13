<div class="block_admin">
<?php
  if (isset($blocks) && is_array($blocks)  && count($blocks)) {
    // echo '<pre>'. print_r($blocks, true) . '</pre>';


      $tmp_last_group = -999;
      $i = 0;
      $groups = array();
      foreach ($blocks as $block) {
        if ($tmp_last_group != $block->group) {
          $tmp_last_group = $block->group;
          if ($tmp_last_group != -999) {
            echo '</ul>';
          }
          echo '<label>' . t('customizr_group_' . $block->group) . '</label><ul id="sortable_' . $block->group . '" class="connectedSortable">';
          $groups[] = $block->group;
        }
        echo '<li id="item_id_' . $i++ . '" ><div class="block" path="' . $block->path . '"><a href="/admin/stylecustomizr/block/' . urlencode(base64_encode($block->path)) . '">' . $block->title . '</a></div></li>';
      }
      echo '</ul>';

      if (isset($add_empty_group)) {
        $group = 1;
        while (in_array($group, $groups)) {
          $group++;
        }
        echo '<label>' . t('customizr_group_' . $group) . '</label><ul id="sortable_' . $group . '" class="connectedSortable"></ul>';
      }


      echo '<br /><a href="/admin/stylecustomizr/blocks/new_group">' . t('Add empty group') . '</a> <a href="/admin/stylecustomizr/block/new">' . t('Add new block') . '</a>';

  }

?>
</div>
  <style>
    .connectedSortable {
      list-style-type: none;
      margin: 0;
      padding: 0;
      padding-left: 1px;
      padding-right: 1px;
      min-height: 25px;
      border: 1px solid #bbb;
      width: 100%;
      margin-bottom: 20px;
    }

    .connectedSortable li {
      display: block;
      background: #ccc;
      border: 1px solid #aaa;
      color: black;
      font-weight: normal;
      margin: 0;
      margin-top: 1px;
      margin-bottom: 1px;
      padding: 5px;
      cursor: move;
    }

    
  </style>
  <script>
  $(function() {
    $( ".connectedSortable" ).sortable({
      connectWith: ".connectedSortable"
    }).disableSelection();

    $( ".connectedSortable" ).on( "sortreceive", function( event, ui ) {
      var new_group = event.currentTarget.id.split('_')[1]; 
      var item_id = ui.item[0].id;
      jQuery.post('/admin/stylecustomizr/change_group', {'path': jQuery('#' + item_id + ' div').attr('path'), 'group': new_group}, function(data) {
        // alert(data);
      });
    });
  });
  </script>
