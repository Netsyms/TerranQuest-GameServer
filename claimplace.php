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

// Update the user's health and level
$exp = pow(pow(floor($user['level']) + 1, 2), -0.9);
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

// Update the place
$database->update('locations', ['currentlife' => 100, 'maxlife' => 100, 'owneruuid' => $_SESSION['uuid'], 'teamid' => $user['teamid']], ['locationid' => $VARS['locationid']]);

sendOK(($dolevelup ? "Level up!" : "Success!"));
