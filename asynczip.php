<?PHP

/*
http://api.tiles.mapbox.com/v4/mapbox.satellite/19/107092/200396.png?access_token=<mapbox_access_token>
http://api.tiles.mapbox.com/v4/mapbox.outdoors/19/107092/200396.png?access_token=<mapbox_access_token>

http://tiles.strabospot.org/v5/mapbox.outdoors/19/107092/200396.png?access_token=<mapbox_access_token>


*/

//orig -97.6710000532257 ,38.36599630861136 ,-97.65741734677427, 38.37568636700058 20

//mapbox.satellite -97.6897537708287,38.565649842911625,-97.66258835792587,38.584975076629206 19

//http://tiles.strabospot.org/zipcount?layer=mapbox.satellite&extent=-97.6897537708287,38.565649842911625,-97.66258835792587,38.584975076629206&zoom=19

include("db.php");
include("zipinput.php");

$layer=isset($_GET['layer']) ? $_GET['layer'] : "";
$extent=isset($_GET['extent']) ? $_GET['extent'] : "";
$zoom=isset($_GET['zoom']) ? $_GET['zoom'] : "";
$id=isset($_GET['id']) ? $_GET['id'] : "";
$access_token=isset($_GET['access_token']) ? $_GET['access_token'] : "";
$username=isset($_GET['username']) ? $_GET['username'] : "";

if(isset($_GET['mapid']) && $_GET['mapid']!=""){
	$uid = $_GET['mapid'];
}else{
	$uid = uniqid();
}

// every value below reaches a shell command and SQL: refuse anything that is not shaped like a real request
zipInputRequire(array('layer' => $layer, 'extent' => $extent, 'zoom' => $zoom, 'id' => $id,
	'access_token' => $access_token, 'username' => $username, 'mapid' => $uid));

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Promise;

$client = new Client();

include("Classes/StraboTileClass.php");

$tile = new StraboTileClass();

$tile->setClient($client);

$db->query("
		delete from tile_downloads where uid = '$uid';
");

$db->query("
		insert into tile_downloads 
		( uid ) values ('$uid');
");

//echo "/usr/bin/php cmdlinezip.php $uid $layer $extent $zoom $id $access_token $username  &>/dev/null &";exit();

//exec("/usr/bin/php cmdlinezip.php $uid $layer $extent $zoom $id $access_token $username  &>/dev/null &",$out);

$args = array($uid, $layer, $extent, $zoom, $id, $access_token, $username);
exec("/usr/bin/php cmdlinezip.php " . implode(" ", array_map('escapeshellarg', $args)) . " > /dev/null &",$out);

//$tile->dumpVar($out);exit();

$results['id']=$uid;

header('Content-Type: application/json');
echo json_encode($results);

?>