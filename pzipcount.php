<?PHP

/*
http://api.tiles.mapbox.com/v4/mapbox.satellite/19/107092/200396.png?access_token=<mapbox_access_token>
http://api.tiles.mapbox.com/v4/mapbox.outdoors/19/107092/200396.png?access_token=<mapbox_access_token>

http://tiles.strabospot.org/v5/mapbox.outdoors/19/107092/200396.png?access_token=<mapbox_access_token>

http://tiles.strabospot.org/zipcount?layer=mapbox.satellite&extent=-97.6897537708287,38.565649842911625,-97.66258835792587,38.584975076629206&zoom=19

*/

function dumpVar($var){
	echo "<pre>";
	print_r($var);
	echo "</pre>";
}

include("zipinput.php");

$extent=isset($_GET['extent']) ? $_GET['extent'] : "";
$zoom=isset($_GET['zoom']) ? $_GET['zoom'] : "";

zipInputRequire(array('extent' => $extent, 'zoom' => $zoom));

//echo "python zipcount.py $extent $zoom";exit();

exec("python zipCount.py " . escapeshellarg($extent) . " " . escapeshellarg($zoom), $res);
$res = $res[0];

header('Content-Type: application/json; charset=utf-8');
echo $res;

?>