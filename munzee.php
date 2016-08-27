<?php

require 'required.php';
require 'onlyloggedin.php';

header("Content-Type: text/html");

if (!is_empty($_GET['code'])) {
    $code = $_GET['code'];
    $url = 'https://api.munzee.com/oauth/login';
// "client_id=yourclientid&client_secret=yourclientsecret&grant_type=authorization_code&code=JkEQQmjgbPavmqtJtbYEyAD7lYAMYLKBEZhlfeTn&redirect_uri=https://myfancymunzeeapp.org/handle_oauth"
    $fields = array(
        'client_id' => urlencode(MUNZEE_KEY),
        'client_secret' => urlencode(MUNZEE_SECRET),
        'grant_type' => 'authorization_code',
        'code' => urlencode($code),
        'redirect_uri' => urlencode("http://gs.terranquest.net/munzee.php")
    );
//url-ify the data for the POST
    foreach ($fields as $key => $value) {
        $fields_string .= $key . '=' . $value . '&';
    }
    rtrim($fields_string, '&');
//open connection
    $ch = curl_init();

    $options = array(
        CURLOPT_URL => $url,
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $fields_string,
        CURLOPT_RETURNTRANSFER => true, // return web page
        CURLOPT_HEADER => false, // don't return headers
        CURLOPT_FOLLOWLOCATION => true, // follow redirects
        CURLOPT_MAXREDIRS => 10, // stop after 10 redirects
        CURLOPT_ENCODING => "", // handle compressed
        CURLOPT_USERAGENT => "TerranQuest Game Server (terranquest.net)", // name of client
        CURLOPT_AUTOREFERER => true, // set referrer on redirect
        CURLOPT_CONNECTTIMEOUT => 120, // time-out on connect
        CURLOPT_TIMEOUT => 120, // time-out on response
    );
    curl_setopt_array($ch, $options);
//execute post
    $result = curl_exec($ch);
//close connection
    curl_close($ch);
    
    $jsonresult = json_decode($result, TRUE);
    $data = $jsonresult['data'];
    if ($jsonresult['status_code'] == 200) {
        $database->insert('munzee', ['bearertoken' => $data['token']['access_token'], 'refreshtoken' => $data['token']['refresh_token'], 'expires' => $data['token']['expires'], 'player_uuid' => $_SESSION['uuid']]);
        echo "Your Munzee account has been linked to TerranQuest!<br /><a href='about:closeme'>Back to game</a>";
        die();
    } else {
        echo "Munzee is having problems right now.  Try again later.<br /><a href='about:closeme'>Back to game</a>";
        die();
    }
}