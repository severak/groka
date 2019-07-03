<?php
require __DIR__ . '/../config.php';
require __DIR__ . '/../lib/grokabot.php';
require __DIR__ . '/../lib/groonga.php';
require __DIR__ . '/../lib/utils.php';

$sleep = isset($argv[1]) ? intval($argv[1]) : 0;

$bot = new grokabot;
$bot->userAgent = GROKABOT_USERAGENT;
$g = new groonga(GROONGA_URL);

while($url = fgets(STDIN)){
	$url = trim($url);
	
	if (empty($url)) {
		continue;
	}
	
	if ($sleep>0) {
		echo 'waiting ' .  $sleep . 's...' . PHP_EOL;
		sleep($sleep);
	}
	
	$response = $bot->add($url, $g);

	if ($response===true) {
        echo 'OK ' . $url . PHP_EOL;
    } else {
	    echo 'ERR ' . $url . ' - ' . $response . PHP_EOL;
    }

}


