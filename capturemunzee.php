<?php

if (!isset($database) || ($database == null)) {
    sendError("Please don't do that.", true);
}

/* If the user has a Munzee key */
if ($database->has('munzee', ['player_uuid' => $_SESSION['uuid']])) {


    /* Check if we need to refresh the bearer token first */
    if ($database->has('munzee', ['player_uuid' => $_SESSION['uuid'], 'expires[<=]' => (time() + 30)])) {
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
            CURLOPT_USERAGENT => "TerranQuest Game Server (terranquest.net)", // name of client
            CURLOPT_AUTOREFERER => true, // set referrer on redirect
            CURLOPT_CONNECTTIMEOUT => 120, // time-out on connect
            CURLOPT_TIMEOUT => 120, // time-out on response
        );
        curl_setopt_array($ch, $options);

        $result = curl_exec($ch);

        curl_close($ch);

        $data = json_decode($result, TRUE);

        if ($data['status_code'] == 200) {
            $database->update('munzee', ['bearertoken' => $data['token']['access_token'], 'refreshtoken' => $data['token']['refresh_token'], 'expires' => $data['token']['expires']], ['player_uuid' => $_SESSION['uuid']]);
        }
    }


    /* Check again now */
    if ($database->has('munzee', ['player_uuid' => $_SESSION['uuid'], 'expires[>]' => (time() + 30)])) {
        $url = 'https://api.munzee.com/capture/light/';
        $header = array(
            'Content-type: application/json',
            'Authorization: ' . $database->select('munzee', ['bearertoken'], ['player_uuid' => $_SESSION['uuid']])[0]
        );


        $fields_string = 'data={"language":"EN","latitude":' . $latitude . ',"longitude":' . $longitude . ',"code":"' . $origcode . '","time":' . time() . ',"accuracy":' . $accuracy . '}';
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
        
        $result = curl_exec($ch);
//close connection
        curl_close($ch);

        $data = json_decode($result, TRUE);

        // Add munzee capture info to response
        $returndata["messages"][] = ["title" => "Captured a Munzee!", "text" => $data["data"]["result"]];
    }
}