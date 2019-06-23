<?php
// get all the links from html page, resolves them to absolute URL and print to stdout
if (!isset($argv[1])) {
	die('needs URL to index as first argument');
}

require __DIR__ . '/../config.php';
require __DIR__ . '/../lib/grokabot.php';

$url = $argv[1];

$bot = new grokabot;
$html = $bot->get($url);


$dom = new DOMDocument();
libxml_use_internal_errors(true);
$dom->loadHTML($html);
		
if (!$dom) die('ERROR - cannot load HTML');
		
$xml = simplexml_import_dom($dom);

foreach ($xml->xpath('//a') as $a) {
	$absolute = absolutize($url, $a['href']);
	if ($absolute) {
		echo $absolute . PHP_EOL;
	}
}

function absolutize($base, $url)
{
	if (empty($url)) {
		return false;
	}
	
	if (strpos($url, '#')===0) {
		return false;
	}
	
	$origin = parse_url($base);
	$target = parse_url($url);
	
	if (!empty($target['scheme']) &&  !in_array($target['scheme'], ['http', 'https'])) {
		return false; // we don't want this protocol
	}
	
	if (!empty($target['scheme']) && !empty($target['host'])) {
		return $url; // already absolute
	}
	
	if (empty($origin['path'])) $origin['path'] = '';
	
	if (strpos($target['path'], '/')===0) {
		// absolute link
		return $origin['scheme'] . '://' . $origin['host'] . $target['path'];
	}
	
	if (strpos($target['path'], './')===false) {
		return $origin['scheme'] . '://' . $origin['host'] . oneup($origin['path'])  . '/' . $target['path'];
	}
	
	// not resolving paths yet, sorry
	
	return false;
}

function oneup($path)
{
	if ($path=='') return '';
	
	if ($path=='/') return '/';
	
	return dirname($path);
}