<?php

/*
 * Send the client a list of servers that share the database.  The client should
 * pick one at random and use it for the entire session.
 */

require 'required.php';

$servers = [];

if (is_empty(SERVER_URLS) || SERVER_URLS == []) {
    $servers[] = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
} else {
    foreach (SERVER_URLS as $server) {
        $servers[] = $server;
    }
}

die(json_encode($servers));