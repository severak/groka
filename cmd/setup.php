<?php

require __DIR__ . "/../config.php";
require __DIR__ . "/../lib/groonga.php";
require __DIR__ . "/../lib/utils.php";

$g = new groonga(GROONGA_URL);

if (!$g->status()) {
	die ('groonga server not available');
}

if ($g->select(['table'=>'groka'])) {
	die('table groka already exists');
}

echo 'setting up database...' . PHP_EOL;

// setup tables
if (!$g->table_create(['name'=>'groka', 'flags'=>'TABLE_PAT_KEY', 'key_type'=>'ShortText'])) {
	die('problem with creating table');
}
$g->column_create(['table'=>'groka', 'name'=>'title', 'type'=>'ShortText']);
$g->column_create(['table'=>'groka', 'name'=>'description', 'type'=>'ShortText']);
$g->column_create(['table'=>'groka', 'name'=>'text', 'type'=>'Text']);

$g->table_create(['name'=>'groka_index', 'flags'=>'TABLE_PAT_KEY', 'key_type'=>'ShortText', 'default_tokenizer'=>'TokenBigram', 'normalizer'=>'NormalizerAuto']);
$g->column_create(['table'=>'groka_index', 'name'=>'title_index', 'flags'=>'COLUMN_INDEX|WITH_POSITION', 'type'=>'groka', 'source'=>'title']);
$g->column_create(['table'=>'groka_index', 'name'=>'text_index', 'flags'=>'COLUMN_INDEX|WITH_POSITION', 'type'=>'groka', 'source'=>'text']);

echo 'OK' . PHP_EOL;

