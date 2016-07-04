<?php

require 'required.php';

if (is_empty($VARS['user'])) {
    sendError("Missing data.", true);
}

$inv = $database->select('items', ['[>]inventory' => ['itemid' => 'itemid'], '[>]itemclasses' => ['classid', 'classid']], ['inventory.itemuuid', 'inventory.itemid', 'inventory.itemjson', 'items.itemname', 'items.itemdesc', 'itemclasses.classid', 'itemclasses.classname'], ['inventory.playeruuid' => file_get_contents("https://sso.netsyms.com/api/getguid.php?user=" . $VARS['user'])]);

$out['status'] = 'OK';
$out['items'] = $inv;
echo json_encode($out);