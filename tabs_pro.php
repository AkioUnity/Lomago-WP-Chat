<!--Messenger-TAB-4-all-->
<!--include "wp-content/plugins/whatsapp-payment/tabs_pro.php";-->

<?php
global $user_ID;
global $pts_settings0;
global $whatsappdb;
global $wa_portal_id;
global $agentresult;
$sql = "SELECT text FROM auto_messages_".$wa_portal_id." WHERE type='general' and step=2";
$reply_row = $whatsappdb->get_row($sql);
$status_message = $reply_row->text;
$sql = "SELECT text FROM auto_messages_".$wa_portal_id." WHERE type='general' and step=3";
$reply_row = $whatsappdb->get_row($sql);
$credit_message = $reply_row->text;
//get fake online numbers as random
$isWeekend=(date('N')>=6)?1:0;
$sql="SELECT number1,number2 FROM fake_onlines WHERE start_time<=".date('H')." and ".date('H')."< end_time and is_weekend=".$isWeekend;
$online_numbers = $whatsappdb->get_row($sql);
$online_count=rand($online_numbers->number1,$online_numbers->number2+1);
?>
<h4 style="color:#777777">
	<?php echo $online_count ?> andere Besucher sind auch gerade hier!<?php echo $agentresult->bezeichnung ?>
</h4>
<hr class="chat">
<br>
<table class="widefat">
    <tbody>
    <tr>
        <td id="xyz_ips_vAlign"> </td>
        <td id="xyz_ips_vAlign">
	        <?php
	        if ($user_ID == 0) {
		        ?>
                <h4 style="color:#ae0303">
                    Für diese Funktion muss man angemeldet sein
                </h4>
	        <?php } ?>
            <h4 style="color:#ae0303" class="check_message">
                Diese Funktion ist noch nicht freigeschaltet (Code 100)
            </h4>
            <h4 style="color:#ae0303" class="status_message">
		        <?php echo $status_message ?>
            </h4>
            <h4 style="color:#ae0303" class="credit_message">
		        <?php echo $credit_message ?>
            </h4>
            <h3>Chat starten</h3>
        </td>
    </tr>
    </tbody>
</table>
