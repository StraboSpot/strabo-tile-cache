<?PHP

/*
http://api.tiles.mapbox.com/v4/mapbox.satellite/19/107092/200396.png?access_token=<mapbox_access_token>
http://api.tiles.mapbox.com/v4/mapbox.outdoors/19/107092/200396.png?access_token=<mapbox_access_token>

http://tiles.strabospot.org/v5/mapbox.outdoors/19/107092/200396.png?access_token=<mapbox_access_token>


*/

include("Classes/StraboTileClass.php");

$tile = new StraboTileClass();

//orig -97.6710000532257 ,38.36599630861136 ,-97.65741734677427, 38.37568636700058 20

//mapbox.satellite -97.6897537708287,38.565649842911625,-97.66258835792587,38.584975076629206 19

//http://tiles.strabospot.org/zipcount?layer=mapbox.satellite&extent=-97.6897537708287,38.565649842911625,-97.66258835792587,38.584975076629206&zoom=19

include("zipinput.php");

$layer=isset($_GET['layer']) ? $_GET['layer'] : "";
$extent=isset($_GET['extent']) ? $_GET['extent'] : "";
$zoom=isset($_GET['zoom']) ? $_GET['zoom'] : "";

zipInputRequire(array('layer' => $layer, 'extent' => $extent, 'zoom' => $zoom));




$tile->downloadZip($layer,$extent,$zoom);






?>