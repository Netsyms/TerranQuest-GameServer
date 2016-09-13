<?php

/*
 * Coordinate decimal places to earth resolution
  decimal
  places   degrees          distance
  -------  -------          --------
  0        1                111  km
  1        0.1              11.1 km
  2        0.01             1.11 km
  3        0.001            111  m
  4        0.0001           11.1 m
  5        0.00001          1.11 m
  6        0.000001         11.1 cm
  7        0.0000001        1.11 cm
  8        0.00000001       1.11 mm
 */

require 'required.php';
require 'onlyloggedin.php';

use AnthonyMartin\GeoLocation\GeoLocation as GeoLocation;

if (!preg_match('/-?[0-9]{1,3}\.[0-9]{2,}/', $VARS['lat'])) {
    sendError("Latitude (lat) is in the wrong format.", true);
}
if (!preg_match('/-?[0-9]{1,3}\.[0-9]{2,}/', $VARS['long'])) {
    sendError("Longitude (long) is in the wrong format.", true);
}

if (is_empty($VARS['msg'])) {
    // Get messages
    if (is_empty($VARS['lat']) || is_empty($VARS['long'])) {
        sendError("Missing information.", true);
    }

    $radius = 50;
    if (!is_empty($VARS['radius']) && is_numeric($VARS['radius'])) {
        $radius = intval($VARS['radius']);
    }

    $userlocation = GeoLocation::fromDegrees($VARS['lat'], $VARS['long']);
    $searchbounds = $userlocation->boundingCoordinates($radius, 'miles');

    //echo $searchbounds[0]->getLatitudeInDegrees();
    //echo $searchbounds[0]->getLongitudeInDegrees();
    //echo $searchbounds[1]->getLatitudeInDegrees();
    //echo $searchbounds[1]->getLongitudeInDegrees();

    $msgs = $database->select('messages', ["[>]players" => ["uuid" => "uuid"]], ['messages.uuid', 'messages.message', 'messages.time', 'players.nickname'], ['AND' => [
            'lat[>]' => $searchbounds[0]->getLatitudeInDegrees(),
            'lat[<]' => $searchbounds[1]->getLatitudeInDegrees(),
            'long[>]' => $searchbounds[0]->getLongitudeInDegrees(),
            'long[<]' => $searchbounds[1]->getLongitudeInDegrees()],
        "ORDER" => "messages.time DESC",
        "LIMIT" => 30
    ]);

    echo json_encode($msgs);
} else {
    // Post message
    if (is_empty($VARS['lat']) || is_empty($VARS['long']) || is_empty($VARS['msg'])) {
        sendError("Missing information.", true);
    }
    
    $msg = strip_tags($VARS['msg']);

    $database->insert('messages', ['#time' => 'NOW()', 'uuid' => $_SESSION['uuid'], 'message' => $msg, 'lat' => $VARS['lat'], 'long' => $VARS['long']]);

    sendOK();
}