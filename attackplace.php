<?php

require 'required.php';
require 'onlyloggedin.php';

use AnthonyMartin\GeoLocation\GeoLocation as GeoLocation;

if (is_empty($VARS['locationid'])) {
    sendError("No target!", true);
}

$place = $database->select('locations', ['locationid', 'teamid', 'owneruuid', 'currentlife', 'maxlife', 'osmid'], ['locationid' => $VARS['locationid']])[0];
$user = $database->select('players', ['level', 'teamid', 'energy', 'maxenergy', 'latitude', 'longitude'], ['uuid' => $_SESSION['uuid']])[0];

// This (probably) shouldn't happen in normal play
if ($place['teamid'] == $user['teamid']) {
    sendError("Cannot attack!", true);
}

// The damage formulas
require_once 'type_grid.php';
$userdrain = pow(floor($user['level']), 0.5) * 5;
$type_mod = $TYPE_GRID[$user['teamid']][$place['teamid']];
if ($type_mod == 0.5) {
    $type_mod = 0.8;
} else if ($type_mod == 2) {
    $type_mod = 1.5;
}

$terrain_mod = 1.0;
$weather_mod = 1.0;
$distance_mod = 1.0;
if (!is_empty($VARS['lat']) && !is_empty($VARS['long'])) {
    try {
        require_once 'latlong_validate.php';
        if (!is_empty(DARKSKY_APIKEY)) {
            require 'weather_inc.php';
            $weather_icon = $currently['icon'];
            $weather_mod = $WEATHER_GRID[$weather_icon][$user['teamid'] - 1];
            if ($weather_mod == 0.5) {
                $weather_mod = .9;
            } else if ($weather_mod == 2) {
                $weather_mod = 1.1;
            }
        }
    } catch (Exception $e) {
        $weather_mod = 1.0;
    }

    try {
        $terrain = json_decode(file_get_contents("http://gis.terranquest.net/terrain.php?key=" . GIS_API_KEY . "&lat=" . $VARS['lat'] . "&long=" . $VARS['long']), TRUE);
        if ($terrain['status'] == 'OK') {
            $terraintype = $terrain['type'];
            $terrain_mod = $TERRAIN_GRID[$terraintype][$user['teamid'] - 1];
            if ($terrain_mod == 0.5) {
                $terrain_mod = .9;
            } else if ($terrain_mod == 2) {
                $terrain_mod = 1.1;
            } else if ($terrain_mod == 3) {
                $terrain_mod = 1.3;
            }
        }
    } catch (Exception $e) {
        $terrain_mod = 1.0;
    }

    try {
        // Round to 2 digits (approx. 1.11 meters/3.6 feet)
        $lat = number_format((float) $VARS['lat'], 5, '.', '');
        $long = number_format((float) $VARS['long'], 5, '.', '');
        $placedata = json_decode(file_get_contents("http://gis.terranquest.net/place.php?key=" . GIS_API_KEY . "&osmid=" . $place['osmid']), true);
        $placelong = $placedata['features'][0]['geometry']['coordinates'][0];
        $placelat = $placedata['features'][0]['geometry']['coordinates'][1];
        $playerlocation = GeoLocation::fromDegrees($lat, $long);
        $placelocation = GeoLocation::fromDegrees($placelat, $placelong);
        $distance_mod = DISTANCE_GRID($playerlocation->distanceTo($placelocation, 'miles'));
    } catch (Exception $e) {
        $distance_mod = 1.0;
    }
}

$damage = pow(floor($user['level']), 0.5) * 4 * $type_mod * $terrain_mod * $weather_mod * $distance_mod;
//$damage = 2 * $userdrain * $TYPE_GRID[$user['teamid']][$place['teamid']];
// Check if action possible
if ($user['energy'] < $userdrain) {
    sendError("No life left!", true);
}

// Calculate resulting user HP
$userhp = $user['energy'] - $userdrain;

// Calculate resulting place HP
$placehp = $place['currentlife'] - $damage;

// No negatives plz
if ($placehp < 0) {
    $placehp = 0;
}

// Update the user's health and level
$exp = pow(pow(floor($user['level']) + 1, 2), -1.2);
$userlevel = $user['level'] + $exp;
// If the new level is a whole int bigger than the current
$dolevelup = false;
if (floor($userlevel) > floor($user['level'])) {
    $dolevelup = true;
    $newmaxhp = floor($userlevel) * 100;
    $database->update('players', ['energy' => $newmaxhp, 'maxenergy' => $newmaxhp, 'level' => $userlevel], ['uuid' => $_SESSION['uuid']]);
} else {
    $database->update('players', ['energy' => $userhp, 'level' => $userlevel], ['uuid' => $_SESSION['uuid']]);
}

if ($placehp == 0) {
    // It's dead
    $database->update('locations', ['owneruuid' => null, 'teamid' => 0, 'currentlife' => 0, 'maxlife' => 0], ['locationid' => $VARS['locationid']]);
} else {
    // or not
    $database->update('locations', ['currentlife' => $placehp], ['locationid' => $VARS['locationid']]);
}

sendOK(($dolevelup ? "Level up!" : "Success!"));
