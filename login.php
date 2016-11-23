<?php

require 'required.php';

if (is_empty($VARS['user'])) {
    sendError(USERNAME_MISSING, true);
}

if (is_empty($VARS['pass'])) {
    sendError(PASSWORD_MISSING, true);
}

$VARS['user'] = strtolower(str_replace(" ", "", $VARS['user']));

/* Insert code to check login here */
/* ------------------------------- */

$loginok = file_get_contents("https://sso.netsyms.com/api/simplehashauth.php?get=1&user=" . urlencode($VARS['user']) . "&pass=" . hash('sha1', $VARS['pass']));

if ($loginok != "OK") {
    sendError(str_replace("Error: ", "", $loginok), true);
}

/* Put code here to get the unique UUID (internal ID) for the player */
$guid = file_get_contents("https://sso.netsyms.com/api/getguid.php?user=" . urlencode($VARS['user']));

/* ------------------------------- */

if (is_empty($guid)) {
    sendError(ACCOUNT_MISSING, true);
}

if (!$database->has('players', ['uuid' => $guid])) {
    $database->insert('players', ['uuid' => $guid, 'level' => 1.0, 'energy' => 100, 'maxenergy' => 100, '#lastping' => 'NOW()', 'nickname' => $VARS['user']]);
}
// Setup the session
$_SESSION['username'] = $VARS['user'];
$_SESSION['guid'] = $_SESSION['uuid'] = $guid;
$_SESSION['loggedin'] = true;

// Give out the beta tester badge and stuff to people
if (BETA_MODE) {
    if (!$database->has('player_badges', ["AND" => ['playeruuid' => $guid, 'badgeid' => 1]])) {
        $database->insert('player_badges', ['playeruuid' => $guid, 'badgeid' => 1, '#gotdate' => "NOW()"]);
        // Give some free credits as thanks
        $database->update('players', ['credits' => 500], ['uuid' => $guid]);
    }
}
sendOK();
