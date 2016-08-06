<?php

require 'required.php';
require 'onlyloggedin.php';

if (is_empty($VARS['locationid'])) {
    sendError("No target!", true);
}

$place = $database->select('locations', ['locationid', 'teamid', 'owneruuid', 'currentlife', 'maxlife'], ['locationid' => $VARS['locationid']])[0];
$user = $database->select('players', ['level', 'teamid', 'energy', 'maxenergy', 'latitude', 'longitude'], ['uuid' => $_SESSION['uuid']])[0];

// This (probably) shouldn't happen in normal play
if ($place['teamid'] == $user['teamid']) {
    sendError("Don't attack your own kind!", true);
}

if ($place['currentlife'] > 0) {
    sendError("Cannot claim!", true);
}

$userdrain = 5 * floor($user['level']);

// Calculate resulting user HP
$userhp = $user['energy'] - $userdrain;
// Check if action possible
if ($userhp < 0) {
    sendError("Not enough life left!", true);
}

// Update the user's health
// TODO: calculate XP and add to decimal portion of level
$database->update('players', ['energy' => $userhp], ['uuid' => $_SESSION['uuid']]);

// Update the place
$database->update('locations', ['currentlife' => 100, 'maxlife' => 100, 'owneruuid' => $_SESSION['uuid'], 'teamid' => $user['teamid']], ['locationid' => $VARS['locationid']]);

sendOK("Success!");
