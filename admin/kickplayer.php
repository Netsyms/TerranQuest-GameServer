<?php

require_once './required.php';

if (!isAdmin()) {
    die("Unauthorized.");
}

$database->update("players", ["kick" => $_POST['msg']], ["uuid" => $_POST['uuid']]);