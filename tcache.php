<?PHP

function dumpVar($var){
	echo "<pre>";
	print_r($var);
	echo "</pre>";
}

$uri = $_SERVER['REQUEST_URI'];

$access_token="pk.eyJ1Ijoic3RyYWJvLWdlb2xvZ3kiLCJhIjoiY2lpYzdhbzEwMDA1ZnZhbTEzcTV3Z3ZnOSJ9.myyChr6lmmHfP8LYwhH5Sg";

$moreget = explode("?",$uri)[1];
if($moreget!=""){
	$parts = explode("=",$moreget);
	if($parts[0]=="access_token"){
		$access_token=$parts[1];
	}
}

$cruft = $_GET['cruft'];

$parts=explode("/",$cruft);

$valid_layers = array("mapbox.satellite","mapbox.outdoors");

$dir1 = $parts[0];
$dir2 = $parts[1];
$dir3 = $parts[2];
$filename = $parts[3];
$fileextension=explode(".",$filename)[1];

switch( $fileextension ) {
    case "gif": $ctype="image/gif"; break;
    case "png": $ctype="image/png"; break;
    case "jpeg":
    case "jpg": $ctype="image/jpeg"; break;
    default:
}

if(in_array($dir1,$valid_layers)){

	if(!file_exists("cache/$dir1")){
		mkdir("cache/$dir1");
	}

	if(!file_exists("cache/$dir1/$dir2")){
		mkdir("cache/$dir1/$dir2");
	}

	if(!file_exists("cache/$dir1/$dir2/$dir3")){
		mkdir("cache/$dir1/$dir2/$dir3");
	}

	if(!file_exists("cache/$dir1/$dir2/$dir3/$filename" )){
		
		$url = "http://api.tiles.mapbox.com/v4/$dir1/$dir2/$dir3/$filename?access_token=$access_token";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($ch, CURLOPT_TIMEOUT, 2);
		$data = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$fsize=(curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD));
		curl_close($ch);
		
		if($httpcode>=200 && $httpcode<300){
			//save file and send to browser
			//header("Access-Control-Allow-Origin: *");
			header("Content-Type: $ctype");
			header('Content-Length: ' . $fsize);
			echo $data;
			file_put_contents("cache/$dir1/$dir2/$dir3/$filename",$data);
		}else{
			http_response_code($httpcode);
			//header("Access-Control-Allow-Origin: *");
			header('Content-Type: application/json');
			echo $data;
		}

	}else{
		//read file and send to browser
		//header("Access-Control-Allow-Origin: *"); //this breaks the old app
		header("Content-Type: $ctype");
		header('Content-Length: ' . filesize("cache/$dir1/$dir2/$dir3/$filename"));
		readfile("cache/$dir1/$dir2/$dir3/$filename");
	}

}else{

	http_response_code(404);
	echo "Error. Layer $dir1 is not valid. Valid layers are: ";
	$vadelim="";
	foreach($valid_layers as $va){
		echo $vadelim.$va;$vadelim=", ";
	}
	echo ".";

}

?>