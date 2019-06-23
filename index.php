<!doctype html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>groka</title>
</head>
<body>
<h1>groka <small>your own personal google</small></h1>
<form>
	<input type="search" name="q" <?php if (isset($_GET['q'])) echo 'value="' . $_GET['q'] . '"';  ?> >
	<button>grok!</button> 
	<?php if (isset($_GET['site'])) { ?>
	/ only from site: <input name="site" placeholder="ss64.com"  <?php if (isset($_GET['site'])) echo 'value="' . $_GET['site'] . '"';  ?>>
	<?php } ?>
</form>

<?php
if (!empty($_GET['q'])) {
	require "config.php";
	require "lib/groonga.php";
	require "lib/utils.php";
	$Q = cleantext($_GET['q']);
	$g = new groonga(GROONGA_URL);
	
	$grokaQuery = [
		'table'=>'groka', 
		'output_columns'=>'title,description,snippet_html(text),_key,_score', 
		'query'=>$Q, 
		'match_columns'=>'title||text', 
		'sort_keys'=>'-_score', 
		'limit'=>20 // todo - pagination of results
	];
	
	$fromSite = false;
	
	if (isset($_GET['site'])) {
		$fromSite = $_GET['site'];
	}
	
	if ($fromSite) {
		$fromSite = str_replace([], [], $fromSite);
		$grokaQuery['filter'] = '_key @^ "https://'. $fromSite  . '" || _key @^ "http://'. $fromSite  . '"';
	}
	
	$results = $g->select($grokaQuery);
	
	if (isset($results[0])) {
		$count = array_shift($results[0]);
		$schema = array_shift($results[0]);
		
		echo '<p>' . $count[0] . ' results</p>'; 
		
		foreach ($results[0] as $found) {
			echo '<h2>'.$found[0].'</h2>';
			// echo '<p>'.$found[1].'</p>';
			foreach ($found[2] as $matchedText) {
				echo '<p>'.$matchedText.'</p>';
			}
			echo '<a href="'.$found[3].'">'.$found[3].'</a>';
			echo '<!-- <p>' . $found[4] . '</p> -->';
		}
	}
}

?>
<style>
.keyword { background-color: yellow; }
</style>
</body>
</html>