<?php

$msgs = $database->select('messages', ['username', 'message', 'time'], ['AND' => [
            'lat[>]' => $searchbounds[0]->getLatitudeInDegrees(),
            'lat[<]' => $searchbounds[1]->getLatitudeInDegrees(),
            'long[>]' => $searchbounds[0]->getLongitudeInDegrees(),
            'long[<]' => $searchbounds[1]->getLongitudeInDegrees()],
        "ORDER" => "time DESC",
        "LIMIT" => 30
    ]);