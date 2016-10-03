<?php

require 'required.php';
require 'onlyloggedin.php';
require 'latlong_validate.php';

$terrain = json_decode(file_get_contents("http://gis.terranquest.net/terrain.php?key=" . GIS_API_KEY . "&lat=" . $VARS['lat'] . "&long=" . $VARS['long']));

if (!is_empty($terrain['error'])) {
    sendError($terrain['error'], true);
} else {
    $out = [
        "status" => "OK",
        "typeid" => $terrain['type'],
        "latitude" => $terrain['latitude'],
        "longitude" => $terrain['longitude'],
        "typename" => $terrain['name']
    ];
    die(json_encode($out));
}