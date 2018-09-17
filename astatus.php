<?PHP

$uid=pg_escape_string($_GET['uid']);

include("db.php");

$row = $db->get_row("select * from tile_downloads where uid='$uid' limit 1");

if($row->pkey!=""){

	$out['uid']=$row->uid;
	$out['tilecount']=$row->tilecount;
	$out['percent']=$row->percent;
	$out['status']=$row->status;
	$out['datestarted']=$row->datestarted;
	$out['comment']=$row->comment;
	$out['randnum']=uniqid();

}else{

	$out['error']="Tile Set not Found.";

}

header('Content-Type: application/json');
echo json_encode($out);

?>