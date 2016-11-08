<?php

require 'required.php';
require 'onlyloggedin.php';

if (!is_empty($VARS['markread'])) {
    if (preg_match("/[0-9]+/", $VARS['markread'])) {
        $database->update('private_messages', ['msg_read' => 1], [
            'AND' => [
                "id" => $VARS['markread'],
                "to_uuid" => $_SESSION['uuid']
        ]]);
        sendOK();
    } else {
        sendError("Malformed input.", true);
    }
} else if (!is_empty($VARS['delete'])) {
    if (preg_match("/[0-9]+/", $VARS['delete'])) {
        $database->delete('private_messages', [
            'AND' => [
                "id" => $VARS['delete'],
                "to_uuid" => $_SESSION['uuid']
        ]]);
        sendOK();
    } else {
        sendError("Malformed input.", true);
    }
} else {
    $where = [
        "private_messages.to_uuid" => $_SESSION['uuid']
    ];
    if ($VARS['filter'] == 'read') {
        $where["msg_read"] = 1;
    } else if ($VARS['filter'] == 'unread') {
        $where["msg_read"] = 0;
    }
    $out = ["status" => "OK", "msgs" => []];
    $out['msgs'] = $database->select('private_messages', [
        "[>]players" => [
            "from_uuid" => "uuid"
        ]
            ], [
        'private_messages.id', 'private_messages.message', 'private_messages.time', 'players.nickname', 'private_messages.msg_read'
            ], [
        'AND' => $where,
        "ORDER" => "private_messages.time DESC"]
    );  
    
    echo json_encode($out);
}