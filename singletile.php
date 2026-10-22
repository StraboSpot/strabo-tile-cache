<?PHP

/*
http://tiles.strabospot.org/v4/mapbox.satellite/19/119885/201168.png?access_token=<mapbox_access_token>
http://tiles.strabospot.org/v4/mapbox.outdoors/19/119886/201171.png?access_token=<mapbox_access_token>

http://b.tiles.mapbox.com/v4/mapbox.satellite/19/119885/201168.png?access_token=<mapbox_access_token>
http://b.tiles.mapbox.com/v4/mapbox.outdoors/19/119886/201171.png?access_token=<mapbox_access_token>
*/

include("Classes/StraboTileClass.php");

$tile = new StraboTileClass();

//dumpVar($_GET);
$uri = $_SERVER['REQUEST_URI'];

$moreget = explode("?",$uri)[1];
if($moreget!=""){
	$parts = explode("=",$moreget);
	if($parts[0]=="access_token"){
		$access_token=$parts[1];
	}
}

$cruft = isset($_GET['cruft']) ? $_GET['cruft'] : "";

// the tile path becomes a cache path inside the web root: no dot segments, dotfiles or script extensions
if(!is_string($cruft) || strpos($cruft, "..") !== false || preg_match('#(^|/)\.#', $cruft) || preg_match('#\.(php[0-9]?|phtml|phar|pht|htaccess|htpasswd|ini|cgi|pl|py|sh)(/|$)#i', $cruft)){
	http_response_code(400);
	exit();
}

$tile->showSingleTile($cruft,$access_token);










?>