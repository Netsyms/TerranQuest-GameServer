<?php

define("DEBUG", false);
define("BETA_MODE", false);

define("DB_TYPE", "mysql");
define("DB_NAME", "");
define("DB_SERVER", "");
define("DB_USER", "");
define("DB_PASS", "");
define("DB_CHARSET", "latin1");

define("MIN_CLIENT_VERSION", "1.6.0");

define("GEOCACHE_KEY", "");
define("MUNZEE_KEY", "");
define("MUNZEE_SECRET", "");

// API key for the TerranQuest GIS server.
// Has global terrain data and other large sets.
// Get instructions for requesting a key at gis.terranquest.net.
define("GIS_API_KEY", "");

define("GOOGLEPLAY_PUBLICKEY", "");
define("APP_STORE_SANDBOX", true);

define("DARKSKY_APIKEY", "");

// List of players with special chat colors.
// Server messages (from the admin panel) are always special.
define("CHAT_ADMINS", ["admin"]);
// Color for chat admin names.  Accepts any HTML named color or hexcode (#ff0000)
define("CHAT_ADMIN_COLOR", "red");

// Admin control panel login
define("ADMIN_USER", "");
define("ADMIN_PASS", "");

// Geocoding API key for admin panel lookups
define("MAPQUEST_KEY", "");

// Used for timestamps in the admin panel.
// For supported values, see http://php.net/manual/en/timezones.php
define("TIMEZONE", "UTC");