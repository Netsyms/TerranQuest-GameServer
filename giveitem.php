<?php

require 'required.php';
require 'onlyloggedin.php';

$itemuuid = $VARS['itemuuid'];
$player = $VARS['giveto'];

if (is_empty($itemuuid) || !is_numeric($itemuuid) || !$database->has('inventory', ["AND" => ['itemuuid' => $itemuuid, 'playeruuid' => $_SESSION['uuid']]])) {
    sendError(INVALID_ITEMID, true);
}

if (is_empty($player) || !$database->has('players', ['nickname' => $player])) {
    sendError(INVALID_NICKNAME, true);
}

$playeruuid = $database->select('players', ['uuid'], ['nickname' => $player])[0]['uuid'];


$database->update('inventory', ['playeruuid' => $playeruuid], ['itemuuid' => $itemuuid]);

sendOK();