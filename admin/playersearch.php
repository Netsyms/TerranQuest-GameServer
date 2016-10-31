<?php

require_once './required.php';

if (!isAdmin()) {
    die("Unauthorized.");
}

header('Content-Type: application/json');

$data = $database->select("players", "nickname", ["nickname[~]" => $_GET['q']]);
echo json_encode($data);