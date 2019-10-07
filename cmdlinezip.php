<?PHP

/*
http://api.tiles.mapbox.com/v4/mapbox.satellite/19/107092/200396.png?access_token=pk.eyJ1Ijoic3RyYWJvLWdlb2xvZ3kiLCJhIjoiY2lpYzdhbzEwMDA1ZnZhbTEzcTV3Z3ZnOSJ9.myyChr6lmmHfP8LYwhH5Sg
http://api.tiles.mapbox.com/v4/mapbox.outdoors/19/107092/200396.png?access_token=pk.eyJ1Ijoic3RyYWJvLWdlb2xvZ3kiLCJhIjoiY2lpYzdhbzEwMDA1ZnZhbTEzcTV3Z3ZnOSJ9.myyChr6lmmHfP8LYwhH5Sg

http://tiles.strabospot.org/v5/mapbox.outdoors/19/107092/200396.png?access_token=pk.eyJ1Ijoic3RyYWJvLWdlb2xvZ3kiLCJhIjoiY2lpYzdhbzEwMDA1ZnZhbTEzcTV3Z3ZnOSJ9.myyChr6lmmHfP8LYwhH5Sg


*/

//orig -97.6710000532257 ,38.36599630861136 ,-97.65741734677427, 38.37568636700058 20

//mapbox.satellite -97.6897537708287,38.565649842911625,-97.66258835792587,38.584975076629206 19

//http://tiles.strabospot.org/zipcount?layer=mapbox.satellite&extent=-97.6897537708287,38.565649842911625,-97.66258835792587,38.584975076629206&zoom=19

//layer=mapboxstyles&username=jasonash&access_token=pk.eyJ1IjoiamFzb25hc2giLCJhIjoiY2l2dTUycmNyMDBrZjJ5bzBhaHgxaGQ1diJ9.O2UUsedIcg1U7w473A5UHA&id=cjl3xdv9h22j12tqfmyce22zq&extent=-97.89028775422965,38.45640879187488,-97.47830044954215,38.77830645970107&zoom=14

//http://devtiles.strabospot.org/zipcount?layer=mapboxstyles&username=jasonash&access_token=pk.eyJ1IjoiamFzb25hc2giLCJhIjoiY2l2dTUycmNyMDBrZjJ5bzBhaHgxaGQ1diJ9.O2UUsedIcg1U7w473A5UHA&id=cjl3xdv9h22j12tqfmyce22zq&extent=-97.89028775422965,38.45640879187488,-97.47830044954215,38.77830645970107&zoom=14

///usr/bin/php cmdlinezip.php abc123 mapboxstyles -97.89028775422965,38.45640879187488,-97.47830044954215,38.77830645970107 14 cjl3xdv9h22j12tqfmyce22zq pk.eyJ1IjoiamFzb25hc2giLCJhIjoiY2l2dTUycmNyMDBrZjJ5bzBhaHgxaGQ1diJ9.O2UUsedIcg1U7w473A5UHA jasonash

$uid=$argv[1];
$layer=$argv[2];
$extent=$argv[3];
$zoom=$argv[4];
$id=$argv[5];
$access_token=$argv[6];
$username=$argv[7];

if($uid==""){
	exit();
}

/*
echo "uid: $uid\n";
echo "layer: $layer\n";
echo "extent: $extent\n";
echo "zoom: $zoom\n";
echo "id: $id\n";
echo "access_token: $access_token\n";
echo "username: $username\n";
*/
//exit();

include("db.php");


require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Promise;

$client = new Client();


include("Classes/StraboTileClass.php");

$tile = new StraboTileClass();

$tile->setClient($client);
$tile->setDB($db);

//$tile->dbTest();exit();



$tile->asyncDownloadZip($uid,$layer,$extent,$zoom,$id,$access_token,$username);






?>