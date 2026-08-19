<?PHP




$extent=$_GET['extent'];
$zoom=$_GET['zoom'];

include("db.php");


require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Promise;

$client = new Client();


include("Classes/StraboTileClass.php");

$tile = new StraboTileClass();

$tile->setClient($client);
$tile->setDB($db);

$tile->countZip($extent,$zoom);





/*
//Old 20251016
$extent=$_GET['extent'];
$zoom = str_replace(" ", "", $res);
$zoom=$_GET['zoom'];

//echo "python zipcount.py $extent $zoom";exit();

exec("python zipCount.py $extent $zoom", $res);
$res = $res[0];

header('Content-Type: application/json; charset=utf-8');
echo $res;
*/











?>