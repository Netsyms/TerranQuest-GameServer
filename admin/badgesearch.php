<?php

require_once './required.php';

if (!isAdmin()) {
    die("Unauthorized.");
}

header('Content-Type: application/json');

$data = $database->select("badges", "badgesid", ["OR" => ["badgesid[~]" => $_GET['q'], "badgename[~]" => $_GET['q']]]);
echo json_encode($data);