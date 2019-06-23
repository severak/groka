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
$html = $bot->get($url);

if (!$html) die('cannot download this');

$info = $bot->analyze($html);
if (!$info) die('cannot analyze');


$info['title'] = cleantext($info['title']);
$info['description'] = cleantext($info['description']);
$info['text'] = cleantext($info['text']);
$info['_key'] = $url;

$g = new groonga(GROONGA_URL);

if ($g->load(['table'=>'groka'], $info)) {
	echo 'OK';
}