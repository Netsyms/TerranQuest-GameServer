<?php

/**
 * Takes the latitude and longitude and gets nearby places from OSM.
 * 
 * Uses WGS84 in the DD.DD format, because I say so.
 */
require 'required.php';

//ini_set('memory_limit','32M');

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

if ($VARS['debug']) {
    echo "got to the place data query ";
}

$places = json_decode(file_get_contents("http://gis.terranquest.net/places.php?key=" . GIS_API_KEY . "&lat=" . $VARS['lat'] . "&long=" . $VARS['long'] . "&radius=" . $radius), TRUE)['features'];

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
    $osmid = $place['properties']['osm_id'];
    $name = $place['properties']['name'];
    $plong = $place['geometry']['coordinates'][0];
    $plat = $place['geometry']['coordinates'][1];
    if (!$database->has('locations', ['osmid' => $osmid])) {
        $database->insert('locations', ['osmid' => $osmid, 'teamid' => 0]);
    }
    $gameinfo = $database->select('locations', ["[>]players" => ["owneruuid" => "uuid"]], ['locations.locationid', 'players.nickname', 'locations.teamid', 'locations.currentlife', 'locations.maxlife'], ['osmid' => $osmid])[0];
    //$gameinfo = $database->select('locations', ['locationid', 'teamid', 'currentlife', 'maxlife'], ['osmid' => $osmid])[0];
    // Reset owner info for dead places
    if ($gameinfo['currentlife'] <= 0) {
        $database->update('locations', ['teamid' => 0, 'owneruuid' => null, 'maxlife' => 0], ['locationid' => $gameinfo['locationid']]);
        $gameinfo = $database->select('locations', ['locationid', 'teamid', 'currentlife', 'maxlife'], ['osmid' => $osmid])[0];
        $gameinfo['nickname'] = "";
    }
    $geo['features'][] = array("type" => "Feature",
        "geometry" => [
            "type" => "Point",
            "coordinates" => [
                floatval($plong),
                floatval($plat)
            ]
        ],
        "properties" => [
            "osm_id" => intval($osmid),
            "name" => $name,
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