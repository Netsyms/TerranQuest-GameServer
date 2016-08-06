<?php

require 'required.php';
require 'onlyloggedin.php';

if ($VARS['teamid'] < 1 || $VARS['teamid'] > 6) {
    sendError("Invalid team ID.", true);
}
$currentteam = $database->select('players', 'teamid', ['uuid' => $_SESSION['uuid']])[0];
if ($currentteam > 0 && $currentteam < 7) {
    sendError("You have already chosen a team.  Contact support for problems.", true);
}
$database->update('players', ['teamid' => $VARS['teamid']], ['uuid' => $_SESSION['uuid']]);
sendOK();