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

$oldUrlToRemove = false;

$html = $bot->get($url);

if (in_array($bot->status, [301, 302])) {
    $newUrl = $bot->url;
    if (str_replace('https://', 'http://', $newUrl)==$url) {
        // this is HTTP to HTTPS upgrade, we should follow it
        $html = $bot->get($newUrl);
        $oldUrlToRemove = $url;
        echo 'INFO: HTTP upgrade' . PHP_EOL;
    } else {
        $html = $bot->get($newUrl);
        echo 'INFO: redir to ' . $newUrl . PHP_EOL;
    }
    $url = $newUrl;
}

if (!$html) die('ERR - cannot download ' . $url);

if ($bot->status!=200) {
    die('ERR - status '.$bot->status);
}

$mimeType = strtok($bot->contentType, ';');
if ($mimeType=='text/html') {
    $info = $bot->analyze($html);
    if (!isset($info['title'])) die('ERR - cannot find title');

    $info['_key'] = $url;
    $info['title'] = cleantext($info['title']);
    $info['description'] = cleantext($info['description']);
    $info['text'] = cleantext($info['text']);
    if (!$info) die('ERR - cannot analyze HTML');
} elseif ($mimeType=='text/plain') {
    $info['_key'] = $url;
    $info['title'] = basename(parse_url($url, PHP_URL_PATH));
    $info['description'] = $info['title']; // TODO - something better, like first non blank line
    $info['text'] = $html;
} else {
    die('ERR - unsupported mime type ' . $mimeType);
}

if ($g->load(['table'=>'groka'], $info)) {

    if ($oldUrlToRemove) {
        $g->delete(['table'=>'groka', 'key'=>$url]);
    }

	echo 'OK';
} else {
	echo 'ERROR - cannot save';
}