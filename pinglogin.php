<?php

require 'required.php';

if (is_empty($VARS['user'])) {
    sendError("Missing data.", true);
}

$guid = file_get_contents("https://sso.netsyms.com/api/getguid.php?user=" . $VARS['user']);

if ($database->has('players', ['uuid' => $guid])) {
    sendOK();
} else {
    $database->insert('players', ['uuid' => $guid, 'level' => 1.0, 'energy' => 100, 'maxenergy' => 100, '#lastping' => 'NOW()']);
    sendOK("Successfully synced Netsyms account to TerranQuest.");
    ini_set("sendmail_from", "sso@netsyms.com");

    $message = "This is just a quick message confirming that you have successfully linked TerranQuest to your Netsyms Technologies account.  \n\n";
    $message .= "If you have any questions or need assistance with anything, email admin@netsyms.com and we will be happy to assist you.  \n\n";
    $message .= "Have a nice day, " . $_SESSION['user'] . ".  We hope you continue to use our services.";
    $message .= "\n\n--------\nNetsyms Technologies\n\nThis is an automated email.  Do not reply to it.";

    $headers = "From: Account System <sso@netsyms.com>";

    $email = file_get_contents("https://sso.netsyms.com/api/getemail.php?user=" . $VARS['user']);

    mail($email, "Account Update", $message, $headers);
}