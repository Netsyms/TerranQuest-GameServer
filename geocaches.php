<?php

require 'required.php';

$okapi = "http://opencaching.us/okapi/";

if (is_empty($VARS['lat']) || is_empty($VARS['long'])) {
    sendError("Missing information.", true);
}

if (!preg_match('/-?[0-9]{1,3}\.[0-9]{3,}/', $VARS['lat'])) {
    sendError("Latitude (lat) is in the wrong format.", true);
}

if (!preg_match('/-?[0-9]{1,3}\.[0-9]{3,}/', $VARS['long'])) {
    sendError("Longitude (long) is in the wrong format.", true);
}

$lat = $VARS['lat'];
$long = $VARS['long'];

$limit = 25;
if (!is_empty($VARS['limit']) && is_numeric($VARS['limit'])) {
    $limit = intval($VARS['limit']);
}

$json = file_get_contents($okapi . "services/caches/search/nearest?center=" . $lat . "|" . $long . "&limit=" . $limit . "&consumer_key=" . GEOCACHE_KEY);

if (!$json)
    sendError("Something went wrong, try again later.", true);

$caches = json_decode($json)->results;

$list = "";
foreach ($caches as $val) {
    $list .= $val . "|";
}
echo file_get_contents($okapi . "services/caches/geocaches?consumer_key=" . GEOCACHE_KEY . "&cache_codes=" . rtrim($list, "|"));

