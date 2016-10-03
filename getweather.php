<?php

require 'required.php';
require 'onlyloggedin.php';
require 'weather_inc.php';

$output['currently'] = $currently;

// Re-encode the data to JSON and send to client
echo json_encode($output);