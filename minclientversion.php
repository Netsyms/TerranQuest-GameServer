<?php

/*
 * Give the minimum allowed game client version.  The client should check on 
 * startup and not continue if its version is less than the given version.
 */

require 'settings.php';
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

echo json_encode(["status" => "OK", "version" => MIN_CLIENT_VERSION]);
