<?php
if (!isset($argv[1])) {
	die('needs URL to index as first argument');
}

require __DIR__ . '/../config.php';
require __DIR__ . '/../lib/grokabot.php';
require __DIR__ . '/../lib/groonga.php';
require __DIR__ . '/../lib/utils.php';

$url = $argv[1];

$bot = new grokabot;
$bot->userAgent = GROKABOT_USERAGENT;
$g = new groonga(GROONGA_URL);

$response = $bot->add($url, $g);

if ($response===true) {
    echo 'OK' . PHP_EOL;
} else {
    echo 'ERROR - ' . $response . PHP_EOL;
}