<?php

require_once './required.php';

header('Content-Type: application/json');

if (!isAdmin()) {
    die("Unauthorized.");
}

$players = $database->select('players', '*', ['lastping[>]' => date('Y-m-d H:i:s', strtotime('-2 days'))]);

$out = [
    'name' => "Places",
    'type' => "FeatureCollection",
    'features' => [
    ]
];
foreach ($players as $player) {
    // Format stuff
    $lastping = date('Y-m-d h:i:s A', strtotime($player['lastping']));
    
    $level = floatval($player['level']);
    $energy = intval($player['energy']);
    $maxenergy = intval($player['maxenergy']);
    $teamid = intval($player['teamid']);
    $credits = intval($player['credits']);
    
    $out['features'][] = array("type" => "Feature",
        "geometry" => [
            "type" => "Point",
            "coordinates" => [
                floatval($player['longitude']),
                floatval($player['latitude'])
            ]
        ],
        "properties" => [
            "uuid" => $player['uuid'],
            "level" => $level,
            "energy" => $energy,
            "maxenergy" => $maxenergy,
            "lastping" => $lastping,
            "teamid" => $teamid,
            "credits" => $credits,
            "nickname" => $player['nickname']
        ]
    );
}

echo json_encode($out);
