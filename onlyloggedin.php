<?php

/**
 * Require/include this to make login required.
 */

if ($_SESSION['loggedin'] != true) {
    sendError(SESSION_EXPIRED, true);
}