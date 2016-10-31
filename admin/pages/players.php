<?php
if (IN_ADMIN !== true) {
    die("Error.");
}
if ($_GET['sub'] == 'active') {
    require 'pages/players/active.php';
    die();
} else {
    require 'pages/players/default.php';
    die();
}
?>