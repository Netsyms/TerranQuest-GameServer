<?php

require 'required.php';

require 'onlyloggedin.php';

$where = ['inventory.playeruuid' => $_SESSION['uuid']];
if (!is_empty($VARS['classname'])) {
    $where = ["AND" => ['inventory.playeruuid' => $_SESSION['uuid'], 'itemclasses.classname' => $VARS['classname']]];
}

$inv = $database->select(
        'items', [
    '[>]inventory' => ['itemid' => 'itemid'],
    '[>]itemclasses' => ['classid', 'classid']
        ], [
    'inventory.itemuuid',
    'inventory.itemid',
    'inventory.itemjson',
    'items.itemname',
    'items.itemdesc',
    'items.itemcode',
    'itemclasses.classid',
    'itemclasses.classname'
        ], $where
);

if ($inv == FALSE) {
    $inv = [];
}

$out['status'] = 'OK';
$out['items'] = $inv;
echo json_encode($out);
