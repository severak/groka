<?php
if (!isset($argv[1])) {
	die('needs URL to index as first argument');
}

require __DIR__ . '/../config.php';
require __DIR__ . '/../lib/grokabot.php';
require __DIR__ . '/../lib/groonga.php';
require __DIR__ . '/../lib/utils.php';

$url = $argv[1];

$g = new groonga(GROONGA_URL);

if ($g->delete(['table'=>'groka', 'key'=>$url])) {
	echo 'OK, removed';
} else {
	echo 'ERROR';
}