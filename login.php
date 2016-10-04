<?php

require 'required.php';

if (is_empty($VARS['user'])) {
    sendError("Missing username.", true);
}

if (is_empty($VARS['pass'])) {
    sendError("Missing password.", true);
}

$VARS['user'] = strtolower(str_replace(" ", "", $VARS['user']));

/* Insert code to check login here, it should return "OK" or an error string */
/* ------------------------------- */

$logininfo = file_get_contents("https://sso.netsyms.com/api/simplehashauth.php?get=1&user=" . urlencode($VARS['user']) . "&pass=" . hash('sha1', $VARS['pass']));

/* ------------------------------- */
if ($logininfo != "OK") {
    sendError(str_replace("Error: ", "", $logininfo), true);
}

$guid = file_get_contents("https://sso.netsyms.com/api/getguid.php?user=" . urlencode($VARS['user']));

if (is_empty($guid)) {
    sendError("Account does not exist.", true);
}

if ($database->has('players', ['uuid' => $guid])) {
    sendOK();
} else {
    $database->insert('players', ['uuid' => $guid, 'level' => 1.0, 'energy' => 100, 'maxenergy' => 100, '#lastping' => 'NOW()', 'nickname' => $VARS['user']]);
    sendOK("Successfully synced Netsyms account to TerranQuest.");
    ini_set("sendmail_from", "sso@netsyms.com");

    $message = "This is just a quick message confirming that you have successfully linked TerranQuest to your Netsyms Technologies account.  \n\n";
    $message .= "If you have any questions or need assistance with anything, email admin@netsyms.com and we will be happy to assist you.  \n\n";
    $message .= "Have a nice day, " . $VARS['user'] . ".  We hope you continue to use our services.";
    $message .= "\n\n--------\nNetsyms Technologies\n\nThis is an automated email.  Do not reply to it.";

    $headers = "From: Account System <sso@netsyms.com>";

    $email = file_get_contents("https://sso.netsyms.com/api/getemail.php?user=" . $VARS['user']);

    mail($email, "Account Update", $message, $headers);
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
        $database->update('players', ['credits' => 250], ['uuid' => $guid]);
    }
}