<?php

/**
 * A 2-dimensional array of damage multipliers.
 * The first dimension is the attacking type, the second is the defending.
 * Example: To find the multiplier when team 1 attacks team 2: $TYPE_GRID[1][2]
 */
$TYPE_GRID = [
    ["", "water", "fire", "earth", "wind", "light", "dark"],
    ["water", 0, 2, 0.5, 1, 1, 1],
    ["fire", 0.5, 0, 0.5, 0.5, 2, 1],
    ["earth", 2, 1, 0, 0.5, 0.5, 1],
    ["wind", 0.5, 0.5, 2, 0, 1, 0.5],
    ["light", 1, 1, 1, 1, 0, 2],
    ["dark", 1, 0.5, 1, 2, 0.5, 0]
];

$TERRAIN_GRID = [
    // Water	Fire	Earth	Wind	Light	Dark
    0 => [3, 0.5, 0.5, 1, 1, 1],
    1 => [1, 1, 1, 1, 1, 2],
    2 => [1, 1, 1, 1, 1, 1],
    3 => [1, 1, 1, 1, 1, 1],
    4 => [1, 1, 1, 1, 1, 1],
    5 => [1, 1, 1, 1, 1, 1],
    6 => [1, 1, 1, 1, 1, 1],
    7 => [1, 1, 1, 1, 1, 1],
    8 => [1, 2, 1, 1, 1, 1],
    9 => [1, 2, 1, 1, 1, 1],
    10 => [1, 1, 2, 2, 2, 0.5],
    11 => [1, 1, 2, 2, 2, 0.5],
    12 => [0.5, 1, 1, 1, 1, 1],
    13 => [1, 1, 1, 0.5, 0.5, 2]
];

$WEATHER_GRID = [
    // Water	Fire	Earth	Wind	Light	Dark
    "rain" => [2, 0.5, 1, 1, 1, 1],
    "clear-day" => [1, 1, 1, 1, 2, 1],
    "clear-night" => [1, 1, 1, 1, 1, 2],
    "partly-cloudy-day" => [1, 1, 1, 1, 2, 1],
    "partly-cloudy-night" => [1, 1, 1, 1, 1, 2],
    "cloudy" => [1, 1, 1, 1, 1, 1],
    "sleet" => [1, 1, 1, 1, 1, 1],
    "snow" => [1, 1, 1, 1, 1, 1],
    "wind" => [1, 1, 1, 2, 1, 1],
    "fog" => [1, 1, 1, 1, 1, 1]
];

/**
 * Get a multiplier for the distance between the player and place.
 * @param float $d the number of miles between
 * @return float The damage multiplier
 */
function DISTANCE_GRID($d) {
    $distance = floor($d * 100.0);
    if ($distance <= 5) { // ~250 feet
        return 1;
    } else if ($distance <= 10) { // ~500 feet
        return 0.95;
    } else if ($distance <= 20) { // ~1000 feet
        return 0.8;
    } else if ($distance <= 50) { // ~2500 feet
        return 0.6;
    } else if ($distance <= 100) { // 1 mile (5280 feet)
        return 0.4;
    } else { // Greater than 1 mile
        return 0.2;
    }
}