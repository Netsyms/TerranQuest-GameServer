<?php

require 'required.php';

require 'onlyloggedin.php';

if (is_empty($VARS['user'])) {
    sendError("Missing data.", true);
}

$stats = $database->select('players', ['level', 'energy', 'maxenergy', 'teamid', 'lastping', 'kick'], ['nickname' => $VARS['user']])[0];

$uuid = $_SESSION['uuid'];
$kick = $database->select('players', ['kick'], ['uuid' => $uuid])[0]['kick'];
if (!is_empty($kick)) {
    $_SESSION['loggedin'] = false;
    session_unset();
    session_destroy();
    $database->update('players', ['kick' => ''], ['uuid' => $uuid]);
    die(json_encode(['status' => 'ERROR', 'kick' => 1, 'message' => $kick]));
}

$out = [];
$out['status'] = 'OK';
$out['stats'] = $stats;
$out['stats']['lastping'] = 0;
echo json_encode($out);
