<?php

/**
 * This file contains global settings and things that should be loaded at the
 * top of each file.
 */
ob_start();
session_start();

header("Access-Control-Allow-Origin: *");

if (strtolower($_GET['format']) == 'plain') {
    define("JSON", false);
    header('Content-Type: text/plain');
} else {
    define("JSON", true);
    header('Content-Type: application/json');
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

// Show errors and stuff?
define("DEBUG", false);

// Use POST instead of GET?
if (!is_empty($_GET['post']) && $_GET['post'] == '1') {
    define("GET", false);
} else {
    define("GET", true);
}


if (!DEBUG) {
    error_reporting(0);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
}
$VARS;
if (GET) {
    $VARS = $_GET;
} else {
    $VARS = $_POST;
}

/**
 * Checks if a string or whatever is empty.
 * @param $str The thingy to check
 * @return boolean True if it's empty or whatever.
 */
function is_empty($str) {
    return (!isset($str) || $str == '' || $str == null);
}
