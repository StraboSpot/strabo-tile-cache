<?php

/**
* Strabo Tile Class.
*
* This class provides functionality for offline
* tile gathering/download.
*
* @package    Strabo Application
* @author     Jason Ash <jasonash@ku.edu>
*/


/**
* Strabo Tile Class.
*
* This class provides functionality for offline
* tile gathering/download.
*/
class StraboTileClass
{

  	/**
 	* Constructor
 	*/
 	public function StraboTileClass(){
 		$this->db=$db;
 		$this->valid_layers = array("mapbox.satellite","mapbox.outdoors");
 	}

  	/**
 	* Dump Var
 	*
 	* Helper function to debug any
 	* variable.
 	*
 	*/
 	public function dumpVar($var){
 	
 		echo "<pre>";
 		print_r($var);
 		echo "</pre>"; 
 	
 	}

	public function showSingleTile($cruft,$access_token){
		$response = $this->loadTile($cruft,$access_token);
		if($response=="success"){
			//show tile
			$parts=explode("/",$cruft);
			$dir1 = $parts[0];
			$dir2 = $parts[1];
			$dir3 = $parts[2];
			$filename = $parts[3];
			$fileextension=explode(".",$filename)[1];

			$ctype = $this->getCType($fileextension);
			//read file and send to browser
			header("Access-Control-Allow-Origin: *");
			header("Content-Type: $ctype");
			header('Content-Length: ' . filesize("cache/$dir1/$dir2/$dir3/$filename"));
			readfile("cache/$dir1/$dir2/$dir3/$filename");

		}else{
			http_response_code(404);
			header('Content-Type: application/json');
			echo $response;
		}
	}

	public function getCType($fileextension){
		switch( $fileextension ) {
			case "gif": $ctype="image/gif"; break;
			case "png": $ctype="image/png"; break;
			case "jpeg":
			case "jpg": $ctype="image/jpeg"; break;
			default:
		}
		return $ctype;
	}

