<?php
require "config.php";
require "lib/utils.php";
require "lib/grokabot.php";
require "lib/groonga.php";
?>
<!doctype html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>groka</title>
</head>
<body>
<div class="page">
<h1><a href="/">groka</a> <small>your own personal google</small></h1>
<?php
function add_url($url) {
    $bot = new grokabot;
    $bot->userAgent = GROKABOT_USERAGENT;
    $g = new groonga(GROONGA_URL);

    $response = $bot->add($url, $g);

    if ($response===true) {
        echo '<p class="success">OK! Your page was added to index!</p>';
    } else {
        echo '<p class="error">Error while adding to index: ' . $response . '</p>'. PHP_EOL;
    }
}


if (isset($_POST['add'])) {
	$url = $_POST['add'];
	add_url($url);
}
?>


<form method="post">
	<input type="search" name="add" placeholder="example.com" >
	<button>add my site to index</button> 
</form>
<hr>
<form method="post">
	<input type="search" name="list" placeholder="example.com" >
	<button>list all known from site</button> 
</form>

<?php
if (!empty($_POST['list'])) {

	$fromSite = $_POST['list'];
	$fromSite = str_replace([], [], $fromSite);
	$g = new groonga(GROONGA_URL);
	
	$grokaQuery = [
		'table'=>'groka', 
		'output_columns'=>'title,description,text,_key,_score', 
		'match_columns'=>'title||text', 
		'sort_keys'=>'-_score', 
		'filter' => '_key @^ "https://'. $fromSite  . '" || _key @^ "http://'. $fromSite  . '"',
		'limit'=>20 // todo - pagination of results
	];
	
	$results = $g->select($grokaQuery);
	
	if (isset($results[0])) {
		$count = array_shift($results[0]);
		$schema = array_shift($results[0]);
		
		echo '<p>' . $count[0] . ' results</p>'; 
		
		foreach ($results[0] as $found) {
			echo '<h2><a href="'.$found[3].'">'.$found[0].'</a></h2>';
			echo '<p>'.$found[1].'</p>';
			echo '<a href="'.$found[3].'">'.$found[3].'</a>';
			echo '<!-- <p>' . $found[4] . '</p> -->';
			echo '<br><br>';
		}
	}
}

?>
<hr>
<small><a href="https://github.com/severak/groka">about project</a> * this instance is run by <?php echo GROKA_PROVIDER; ?> * <a href="index.php">search with groka</a> </small>
<style>
body { font-family: sans-serif; }
.page { max-width: 50em; margin: 1em auto; }
h1, h2, hr, a { color: green; }
h1 small { font-size: 50%; }
h1 a, h2  a { text-decoration: none; }
input, button { border: 1px solid green; }
.keyword { background-color: yellow; }
.error { color: red; }
</style>
</body>
</html>