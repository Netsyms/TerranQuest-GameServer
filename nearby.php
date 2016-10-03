<?php

/*
  Coordinate decimal places to earth resolution
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

use AnthonyMartin\GeoLocation\GeoLocation as GeoLocation;

if (is_empty($VARS['lat']) || is_empty($VARS['long'])) {
    sendError("Missing information.", true);
}

if (!preg_match('/-?[0-9]{1,3}\.[0-9]{3,}/', $VARS['lat'])) {
    sendError("Latitude (lat) is in the wrong format.", true);
}

if (!preg_match('/-?[0-9]{1,3}\.[0-9]{3,}/', $VARS['long'])) {
    sendError("Longitude (long) is in the wrong format.", true);
}

$radius = .25;
if (!is_empty($VARS['radius']) && is_numeric($VARS['radius'])) {
    $radius = intval($VARS['radius']);
}

$userlocation = GeoLocation::fromDegrees($VARS['lat'], $VARS['long']);
$searchbounds = $userlocation->boundingCoordinates($radius, 'miles');


$people = $database->select('players', ['uuid', 'level', 'nickname', 'teamid', 'energy', 'maxenergy'], ['AND' => [
        'latitude[>]' => $searchbounds[0]->getLatitudeInDegrees(),
        'latitude[<]' => $searchbounds[1]->getLatitudeInDegrees(),
        'longitude[>]' => $searchbounds[0]->getLongitudeInDegrees(),
        'longitude[<]' => $searchbounds[1]->getLongitudeInDegrees(),
        'lastping[>]' => date('Y-m-d H:i:s', strtotime('-1 minute'))],
    "LIMIT" => 20
        ]);

$out = ["status" => "OK", "people" => []];
foreach ($people as $person) {
    $out['people'][] = ["uuid" => $person["uuid"], "name" => $person["nickname"], "team" => $person["teamid"]];
}

echo json_encode($out);
