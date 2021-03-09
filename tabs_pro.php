<?php
global $user_ID;
global $pts_settings0;
global $whatsappdb;
$sql = "SELECT text FROM auto_messages WHERE type='general' and step=2";
$reply_row = $whatsappdb->get_row($sql);
$status_message = $reply_row->text;
$sql = "SELECT text FROM auto_messages WHERE type='general' and step=3";
$reply_row = $whatsappdb->get_row($sql);
$credit_message = $reply_row->text;

if ($user_ID == 0) {
    ?>
    <h4 style="color:#ae0303">
        Für diese Funktion muss man angemeldet sein
    </h4>
<?php } ?>
<h4 style="color:#ae0303" class="status_message">
    <?php echo $status_message ?>
</h4>
<h4 style="color:#ae0303" class="credit_message">
    <?php echo $credit_message ?>
</h4>
<h3>Kontaktanfrage starten</h3>


