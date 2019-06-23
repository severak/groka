<?php
require __DIR__ . '/../config.php';
require __DIR__ . '/../lib/grokabot.php';
require __DIR__ . '/../lib/groonga.php';
require __DIR__ . '/../lib/utils.php';

$sleep = isset($argv[1]) ? intval($argv[1]) : 0;

$bot = new grokabot;
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
	
	$html = $bot->get($url);

	if (!$html) {
		echo 'ERR - cannot download ' . $url . PHP_EOL;
		continue;
	}

	$info = $bot->analyze($html);
	if (!$info) {
		echo 'ERR - cannot analyze ' . $url . PHP_EOL;
		continue;
	}

	if (!isset($info['title'])) {
		echo 'ERR - find title ' . $url . PHP_EOL;
		continue;
	}

	$info['title'] = cleantext($info['title']);
	$info['description'] = cleantext($info['description']);
	$info['text'] = cleantext($info['text']);
	$info['_key'] = $url;

	if ($g->load(['table'=>'groka'], $info)) {
		echo 'OK ' . $url . PHP_EOL;
	} else {
		echo 'ERROR - cannot save' . $url . PHP_EOL;
	}
	
}


