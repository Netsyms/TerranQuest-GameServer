<?php

require 'required.php';
require 'onlyloggedin.php';

if (is_empty($VARS['locationid'])) {
    sendError(PLACE_ID_NOT_SENT, true);
}

$place = $database->select('locations', ['locationid', 'teamid', 'owneruuid', 'currentlife', 'maxlife'], ['locationid' => $VARS['locationid']])[0];
$user = $database->select('players', ['level', 'teamid', 'energy', 'maxenergy', 'latitude', 'longitude'], ['uuid' => $_SESSION['uuid']])[0];

// This (probably) shouldn't happen in normal play
if ($place['teamid'] != $user['teamid']) {
    sendError(PLACE_OWNED_BY_WRONG_TEAM, true);
}

if ($place['currentlife'] == 100) {
    sendError(PLACE_HEALTH_IS_FULL, true);
}

$userdrain = 2 * floor($user['level']);

// Calculate resulting user HP
$userhp = $user['energy'] - $userdrain;
// Check if action possible
if ($userhp < 0) {
    sendError(PLAYER_NO_LIFE_LEFT, true);
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

$placelife = $place['currentlife'] + 10;
if ($placelife > $place['maxlife']) {
    $placelife = $place['maxlife'];
}

// Update the place
$database->update('locations', ['currentlife' => $placelife, 'owneruuid' => $_SESSION['uuid'], 'teamid' => $user['teamid']], ['locationid' => $VARS['locationid']]);

echo json_encode(["status" => "OK", "message" => ($dolevelup ? PLAYER_LEVEL_UP : PLACE_HEALTH_REFILLED), "levelup" => ($dolevelup ? true : false)]);
