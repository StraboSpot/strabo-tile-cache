<?PHP

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

include("db.php");

require 'vendor/autoload.php';

use GuzzleHttp\Client;
use GuzzleHttp\Promise;

$client = new Client();

include("Classes/StraboTileClass.php");

$tile = new StraboTileClass();

$tile->setClient($client);
$tile->setDB($db);

$tile->asyncDownloadZip($uid,$layer,$extent,$zoom,$id,$access_token,$username);

?>