<?php

// Validate input
if (is_empty($VARS['lat']) || is_empty($VARS['long'])) {
    sendError("Missing information.", true);
}
if (!preg_match('/-?[0-9]{1,3}\.[0-9]{2,}/', $VARS['lat'])) {
    sendError("Latitude (lat) is in the wrong format.", true);
}
if (!preg_match('/-?[0-9]{1,3}\.[0-9]{2,}/', $VARS['long'])) {
    sendError("Longitude (long) is in the wrong format.", true);
}