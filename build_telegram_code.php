<?php
$telegram_code = "<h4>per Telegram APP : <img src='\pts_bilderupload/telegram.png'";
if ($user_ID == 0)
    $telegram_code .= " class='button_disabled'";
else
    $telegram_code .= "class='whatsapp-button' onclick=\"call_telegram(" . $agentresult->ID . ",'" . $agentresult->bezeichnung . "','" . $agentresult->sbid . "','" . $agentresult->mobilenumber_1 . "')\" ";
$telegram_code .= "width=\"30\"> ab ".$agentresult->chatpreis_3."  €</h4>";
