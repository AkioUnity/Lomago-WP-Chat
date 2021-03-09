<style>
    .wait {
        background-color: #ff9d17;
        border: 2px solid #000000;
        height: 10px;
        border-radius: 50%;
        -moz-border-radius: 50%;
        -webkit-border-radius: 50%;
        width: 10px;
    }

    .connect {
        background-color: #00ff09;
        border: 2px solid #000000;
        height: 10px;
        border-radius: 50%;
        -moz-border-radius: 50%;
        -webkit-border-radius: 50%;
        width: 10px;
    }

    .username {
        color: #3b5998;
    }

    .time {
        color: #984f58;
    }

    td, th {
        padding: 1px 1px 1px 10px;
    }
</style>


<?php

global $user_ID;
//echo $user_ID;

if (!$user_ID)
    return;

global $wpdb;

$sql="SELECT berater_status from pts_useradressen WHERE ID=".$user_ID;
$consultant_status = $wpdb->get_row($sql);
if ($consultant_status->berater_status!='on')
    return;

$sql="SELECT * from cockpit_settings WHERE consultant_id=".$user_ID;
$setting = $wpdb->get_row($sql);

if (!$setting){
    $sql = "INSERT INTO cockpit_settings (consultant_id) VALUES (" . $user_ID . ")";
    $wpdb->query($sql);
    $sql="SELECT * from cockpit_settings WHERE consultant_id=".$user_ID;
    $setting = $wpdb->get_row($sql);
}
$sql = "SELECT LAMOGA_WAF_request.*,pts_useradressen.user_login,pts_useradressen.telefon_mobil FROM LAMOGA_WAF_request INNER JOIN pts_useradressen on LAMOGA_WAF_request.user_id=pts_useradressen.ID WHERE status>-1  and customer_phone!='null' and consultant_id=".$user_ID." ORDER BY LAMOGA_WAF_request.requested_time";
$result = $wpdb->get_results($sql);

$sql="SELECT text from auto_messages WHERE name='activate'";
$activeMessages = $wpdb->get_results($sql);
?>

<div class="setting" style="font-size: 20px">
    <label style="font-weight: 600">Einstellungen :  </label><label style="margin-left: 10px"> Kunde sofort ausblenden wenn offline </label>
    <label for="check_offline"></label>
    <input type="checkbox" id="check_offline" <?php echo ($setting->offline==1 ? 'checked' : '');?> style="margin: auto">
    <label style="margin-left: 30px">       oder nach: </label>
    <input type="number" value="<?php echo $setting->wait_minute; ?>" style="width: 90px; height: 28px;" id="after_minutes"> Minuten
</div>

<h4>Kunden Anfragen</h4>

<table id="cockpitTable" style="font-size: 18px;font-weight:600 ">
    <?php foreach ($result as $results) { ?>
        <tr class='clickable-row' data-username='<?php echo $results->user_login; ?>'
            data-phone='<?php echo $results->telefon_mobil; ?>'
            data-consultant='<?php echo $results->consultant_name; ?>'>
            <td>#</td>
            <td><a href="#" class="username"><?php echo $results->user_login; ?></a></td>
            <td class="time"><?php echo $results->requested_time; ?></td>
            <td>
                <div class="wait"></div>
            </td>
            <td>Wait</td>
        </tr>
    <?php } ?>
</table>

<script>
    jQuery(document).ready(function ($) {

        let ajaxscript = { ajax_url : '//www.lamoga.de/wp-admin/admin-ajax.php' };
        let lastTime;
        let lastLength;
        let ajax_call = function () {
            // console.log("send");
            $.post(
                ajaxscript.ajax_url,
                {
                    action:"cockpit_action",
                    offline: $("#check_offline").is(":checked"),
                    wait_minute: $("#after_minutes").val()
                },
                function(res) {
                    console.log(res);
                    let content='';
                    if (res.length==0){
                        $('#cockpitTable').html("");
                        lastTime=0;
                        return;
                    }
                    // console.log(res[0]);
                    // console.log(res[res.length-1].requested_time);
                    if (lastTime==res[res.length-1].requested_time)
                        return;
                    lastTime=res[res.length-1].requested_time;
                    for (let i=0;i<res.length;i++){
                        let item=res[i];
                        console.log(item);
                        let status='wait';
                        if (item.status==1){
                            status='connect'
                        }
                        let phone=item.vorwahl_1+item.rufnummer_3;
                        let image_url='pts_bilderupload/whatsapp.png';
                        if (item.type=='facebook')
                            image_url='wp-content/uploads/2019/04/facebook.png';
                        else if (item.type=='telegram')
                            image_url='wp-content/uploads/2019/04/telegram.png';
                        console.log(item.type);
                        content+="<tr class='clickable-row'  data-userid='"+item.user_id+"' data-username='"+item.user_login+"' data-phone='"+phone+"' data-consultant='"+item.consultant_name+"' data-type='"+item.type+"' data-page_id='"+item.customer_phone+"' >" +
                            "            <td>#</td>" +
                            "            <td><a href=\"#\" class=\"username\">"+item.user_login+"</a></td>" +
                            "            <td class=\"time\">"+item.requested_time+"</td>" +
                            "            <td>" +
                            "                <div class=\""+status+"\"></div>" +
                            "            </td>" +
                            "            <td>"+status+"</td>" +
                            "<td><img src='/"+image_url+"' width='20'></td>"+
                            "        </tr>";
                    }
                    $('#cockpitTable').html(content);
                    // console.log(content);
                    $(".clickable-row").click(function () {
                        let user_id= $(this).data("userid");
                        let consultant = $(this).data("consultant");
                        let username = $(this).data("username");
                        let url='https://www.lomago.io:1337/send?page_id='+$(this).data("page_id");
                        let message=<?php echo json_encode($activeMessages[0]->text) ?>;
                        if ($(this).data("type")==='whatsapp'){
                            let phone = $(this).data("phone");
                            let consultant_phone = '8562092175213';
                            console.log(username);
                            let base_url = 'https://www.waboxapp.com/api/send/chat?';
                            let token = "51ed0669bea9c01cf3cf2144cd0049975c7a994025fa9";
                            url = base_url + "token=" + token + "&uid=" + consultant_phone + "&to=" + phone + "&custom_uid=" + Date.now();
                            message=<?php echo json_encode($activeMessages[0]->text) ?>;
                        }

                        message=message.replace('$customer',username);
                        message=message.replace('$consultant',consultant);
                        message=message.replace('$consultant',consultant);
                        message="&text=" + encodeURI(message);
                        url=url+message;
                        let type="&type="+$(this).data("type");
                        url=url+type;
                        $.ajax({
                            url: url,
                            type: "GET",
                            crossDomain: true,
                            dataType: 'jsonp',
                            headers: {'Access-Control-Allow-Origin': '*'},
                            // beforeSend: function(xhr){xhr.setRequestHeader('X-Test-Header', 'test-value');},
                            success: function (data) {
                                // $( ".result" ).html( data );
                                // console.log(message);
                            }
                        });
                        $.post(
                            ajaxscript.ajax_url,
                            {
                                action:"cockpit_connect",
                                user_id:user_id
                            },
                            function(res) {
                                console.log(res);
                                lastTime='';
                            }
                        );
                    });
                });
        };

        var interval = 1000 * 3;
        ajax_call();
        setInterval(ajax_call, interval);
    });

</script>

