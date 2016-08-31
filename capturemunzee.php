<?php

if (!isset($database) || ($database == null)) {
    sendError("Please don't do that.", true);
}

/* If the user has a Munzee key */
if ($database->has('munzee', ['player_uuid' => $_SESSION['uuid']])) {

    file_put_contents("munzee.log", "Checking if user " . $_SESSION['uuid'] . " has an unexpired token\n", FILE_APPEND);
    /* Check if we need to refresh the bearer token first */
    if ($database->has('munzee', ["AND" => ['player_uuid' => $_SESSION['uuid'], 'expires[<=]' => (time() + 30)]])) {
        file_put_contents("munzee.log", "User " . $_SESSION['uuid'] . " has an expired token.  Refreshing.\n", FILE_APPEND);
        $url = 'https://api.munzee.com/oauth/login';
        $fields = array(
            'client_id' => urlencode(MUNZEE_KEY),
            'client_secret' => urlencode(MUNZEE_SECRET),
            'grant_type' => 'refresh_token',
            'refresh_token' => urlencode($database->select('munzee', 'refreshtoken', ['player_uuid' => $_SESSION['uuid']]))
        );

        foreach ($fields as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }
        rtrim($fields_string, '&');

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

        $result = curl_exec($ch);

        curl_close($ch);

        $data = json_decode($result, TRUE)['data'];
        file_put_contents("munzee.log", "$result\n\n", FILE_APPEND);
        if ($data['status_code'] == 200) {
            file_put_contents("munzee.log", "User " . $_SESSION['uuid'] . " has a new unexpired token!\n", FILE_APPEND);
            $database->update('munzee', ['bearertoken' => $data['token']['access_token'], 'refreshtoken' => $data['token']['refresh_token'], 'expires' => $data['token']['expires']], ['player_uuid' => $_SESSION['uuid']]);
        }
    }
    file_put_contents("munzee.log", "User " . $_SESSION['uuid'] . " has an valid token.\n", FILE_APPEND);

    /* Check again now */
    if ($database->has('munzee', ["AND" => ['player_uuid' => $_SESSION['uuid'], 'expires[>]' => (time() + 30)]])) {
        file_put_contents("munzee.log", "User " . $_SESSION['uuid'] . " attempting capture of $origcode.\n", FILE_APPEND);
        $url = 'https://api.munzee.com/capture/light/';
        $header = array(
            'Content-type: application/json',
            'Authorization: ' . $database->select('munzee', ['bearertoken'], ['player_uuid' => $_SESSION['uuid']])[0]['bearertoken']
        );
        
        $time = time();
        $fields_string = 'data={"language":"EN","latitude":"'.$latitude.'","longitude":"'.$longitude.'","code":"'.$origcode.'","time":'.$time.',"accuracy":'.$accuracy.'}';
//open connection
        $ch = curl_init();

        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $fields_string,
            CURLOPT_HTTPHEADER => $header,
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

        file_put_contents("munzee.log", "User " . $_SESSION['uuid'] . " attempting to capture $origcode:\n", FILE_APPEND);
        $result = curl_exec($ch);
//close connection
        curl_close($ch);

        
        $data = json_decode($result, TRUE);
        if ($data['status_code'] == 200) {
            file_put_contents("munzee.log", "User " . $_SESSION['uuid'] . " captured $origcode:\n", FILE_APPEND);
            file_put_contents("munzee.log", "  Sent data: $fields_string\n\n", FILE_APPEND);
            file_put_contents("munzee.log", "  Result: $result\n\n", FILE_APPEND);
            
            // Add munzee capture info to response
            $returndata["messages"][] = ["title" => "Munzee", "text" => $data["data"]["result"]];
        } else {
            file_put_contents("munzee.log", "User " . $_SESSION['uuid'] . " did not capture $origcode:\n", FILE_APPEND);
            file_put_contents("munzee.log", "  Sent headers: " . var_export($header, true) . "\n\n", FILE_APPEND);
            file_put_contents("munzee.log", "  Sent data: $fields_string\n\n", FILE_APPEND);
            file_put_contents("munzee.log", "  Response: " . var_export($result, true) . "\n\n", FILE_APPEND);
        }
    }
}