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
