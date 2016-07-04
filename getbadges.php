<?php

require 'required.php';

if (is_empty($VARS['user'])) {
    sendError("Missing data.", true);
}

$badges = $database->select(
        'player_badges', ["[>]badges" => ["badgeid" => "badgeid"]], ['badgesid', 'badgename', 'badgedesc', 'gotdate'], ['playeruuid' => file_get_contents("https://sso.netsyms.com/api/getguid.php?user=" . $VARS['user'])]
);

$out = [];
$out['status'] = 'OK';
$out['badges'] = $badges;
echo json_encode($out);