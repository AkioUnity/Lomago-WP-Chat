<?php
//-------- my code--------akio
function users_last_login()
{
//    $cur_login = current_time(timestamp, 0);
    $userinfo = wp_get_current_user();
    global $whatsappdb;
    $sql = "SELECT consultant_id from LAMOGA_WAF_request WHERE status>-1 and user_id=" . $userinfo->ID;
    $results = $whatsappdb->get_results($sql);
    foreach ($results as $result) {
        $consultant_id = $result->consultant_id;
        $sql = "SELECT * from cockpit_settings WHERE consultant_id=" . $consultant_id;
        $setting = $whatsappdb->get_row($sql);
        if ($setting && $setting->offline == 1) {
            $sql = "UPDATE LAMOGA_WAF_request SET status=-1 WHERE user_id=" . $userinfo->ID . " and consultant_id=" . $consultant_id;
            $whatsappdb->query($sql);
        }
    }
}

add_action('clear_auth_cookie', 'users_last_login');

add_action('wp_ajax_cockpit_action', 'cockpit_action');
add_action('wp_ajax_nopriv_cockpit_action', 'cockpit_action');

function cockpit_action()
{
    $offline = $_POST['offline'];
    $wait_minute = $_POST['wait_minute'];
    global $whatsappdb;
    $userinfo = wp_get_current_user();
    $sql = "UPDATE cockpit_settings SET offline=" . $offline . ",wait_minute=" . $wait_minute . " WHERE consultant_id=" . $userinfo->ID;
    $whatsappdb->query($sql);
    $sql = "UPDATE LAMOGA_WAF_request SET status=-1 WHERE requested_time < (NOW() - INTERVAL " . $wait_minute . " MINUTE);";
    $deleteRes = $whatsappdb->query($sql);
    $sql = "SELECT LAMOGA_WAF_request.*,pts_useradressen.user_login,pts_useradressen.telefon_mobil,pts_useradressen.vorwahl_1,pts_useradressen.rufnummer_3 FROM LAMOGA_WAF_request INNER JOIN pts_useradressen on LAMOGA_WAF_request.user_id=pts_useradressen.ID WHERE status>-1 and customer_phone!='null' and consultant_id=" . $userinfo->ID . " ORDER BY LAMOGA_WAF_request.requested_time";
    $result = $whatsappdb->get_results($sql);
    echo wp_send_json($result);
    die(); // this is required to terminate immediately and return a proper response
}

add_action('wp_ajax_cockpit_request', 'cockpit_request');
add_action('wp_ajax_nopriv_cockpit_request', 'cockpit_request');

function cockpit_request()
{
    global $whatsappdb;
    $sql = "SELECT count(*) as count FROM LAMOGA_WAF_request INNER JOIN pts_useradressen on LAMOGA_WAF_request.user_id=pts_useradressen.ID WHERE status>-1 ORDER BY LAMOGA_WAF_request.requested_time";
    $result = $whatsappdb->get_row($sql);
    echo wp_send_json($result);
    die(); // this is required to terminate immediately and return a proper response
}

add_action('wp_ajax_cockpit_connect', 'cockpit_connect');
add_action('wp_ajax_nopriv_cockpit_connect', 'cockpit_connect');

function cockpit_connect()
{
    $user_id = $_POST['user_id'];
    global $whatsappdb;
    $sql = "UPDATE LAMOGA_WAF_request SET status='1' WHERE user_id=" . $user_id;
    $result = wp_send_json($whatsappdb->get_results($sql));
    echo $result;
    die(); // this is required to terminate immediately and return a proper response
}

add_action('wp_ajax_whatsapp_request', 'whatsapp_request');
add_action('wp_ajax_nopriv_whatsapp_request', 'whatsapp_request');

