<?php

require 'required.php';
require 'onlyloggedin.php';

if (is_empty($VARS['locationid'])) {
    sendError(PLACE_ID_NOT_SENT, true);
}

$itemuuid = $VARS['itemid'];

if (is_empty($itemuuid) || !is_numeric($itemuuid)) {
    sendError(INVALID_ITEMID . " 1", true);
}

if (!$database->has('inventory', ["AND" => ['itemuuid' => $itemuuid, 'playeruuid' => $_SESSION['uuid']]])) {
    sendError(INVALID_ITEMID . " 1.5", true);
}

$item = $database->select(
                'items', [
            '[>]inventory' => ['itemid' => 'itemid'],
            '[>]itemclasses' => ['classid', 'classid']
                ], [
            'inventory.itemuuid',
            'inventory.itemid',
            'inventory.itemjson',
            'items.itemname',
            'items.itemcode',
            'itemclasses.classid',
            'itemclasses.classname'
                ], [
            "AND" => [
                'itemuuid' => $itemuuid,
                'playeruuid' => $_SESSION['uuid']
            ]
                ]
        )[0];

$place = $database->select('locations', ['locationid', 'teamid', 'owneruuid', 'currentlife', 'maxlife'], ['locationid' => $VARS['locationid']])[0];
$user = $database->select('players', ['level', 'teamid', 'energy', 'maxenergy', 'latitude', 'longitude'], ['uuid' => $_SESSION['uuid']])[0];

// This (probably) shouldn't happen in normal play
if ($place['teamid'] != $user['teamid']) {
    sendError(PLACE_OWNED_BY_WRONG_TEAM, true);
}

$userdrain = 2 * floor($user['level']);

// Calculate resulting user HP
$userhp = $user['energy'] - $userdrain;
// Check if action possible
if ($userhp < 0) {
    sendError(PLAYER_NO_LIFE_LEFT, true);
}


$item['itemcode'] = json_decode($item['itemcode'], true);
if ($item['itemjson'] == "[]" || $item['itemjson'] == "") {
    $itemusesjson = json_encode(['uses' => $item['itemcode']['uses']]);
    $database->update('inventory', ['itemjson' => $itemusesjson], ['itemuuid' => $itemuuid]);
}
$itemusedata = json_decode($database->select('inventory', ['itemjson'], ['itemuuid' => $itemuuid])[0]['itemjson'], true);

switch ($item['classname']) {
    case "artifact": {
            if ($itemusedata['uses'] <= 1) {
                $database->delete('inventory', ["AND" => ['itemuuid' => $itemuuid, 'playeruuid' => $_SESSION['uuid']]]);
            } else if ($itemusedata['uses'] > 1) {
                $itemusedata['uses'] -= 1;
                $database->update('inventory', ['itemjson' => json_encode($itemusedata)], ['itemuuid' => $itemuuid]);
            }
            break;
        }
    default:
        sendError(INVALID_ITEMID . " 2", true);
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

$placelife = $place['currentlife'] + $item['itemcode']['amount'];

$placemax = $place['maxlife'] + $item['itemcode']['amount'];

// Update the place
$database->update('locations', ['currentlife' => $placelife, 'maxlife' => $placemax, 'owneruuid' => $_SESSION['uuid'], 'teamid' => $user['teamid']], ['locationid' => $VARS['locationid']]);

echo json_encode(["status" => "OK", "message" => ($dolevelup ? PLAYER_LEVEL_UP : PLACE_ARTIFACT_ADDED), "levelup" => ($dolevelup ? true : false)]);
