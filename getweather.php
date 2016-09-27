<?php

require 'required.php';
require 'onlyloggedin.php';

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

// Round to 2 digits (approx. 1.1km)
$lat = number_format((float) $VARS['lat'], 2, '.', '');
$long = number_format((float) $VARS['long'], 2, '.', '');

// Delete old records
$database->delete('weathercache', ["date[<]" => date('Y-m-d H:i:s', strtotime('-1 hour'))]);

// If we don't get a cache hit, request from the API
if (!$database->has('weathercache', ["AND" => ["latitude" => $lat, "longitude" => $long]])) {
    $weather = json_decode(file_get_contents("https://api.darksky.net/forecast/" . DARKSKY_APIKEY . "/$lat,$long"), TRUE);
    $currentjson = json_encode($weather['currently']);
    $database->insert('weathercache', ["latitude" => $lat, "longitude" => $long, "#date" => "NOW()", "currentjson" => $currentjson]);
}

// Build output array
$output = ["status" => "OK", "currently" => []];
// Get the cached record and put it in the output array
$output['currently'] = json_decode($database->select('weathercache', 'currentjson', ["AND" => ["latitude" => $lat, "longitude" => $long]])[0], TRUE);

// Re-encode the data to JSON and send to client
echo json_encode($output);