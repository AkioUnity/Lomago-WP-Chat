<?php
$facebookapp_code = "<h4>per Facebook APP : <img src='\pts_bilderupload/facebook.png'";
if ($user_ID == 0)
    $facebookapp_code .= " class='button_disabled'";
else
    $facebookapp_code .= "class='whatsapp-button' onclick=\"call_facebook(" . $agentresult->ID . ",'" . $agentresult->bezeichnung . "','" . $agentresult->sbid . "','" . $agentresult->mobilenumber_1 . "')\" ";
$facebookapp_code .= "width=\"30\"></h4>";

