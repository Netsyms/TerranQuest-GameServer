<?php

require 'required.php';
require 'onlyloggedin.php';

$itemuuid = $VARS['itemuuid'];
$player = $VARS['giveto'];

if (is_empty($itemuuid) || !is_numeric($itemuuid) || !$database->has('inventory', ["AND" => ['itemuuid' => $itemuuid, 'playeruuid' => $_SESSION['uuid']]])) {
    sendError("Invalid itemuuid.", true);
}

if (is_empty($player) || !$database->has('players', ['nickname' => $player])) {
    sendError("Invalid nickname.", true);
}

$playeruuid = $database->select('players', ['uuid'], ['nickname' => $player])[0];


$database->update('inventory', ['playeruuid' => $playeruuid], ['itemuuid' => $itemuuid]);

sendOK();