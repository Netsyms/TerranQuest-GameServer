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

// The underwhelming damage formulas :P
$userdrain = 5 * floor($user['level']);
$damage = 2 * $userdrain;

// Check if action possible
if ($user['energy'] < $userdrain) {
    sendError("Not enough life left!", true);
}

// Calculate resulting user HP
$userhp = $user['energy'] - $userdrain;

// Calculate resulting place HP
$placehp = $place['currentlife'] - $damage;

// No negatives plz
if ($placehp < 0) {
    $placehp = 0;
}

// Update the user's health
// TODO: calculate XP and add to decimal portion of level
$database->update('players', ['energy' => $userhp], ['uuid' => $_SESSION['uuid']]);

if ($placehp == 0) {
    // It's dead
    $database->update('locations', ['owneruuid' => null, 'teamid' => 0, 'currentlife' => 0, 'maxlife' => 0], ['locationid' => $VARS['locationid']]);
} else {
    // or not
    $database->update('locations', ['currentlife' => $placehp], ['locationid' => $VARS['locationid']]);
}

sendOK("Success!");