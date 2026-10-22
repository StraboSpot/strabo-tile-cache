<?php
/*
Input checks for the offline-map zip scripts (asynczip.php, zip.php, cmdlinezip.php, pzipcount.php).

These values end up in shell commands (cmdlinezip.php, ln, du, zip, python) and in SQL, so each one must
match the shape real requests use, or the request is refused before anything runs. PHP2Shell fix, 2026-10-22.
Keep it PHP 7.3 compatible (prod still runs the 7.3 image).
*/

function zipInputPatterns(){
	return array(
		'layer' => '/^[A-Za-z0-9._-]{1,40}$/',
		// four numbers as JavaScript prints them (left,bottom,right,top); tiny values near 0 come as e.g. 5e-7
		'extent' => '/^-?[0-9]+(\.[0-9]+)?(e-[0-9]+)?(,-?[0-9]+(\.[0-9]+)?(e-[0-9]+)?){3}$/',
		'zoom' => '/^[0-9]{1,2}$/',
		'id' => '/^[A-Za-z0-9._-]{0,64}$/',
		'access_token' => '/^[A-Za-z0-9._-]{0,300}$/',
		'username' => '/^[A-Za-z0-9._-]{0,64}$/',
		'mapid' => '/^[A-Za-z0-9]{1,40}$/'
	);
}

// true when every named value is a string matching its pattern (missing optional values count as "")
function zipInputOk($values){
	$patterns = zipInputPatterns();
	foreach($values as $name => $value){
		if(!isset($patterns[$name])) return false;
		if($value === null) $value = "";
		if(!is_string($value)) return false;
		if(!preg_match($patterns[$name], $value)) return false;
	}
	return true;
}

// web scripts: stop with 400 and a JSON error when the input is not acceptable
function zipInputRequire($values){
	if(!zipInputOk($values)){
		http_response_code(400);
		header('Content-Type: application/json');
		echo json_encode(array('error' => 'Invalid parameters.'));
		exit();
	}
}
