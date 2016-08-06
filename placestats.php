<?php

/**
 * Get the stats for a place.  Useful for reloading stats after doing something.
 */
require 'required.php';

if (is_empty($VARS['locationid'])) {
    sendError("Missing internal location ID.", true);
}

$data['status'] = 'OK';
if (!$database->has('locations', ['locationid' => $VARS['locationid']])) {
    sendError("No stats found.", true);
}
$gameinfo = $database->select('locations', ['locationid', 'teamid', 'owneruuid', 'currentlife', 'maxlife'], ['locationid' => $VARS['locationid']])[0];

$data['stats'] = $gameinfo;
echo json_encode($data);