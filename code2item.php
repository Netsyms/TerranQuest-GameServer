<?php

require 'required.php';
require 'onlyloggedin.php';

/**
 * https://www.sitepoint.com/php-random-number-generator/
 */
class Random {

    // random seed
    private static $RSeed = 0;

    // set seed
    public static function seed($s = 0) {
        self::$RSeed = abs(intval($s)) % 9999999 + 1;
        self::num();
    }

    // generate random number
    public static function num($min = 0, $max = 9999999) {
        if (self::$RSeed == 0) {
            self::seed(mt_rand());
        }
        self::$RSeed = (self::$RSeed * 125) % 2796203;
        return self::$RSeed % ($max - $min + 1) + $min;
    }

}

$origcode = $VARS['code'];
$latitude = $VARS['latitude'];
$longitude = $VARS['longitude'];
$accuracy = $VARS['accuracy'];

$returndata = [
    "status" => "OK",
    "messages" => []
];

if (is_empty($origcode)) {
    sendError(BARCODE_INVALID, true);
}

try {
    if (strpos($origcode, "munzee") > 1) {
        include 'capturemunzee.php';
    }
} catch (Exception $ex) {
    //file_put_contents("munzee.log", "Error with Munzee code: $ex\n", FILE_APPEND);
}

if ($database->has('claimedcodes', ["AND" => ['code' => $origcode, 'playeruuid' => $_SESSION['uuid']]])) {
    $returndata['messages'][] = ["title" => BARCODE_ALREADY_FOUND_TITLE, "text" => BARCODE_ALREADY_FOUND_MSG];
} else {
    if ($origcode == "http://terranquest.net/#9001") {
        // Secret awesome codez
        $database->insert('inventory', ['playeruuid' => $_SESSION['uuid'], 'itemid' => 9001]);
        $database->insert('claimedcodes', ['code' => $origcode, 'playeruuid' => $_SESSION['uuid']]);
        $itemname = $database->select('items', ['itemname'], ['itemid' => 9001])[0]['itemname'];
    } else {
        $codearray = str_split($origcode);


        $codeint = 0;
        foreach ($codearray as $chr) {
            $codeint += ord($chr);
        }

        Random::seed($codeint);
        $itemcode = Random::num(1, 10);

        $database->insert('inventory', ['playeruuid' => $_SESSION['uuid'], 'itemid' => $itemcode]);
        $database->insert('claimedcodes', ['code' => $origcode, 'playeruuid' => $_SESSION['uuid']]);
        $itemname = $database->select('items', ['itemname'], ['itemid' => $itemcode])[0]['itemname'];
    }
    $returndata["messages"][] = ["title" => BARCODE_ITEM_FOUND_TITLE, "text" => str_replace("%1", $itemname, BARCODE_ITEM_FOUND_MSG)];
}

die(json_encode($returndata));
