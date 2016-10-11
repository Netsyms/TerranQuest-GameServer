<?php

require 'required.php';

require 'onlyloggedin.php';

if (is_empty($VARS['user'])) {
    sendError("Missing data.", true);
}

$stats = $database->select('players', ['level', 'energy', 'maxenergy', 'teamid', 'lastping'], ['uuid' => $_SESSION['uuid']])[0];


$out = [];
$out['status'] = 'OK';
$out['stats'] = $stats;
$out['stats']['lastping'] = 0;
echo json_encode($out);
