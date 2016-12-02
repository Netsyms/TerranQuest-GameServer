<?php

/**
 * This file contains global settings and things that should be loaded at the
 * top of each file.
 */
ob_start();
session_start();

header("Access-Control-Allow-Origin: *");

if (isset($_GET['format']) && strtolower($_GET['format']) == 'plain') {
    define("JSON", false);
    header('Content-Type: text/plain; charset=utf-8');
} else {
    define("JSON", true);
    header('Content-Type: application/json; charset=utf-8');
}

// Composer
require 'vendor/autoload.php';
// API response formatters
require 'response.php';
// Settings file
require 'settings.php';

// Database settings
// Also inits database and stuff
$database;
try {
    $database = new medoo([
        'database_type' => DB_TYPE,
        'database_name' => DB_NAME,
        'server' => DB_SERVER,
        'username' => DB_USER,
        'password' => DB_PASS,
        'charset' => DB_CHARSET
    ]);
} catch (Exception $ex) {
    header('HTTP/1.1 500 Internal Server Error');
    sendError('Database error.  Try again later.', true);
}


if (!DEBUG) {
    error_reporting(0);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
}


$VARS;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $VARS = $_POST;
    define("GET", false);
} else {
    $VARS = $_GET;
    define("GET", true);
}

/**
 * Checks if a string or whatever is empty.
 * @param $str The thingy to check
 * @return boolean True if it's empty or whatever.
 */
function is_empty($str) {
    return (is_null($str) || !isset($str) || $str == '');
}

if (is_empty($VARS['lang'])) {
    require_once "lang/en_us.php";
} else {
    switch ($VARS['lang']) {
        case "test":
            require_once "lang/test.php";
            break;
        case "en-US":
            require_once "lang/en_us.php";
            break;
        default:
            require_once "lang/en_us.php";
    }
}