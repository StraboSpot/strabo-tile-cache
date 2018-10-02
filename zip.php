<?PHP
exit();
include("Classes/StraboTileClass.php");

$tile = new StraboTileClass();

$layer=pg_escape_string($_GET['layer']);
$extent=pg_escape_string($_GET['extent']);
$zoom=pg_escape_string($_GET['zoom']);

$tile->downloadZip($layer,$extent,$zoom);

?>