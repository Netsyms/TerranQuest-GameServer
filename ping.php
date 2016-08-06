<?php

require 'required.php';

require 'onlyloggedin.php';

if (is_empty($VARS['user']) || is_empty($VARS['lat']) || is_empty($VARS['long'])) {
    sendError("Missing data.", true);
}

if (!preg_match('/-?[0-9]{1,3}\.[0-9]{4,}/', $VARS['lat'])) {
    sendError("Latitude (lat) is in the wrong format.", true);
}

if (!preg_match('/-?[0-9]{1,3}\.[0-9]{4,}/', $VARS['long'])) {
    sendError("Longitude (long) is in the wrong format.", true);
}

$uuid = $_SESSION['uuid'];

$database->update('players', ['latitude' => $VARS['lat'], 'longitude' => $VARS['long'], '#lastping' => 'NOW()'], ['uuid' => $uuid]);

sendOK();