<?php

require 'required.php';
require 'onlyloggedin.php';

if (!$database->has('shopitems', ['merchid' => $VARS['merchid']])) {
    sendError("That item is not available at this time.", true);
}

$shopitem = $database->select('shopitems', ['merchid', 'itemid', 'quantity', 'cost'], ['merchid' => $VARS['merchid']])[0];

if (!is_empty($VARS['cost']) && !($shopitem['cost'] == $VARS['cost'])) {
    sendError("That item is no longer available at that price.", true);
}

$credits = $database->select('players', ['credits'], ['uuid' => $_SESSION['uuid']])[0]['credits'];
if ($credits < $shopitem['cost']) {
    sendError("You don't have enough money!", true);
}

for ($i = 0; $i < $shopitem['quantity']; $i++) {
    $database->insert('inventory', ['playeruuid' => $_SESSION['uuid'], 'itemid' => $shopitem['itemid']]);
}

$database->update('players', ['credits' => ($credits - $shopitem['cost'])], ['uuid' => $_SESSION['uuid']]);

sendOK("Thanks for your purchase!");