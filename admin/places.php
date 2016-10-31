<?php

require_once './required.php';

header('Content-Type: application/json');

if (!isAdmin()) {
    die("Unauthorized.");
}

$places = $database->select('locations', ["[>]players" => ["owneruuid" => "uuid"]], ['locations.locationid', 'players.nickname', 'locations.teamid', 'locations.owneruuid', 'locations.currentlife', 'locations.maxlife'], ['locations.owneruuid[!]' => null]);

$out = [
    'name' => "Places",
    'type' => "FeatureCollection",
    'features' => [
    ]
];
foreach ($places as $place) {
    $id = intval($place['locationid']);
    $owner = $place['nickname'];
    $teamid = intval($place['teamid']);
    $life = intval($place['currentlife']);
    $maxlife = intval($place['maxlife']);
    $osmid = intval($place['osmid']);
    
    $out['features'][] = array("type" => "Feature",
        "geometry" => [
            "type" => "Point",
            "coordinates" => [
                floatval($place['longitude']),
                floatval($place['latitude'])
            ]
        ],
        "properties" => [
            "id" => $id,
            "owner" => $owner,
            "life" => $life,
            "maxlife" => $maxlife,
            "teamid" => $teamid
        ]
    );
}

echo json_encode($out);
