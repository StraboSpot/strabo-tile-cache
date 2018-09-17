<?PHP

include("db.php");

$layer=pg_escape_string($_GET['layer']);
$extent=pg_escape_string($_GET['extent']);
$zoom=pg_escape_string($_GET['zoom']);
$id=pg_escape_string($_GET['id']);
$access_token=pg_escape_string($_GET['access_token']);
$username=pg_escape_string($_GET['username']);

if($_GET['mapid']!=""){
	$uid = pg_escape_string($_GET['mapid']);
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

$db->query("delete from tile_downloads where uid = '$uid';");

$db->query("insert into tile_downloads ( uid ) values ('$uid');");

exec("/usr/bin/php cmdlinezip.php $uid $layer $extent $zoom $id $access_token $username  &>/dev/null &",$out);

$results['id']=$uid;

header('Content-Type: application/json');
echo json_encode($results);

?>