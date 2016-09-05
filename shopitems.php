<?php

require 'required.php';

$shop = $database->select('shopitems', '*');
$coins = $database->select('shopcoins', '*');

if ($_SESSION['loggedin']) {
    $balance = $database->select('players', ['credits'], ['uuid' => $_SESSION['uuid']])[0]['credits'];
} else {
    $balance = null;
}

$out = [
    "status" => "OK",
    "items" => $shop,
    "coins" => $coins,
    "balance" => $balance
];

echo json_encode($out);