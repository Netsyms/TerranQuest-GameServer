<?php

require 'required.php';

if ($_SESSION['loggedin'] != true) {
    sendError('Your session has expired.  Please log in again.', true);
} else {
    
}

if (is_empty($VARS['lat']) || is_empty($VARS['long'])) {
    sendOK("Missing data.", true);
}

if (!preg_match('/-?[0-9]{1,3}\.[0-9]{4,}/', $VARS['lat'])) {
    sendOK("Latitude (lat) is in the wrong format.", true);
}

if (!preg_match('/-?[0-9]{1,3}\.[0-9]{4,}/', $VARS['long'])) {
    sendOK("Longitude (long) is in the wrong format.", true);
}

$uuid = $_SESSION['uuid'];

// Kick user out of game
$kick = $database->select('players', ['kick'], ['uuid' => $uuid])[0]['kick'];
if (!is_empty($kick)) {
    $_SESSION['loggedin'] = false;
    session_unset();
    session_destroy();
    $database->update('players', ['kick' => ''], ['uuid' => $uuid]);
    sendError($kick, true);
}

$database->update('players', ['latitude' => $VARS['lat'], 'longitude' => $VARS['long'], '#lastping' => 'NOW()'], ['uuid' => $uuid]);

sendOK();
