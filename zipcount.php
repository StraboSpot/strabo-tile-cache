<?PHP

include("Classes/StraboTileClass.php");

$tile = new StraboTileClass();

$layer=$_GET['layer'];
$extent=$_GET['extent'];
$zoom=$_GET['zoom'];

$tile->countZip($extent,$zoom);

?>