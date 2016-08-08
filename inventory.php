<?php

require 'required.php';

require 'onlyloggedin.php';

if (is_empty($VARS['user'])) {
    sendError("Missing data.", true);
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
        ], ['inventory.playeruuid' => $_SESSION['uuid']]
);

$out['status'] = 'OK';
$out['items'] = $inv;
echo json_encode($out);
