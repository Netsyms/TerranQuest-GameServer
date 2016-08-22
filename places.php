<?php

/**
 * Takes the latitude and longitude and gets nearby places from OSM.
 * 
 * Uses WGS84 in the DD.DD format, because I say so.
 */
require 'required.php';

//ini_set('memory_limit','32M');

$placebase;
try {
    $placebase = new medoo([
        'database_type' => PDB_TYPE,
        'database_name' => PDB_NAME,
        'server' => PDB_SERVER,
        'username' => PDB_USER,
        'password' => PDB_PASS,
        'charset' => PDB_CHARSET
    ]);
} catch (Exception $ex) {
    header('HTTP/1.1 500 Internal Server Error');
    sendError('Location database error.  Try again later.', true);
}

use AnthonyMartin\GeoLocation\GeoLocation as GeoLocation;

if (is_empty($VARS['lat'])) {
    sendError("Missing required latitude (lat) variable.", true);
}

if (is_empty($VARS['long'])) {
    sendError("Missing required longitude (long) variable.", true);
}

if (!preg_match('/-?[0-9]{1,3}\.[0-9]{1,}/', $VARS['lat'])) {
    sendError("Latitude (lat) is in the wrong format, or does not have enough precision (DD.DD, at least 2 decimal places.", true);
}

if (!preg_match('/-?[0-9]{1,3}\.[0-9]{1,}/', $VARS['long'])) {
    sendError("Longitude (long) is in the wrong format, or does not have enough precision (DD.DD, at least 2 decimal places.", true);
}

$lat = number_format((float) $VARS['lat'], 5, '.', '');
$long = number_format((float) $VARS['long'], 5, '.', '');

$radius = 5;
if (!is_empty($VARS['radius']) && is_numeric($VARS['radius'])) {
    $radius = floatval($VARS['radius']);
}

$userlocation = GeoLocation::fromDegrees($VARS['lat'], $VARS['long']);
$searchbounds = $userlocation->boundingCoordinates($radius, 'miles');
if ($VARS['debug']) {
    echo "got to the place data query ";
}
if (is_empty($VARS['names'])) {
    $places = $placebase->select('places', '*', ['AND' => [
            'latitude[>]' => $searchbounds[0]->getLatitudeInDegrees(),
            'latitude[<]' => $searchbounds[1]->getLatitudeInDegrees(),
            'longitude[>]' => $searchbounds[0]->getLongitudeInDegrees(),
            'longitude[<]' => $searchbounds[1]->getLongitudeInDegrees()],
        "LIMIT" => 200
    ]);
} else {
    $places = $placebase->select('places', '*', ['AND' => [
            'latitude[>]' => $searchbounds[0]->getLatitudeInDegrees(),
            'latitude[<]' => $searchbounds[1]->getLatitudeInDegrees(),
            'longitude[>]' => $searchbounds[0]->getLongitudeInDegrees(),
            'longitude[<]' => $searchbounds[1]->getLongitudeInDegrees(),
            'name[!]' => ''],
        "LIMIT" => 200
    ]);
}



$data['status'] = 'OK';
$data['places'] = $places;
header('Content-Type: application/json');
$geo['name'] = "Places";
$geo['type'] = 'FeatureCollection';
$geo['features'] = [];
if ($VARS['debug']) {
    echo "got to the game data loop ";
}
foreach ($places as $place) {
    if (!$database->has('locations', ['osmid' => $place['osmid']])) {
        $database->insert('locations', ['osmid' => $place['osmid'], 'teamid' => 0]);
    }
    $gameinfo = $database->select('locations', ['locationid', 'teamid', 'owneruuid', 'currentlife', 'maxlife'], ['osmid' => $place['osmid']])[0];
    // Reset owner info for dead places
    if ($gameinfo['currentlife'] <= 0) {
        $database->update('locations', ['teamid' => 0, 'owneruuid' => null], ['locationid' => $gameinfo['locationid']]);
        $gameinfo = $database->select('locations', ['locationid', 'teamid', 'owneruuid', 'currentlife', 'maxlife'], ['osmid' => $place['osmid']])[0];
    }
    $geo['features'][] = array("type" => "Feature",
        "geometry" => [
            "type" => "Point",
            "coordinates" => [
                floatval($place['longitude']),
                floatval($place['latitude'])
            ]
        ],
        "properties" => [
            "osm_id" => intval($place['osmid']),
            "name" => ($place['name'] == '' ? null : $place['name']),
            "name:en" => ($place['name'] == '' ? null : $place['name']),
            "amenity" => ($place['amenity'] == '' ? null : $place['amenity']),
            "historic" => ($place['historic'] == '' ? null : $place['historic']),
            "tourism" => ($place['tourism'] == '' ? null : $place['tourism']),
            "gameinfo" => $gameinfo//['teamid' => $gameinfo['teamid'], 'owneruuid' => $gameinfo['owneruuid']]
        ]
    );
}
if ($VARS['debug']) {
    echo "got all the way to the encode ";
}

function utf8ize($d) {
    if (is_array($d)) {
        foreach ($d as $k => $v) {
            $d[$k] = utf8ize($v);
        }
    } else if (is_object($d)) {
        foreach ($d as $k => $v) {
            $d->$k = utf8ize($v);
        }
    } else {
        return utf8_encode($d);
    }
    return $d;
}

echo json_encode(utf8ize($geo));
if ($VARS['debug']) {
    switch (json_last_error()) {
        case JSON_ERROR_NONE:
            echo ' - No errors';
            break;
        case JSON_ERROR_DEPTH:
            echo ' - Maximum stack depth exceeded';
            break;
        case JSON_ERROR_STATE_MISMATCH:
            echo ' - Underflow or the modes mismatch';
            break;
        case JSON_ERROR_CTRL_CHAR:
            echo ' - Unexpected control character found';
            break;
        case JSON_ERROR_SYNTAX:
            echo ' - Syntax error, malformed JSON';
            break;
        case JSON_ERROR_UTF8:
            echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
            break;
        default:
            echo ' - Unknown error';
            break;
    }
}