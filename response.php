<?php

/**
 * Send an OK message.
 * @param type $message the message
 * @param type $die to die or not to die?
 */
function sendOK($message = "", $die = false) {
    if (!is_empty($message) && JSON) {
        echo '{ "status": "OK", "message": "'.$message.'" }';
    } elseif (is_empty($message) && JSON) {
        echo '{ "status": "OK" }';
    } elseif (!is_empty($message) && !JSON) {
        echo "OK:$message";
    } else {
        echo "OK";
    }
    if ($die) {
        die();
    }
}

/**
 * Send an error message.
 * @param type $message the message
 * @param type $die to die or not to die?
 */
function sendError($error, $die = false) {
    if (JSON) {
        echo '{ "status": "ERROR", "message": "' . $error . '" }';
    } else {
        echo "Error: $error";
    }
    if ($die) {
        die();
    }
}