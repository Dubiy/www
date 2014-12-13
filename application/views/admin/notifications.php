<script type="text/javascript" src="/js/ckeditor/ckeditor.js"></script>
<div class="block_admin">
    <div class="broadcast_timer">
        Начало трансляции в <input type="text" value="<? echo date('Y-m-d H:i:s'); ?>">
    </div>
    <button class="start_broadcast">Уведомить о начале трансляции</button>
</div>
<div class="block_admin add_alert">
    <label>Алерт на главной</label>
    <?php
    if (isset($main_page_alert[0])) {
        $array = unserialize($main_page_alert[0]->array);
        $main_page_alert_text = $array['text'];
    } else {
        $main_page_alert_text = '';
    }

    ?>
    <!-- <textarea id="show_editor_here"><?php echo $main_page_alert_text; ?></textarea> -->
    <div class="textarea" contenteditable="true" id="show_editor_here"><?php echo $main_page_alert_text; ?></div>
    <div class="accept"></div>
</div>

<style type="text/css">
    .start_broadcast {
        border: 1px solid #888888;
        border-radius: 4px;
        cursor: pointer;
        padding: 5px;
    }

    .block_admin label {
        display: block;
        font-weight: bold;
    }

    .block_admin.add_alert .textarea {
        display: inline-block;
        width: 470px;
        border: 1px solid #555;
        background-color: #999;
    }

    .block_admin.add_alert .accept {
        width: 20px;
        height: 20px;
        margin-left: 10px;
        display: inline-block;
        background: url(/img/admin/accept.png) no-repeat;
        cursor: pointer;
    }

</style>

<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('.block_admin.add_alert .accept').click(function () {
            jQuery.post('/admin/notification', {'action': 'add_alert', 'text': jQuery('.block_admin.add_alert .textarea').html()}, function () {
                jQuery('.block_admin.add_alert textarea').css('background-color', '#00FF00').animate({backgroundColor: '#FFFFFF'}, 500);
            });
        });

        jQuery('.start_broadcast').click(function () {
            var self = this;
            jQuery.post('/admin/archive_handler', {'action': 'start_broadcast', 'start_time': $('.broadcast_timer input').val()}, function (data) {
                jQuery(self).css('background-color', 'green').animate({'backgroundColor': '#F0F0F0'}, 1000);
            });
        });

        CKEDITOR.disableAutoInline = true;
        var editor = CKEDITOR.inline(document.getElementById("show_editor_here"));
    });

</script>


<div class="block_admin add_notification">
    <label>My-trade note</label>
    <textarea></textarea>

    <div class="accept"></div>
</div>
<div class="block_admin notifications">
    <?php
    if (isset($notifications) && is_array($notifications) && count($notifications)) {
        foreach ($notifications as $notification) {
            ?>
            <div class="notification" notification_id="<?php echo $notification->notification_id; ?>">
                <div class="datetime time_since" datetime="<?php echo $notification->datetime; ?>"><?php echo time_since($notification->datetime); ?></div>
                <div class="notification_text"><?php echo $notification->text; ?></div>
                <div class="delete"></div>
            </div>
            <div class="clear"></div>
        <?php
        }
    } else {
        echo 'Нет оповещений';
    }
    ?>
</div>