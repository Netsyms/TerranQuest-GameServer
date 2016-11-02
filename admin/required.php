<?php

ob_start();
session_start();
// Load database and settings
require_once '../vendor/autoload.php';
require_once '../settings.php';

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
    die("Database error.");
}

/**
 * Convert a UTC datetime to local time.
 * @param String $format see date()
 * @param String $date the date to convert.  Defaults to NOW.
 * @return output of date()
 */
function date_tz($format, $date = 'NOW', $intz = 'UTC', $outtz = TIMEZONE) {
    return date($format, strtotime($date));
    //$d = new DateTime($date, new DateTimeZone($intz));
    //$d->setTimezone(new DateTimeZone($outtz));
    //return $d->format($format);
}

/**
 * Checks if a string or whatever is empty.
 * @param $str The thingy to check
 * @return boolean True if it's empty or whatever.
 */
function is_empty($str) {
    return (!isset($str) || $str == '' || $str == null);
}

function isAdmin() {
    return $_SESSION['admin'] === true;
}

function getTeamInfoFromId($id) {
    $$team_string = "None";
    $$team_color = "FFFFFF";
    switch ($id) {
        case "1":
            $team_string = "Water";
            $team_color = "00BFFF";
            break;
        case "2":
            $team_string = "Fire";
            $team_color = "FF4000";
            break;
        case "3":
            $team_string = "Earth";
            $team_color = "D1A000";
            break;
        case "4":
            $team_string = "Wind";
            $team_color = "96FFFF";
            break;
        case "5":
            $team_string = "Light";
            $team_color = "FFFF96";
            break;
        case "6":
            $team_string = "Dark";
            $team_color = "ABABAB";
            break;
        default:
            $team_string = "None";
            $team_color = "FFFFFF";
            break;
    }
    return ['name' => $team_string, 'color' => $team_color];
}

function getTeamNameFromId($id) {
    return getTeamInfoFromId($id)['name'];
}

function getTeamColorFromId($id) {
    return getTeamInfoFromId($id)['color'];
}

define("IN_ADMIN", true);
