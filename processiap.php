<?php

require 'required.php';
require 'onlyloggedin.php';

function verify_market_in_app($signed_data, $signature, $public_key_base64) {
    $key = "-----BEGIN PUBLIC KEY-----\n" .
            chunk_split($public_key_base64, 64, "\n") .
            '-----END PUBLIC KEY-----';
    //using PHP to create an RSA key
    $key = openssl_get_publickey($key);
    //$signature should be in binary format, but it comes as BASE64. 
    //So, I'll convert it.
    $signature = base64_decode($signature);
    //using PHP's native support to verify the signature
    $result = openssl_verify(
            $signed_data, $signature, $key, OPENSSL_ALGO_SHA1);
    if (0 === $result) {
        return false;
    } else if (1 !== $result) {
        return false;
    } else {
        return true;
    }
}

function verify_app_store_in_app($receipt, $is_sandbox) {
    //$sandbox should be TRUE if you want to test against itunes sandbox servers
    if ($is_sandbox) {
        $verify_host = "ssl://sandbox.itunes.apple.com";
    } else {
        $verify_host = "ssl://buy.itunes.apple.com";
    }

    $json = '{"receipt-data" : "' . $receipt . '" }';
    //opening socket to itunes
    $fp = fsockopen($verify_host, 443, $errno, $errstr, 30);
    if (!$fp) {
        // HTTP ERROR
        return false;
    } else {
        //iTune's request url is /verifyReceipt     
        $header = "POST /verifyReceipt HTTP/1.0\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . strlen($json) . "\r\n\r\n";
        fputs($fp, $header . $json);
        $res = '';
        while (!feof($fp)) {
            $step_res = fgets($fp, 1024);
            $res = $res . $step_res;
        }
        fclose($fp);
        //taking the JSON response
        $json_source = substr($res, stripos($res, "\r\n\r\n{") + 4);
        //decoding
        $app_store_response_map = json_decode($json_source);
        $app_store_response_status = $app_store_response_map->{'status'};
        if ($app_store_response_status == 0) {//eithr OK or expired and needs to synch
            //here are some fields from the json, btw.
            $json_receipt = $app_store_response_map->{'receipt'};
            $transaction_id = $json_receipt->{'transaction_id'};
            $original_transaction_id = $json_receipt->{'original_transaction_id'};
            $json_latest_receipt = $app_store_response_map->{'latest_receipt_info'};
            return true;
        } else {
            return false;
        }
    }
}

$purchase_valid = false;

switch ($VARS['os']) {
    case 'android':
        $purchase_valid = verify_market_in_app($VARS['data'], $VARS['signature'], GOOGLEPLAY_PUBLICKEY);
        break;
    case 'ios':
        $purchase_valid = verify_app_store_in_app($VARS['data'], APP_STORE_SANDBOX);
        break;
}

if ($purchase_valid) {
    $creditstoadd = $database->select('shopcoins', ['coins'], ['merchid' => $VARS['id']])[0]['coins'];
    $database->update('players', ['credits[+]' => $creditstoadd], ['uuid' => $_SESSION['uuid']]);
    sendOK();
} else {
    sendError("Purchase not valid!", true);
}