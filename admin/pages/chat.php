<?php
if (IN_ADMIN !== true) {
    die("Error.");
}
if ($_GET['sub'] == 'system') {
    require 'pages/chat/system.php';
    die();
} else {
    require 'pages/chat/log.php';
    die();
}
?>