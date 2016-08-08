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

if (is_empty($origcode)) {
    sendError("Bad code!", true);
}

if ($database->has('claimedcodes', ["AND" => ['code' => $origcode, 'playeruuid' => $_SESSION['uuid']]])) {
    sendError("You've already found this code!", true);
}

$codearray = str_split($origcode);

$codeint = 0;
foreach ($codearray as $chr) {
    $codeint += ord($chr);
}

Random::seed($codeint);
$itemcode = Random::num(1, 6);

$database->insert('inventory', ['playeruuid' => $_SESSION['uuid'], 'itemid' => $itemcode]);
$database->insert('claimedcodes', ['code' => $origcode, 'playeruuid' => $_SESSION['uuid']]);
$itemname = $database->select('items', ['itemname'], ['itemid' => $itemcode])[0]['itemname'];
sendOK($itemname);
