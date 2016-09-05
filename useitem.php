<?php

require 'required.php';
require 'onlyloggedin.php';

$itemuuid = $VARS['itemuuid'];

if (is_empty($itemuuid) || !is_numeric($itemuuid) || !$database->has('inventory', ["AND" => ['itemuuid' => $itemuuid, 'playeruuid' => $_SESSION['uuid']]])) {
    sendError("Invalid itemuuid.", true);
}

$item = $database->select(
                'items', [
            '[>]inventory' => ['itemid' => 'itemid'],
            '[>]itemclasses' => ['classid', 'classid']
                ], [
            'inventory.itemuuid',
            'inventory.itemid',
            'inventory.itemjson',
            'items.itemname',
            'items.itemcode',
            'itemclasses.classid',
            'itemclasses.classname'
                ], [
            "AND" => [
                'itemuuid' => $itemuuid,
                'playeruuid' => $_SESSION['uuid']
            ]
                ]
        )[0];

$player = $database->select('players', ['energy', 'maxenergy'], ['uuid' => $_SESSION['uuid']])[0];


$item['itemcode'] = json_decode($item['itemcode'], true);
if ($item['itemjson'] == "[]" || $item['itemjson'] == "") {
    $itemusesjson = json_encode(['uses' => $item['itemcode']['uses']]);
    $database->update('inventory', ['itemjson' => $itemusesjson], ['itemuuid' => $itemuuid]);
}
$itemusedata = json_decode($database->select('inventory', ['itemjson'], ['itemuuid' => $itemuuid])[0]['itemjson'], true);

switch ($item['classname']) {
    case "healmagic":
        // Only use item if it will do something
        if ($player['energy'] < $player['maxenergy']) {
            $newhp = $player['energy'] + $item['itemcode']['amount'];
            if ($newhp > $player['maxenergy']) {
                $newhp = $player['maxenergy'];
            }
            $database->update('players', ['energy' => $newhp], ['uuid' => $_SESSION['uuid']]);
            if ($itemusedata['uses'] <= 1) {
                $database->delete('inventory', ["AND" => ['itemuuid' => $itemuuid, 'playeruuid' => $_SESSION['uuid']]]);
            } else if ($itemusedata['uses'] > 1) {
                $itemusedata['uses'] -= 1;
                $database->update('inventory', ['itemjson' => json_encode($itemusedata)], ['itemuuid' => $itemuuid]);
            }
        }
        break;
}
sendOK();
//echo json_encode($item);