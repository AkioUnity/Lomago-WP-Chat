<?php
$telegram_code = "<h4>per Telegram APP : <img src='\pts_bilderupload/telegram.png'";
if ($user_ID == 0)
    $telegram_code .= " class='button_disabled'";
else
    $telegram_code .= "class='whatsapp-button' onclick=\"call_telegram(" . $agentresult->ID . ",'" . $agentresult->bezeichnung . "','" . $agentresult->sbid . "','" . $agentresult->mobilenumber_1 . "')\" ";
$telegram_code .= "width=\"30\"></h4>";


//
//<?php
//$telegram_code="<h3>Kontaktanfrage starten</h3>
//<h4>per Telegram APP : <img class='whatsapp-button' src=\"pts_bilderupload/whatsapp.png\" onclick=\"call_telegram(".$agentresult->ID.",'".$agentresult->bezeichnung."','".$agentresult->sbid."','".$agentresult->mobilenumber_1."',".$agentresult->chatpreis_1.")\" width=\"30\"></h4><p id='whatsapp_message'></p>";
//
