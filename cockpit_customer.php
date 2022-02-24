<style>
    /* The Modal (background) */
    .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1; /* Sit on top */
        padding-top: 100px; /* Location of the box */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
    }

    /* Modal Content */
    .modal-content {
        background-color: #fefefe;
        margin: auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        top:70px;
    }

    /* The Close Button */
    .close {
        color: #aaaaaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: #000;
        text-decoration: none;
        cursor: pointer;
    }

    table, th, td {
        border: 2px solid #e0dede;
        border-collapse: collapse;
        padding: 3px;
    }
</style>

<!-- The Modal -->
<div id="myModal" class="modal">

    <!-- Modal content -->
    <div class="modal-content">
        <span class="close">&times;</span>
        <p id="info_content">info content</p>
    </div>
</div>

<script>
    // Get the modal
    var modal = document.getElementById("myModal");

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

<?php

$sql = "SELECT * FROM LAMOGA_WAF_request_".$wa_portal_id." WHERE user_id=".$user_ID;
$result = $whatsappdb->get_results($sql);
?>

<h4>Meine aktiven Messenger Sitzungen</h4>

<table id="connectionTable" style="font-size: 18px;font-weight:600 ">
    <tr>
        <th>Beratername</th>
        <!--        Start time-->
        <!--        <th>Startzeit</th>-->
        <!--        Request time-->
        <th>Anfrage Zeitpunkt</th>
        <!--        Request time-->
        <!--        <th>Startmessages gesendet</th>-->
        <th>Messenger</th>
        <th>Messenger PIN</th>
        <th>Connection Info</th>
        <!--        disconnect-->
        <th>Status</th>
        <th>Aktion</th>
    </tr>
	<?php foreach ($result as $results) {
		$pin= $results->consultant_phone;
		//get information
		$sql = "SELECT text FROM auto_messages_".$wa_portal_id." WHERE type='" . $results->type . "' and step=1";
		$reply_row = $whatsappdb->get_row($sql);
		$message = $reply_row->text;

		$sql = "SELECT user_login,telefon_mobil,vorwahl_3,rufnummer_3,telegram_id from pts_useradressen_".$wa_portal_id." where ID=" . $user_ID;
		$row = $whatsappdb->get_row($sql);
		$username = $row->user_login;

		$message = str_replace('$customer', $username, $message);
		$message = str_replace('$consultant', $results->consultant_name, $message);
		$message = str_replace('$pin', $pin, $message);

	    ?>
        <tr class='clickable-row1' data-id='<?php echo $results->id; ?>'>
            <td class="username1"><?php echo $results->consultant_name ?></td>
            <td class="time1"><?php echo date_format(date_create($results->requested_time),'d.m.y / H:i:s'); ?></td>
            <td><?php echo $results->type ?></td>
            <td><?php
				echo strlen($pin)>8?"No":$pin ?></td>
            <td>
                <a href="#" class='info_popup' data-id='<?php echo $results->id; ?>' data-content='<?php echo $message; ?>'>Info Popup</a>
            </td>
            <td>
				<?php if ($results->status==1)
					echo "<div class=\"connected\"> </div>";
				else
					echo "<div class=\"wait\"> </div>";
				?>
            </td>
            <td>
                <a href="#" class='clickable-row' data-id='<?php echo $results->id; ?>'>trennen</a>
            </td>
        </tr>
	<?php } ?>
</table>

<script>
    // wordpress doesn't support normal js code. so we should use bellow code'
    jQuery(document).ready(function ($) {
        $(".info_popup").click(function () {
            let id = $(this).data("content");
            $("#info_content").html(id);
            console.log("info_popup",id);
            modal.style.display = "block";
        });
        $(".clickable-row").click(function () {
            let id= $(this).data("id");
            let row=$(this);
            let ajaxscript = { ajax_url : '//www.lamoga.de/wp-admin/admin-ajax.php' };
            $.post(
                ajaxscript.ajax_url,
                {
                    action:"cockpit_disconnect",
                    id: id
                },
                function(res) {
                    console.log(res);
                    row.closest("tr").remove();
                }
            );
        });
    });
</script>
