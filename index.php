<!doctype html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>groka</title>
</head>
<body>
<h1>groka</h1>
<form>
	<input type="search" name="q" <?php if (isset($_GET['q'])) echo 'value="' . $_GET['q'] . '"';  ?> >
	<button>grok!</button>
</form>

<?php
if (!empty($_GET['q'])) {
	require "config.php";
	require "lib/groonga.php";
	require "lib/utils.php";
	$Q = cleantext($_GET['q']);
	$g = new groonga(GROONGA_URL);
	$results = $g->select(['table'=>'groka', 'output_columns'=>'title,description,_key,_score', 'query'=>$Q, 'match_columns'=>'title||text', 'sort_keys'=>'-_score']);
	
	if (isset($results[0])) {
		$count = array_shift($results[0]);
		$schema = array_shift($results[0]);
		
		echo '<p>' . $count[0] . ' results</p>'; 
		
		foreach ($results[0] as $found) {
			echo '<h2>'.$found[0].'</h2>';
			echo '<p>'.$found[1].'</p>';
			echo '<a href="'.$found[2].'">'.$found[2].'</a>';
			echo '<p>' . $found[3] . '</p>';
		}
	}
}

?>
</body>
</html>