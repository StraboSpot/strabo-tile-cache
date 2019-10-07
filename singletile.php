<?PHP

/*
http://tiles.strabospot.org/v4/mapbox.satellite/19/119885/201168.png?access_token=pk.eyJ1Ijoic3RyYWJvLWdlb2xvZ3kiLCJhIjoiY2lpYzdhbzEwMDA1ZnZhbTEzcTV3Z3ZnOSJ9.myyChr6lmmHfP8LYwhH5Sg
http://tiles.strabospot.org/v4/mapbox.outdoors/19/119886/201171.png?access_token=pk.eyJ1Ijoic3RyYWJvLWdlb2xvZ3kiLCJhIjoiY2lpYzdhbzEwMDA1ZnZhbTEzcTV3Z3ZnOSJ9.myyChr6lmmHfP8LYwhH5Sg

http://b.tiles.mapbox.com/v4/mapbox.satellite/19/119885/201168.png?access_token=pk.eyJ1Ijoic3RyYWJvLWdlb2xvZ3kiLCJhIjoiY2lpYzdhbzEwMDA1ZnZhbTEzcTV3Z3ZnOSJ9.myyChr6lmmHfP8LYwhH5Sg
http://b.tiles.mapbox.com/v4/mapbox.outdoors/19/119886/201171.png?access_token=pk.eyJ1Ijoic3RyYWJvLWdlb2xvZ3kiLCJhIjoiY2lpYzdhbzEwMDA1ZnZhbTEzcTV3Z3ZnOSJ9.myyChr6lmmHfP8LYwhH5Sg
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

$cruft = $_GET['cruft'];

$tile->showSingleTile($cruft,$access_token);










?>