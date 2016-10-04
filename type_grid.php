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
