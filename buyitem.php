<?php

require 'required.php';
require 'onlyloggedin.php';

if (!$database->has('shopitems', ['merchid' => $VARS['merchid']])) {
    sendError(ITEM_UNAVAILABLE, true);
}

$shopitem = $database->select('shopitems', ['merchid', 'itemid', 'quantity', 'cost'], ['merchid' => $VARS['merchid']])[0];

if (!is_empty($VARS['cost']) && !($shopitem['cost'] == $VARS['cost'])) {
    sendError(ITEM_INCORRECT_PRICE, true);
}

$credits = $database->select('players', ['credits'], ['uuid' => $_SESSION['uuid']])[0]['credits'];
if ($credits < $shopitem['cost']) {
    sendError(PLAYER_NOT_ENOUGH_CREDITS, true);
}

for ($i = 0; $i < $shopitem['quantity']; $i++) {
    $database->insert('inventory', ['playeruuid' => $_SESSION['uuid'], 'itemid' => $shopitem['itemid']]);
}

$database->update('players', ['credits' => ($credits - $shopitem['cost'])], ['uuid' => $_SESSION['uuid']]);

sendOK(ITEM_PURCHASED);