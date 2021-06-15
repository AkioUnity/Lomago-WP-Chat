<?php

$whatsapp_code = "<h4>per Whats APP : "
    . "<img src=\"pts_bilderupload/whatsapp.png\"";
if ($user_ID == 0)
    $whatsapp_code .= " class='button_disabled'";
else
    $whatsapp_code .= "class='whatsapp-button' onclick=\"call_whatsapp(" . $agentresult->ID . ",'" . $agentresult->bezeichnung . "','" . $agentresult->sbid . "','" . $agentresult->mobilenumber_1 . "'," . $agentresult->chatpreis_1 . ")\" ";
$whatsapp_code .= "width=\"30\"> ab ".$agentresult->chatpreis_1."  €</h4> <p class='whatsapp_message'></p>";

// "wp-content/plugins/whatsapp-payment/";--
//