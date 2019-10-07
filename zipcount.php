<?PHP

/*
http://api.tiles.mapbox.com/v4/mapbox.satellite/19/107092/200396.png?access_token=pk.eyJ1Ijoic3RyYWJvLWdlb2xvZ3kiLCJhIjoiY2lpYzdhbzEwMDA1ZnZhbTEzcTV3Z3ZnOSJ9.myyChr6lmmHfP8LYwhH5Sg
http://api.tiles.mapbox.com/v4/mapbox.outdoors/19/107092/200396.png?access_token=pk.eyJ1Ijoic3RyYWJvLWdlb2xvZ3kiLCJhIjoiY2lpYzdhbzEwMDA1ZnZhbTEzcTV3Z3ZnOSJ9.myyChr6lmmHfP8LYwhH5Sg

http://tiles.strabospot.org/v5/mapbox.outdoors/19/107092/200396.png?access_token=pk.eyJ1Ijoic3RyYWJvLWdlb2xvZ3kiLCJhIjoiY2lpYzdhbzEwMDA1ZnZhbTEzcTV3Z3ZnOSJ9.myyChr6lmmHfP8LYwhH5Sg

http://tiles.strabospot.org/zipcount?layer=mapbox.satellite&extent=-97.6897537708287,38.565649842911625,-97.66258835792587,38.584975076629206&zoom=19

*/

include("Classes/StraboTileClass.php");

$tile = new StraboTileClass();

$layer=$_GET['layer'];
$extent=$_GET['extent'];
$zoom=$_GET['zoom'];

$tile->countZip($extent,$zoom);

?>