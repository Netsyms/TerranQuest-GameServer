<?php

require_once './required.php';

if (!isAdmin()) {
    die("Unauthorized.");
}

$database->update("players", ["level" => $_POST['level'], "energy" => $_POST['life'], "credits" => $_POST['credits'], "teamid" => $_POST['team']], ["uuid" => $_POST['uuid']]);

header('Location: ./?page=players&msg=success');