function whatsapp_request()
{

    $consultant_id = $_POST['consultant_id'];
    $consultant_name = $_POST['consultant_name'];
    $sbid = $_POST['sbid'];
    $mobilenumber_1 = $_POST['mobilenumber_1'];
    $type = isset($_POST['type']) ? $_POST['type'] : 'whatsapp';

    global $whatsappdb;
    $userinfo = wp_get_current_user();
    $user_ID = $userinfo->ID;

    $res = array();

    if ($user_ID == $consultant_id) {
        $res['message'] = 'Sorry man kann sich nicht selbst kontaktieren';
        $res['error'] = true;
        $result = wp_send_json($res);
        echo $result;
        die(); // this is required to terminate immediately and return a proper response
    }


    $sql = "SELECT user_login,telefon_mobil,vorwahl_1,rufnummer_3,telegram_id from pts_useradressen where ID=" . $user_ID;
    $row = $whatsappdb->get_row($sql);
    $username = $row->user_login;

    $sql = "SELECT text FROM auto_messages WHERE type='" . $type . "' and step=1";
    $reply_row = $whatsappdb->get_row($sql);
    $message = $reply_row->text;

    $message = str_replace('$customer', $username, $message);
    $message = str_replace('$consultant', $consultant_name, $message);

    $phone = $row->vorwahl_1 . $row->rufnummer_3;
    $data = array("username" => $username, 'to' => $phone, 'user_id' => $user_ID);
//    $sql = "SELECT vorwahl_1,rufnummer_1 from pts_useradressen where ID=" . $consultant_id;
//    $row = $whatsappdb->get_row($sql);
//    $consultant_phone=$row->vorwahl_1 . $row->rufnummer_1;
    $consultant_phone = $mobilenumber_1;
    $digits = 6;
    if ($type == 'facebook') {
        $pin = 'F' . rand(pow(10, $digits - 1), pow(10, $digits) - 1);
        $consultant_phone = $pin;
        $phone = "null";
        $res['pin'] = $pin;
        $message = str_replace('$pin', $pin, $message);
    } else if ($type == 'telegram') {
        $pin = 'T' . rand(pow(10, $digits - 1), pow(10, $digits) - 1);
        $consultant_phone = $pin;
        $phone = "null";
        $res['pin'] = $pin;
        $message = str_replace('$pin', $pin, $message);
    } else if ($type == 'whatsapp') {
        $res['data'] = $data;
    }

    $res['message'] = $message;

    $sql = "SELECT * FROM LAMOGA_WAF_request WHERE user_id=" . $user_ID . " and type='" . $type . "'";
    $results = $whatsappdb->get_results($sql);
    if (count($results) > 0) {
        $sql = "UPDATE LAMOGA_WAF_request SET requested_time=(now()),consultant_id=" . $consultant_id . ",consultant_name='" . $consultant_name . "',customer_phone='" . $phone . "',consultant_phone='" . $consultant_phone . "',sbid='" . $sbid . "',status='0' WHERE user_id=" . $user_ID . " and type='" . $type . "'";
    } else {
        $sql = "INSERT INTO LAMOGA_WAF_request (consultant_id,user_id,type,requested_time,consultant_name,customer_phone,sbid,consultant_phone) VALUES (" . $consultant_id . "," . $user_ID . ",'" . $type . "',now(),'" . $consultant_name . "','" . $phone . "','" . $sbid . "','" . $consultant_phone . "')";
    }
    $whatsappdb->query($sql);
    $result = wp_send_json($res);
    echo $result;
    die(); // this is required to terminate immediately and return a proper response
}

function call_api($send)
{
    $base_url = 'https://www.lomago.io/whatsapp/api/users/wp?';
    $url = $base_url . "username=" . $send['username'] . "&to=" . $send['to'] . "&user_id=" . $send['user_id'];
    $response = json_decode(getCURL($url), true);
    return ($response);
}

function send_message($send)
{
    $base_url = 'https://www.waboxapp.com/api/send/chat?';
    $token = "51ed0669bea9c01cf3cf2144cd0049975c7a994025fa9";
    $url = $base_url . "token=" . $token . "&uid=" . $send['uid'] . "&to=" . $send['to'] . "&custom_uid=" . time() . "&text=" . urlencode($send['text']);
    $response = json_decode(getCURL($url), true);
//        $this->response($response);
//        $this->response($url);
    return ($response);
}

function getCURL($_url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
//        curl_setopt($ch, CURLOPT_HEADER, false);
//        curl_setopt($ch, CURLOPT_POST, count($postData));
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}


add_action('wp_ajax_login_api', 'login_api');
add_action('wp_ajax_nopriv_login_api', 'login_api');

function login_api()
{
    $creds = array();
    $creds['user_login'] = $_POST["username"];
    $creds['user_password'] = $_POST["password"];
    $creds['remember'] = true;
    $user = wp_signon($creds, false);
    if (is_wp_error($user))
        echo wp_send_json(array('error' => $user->get_error_message()));

    echo wp_send_json($user);
    die();
}

?>