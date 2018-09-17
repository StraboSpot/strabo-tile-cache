<?PHP

include("Classes/StraboTileClass.php");

$tile = new StraboTileClass();

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