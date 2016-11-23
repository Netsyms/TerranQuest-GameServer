<?php

require 'required.php';
require 'onlyloggedin.php';

header("Content-Type: text/html");

if (!is_empty($_GET['code'])) {
    file_put_contents("munzee.log", "User " . $_SESSION['uuid'] . " is attempting OAuth.\n", FILE_APPEND);
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
        CURLOPT_USERAGENT => "TerranQuest Game Server (terranquest.net; Ubuntu; Linux x86_64; PHP 7)", // name of client
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
    file_put_contents("munzee.log", "User " . $_SESSION['uuid'] . " OAuth result:\n", FILE_APPEND);
    file_put_contents("munzee.log", "  Result: $result\n\n", FILE_APPEND);
    if ($jsonresult['status_code'] == 200) {
        if ($database->has('munzee', ['player_uuid' => $_SESSION['uuid']])) {
            $database->update('munzee', ['bearertoken' => $data['token']['access_token'], 'refreshtoken' => $data['token']['refresh_token'], 'expires' => $data['token']['expires']], ['player_uuid' => $_SESSION['uuid']]);
        } else {
            $database->insert('munzee', ['bearertoken' => $data['token']['access_token'], 'refreshtoken' => $data['token']['refresh_token'], 'expires' => $data['token']['expires'], 'player_uuid' => $_SESSION['uuid']]);
        }
        echo MUNZEE_LINKED_HTML;
        die();
    } else {
        echo MUNZEE_FAILED_HTML;
        die();
    }
}