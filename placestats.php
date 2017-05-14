<?php

/**
 * Get the stats for a place.  Useful for reloading stats after doing something.
 */
require 'required.php';

if (is_empty($VARS['locationid'])) {
    sendError("Missing internal location ID.", true);
}

$data['status'] = 'OK';
if (!$database->has('locations', ['locationid' => $VARS['locationid']])) {
    sendError("No stats found.", true);
}
$gameinfo = $database->select('locations', ["[>]players" => ["owneruuid" => "uuid"]], ['locations.locationid', 'locations.teamid', 'locations.owneruuid', 'players.nickname', 'locations.currentlife', 'locations.maxlife'], ['locations.locationid' => $VARS['locationid']])[0];

if ($gameinfo['owneruuid'] == null) {
    $gameinfo['nickname'] = null;
}
$gameinfo['owneruuid'] = "";


// calculate artifact score
$gameinfo['artifact'] = 0;

if ($gameinfo['currentlife'] > 100) {
    $templife = $gameinfo['currentlife'];
    while ($templife > 100) {
        $gameinfo['artifact'] ++;
        $templife -= 75;
    }
}

$data['stats'] = $gameinfo;
echo json_encode($data);
