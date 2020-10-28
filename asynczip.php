<?PHP

/*
http://api.tiles.mapbox.com/v4/mapbox.satellite/19/107092/200396.png?access_token=pk.eyJ1Ijoic3RyYWJvLWdlb2xvZ3kiLCJhIjoiY2lpYzdhbzEwMDA1ZnZhbTEzcTV3Z3ZnOSJ9.myyChr6lmmHfP8LYwhH5Sg
http://api.tiles.mapbox.com/v4/mapbox.outdoors/19/107092/200396.png?access_token=pk.eyJ1Ijoic3RyYWJvLWdlb2xvZ3kiLCJhIjoiY2lpYzdhbzEwMDA1ZnZhbTEzcTV3Z3ZnOSJ9.myyChr6lmmHfP8LYwhH5Sg

http://tiles.strabospot.org/v5/mapbox.outdoors/19/107092/200396.png?access_token=pk.eyJ1Ijoic3RyYWJvLWdlb2xvZ3kiLCJhIjoiY2lpYzdhbzEwMDA1ZnZhbTEzcTV3Z3ZnOSJ9.myyChr6lmmHfP8LYwhH5Sg


*/

//orig -97.6710000532257 ,38.36599630861136 ,-97.65741734677427, 38.37568636700058 20

//mapbox.satellite -97.6897537708287,38.565649842911625,-97.66258835792587,38.584975076629206 19

//http://tiles.strabospot.org/zipcount?layer=mapbox.satellite&extent=-97.6897537708287,38.565649842911625,-97.66258835792587,38.584975076629206&zoom=19

include("db.php");

$layer=$_GET['layer'];
$extent=$_GET['extent'];
$zoom=$_GET['zoom'];
$id=$_GET['id'];
$access_token=$_GET['access_token'];
$username=$_GET['username'];

if($_GET['mapid']!=""){
	$uid = $_GET['mapid'];
}else{
	$uid = uniqid();
}

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

exec("/usr/bin/php cmdlinezip.php $uid $layer $extent $zoom $id $access_token $username  > /dev/null &",$out);

//$tile->dumpVar($out);exit();

$results['id']=$uid;

header('Content-Type: application/json');
echo json_encode($results);

?>