	public function loadTile($dirs,$access_token){

		//dirs looks like: mapbox.satellite/123/456/789.jpg
		
		if($access_token==""){
			//default
			$access_token="pk.eyJ1Ijoic3RyYWJvLWdlb2xvZ3kiLCJhIjoiY2lpYzdhbzEwMDA1ZnZhbTEzcTV3Z3ZnOSJ9.myyChr6lmmHfP8LYwhH5Sg";
		}
		
		$parts=explode("/",$dirs);

		$dir1 = $parts[0];
		$dir2 = $parts[1];
		$dir3 = $parts[2];
		$filename = $parts[3];
		$fileextension=explode(".",$filename)[1];

		$ctype = $this->getCType($fileextension);

		if(in_array($dir1,$this->valid_layers)){

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
					file_put_contents("cache/$dir1/$dir2/$dir3/$filename",$data);
					return "success";
				}else{
					return $data;
				}

			}else{
				return "success";
			}

		}else{

			$message = "Layer $dir1 is not valid. Valid layers are: ";
			$vadelim="";
			foreach($this->valid_layers as $va){
				$message .= $vadelim.$va;$vadelim=", ";
			}
			$message .= ".";
			$out['message']=$message;
			$out=json_encode($out);
			return $out;

		}
	}

	
	public function checkExtent($extent){
		$parts = explode(",",$extent);
		$bl_long = trim($parts[0]);
		$bl_lat = trim($parts[1]);
		$ur_long = trim($parts[2]);
		$ur_lat = trim($parts[3]);
		
		$returnval = true;
		
		if($bl_long=="" || $bl_lat=="" || $ur_long=="" || $ur_lat=="" ){
			$returnval = false;
		}
		
		if($bl_long > 180 || $bl_long < -180 || $ur_long > 180 || $ur_long < -180 ){
			$returnval = false;
		}
		
		if($bl_lat > 90 || $bl_lat <-90 || $ur_lat > 90 || $ur_lat <-90 ){
			$returnval = false;
		}
		
		if(!is_numeric($bl_long) || !is_numeric($bl_lat) || !is_numeric($ur_long) || !is_numeric($ur_lat) ){
			$returnval = false;
		}
		
		return $returnval;
	}


	public function downloadZip($layer,$extent,$zoom,$filetype){
		if($filetype==""){
			$filetype="jpg";
		}
		if($filetype=="jpg" || $filetype=="png"){
			//check layer
			if(in_array($layer,$this->valid_layers)){
				if($this->checkExtent($extent)){
				
					switch ($layer) {
						case "mapbox.satellite":
							$maxzoom = 19;
							break;
						case "mapbox.outdoors":
							$maxzoom = 28;
							break;
						case "openstreetmaps":
							$maxzoom = 16;
							break;
						default:
							$maxzoom = 16;
					}
				
					if(is_numeric($zoom) && $zoom > -1){				
						if($zoom <= $maxzoom){
						
							//everything checks out. Create zip file to download.
							//first, gather tile ids
							$alltiles=[];
						
							$outertiles = $this->getOuterZooms($extent, $zoom);
							foreach($outertiles as $tileId){
								if(!in_array($tileId, $alltiles)){
									array_push($alltiles,$tileId);
								}
							}

							$zoomleveltiles = $this->getTileIds($extent, $zoom);
							foreach($zoomleveltiles as $tileId){
								if(!in_array($tileId, $alltiles)){
									array_push($alltiles,$tileId);
								}
							}
						
							$zipdir = rand(111111111,999999999);
							mkdir("ziptemp/$zipdir");
							mkdir("ziptemp/$zipdir/tilecache");
							mkdir("ziptemp/$zipdir/tilecache/tiles");
							mkdir("ziptemp/$zipdir/tilecache/tiles/$layer");


							$jsontiles=array();
							$tilecount=0;
							foreach($alltiles as $oldtile){
								$oldtile.=".".$filetype;
								$newtile=str_replace("/","_",$oldtile);
								$newtile="tiles/".$layer."/".$newtile;
								
								$this->loadTile($layer."/".$oldtile);
								
								exec("ln cache/$layer/$oldtile ziptemp/$zipdir/tilecache/$newtile");
								
								$jsontiles[]=$newtile;
								$tilecount++;
							}

							//Make details.json
							$details=array();
							$details['layer']=$layer;
							$details['extent']=$extent;
							$details['zoom']=$zoom;
							$details['filetype']=$filetype;
							$details['tile_count']=$tilecount;
							$details['tiles']=$jsontiles;
							$details=json_encode($details,JSON_PRETTY_PRINT);

							file_put_contents("ziptemp/$zipdir/tilecache/details.json",$details);

							exec("cd ziptemp/$zipdir; zip -r tilecache.zip tilecache",$results);

							header("Content-type: application/zip"); 
							header('Content-Length: ' . filesize("ziptemp/$zipdir/tilecache.zip"));
							header("Content-Disposition: attachment; filename=tilecache.zip"); 
							header("Pragma: no-cache"); 
							header("Expires: 0"); 
							readfile("ziptemp/$zipdir/tilecache.zip");

							exec("rm -r ziptemp/$zipdir");






						}else{
							http_response_code(404);
							$out['message'] = "Invalid zoom ($zoom) provided. Maximum zoom for layer $layer is $maxzoom.";
							header('Content-Type: application/json');
							echo json_encode($out);
						}
					}else{
						http_response_code(404);
						$out['message'] =  "Invalid zoom ($zoom) provided.";
						header('Content-Type: application/json');
						echo json_encode($out);
					}
				}else{
					http_response_code(404);
					$out['message'] =  "Invalid extent ($extent) provided.";
					header('Content-Type: application/json');
					echo json_encode($out);
				}
			}else{
				http_response_code(404);
				$message = "Layer $layer is not valid. Valid layers are: ";
				$vadelim="";
				foreach($this->valid_layers as $va){
					$message .= $vadelim.$va;$vadelim=", ";
				}
				$message .= ".";
				$out['message'] =  $message;
				header('Content-Type: application/json');
				echo json_encode($out);
			}
		}else{
			http_response_code(404);
			$out['message'] =  "Invalid filetype ($filetype) provided. Valid filetypes are png and jpg.";
			header('Content-Type: application/json');
			echo json_encode($out);
		}

	}


	public function countZip($layer,$extent,$zoom,$filetype){
		if($filetype==""){
			$filetype="jpg";
		}
		if($filetype=="jpg" || $filetype=="png"){
			//check layer
			if(in_array($layer,$this->valid_layers)){
				if($this->checkExtent($extent)){
				
					switch ($layer) {
						case "mapbox.satellite":
							$maxzoom = 19;
							break;
						case "mapbox.outdoors":
							$maxzoom = 28;
							break;
						case "openstreetmaps":
							$maxzoom = 16;
							break;
						default:
							$maxzoom = 16;
					}
				
					if(is_numeric($zoom) && $zoom > -1){				
						if($zoom <= $maxzoom){
						
							//everything checks out. Create zip file to download.
							//first, gather tile ids
							$alltiles=[];
						
							$outertiles = $this->getOuterZooms($extent, $zoom);
							foreach($outertiles as $tileId){
								if(!in_array($tileId, $alltiles)){
									array_push($alltiles,$tileId);
								}
							}

							$zoomleveltiles = $this->getTileIds($extent, $zoom);
							foreach($zoomleveltiles as $tileId){
								if(!in_array($tileId, $alltiles)){
									array_push($alltiles,$tileId);
								}
							}
						
							$out['count']=count($alltiles);
							header('Content-Type: application/json');
							echo json_encode($out);

						}else{
							http_response_code(404);
							$out['message'] = "Invalid zoom ($zoom) provided. Maximum zoom for layer $layer is $maxzoom.";
							header('Content-Type: application/json');
							echo json_encode($out);
						}
					}else{
						http_response_code(404);
						$out['message'] =  "Invalid zoom ($zoom) provided.";
						header('Content-Type: application/json');
						echo json_encode($out);
					}
				}else{
					http_response_code(404);
					$out['message'] =  "Invalid extent ($extent) provided.";
					header('Content-Type: application/json');
					echo json_encode($out);
				}
			}else{
				http_response_code(404);
				$message = "Layer $layer is not valid. Valid layers are: ";
				$vadelim="";
				foreach($this->valid_layers as $va){
					$message .= $vadelim.$va;$vadelim=", ";
				}
				$message .= ".";
				$out['message'] =  $message;
				header('Content-Type: application/json');
				echo json_encode($out);
			}
		}else{
			http_response_code(404);
			$out['message'] =  "Invalid filetype ($filetype) provided. Valid filetypes are png and jpg.";
			header('Content-Type: application/json');
			echo json_encode($out);
		}

	}
	
	
	//*********************************************************************************
	//		Functions converted from Strabo
	//*********************************************************************************

	// borrowed from http://wiki.openstreetmap.org/wiki/Slippy_map_tilenames
    public function lat2tile($lat, $zoom) {
		return (floor(
			(1 - log(tan($lat * pi() / 180) + 1 / cos($lat * pi() / 180)) / pi()) / 2 * pow(2,$zoom))
		);    
    }

	// borrowed from http://wiki.openstreetmap.org/wiki/Slippy_map_tilenames
    public function long2tile($lon, $zoom) {
      return (floor(($lon + 180) / 360 * pow(2, $zoom)));
    }

	// build an array of numbers from its number line endpoints
	public function numberRangeArray($num1, $num2) {

		if ($num1 < $num2) {
			$smallerNumber = $num1;
			$largerNumber = $num2;
		}
		else {
			$smallerNumber = $num2;
			$largerNumber = $num1;
		}

		$range = array();

		while ($smallerNumber <= $largerNumber) {
			array_push($range, $smallerNumber);
			$smallerNumber++;
		}
		
		return $range;
	}

    public function getAvgTileBytes() {
      return 15000; // TODO: is this right?
    }

    // borrowed from http://wiki.openstreetmap.org/wiki/Slippy_map_tilenames
    public function tile2long($x, $z) {
      return ($x / pow(2, $z) * 360 - 180);
    }

    // borrowed from http://wiki.openstreetmap.org/wiki/Slippy_map_tilenames
    public function tile2lat($y, $z) {
      $n = pi() - 2 * pi() * $y / pow(2, $z);
      return (180 / pi() * atan(0.5 * (exp($n) - exp(-$n))));
    }

    // returns an array of tileIds from two corners of a bounding box
    public function getTileIds($bbox, $zoom) { //bbox in form of lower_left_long, lower_left_lat, upper_right_long, upper_right_lat
      
		$parts = explode(",",$bbox);
		$bl_long = trim($parts[0]);
		$bl_lat = trim($parts[1]);
		$ur_long = trim($parts[2]);
		$ur_lat = trim($parts[3]);

		$x = $this->numberRangeArray($this->long2tile($bl_long, $zoom), $this->long2tile($ur_long, $zoom));
		$y = $this->numberRangeArray($this->lat2tile($bl_lat, $zoom), $this->lat2tile($ur_lat, $zoom));

		$cartesianProduct = [];

		foreach($x as $valuex){
			foreach($y as $valuey){
				array_push($cartesianProduct, $zoom."/".$valuex."/".$valuey);
			}
		}

		return $cartesianProduct;

    }

	public function getOuterZooms($bbox, $currentZoom) {

		$outerZoomMax = $currentZoom - 1;
		
		$currentZoomTileArray = $this->getTileIds($bbox,$currentZoom);

		$tilesToCheck = ['0/0/0'];
		$x = 0;
		$y = 0;
		$z = 0;

		foreach($currentZoomTileArray as $currentTile){
			
			$x = 0;
			$y = 0;
			$z = 0;

			while ($z < $outerZoomMax) {

				$parts = explode("/",$currentTile);
				$endX = $parts[1];
				$endY = $parts[2];
				

				$zDiff = $currentZoom - $z;			// Difference btwn current zoom of map and zoom of tiles being checked
				$d = pow(2, $zDiff);			 // Dimension of the tile grid
				$col = $endX - $d * $x;					 // Column number
				$row = $endY - $d * $y;					 // Row number
				$x = $col < ($d / 2) ? 2 * $x : 2 * $x + 1;
				$y = $row < ($d / 2) ? 2 * $y : 2 * $y + 1;
				$z++;
				$tileId = $z . '/' . $x . '/' . $y;

				if(!in_array($tileId, $tilesToCheck)){
					array_push($tilesToCheck,$tileId);
				}

			}
		}
		
		return $tilesToCheck;
	}

}
?>