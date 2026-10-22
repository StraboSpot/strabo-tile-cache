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


			/*
			
			osm https://tile.openstreetmap.org/15/7490/12570.png
			custom mapbox style https://api.mapbox.com/styles/v1/jasonash/cjl3xdv9h22j12tqfmyce22zq/tiles/256/16/14988/25147?access_token=<mapbox_access_token>
			mapwarper https://www.strabospot.org/mwproxy/32790/16/14986/25147.png
			https://strabospot.org/geotiff/tiles/5b75967d71bc0/15/7493/12572.png strabomymaps

			mapbox classic https://api.mapbox.com/v4/jasonash.f43efc58/14/3747/6286.png?access_token=<mapbox_access_token>

			
			mapbox satellite http://devtiles.strabospot.org/v4/mapbox.satellite/16/14989/25148.png?access_token=<mapbox_access_token>
			mapbox topo http://devtiles.strabospot.org/v4/mapbox.outdoors/16/14989/25148.png?access_token=<mapbox_access_token>

			new urls:
			
			http://devtiles.strabospot.org/v4/osm/15/7490/12570.png
			http://devtiles.strabospot.org/v4/mapboxstyles/jasonash/cjl3xdv9h22j12tqfmyce22zq/16/14988/25147?access_token=<mapbox_access_token>
			http://devtiles.strabospot.org/v4/mapwarper/32790/16/14986/25147.png
			http://devtiles.strabospot.org/v4/strabomymaps/5b75967d71bc0/16/14986/25147.png strabomymaps
			
			if first is mapboxstyles or mapwarper or , the rest need to be fixed.
			
			mapbox.satellite 4	(3) 
			mapbox.outdoors 4	(3)
			osm 4				(3)
			mapboxstyles 6		(5)	id access_token username
			mapwarper 5			(4) id
			strabomymaps 5		(4)	id

			if($dir1=="mapbox.satellite" || $dir1=="mapbox.outdoors" || $dir1=="osm"){
				//http://devtiles.strabospot.org/v4/mapbox.satellite/16/14989/25148.png?access_token=<mapbox_access_token>
				$filename = $parts[3];
				if(!is_numeric($dir2)){exit("Invalid Request");}
				if(!is_numeric($dir3)){exit("Invalid Request");}
				$lookfile="cache/$dir1/$dir2/$dir3/$filename";
			}elseif($dir1=="mapwarper" || $dir1=="strabomymaps"){
				//http://devtiles.strabospot.org/v4/mapwarper/32790/16/14986/25147.png
				//http://devtiles.strabospot.org/v4/strabomymaps/5b75967d71bc0/15/7493/12572.png strabomymaps
				$dir4 = $parts[3];
				if(!file_exists("cache/$dir1/$dir2/$dir3/$dir4")){mkdir("cache/$dir1/$dir2/$dir3/$dir4");}
				$filename = $parts[4];
				if(!is_numeric($dir3)){exit("Invalid Request");}
				if(!is_numeric($dir4)){exit("Invalid Request");}
				$lookfile="cache/$dir1/$dir2/$dir3/$dir4/$filename";
			}elseif($dir1=="mapboxstyles"){
				//http://devtiles.strabospot.org/v4/mapboxstyles/jasonash/cjl3xdv9h22j12tqfmyce22zq/16/14988/25147?access_token=<mapbox_access_token>
				$dir4 = $parts[3];
				$dir5 = $parts[4];
				if(!file_exists("cache/$dir1/$dir2/$dir3/$dir4")){mkdir("cache/$dir1/$dir2/$dir3/$dir4");}
				if(!file_exists("cache/$dir1/$dir2/$dir3/$dir4/$dir5")){mkdir("cache/$dir1/$dir2/$dir3/$dir4/$dir5");}
				$filename = $parts[5];
				if(!is_numeric($dir4)){exit("Invalid Request");}
				if(!is_numeric($dir5)){exit("Invalid Request");}
				$lookfile="cache/$dir1/$dir2/$dir3/$dir4/$dir5/$filename";
			}			
			
			*/

/**
* Strabo Tile Class.
*
* This class provides functionality for offline
* tile gathering/download.
*/
require 'vendor/autoload.php';
include_once(__DIR__."/../config.inc.php");
use \GuzzleHttp\Client;
use \GuzzleHttp\Promise;
		
class StraboTileClass
{
		


  	/**
 	* Constructor
 	*/
 	public function StraboTileClass(){
 		global $mapbox_access_token;
 		$this->db=$db;
 		$this->mapbox_access_token = $mapbox_access_token;
 		$this->valid_layers = array("mapbox.satellite","mapbox.outdoors","mapboxstyles","mapboxclassic","osm","mapwarper","strabomymaps","macrostrat","usgs.hillshade");
 		$this->osmserver="a";
 	

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

	public function setClient($client){
		$this->client = $client;
	}

	public function setDB($db){
		$this->db = $db;
	}

	public function dbTest(){
		$count = $this->db->get_var("select count(*) from users");
		echo "count: $count\n";
	}

	public function threadTest(){

		$urls = array(
			"https://a.tile.openstreetmap.org/10/234/393.png",
			"https://b.tile.openstreetmap.org/11/468/786.png",
			"https://c.tile.openstreetmap.org/11/469/784.png",
			"https://a.tile.openstreetmap.org/11/469/785.png",
			"https://b.tile.openstreetmap.org/11/469/786.png"
		);

		$urls = array(
			"https://a.tile.openstreetmap.org/10/234/393.png",
			"https://b.tile.openstreetmap.org/11/468/786.png",
			"https://c.tile.openstreetmap.org/11/469/784.png",
			"https://a.tile.openstreetmap.org/11/469/785.png",
			"https://b.tile.openstreetmap.org/11/469/786.png",
			"https://c.tile.openstreetmap.org/12/936/1571.png",
			"https://a.tile.openstreetmap.org/12/936/1572.png",
			"https://b.tile.openstreetmap.org/12/936/1573.png",
			"https://c.tile.openstreetmap.org/12/937/1568.png",
			"https://a.tile.openstreetmap.org/12/937/1569.png",
			"https://b.tile.openstreetmap.org/12/937/1570.png",
			"https://c.tile.openstreetmap.org/12/937/1571.png",
			"https://a.tile.openstreetmap.org/12/937/1572.png",
			"https://b.tile.openstreetmap.org/12/937/1573.png",
			"https://c.tile.openstreetmap.org/12/938/1568.png",
			"https://a.tile.openstreetmap.org/12/938/1569.png",
			"https://b.tile.openstreetmap.org/12/938/1570.png",
			"https://c.tile.openstreetmap.org/12/938/1571.png",
			"https://a.tile.openstreetmap.org/12/938/1572.png",
			"https://b.tile.openstreetmap.org/12/938/1573.png"
		);

		foreach($urls as $url){
			$promises[$url] = $this->client->getAsync($url,['http_errors' => false]);
		}

		$results = Promise\unwrap($promises);

		$results = Promise\settle($promises)->wait();

		foreach($results as $key=>$value){
			//echo "<div style=\"font-size:2em;\">$key</div>\n";
			$responsecode = $value['value']->getStatusCode();
			//echo "<div style=\"font-size:2em;\">$responsecode</div>\n";
			//$this->dumpVar($value);
			$body = $value['value']->getBody();
			header ('Content-Type: image/png');
			echo $body; exit();
		}

	}



	public function batchDownloadTiles($tiles){

		/* tiles look like:
		[uid] => 5b7f068e79fd3
		[url] => http://api.tiles.mapbox.com/v4/mapbox.satellite/3/1/3.png?access_token=<mapbox_access_token>
		[location] => mapbox.satellite/3/1/3.png
		*/

		//$this->dumpVar($tiles);exit();
		
		foreach($tiles as $tile){
			$promises[$tile['uid']] = $this->client->getAsync($tile['url'],['http_errors' => false]);
		}

		$results = Promise\unwrap($promises);

		$results = Promise\settle($promises)->wait();

		foreach($results as $uid=>$value){

			$responsecode = $value['value']->getStatusCode();
			if($responsecode >= 200 && $responsecode < 300){

				$location="";
				foreach($tiles as $t){
					if($t['uid']==$uid){
						$location = $t['location'];
						$url = $t['url'];
					}
					
				}
				
				if($location != ""){
					$body = (string) $value['value']->getBody();
					//WMS servers can return an XML error with HTTP 200; never cache non-image bodies
					if($this->looksLikeImage($body)){
						file_put_contents("cache/".$location,$body);
					}
				}
			}

		}
		
		//exit();

	}

	public function checkLink($url){
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
			return true;
		}else{
			return false;
		}
	}

	public function rollOSMServer(){ //rotates osm server from a to b to c to a to b etc...
		if($this->osmserver=="a"){
			$this->osmserver="b";
		}elseif($this->osmserver=="b"){
			$this->osmserver="c";
		}elseif($this->osmserver=="c"){
			$this->osmserver="a";
		}
	}

	public function showSingleTile($cruft,$access_token){

		$response = $this->loadTile($cruft,$access_token);
		if($response=="success"){
			//show tile
			$parts=explode("/",$cruft);
			$dir1 = $parts[0];
			$dir2 = $parts[1];
			$dir3 = $parts[2];
			
			if($dir1=="mapbox.satellite" || $dir1=="mapbox.outdoors" || $dir1=="osm" || $dir1=="macrostrat"){
				$filename = $parts[3];
				$lookfile="cache/$dir1/$dir2/$dir3/$filename";
			}elseif($dir1=="usgs.hillshade"){
				$filename = $parts[3];
				$lookfile="cache/$dir1/$dir2/$dir3/$filename";
			}elseif($dir1=="mapwarper" || $dir1=="strabomymaps" || $dir1=="mapboxclassic"){
				$dir4 = $parts[3];
				if(!file_exists("cache/$dir1/$dir2/$dir3/$dir4")){mkdir("cache/$dir1/$dir2/$dir3/$dir4");}
				$filename = $parts[4];
				$lookfile="cache/$dir1/$dir2/$dir3/$dir4/$filename";
			}elseif($dir1=="mapboxstyles"){
				$dir4 = $parts[3];
				$dir5 = $parts[4];
				if(!file_exists("cache/$dir1/$dir2/$dir3/$dir4")){mkdir("cache/$dir1/$dir2/$dir3/$dir4");}
				if(!file_exists("cache/$dir1/$dir2/$dir3/$dir4/$dir5")){mkdir("cache/$dir1/$dir2/$dir3/$dir4/$dir5");}
				$filename = $parts[5];
				$lookfile="cache/$dir1/$dir2/$dir3/$dir4/$dir5/$filename";
			}else{
				echo "invalid: $dir1";exit();
			}

			$fileextension=explode(".",$filename)[1];

			$ctype = $this->getCType($fileextension);
			//read file and send to browser
			//header("Access-Control-Allow-Origin: *"); this breaks the old tile loader
			header("Content-Type: $ctype");
			header('Content-Length: ' . filesize($lookfile));
			readfile("$lookfile");

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
			$ctype="image/png";
		}
		return $ctype;
	}

	public function hillshadeWmsUrl($z,$x,$y){
		//Direct WMS GetMap against the USGS 3DEP service in EPSG:3857, so the
		//bbox lines up exactly with the slippy-map tile grid. Replaces the old
		//mapserv hop through the public host Apache (the WMS cascade the
		///var/www/hillshade.map file used to do server-side).
		$w = 20037508.342789244;
		$ts = (2 * $w) / pow(2, $z);
		$minx = -$w + ($x * $ts);
		$maxy = $w - ($y * $ts);
		$maxx = $minx + $ts;
		$miny = $maxy - $ts;
		$bbox = sprintf("%.6f,%.6f,%.6f,%.6f", $minx, $miny, $maxx, $maxy);
		return "https://elevation.nationalmap.gov/arcgis/services/3DEPElevation/ImageServer/WMSServer?SERVICE=WMS&VERSION=1.3.0&REQUEST=GetMap&LAYERS=3DEPElevation:Hillshade%20Gray&STYLES=&CRS=EPSG:3857&BBOX=$bbox&WIDTH=256&HEIGHT=256&FORMAT=image/png&TRANSPARENT=TRUE";
	}

	public function looksLikeImage($data){
		if(strlen($data) < 8){return false;}
		if(substr($data,0,8) == "\x89PNG\r\n\x1a\n"){return true;}	//png
		if(substr($data,0,3) == "\xFF\xD8\xFF"){return true;}		//jpg
		if(substr($data,0,4) == "GIF8"){return true;}				//gif
		return false;
	}

	public function loadTile($dirs,$access_token){

		//dirs looks like: mapbox.satellite/123/456/789.jpg
		
		if($access_token==""){
			//default
			//$access_token=$this->mapbox_access_token;
		}
		
		$parts=explode("/",$dirs);

		$dir1 = $parts[0]; //osm, mapbox.satellite, etc...
		$dir2 = $parts[1];
		$dir3 = $parts[2];

		if(in_array($dir1,$this->valid_layers)){

			if(!file_exists("cache/$dir1")){mkdir("cache/$dir1");}
			if(!file_exists("cache/$dir1/$dir2")){mkdir("cache/$dir1/$dir2");}
			if(!file_exists("cache/$dir1/$dir2/$dir3")){mkdir("cache/$dir1/$dir2/$dir3");}

			if($dir1=="mapbox.satellite" || $dir1=="mapbox.outdoors" || $dir1=="osm" || $dir1=="macrostrat" || $dir1=="usgs.hillshade" ){
				$filename = $parts[3];
				if(!is_numeric($dir2)){exit("Invalid Request");}
				if(!is_numeric($dir3)){exit("Invalid Request");}
				$lookfile="cache/$dir1/$dir2/$dir3/$filename";
			}elseif($dir1=="mapwarper" || $dir1=="strabomymaps" || $dir1=="mapboxclassic"){
				$dir4 = $parts[3];
				if(!file_exists("cache/$dir1/$dir2/$dir3/$dir4")){mkdir("cache/$dir1/$dir2/$dir3/$dir4");}
				$filename = $parts[4];
				if(!is_numeric($dir3)){exit("Invalid Request");}
				if(!is_numeric($dir4)){exit("Invalid Request");}
				$lookfile="cache/$dir1/$dir2/$dir3/$dir4/$filename";
			}elseif($dir1=="mapboxstyles"){
				$dir4 = $parts[3];
				$dir5 = $parts[4];
				if(!file_exists("cache/$dir1/$dir2/$dir3/$dir4")){mkdir("cache/$dir1/$dir2/$dir3/$dir4");}
				if(!file_exists("cache/$dir1/$dir2/$dir3/$dir4/$dir5")){mkdir("cache/$dir1/$dir2/$dir3/$dir4/$dir5");}
				$filename = $parts[5];
				if(!is_numeric($dir4)){exit("Invalid Request");}
				if(!is_numeric($dir5)){exit("Invalid Request");}
				$lookfile="cache/$dir1/$dir2/$dir3/$dir4/$dir5/$filename";
			}

			$fileextension=explode(".",$filename)[1];

			$ctype = $this->getCType($fileextension);

			//unlink($lookfile);
			
			if(!file_exists( $lookfile )){

				$fetchtimeout = 5;

				if($dir1=="macrostrat"){
				
					//echo "different path here for macrostrat file doesn't exist";
					
					
					if($dir2==0){
					
						//resize
						$zoom = $dir2;
						$x = $dir3;
						if(!is_numeric($x)){exit("Invalid Request");}
						$y = str_replace(".png","",$filename);
						if(!is_numeric($y)){exit("Invalid Request");}

						$bigfilename = $y.".png";
						$lookfile="cache/macrostratbig/$zoom/$x/$bigfilename";

						if(!file_exists("cache/macrostratbig")){mkdir("cache/macrostratbig");}
						if(!file_exists("cache/macrostratbig/$zoom")){mkdir("cache/macrostratbig/$zoom");}
						if(!file_exists("cache/macrostratbig/$zoom/$x")){mkdir("cache/macrostratbig/$zoom/$x");}
						
						//now look for tile
						if(!file_exists($lookfile)){

							//big tile doesn't exist yet, so let's get it first
							//Macrostrat retired the burwell tile API (it 301s now, and this curl does not follow redirects, so every uncached macrostrat tile died with "Moved Permanently" - 2026-08-24). carto is the direct replacement: identical z/x/y path semantics, same 512px tiles.
							$url = "https://tiles.macrostrat.org/carto/$zoom/$x/$y.png";
							//echo "url: $url";exit();
							//$url = "https://api.mapbox.com/styles/v1/jasonash/cjlr2ulls98xv2sp7m8ba02wz/tiles/512/$zoom/$x/$y?access_token=<mapbox_access_token>";
							$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, $url);
							curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
							curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
							curl_setopt($ch, CURLOPT_TIMEOUT, 10);
							$data = curl_exec($ch);
							$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
							$fsize=(curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD));
							curl_close($ch);
		
							if($httpcode>=200 && $httpcode<300 && $this->looksLikeImage($data)){
								file_put_contents($lookfile,$data);
							}else{
								return "Error getting big tile from Macrostrat: ".substr((string)$data,0,300);
							}
						}
						
						$im = imagecreatefrompng($lookfile);
						if($im === FALSE){
							//corrupt or truncated big tile (old-era cache residue): remove it
							//so the next request refetches a fresh one instead of failing forever
							@unlink($lookfile);
							return "Error reading big tile (corrupt cache entry removed; retry).";
						}
						//old burwell-era bigs are PALETTE PNGs; palette crops can abort GD's
						//libpng write on older GD builds (leaving a 0-byte tile) -> normalize
						if(!imageistruecolor($im)){ imagepalettetotruecolor($im); }

						$im2 = imagescale($im, 256);
						if ($im2 !== FALSE) {

							//cache the tile and let showSingleTile serve it from disk. The old code
							//echoed the PNG inline here and then fell through without returning
							//"success", so showSingleTile stamped the already-echoed image with
							//HTTP 404 + application/json on every first-time fetch (2026-08-24).
							$wrote = imagepng($im2,"cache/macrostrat/$dir2/$dir3/$filename");
							imagedestroy($im2);
							clearstatcache();
							if($wrote === FALSE || (int)@filesize("cache/macrostrat/$dir2/$dir3/$filename") < 8){
								//a failed/empty write must not persist: a 0-byte tile would be
								//served forever by the cache-hit path (prod incident 2026-08-24)
								@unlink("cache/macrostrat/$dir2/$dir3/$filename");
								imagedestroy($im);
								return "Error writing tile.";
							}
						}else{
							imagedestroy($im);
							return "Error creating image.";
						}

						imagedestroy($im);
						return "success";
					
					}else{
					
						$zoom = $dir2 - 1;
						$x = $dir3;
						if(!is_numeric($x)){exit("Invalid Request");}
						$y = str_replace(".png","",$filename);
						if(!is_numeric($y)){exit("Invalid Request");}

						if($x%2==0){
							$x = $x/2;
							$leftright="left";
							$cropx=0;
						}else{
							$x = $x-1;
							$x = $x/2;
							$leftright="right";
							$cropx=256;
						}
						
						if($y%2==0){
							$y = $y/2;
							$topbottom="top";
							$cropy=0;
						}else{
							$y = $y-1;
							$y = $y/2;
							$topbottom="bottom";
							$cropy=256;
						}

						$bigfilename = $y.".png";
						$lookfile="cache/macrostratbig/$zoom/$x/$bigfilename";

						if(!file_exists("cache/macrostratbig")){mkdir("cache/macrostratbig");}
						if(!file_exists("cache/macrostratbig/$zoom")){mkdir("cache/macrostratbig/$zoom");}
						if(!file_exists("cache/macrostratbig/$zoom/$x")){mkdir("cache/macrostratbig/$zoom/$x");}
						
						//now look for tile
						if(!file_exists($lookfile)){

							//big tile doesn't exist yet, so let's get it first
							//Macrostrat retired the burwell tile API (it 301s now, and this curl does not follow redirects, so every uncached macrostrat tile died with "Moved Permanently" - 2026-08-24). carto is the direct replacement: identical z/x/y path semantics, same 512px tiles.
							$url = "https://tiles.macrostrat.org/carto/$zoom/$x/$y.png";
							//$url = "https://api.mapbox.com/styles/v1/jasonash/cjlr2ulls98xv2sp7m8ba02wz/tiles/512/$zoom/$x/$y?access_token=<mapbox_access_token>";
							$ch = curl_init();
							curl_setopt($ch, CURLOPT_URL, $url);
							curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
							curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
							curl_setopt($ch, CURLOPT_TIMEOUT, 10);
							$data = curl_exec($ch);
							$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
							$fsize=(curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD));
							curl_close($ch);
		
							if($httpcode>=200 && $httpcode<300 && $this->looksLikeImage($data)){
								file_put_contents($lookfile,$data);
							}else{
								return "Error getting big tile from Macrostrat: ".substr((string)$data,0,300);
							}
						}
						
						$im = imagecreatefrompng($lookfile);
						if($im === FALSE){
							//corrupt or truncated big tile (old-era cache residue): remove it
							//so the next request refetches a fresh one instead of failing forever
							@unlink($lookfile);
							return "Error reading big tile (corrupt cache entry removed; retry).";
						}
						//old burwell-era bigs are PALETTE PNGs; palette crops can abort GD's
						//libpng write on older GD builds (leaving a 0-byte tile) -> normalize
						if(!imageistruecolor($im)){ imagepalettetotruecolor($im); }

						$im2 = imagecrop($im, ['x' => $cropx, 'y' => $cropy, 'width' => 256, 'height' => 256]);
						if ($im2 !== FALSE) {

							//cache the tile and let showSingleTile serve it from disk. The old code
							//echoed the PNG inline here and then fell through without returning
							//"success", so showSingleTile stamped the already-echoed image with
							//HTTP 404 + application/json on every first-time fetch (2026-08-24).
							$wrote = imagepng($im2,"cache/macrostrat/$dir2/$dir3/$filename");
							imagedestroy($im2);
							clearstatcache();
							if($wrote === FALSE || (int)@filesize("cache/macrostrat/$dir2/$dir3/$filename") < 8){
								//a failed/empty write must not persist: a 0-byte tile would be
								//served forever by the cache-hit path (prod incident 2026-08-24)
								@unlink("cache/macrostrat/$dir2/$dir3/$filename");
								imagedestroy($im);
								return "Error writing tile.";
							}
						}else{
							imagedestroy($im);
							return "Error creating image.";
						}

						imagedestroy($im);
						return "success";
					
					}
					
				
				}else{
				
					if($dir1=="mapbox.satellite"){
						$access_token=$this->mapbox_access_token;
						//$url = "https://api.tiles.mapbox.com/v4/$dir1/$dir2/$dir3/$filename?access_token=$access_token";
						$usefile = str_replace(".png","",$filename);
						$url = "https://api.mapbox.com/styles/v1/mapbox/satellite-v9/tiles/256/$dir2/$dir3/$usefile?access_token=$access_token";
						//echo $url;exit();
					}elseif($dir1=="mapbox.outdoors"){
						$access_token=$this->mapbox_access_token;
						$usefile = str_replace(".png","",$filename);
						
						//https://api.mapbox.com/styles/v1/mapbox/outdoors-v11/tiles/256/13/1291/2990?access_token=<mapbox_access_token>
						
						$url = "https://api.mapbox.com/styles/v1/mapbox/outdoors-v11/tiles/256/$dir2/$dir3/$usefile?access_token=$access_token";
						
						//$url = "http://api.tiles.mapbox.com/v4/$dir1/$dir2/$dir3/$filename?access_token=$access_token";
					}elseif($dir1=="osm"){
						$url = "https://".$this->osmserver.".tile.openstreetmap.org/$dir2/$dir3/$filename";
						$this->rollOSMServer();
						//echo "$url<br>\n";
					}elseif($dir1=="usgs.hillshade"){
						//https://tiles.strabospot.org/v5/usgs.hillshade/10/210/389.png
						//Fetch straight from the USGS 3DEP WMS. The old URL went to
						//strabospot.org/cgi-bin/mapserv, which routed back through the
						//public host Apache and wedged its worker pool (2026-08-18 incident).
						$y = str_replace(".png","",$filename);
						if(!is_numeric($y)){exit("Invalid Request");}
						$url = $this->hillshadeWmsUrl($dir2,$dir3,$y);
						$fetchtimeout = 20; //USGS WMS renders can be slower than plain tile servers
					}elseif($dir1=="mapwarper"){
						$url = "https://www.strabospot.org/mwproxy/$dir2/$dir3/$dir4/$filename";
					}elseif($dir1=="mapboxstyles"){
						$url = "https://api.mapbox.com/styles/v1/$dir2/$dir3/tiles/256/$dir4/$dir5/$filename?access_token=$access_token";
					}elseif($dir1=="mapboxclassic"){
						//https://api.mapbox.com/v4/jasonash.f43efc58/14/3747/6286.png?access_token=<mapbox_access_token>
						$url = "https://api.mapbox.com/v4/$dir2/$dir3/$dir4/$dir5/$filename?access_token=$access_token";
					}elseif($dir1=="strabomymaps"){
						$url="https://strabospot.org/geotiff/tiles/$dir2/$dir3/$dir4/$filename";
					}else{
						echo "invalid: $dir1";exit();
					}
				
					//echo "<a href=\"$url\" target=\"_blank\">$url</a>";exit();
				
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					//curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
					curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (iPhone14,6; U; CPU iPhone OS 15_4 like Mac OS X) AppleWebKit/602.1.50 (KHTML, like Gecko) Version/10.0 Mobile/19E241 Safari/602.1');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
					curl_setopt($ch, CURLOPT_TIMEOUT, $fetchtimeout);
					$data = curl_exec($ch);
					$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
					$fsize=(curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD));
					curl_close($ch);

					//WMS servers can return an XML error with HTTP 200; never cache non-image bodies
					if($httpcode>=200 && $httpcode<300 && $this->looksLikeImage($data)){
						file_put_contents($lookfile,$data);
						return "success";
					}else{
						return $data;
					}
				
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

	public function doDie($text){
		$out['error']=$text;
		header('Content-Type: application/json');
		echo json_encode($out);
		exit();
	}
























































































































































































































































































































































































































	public function rrmdir($dir) { 
		if (is_dir($dir)) { 
			$objects = scandir($dir); 
			foreach ($objects as $object) { 
				if ($object != "." && $object != "..") { 
					if (is_dir($dir."/".$object))
						$this->rrmdir($dir."/".$object);
					else
						unlink($dir."/".$object); 
				} 
			}
		
			rmdir($dir); 
		} 
	}

	public function dbDie($uid,$message){
		$this->db->query("update tile_downloads set percent=100, status='$message' where uid='$uid'");exit();
	}

	public function asyncDownloadZip($uid,$layer,$extent,$zoom,$id,$access_token,$username){

		$time_start = microtime(true);

		//check layer
		if(in_array($layer,$this->valid_layers)){
			if($this->checkExtent($extent)){
			
				switch ($layer) {
					case "macrostrat":
						$maxzoom = 19;
						break;
					case "mapbox.satellite":
						$maxzoom = 19;
						break;
					case "mapbox.outdoors":
						$maxzoom = 19;
						break;
					case "openstreetmaps":
						$maxzoom = 16;
						break;
					default:
						$maxzoom = 28;
				}
			
				if(is_numeric($zoom) && $zoom > -1){				
					if($zoom <= $maxzoom){

							//everything checks out. Create zip file to download.
							//first, gather tile ids
							$alltiles=[];
					
							$outercount = 0;
						
							$outertiles = $this->getOuterZooms($extent, $zoom);
							foreach($outertiles as $tileId){
								if(!in_array($tileId, $alltiles)){
									array_push($alltiles,$tileId);
									$outercount ++;
								}
								if($outercount>6000){ //3000
									$this->doDie("Zoom Level is too large.");

								}
							}

							$zoomleveltiles = $this->getTileIds($extent, $zoom);
							foreach($zoomleveltiles as $tileId){
								if(!in_array($tileId, $alltiles)){
									array_push($alltiles,$tileId);
								}
							}
					
							//check for valid layer here
							if($layer=="mapwarper"){
								if($id=="") $this->doDie("ID not provided.");
								$url = "https://www.strabospot.org/mwproxy/$id/0/0/0.png";
								if(!$this->checkLink($url)) $this->dbDie($uid,"Invalid Map Specified");
							}elseif($layer=="mapboxstyles"){
								if($username=="") $this->doDie("Username not provided.");
								if($access_token=="") $this->dbDie($uid,"Access token not provided.");
								//https://api.mapbox.com/styles/v1/jasonash/cjl3xdv9h22j12tqfmyce22zq/tiles/256/16/14988/25147?access_token=<mapbox_access_token>
								$url = "https://api.mapbox.com/styles/v1/$username/$id/tiles/256/0/0/0?access_token=$access_token";
								if(!$this->checkLink($url)) $this->dbDie($uid,"Invalid Map Specified");
							}elseif($layer=="mapboxclassic"){
								if($id=="") $this->doDie("ID not provided.");
								if($access_token=="") $this->dbDie($uid,"Access token not provided.");
								//https://api.mapbox.com/v4/jasonash.f43efc58/14/3747/6286.png?access_token=<mapbox_access_token>
								$url = "https://api.mapbox.com/v4/$id/0/0/0.png?access_token=$access_token";
								echo $url;
								if(!$this->checkLink($url)) $this->dbDie($uid,"Invalid Map Specified: $url");
							}elseif($layer=="strabomymaps"){
								if($id=="") $this->doDie("ID not provided.");
								$url="https://strabospot.org/geotiff/tiles/$id/0/0/0.png";
								if(!$this->checkLink($url)) $this->dbDie($uid,"Invalid Map Specified");
							}

							$zipdir = $uid;
							$this->rrmdir("ziptemp/$zipdir");
							mkdir("ziptemp/$zipdir");
							mkdir("ziptemp/$zipdir/$uid");
							mkdir("ziptemp/$zipdir/$uid/tiles");
							//mkdir("ziptemp/$zipdir/tilecache/tiles/$layer");

							//first, batch load tiles...
						
							$totalcount=count($alltiles);
							$currentcount=0;
						
							$this->db->query("update tile_downloads set status='Starting Download...', percent=0, tilecount=$totalcount where uid='$uid'");
						
							$batchtiles=array();
							foreach($alltiles as $oldtile){

								$tileparts = explode("/",$oldtile);
								$z = $tileparts[0];
								$x = $tileparts[1];
								$y = $tileparts[2];

								$newtile = "tiles/$z"."_".$x."_"."$y.png";
							
								if($layer=="mapbox.satellite" || $layer=="mapbox.outdoors" || $layer=="osm" || $layer=="macrostrat" || $layer=="usgs.hillshade"){
									if(!file_exists("cache/$layer")){mkdir("cache/$layer");}
									if(!file_exists("cache/$layer/$z")){mkdir("cache/$layer/$z");}
									if(!file_exists("cache/$layer/$z/$x")){mkdir("cache/$layer/$z/$x");}
								}elseif($layer=="mapwarper" || $layer=="strabomymaps" || $layer=="mapboxclassic"){
									if(!file_exists("cache/$layer")){mkdir("cache/$layer");}
									if(!file_exists("cache/$layer/$id")){mkdir("cache/$layer/$id");}
									if(!file_exists("cache/$layer/$id/$z")){mkdir("cache/$layer/$id/$z");}
									if(!file_exists("cache/$layer/$id/$z/$x")){mkdir("cache/$layer/$id/$z/$x");}
								}elseif($layer=="mapboxstyles"){
									if(!file_exists("cache/$layer")){mkdir("cache/$layer");}
									if(!file_exists("cache/$layer/$username")){mkdir("cache/$layer/$username");}
									if(!file_exists("cache/$layer/$username/$id")){mkdir("cache/$layer/$username/$id");}
									if(!file_exists("cache/$layer/$username/$id/$z")){mkdir("cache/$layer/$username/$id/$z");}
									if(!file_exists("cache/$layer/$username/$id/$z/$x")){mkdir("cache/$layer/$username/$id/$z/$x");}
								}

								if($layer=="mapbox.satellite"){
									//http://devtiles.strabospot.org/v4/mapbox.satellite/16/14989/25148.png?access_token=<mapbox_access_token>
									$filetype="png";
									$loadfile="$layer/$z/$x/$y.$filetype";
									$access_token=$this->mapbox_access_token;
									//$url = "http://api.tiles.mapbox.com/v4/$layer/$z/$x/$y.$filetype?access_token=$access_token";
									$url = "https://api.mapbox.com/styles/v1/mapbox/satellite-v9/tiles/256/$z/$x/$y?access_token=$access_token";
								}elseif($layer=="mapbox.outdoors"){
									$filetype="png";
									$loadfile="$layer/$z/$x/$y.$filetype";
									$access_token=$this->mapbox_access_token;
									$url = "https://api.mapbox.com/styles/v1/mapbox/outdoors-v11/tiles/256/$z/$x/$y?access_token=$access_token";
								}elseif($layer=="osm"){
									$filetype="png";
									$loadfile="$layer/$z/$x/$y.$filetype";
									$url = "https://".$this->osmserver.".tile.openstreetmap.org/$z/$x/$y.$filetype";
									$this->rollOSMServer();
								}elseif($layer=="usgs.hillshade"){
									$filetype="png";
									$loadfile="$layer/$z/$x/$y.$filetype";
									//direct USGS WMS fetch; see hillshadeWmsUrl (old mapserv URL wedged the host Apache)
									$url = $this->hillshadeWmsUrl($z,$x,$y);
								}elseif($layer=="mapwarper"){
									$filetype="png";
									$loadfile="$layer/$id/$z/$x/$y.$filetype";
									$url = "https://www.strabospot.org/mwproxy/$id/$z/$x/$y.$filetype";
									if($id=="") $this->dbDie($uid,"ID not provided.");
								}elseif($layer=="strabomymaps"){
									//http://devtiles.strabospot.org/v4/mapwarper/32790/16/14986/25147.png
									//http://devtiles.strabospot.org/v4/strabomymaps/5b75967d71bc0/15/7493/12572.png strabomymaps
									$filetype="png";
									$loadfile="$layer/$id/$z/$x/$y.$filetype";
									if($id=="") $this->dbDie($uid,"ID not provided.");
									$url="https://strabospot.org/geotiff/tiles/$id/$z/$x/$y.$filetype";
								}elseif($layer=="mapboxclassic"){
									//https://api.mapbox.com/v4/jasonash.f43efc58/14/3747/6286.png?access_token=<mapbox_access_token>
									$filetype="png";
									$loadfile="$layer/$id/$z/$x/$y.$filetype";
									if($id=="") $this->dbDie($uid,"ID not provided.");
									$url="https://api.mapbox.com/v4/$id/$z/$x/$y.$filetype?access_token=$access_token";
								}elseif($layer=="macrostrat"){
									$filetype="png";
									$loadfile="$layer/$z/$x/$y.$filetype";
									//Macrostrat tiles are COMPOSED (fetch the carto big at z-1, crop a
									//256px quadrant), so no upstream URL can serve them directly. The old
									//URL here pointed at macrotiles.strabospot.org, DEAD by 2026-08-24
									//(bare Apache 404) - every macrostrat zip shipped with ALL tiles
									//missing. Compose in-process with the same loadTile logic the v5
									//single-tile path uses (cache-checked, self-healing). Deliberately
									//NOT an HTTP call back through our own tile server: that self-loop
									//pattern is what wedged the host Apache on 2026-08-18.
									$url="";
									$this->loadTile($loadfile,"");
								}elseif($layer=="mapboxstyles"){
									//http://devtiles.strabospot.org/v4/mapboxstyles/jasonash/cjl3xdv9h22j12tqfmyce22zq/16/14988/25147?access_token=<mapbox_access_token>
									$filetype="";
									$loadfile="$layer/$username/$id/$z/$x/$y";
									if($id=="") $this->dbDie($uid,"ID not provided.");
									if($username=="") $this->dbDie($uid,"Username not provided.");
									if($access_token=="") $this->dbDie($uid,"Access token not provided.");
									$url = "https://api.mapbox.com/styles/v1/$username/$id/tiles/256/$z/$x/$y?access_token=$access_token";
								}

								//loadTile($dirs,$access_token)  dirs looks like: mapbox.satellite/123/456/789.jpg
								//$this->loadTile($loadfile,$access_token);
							
								if($layer=="macrostrat"){
									//already composed in-process above; nothing to batch-fetch
								}elseif(!file_exists("cache/$loadfile")){
									unset($thistile);
									$tileuid = uniqid();
									$thistile['uid']=$tileuid;
									$thistile['url']=$url;
									$thistile['location']=$loadfile;
									$batchtiles[]=$thistile;
									//echo "file doesn't exist<br>";
								}else{
									//echo "file exists.<br>";
								}
							
								if(count($batchtiles)>=50){
									$this->batchDownloadtiles($batchtiles);
									$batchtiles=array();
									$percent = floor($currentcount/$totalcount*100);
									//echo "percent: $percent\n";
									//echo "update tile_downloads set percent=$percent where uid='$uid';\n";
									$this->db->query("update tile_downloads set status='Gathering Tiles...', percent=$percent where uid='$uid'");
								}
							
								$currentcount++;

							}

							if(count($batchtiles)>0){
								$this->batchDownloadtiles($batchtiles);
								$batchtiles=array();
								$percent = floor($currentcount/$totalcount*100);
								$this->db->query("update tile_downloads set status='Gathering Tiles...', percent=$percent where uid='$uid'");
							}

							$percent = floor($currentcount/$totalcount*100);
							$this->db->query("update tile_downloads set status='Preparing Data...', percent=0 where uid='$uid'");
						

							$jsontiles=array();
							$tilecount=0;
							foreach($alltiles as $oldtile){

								$tileparts = explode("/",$oldtile);
								$z = $tileparts[0];
								$x = $tileparts[1];
								$y = $tileparts[2];

								$newtile = "tiles/$z"."_".$x."_"."$y.png";
							
								if($layer=="mapbox.satellite" || $layer=="mapbox.outdoors" || $layer=="osm" || $layer=="macrostrat" || $layer=="usgs.hillshade"){
									//http://devtiles.strabospot.org/v4/mapbox.satellite/16/14989/25148.png?access_token=<mapbox_access_token>
									$filetype="png";
									$loadfile="$layer/$z/$x/$y.$filetype";
								
								}elseif($layer=="mapwarper" || $layer=="strabomymaps" || $layer=="mapboxclassic"){
									//http://devtiles.strabospot.org/v4/mapwarper/32790/16/14986/25147.png
									//http://devtiles.strabospot.org/v4/strabomymaps/5b75967d71bc0/15/7493/12572.png strabomymaps
									$filetype="png";
									$loadfile="$layer/$id/$z/$x/$y.$filetype";
									if($id==""){
										$this->dbDie($uid,"Error: ID not provided.");
									}
								
								}elseif($layer=="mapboxstyles"){
									//http://devtiles.strabospot.org/v4/mapboxstyles/jasonash/cjl3xdv9h22j12tqfmyce22zq/16/14988/25147?access_token=<mapbox_access_token>
									$filetype="";
									$loadfile="$layer/$username/$id/$z/$x/$y";
									if($id==""){
										$this->dbDie($uid,"Error: ID not provided.");
									}
									if($username==""){
										$this->dbDie($uid,"Error: Username not provided.");
									}
									if($access_token==""){
										$this->dbDie($uid,"Error: Access token not provided.");
									}
								}

								//exec("ln cache/$loadfile ziptemp/$zipdir/$uid/$newtile");
								
								//echo "ln cache/$loadfile ziptemp/$zipdir/$uid/$newtile\n";
								
								exec("ln " . escapeshellarg("cache/$loadfile") . " " . escapeshellarg("ziptemp/$zipdir/$uid/$newtile"));
							
								$jsontiles[]=$newtile;
								$tilecount++;
							
								$percent = floor($tilecount/$totalcount*100);
								if(($percent % 10) == 0 ){
									$this->db->query("update tile_downloads set percent=$percent where uid='$uid'");
								}
							}

							//exit();
						
							//Make details.json
							exec("/usr/bin/du -s " . escapeshellarg("ziptemp/$zipdir/$uid/tiles/"),$filesize);
							$filesize=$filesize[0];
							$filesize=explode("\t",$filesize)[0];

							$details=array();
							$details['layer']=$layer;
							$details['extent']=$extent;
							$details['zoom']=$zoom;
							$details['filetype']=$filetype;
							$details['tile_count']=$tilecount;
							$details['filesize']=$filesize;
							$details['tiles']=$jsontiles;
							$details=json_encode($details,JSON_PRETTY_PRINT);

							file_put_contents("ziptemp/$zipdir/$uid/details.json",$details);

							exec("cd " . escapeshellarg("ziptemp/$zipdir") . "; zip -r " . escapeshellarg("$uid.zip") . " " . escapeshellarg($uid),$results);
						
							$this->db->query("update tile_downloads set status='Zip File Ready.', percent=100 where uid='$uid'");
							
							$time_end = microtime(true);
							$time = $time_end - $time_start;
							
							$this->db->query("update tile_downloads set comment='Took $time to get $tilecount tiles.', percent=100 where uid='$uid'");

					}else{

						$this->dbDie($uid,"Error: Invalid zoom ($zoom) provided. Maximum zoom for layer $layer is $maxzoom.");
					}
				}else{

					$this->dbDie($uid,"Error: Invalid zoom ($zoom) provided.");
				}
			}else{

				$this->dbDie($uid,"Error: Invalid extent ($extent) provided.");
			}
		}else{
			http_response_code(404);
			$message = "Layer $layer is not valid. Valid layers are: ";
			$vadelim="";
			foreach($this->valid_layers as $va){
				$message .= $vadelim.$va;$vadelim=", ";
			}
			$message .= ".";

			$this->dbDie($uid,"Error: $message");
			
		}

	}



















































































































































































	public function countZip($extent,$zoom){

		if($this->checkExtent($extent)){

			$maxzoom = 30;

			if(is_numeric($zoom) && $zoom > -1){				
				if($zoom <= $maxzoom){

						$alltiles=[];
				
						$outercount = 0;
					
						
						
						//Get all tiles above zoom
						
						
						$outertiles = $this->getOuterZooms($extent, $zoom);
						foreach($outertiles as $tileId){
							if(!in_array($tileId, $alltiles)){
								array_push($alltiles,$tileId);
								$outercount ++;
							}
							if($outercount>6000){ //3000
								$this->doDie("Zoom Level is too large.");

							}
						}
						
						
						
						//Get zoom level tiles
						if($zoom > 0){
							$zoomleveltiles = $this->getTileIds($extent, $zoom);
							foreach($zoomleveltiles as $tileId){
								if(!in_array($tileId, $alltiles)){
									array_push($alltiles,$tileId);
								}
							}
						}
						
					
						//$this->dumpVar($alltiles);exit();
						
						//$totalcount=count($alltiles);
						
						//echo $totalcount;
						
						$out['count']=count($alltiles);
						header('Content-Type: application/json');
						echo json_encode($out);

				}else{

					$this->dbDie($uid,"Error: Invalid zoom ($zoom) provided. Maximum zoom is $maxzoom.");
				}
			}else{

				$this->dbDie($uid,"Error: Invalid zoom ($zoom) provided.");
			}
		}else{

			$this->dbDie($uid,"Error: Invalid extent ($extent) provided.");
		}

	}





































































































































































































	public function oldcountZip20251016($extent,$zoom,$filetype = "jpg"){
		if($filetype==""){
			$filetype="jpg";
		}
		if($filetype=="jpg" || $filetype=="png"){

			if($this->checkExtent($extent)){
			
				switch ($layer) {
					case "mapbox.satellite":
						$maxzoom = 30; //19
						break;
					case "mapbox.outdoors":
						$maxzoom = 30; //28
						break;
					case "openstreetmaps":
						$maxzoom = 30; //16
						break;
					default:
						$maxzoom = 30; //16
				}
			
				if(is_numeric($zoom) && $zoom > -1){				
					if($zoom <= $maxzoom){
					
						//everything checks out. Create zip file to download.
						//first, gather tile ids
						$alltiles=[];
					
						$outercount = 0;
						
						$outertiles = $this->getOuterZooms($extent, $zoom);
						foreach($outertiles as $tileId){
							if(!in_array($tileId, $alltiles)){
								array_push($alltiles,$tileId);
								$outercount ++;
							}
							if($outercount>6000){ //3000
								$this->doDie("Zoom Level is too large. $outercount");
							}
						}
						
						//echo "$outercount";exit();
						
						//echo "countalltiles: ".count($alltiles)."<br>";//exit();

						
						$zoomleveltiles = $this->getTileIds($extent, $zoom);
						foreach($zoomleveltiles as $tileId){
							if(!in_array($tileId, $alltiles)){
								array_push($alltiles,$tileId);
							}
						}
					
						$out['count']=count($alltiles);
						//header('Content-Type: application/json');
						echo json_encode($out);
						echo json_encode($alltiles);

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
			$out['message'] =  "Invalid filetype ($filetype) provided. Valid filetypes are png and jpg.";
			header('Content-Type: application/json');
			echo json_encode($out);
		}

	}
	







	public function devcountZip($layer,$extent,$zoom,$filetype){
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
						
							$outercount = 0;
							
							$outertiles = $this->getOuterZooms($extent, $zoom);
							foreach($outertiles as $tileId){
								if(!in_array($tileId, $alltiles)){
									array_push($alltiles,$tileId);
									$outercount ++;
								}
								if($outercount>6000){ //3000
									$this->doDie("Zoom Level is too Large.");
								}
							}
							
							//echo "countalltiles: ".count($alltiles)."<br>";//exit();

							
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
		$ytile = (int) floor((1 - log(tan(deg2rad($lat)) + 1 / cos(deg2rad($lat))) / pi()) / 2 * (1 << $zoom));
	

		if ($ytile < 0) {
			$ytile = 0;
		}
		
		if ($ytile >= (1 << $zoom)) {
			$ytile = (1 << $zoom) - 1;
		}
	
		
		//echo "zoom: $zoom<br>lat: $lat<br>ytile: $ytile"; exit();
		
		return $ytile; 
    }
    
    
    /*


    public function lat2tile($lat, $zoom) {
		return (floor(
			(1 - log(tan($lat * pi() / 180) + 1 / cos($lat * pi() / 180)) / pi()) / 2 * pow(2,$zoom))
		);    
    }


		n = 2 ^ zoom
		xtile = n * ((lon_deg + 180) / 360)
		ytile = n * (1 - (log(tan(lat_rad) + sec(lat_rad)) / π)) / 2


    public function foolat2tile($lat, $zoom) {

		$lat = -80;
		$zoom = 1;
		
		echo "lat: $lat<br>";
		echo "zoom: $zoom<br>";
		
		$n = pow(2, $zoom);
		$lat_rad = deg2rad($lat);
		$sec = 1 / cos($lat_rad);
		//echo $lat_rad;
		
		$ytile = $n * (1 - (log(tan($lat_rad) + $sec) / pi)) / 2;
		
		echo $ytile;
		
		
		

		exit();   
    }

    public function foolat2tile($lat_deg, $zoom) {

		//echo $lat_deg;exit(); //89 = 1
		$lat_deg = -89;
		$lon_deg = -179;
		$zoom = 2;
		
		echo "lat: " . $lat_deg . " zoom: " . $zoom . "<br><br>";

		

		
		$lat_rad = deg2rad($lat_deg);
		$n = pow(2, $zoom);
		
		$xtile = $n * (($lon_deg + 180) / 360);
		$ytile = $n * (1 - (log(tan($lat_rad) + 1 / cos($lat_rad)) / pi())) / 2;
		
		echo "X Tile: " . $xtile . "<br>";
		echo "Y Tile: " . $ytile . "<br>";



		
		exit();
		
		return $ytile;  
    }

    */
    
    public function foolat2tile($lat, $zoom) {

		
		$ytile = (int) floor((1 - log(tan(deg2rad($lat)) + 1 / cos(deg2rad($lat))) / pi()) / 2 * (1 << $zoom));
	

		if ($ytile < 0) {
			$ytile = 0;
		}
		
		if ($ytile >= (1 << $zoom)) {
			$ytile = (1 << $zoom) - 1;
		}
	
		
		//echo "zoom: $zoom<br>lat: $lat<br>ytile: $ytile"; exit();
		
		return $ytile;

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

    //Test function from ChatGPT
    public function latLonToTile(float $lat, float $lon, int $z): array {
		// Clamp latitude to Web Mercator limits to avoid infinity at the poles
		$maxLat = 85.05112878;
		if ($lat > $maxLat)  $lat = $maxLat;
		if ($lat < -$maxLat) $lat = -$maxLat;
	
		// Normalize longitude to [0, 360) then scale to [0, 1)
		$lon = fmod($lon + 180.0, 360.0);
		if ($lon < 0) $lon += 360.0;
	
		$n = 1 << $z; // 2^z
		$x = (int) floor($n * ($lon / 360.0));
	
		$latRad = deg2rad($lat);
		$y = (int) floor(
			$n * (1.0 - (log(tan($latRad) + 1.0 / cos($latRad)) / M_PI)) / 2.0
		);
	
		// Guard against rounding hitting n at the international date line
		if ($x >= $n) $x = $n - 1;
		if ($y >= $n) $y = $n - 1;
		if ($x < 0)   $x = 0;
		if ($y < 0)   $y = 0;
	
		return ['x' => $x, 'y' => $y, 'z' => $z];
	}
    
    // returns an array of tileIds from two corners of a bounding box
    public function getTileIds($bbox, $zoom) { //bbox in form of lower_left_long, lower_left_lat, upper_right_long, upper_right_lat
      
		$parts = explode(",",$bbox);
		$bl_long = trim($parts[0]);
		$bl_lat = trim($parts[1]);
		$ur_long = trim($parts[2]);
		$ur_lat = trim($parts[3]);
		
		if($bl_long == -180) $bl_long = -179.9;
		if($bl_lat == -90) $bl_lat = -89.9;
		if($ur_long == 180) $ur_long = 179.9;
		if($ur_lat == 90) $ur_lat = 89.9;

		$x = $this->numberRangeArray($this->long2tile($bl_long, $zoom), $this->long2tile($ur_long, $zoom));
		$y = $this->numberRangeArray($this->lat2tile($bl_lat, $zoom), $this->lat2tile($ur_lat, $zoom));

		$cartesianProduct = [];

		foreach($x as $valuex){
			foreach($y as $valuey){
				if($valuey >= 0){
					array_push($cartesianProduct, $zoom."/".$valuex."/".$valuey);
				}
			}
		}

		return $cartesianProduct;

    }
    
    public function scountGetTileIds($bbox, $zoom) { //bbox in form of lower_left_long, lower_left_lat, upper_right_long, upper_right_lat
      
		//echo $zoom;exit();
		
		$parts = explode(",",$bbox);
		$bl_long = trim($parts[0]);
		$bl_lat = trim($parts[1]);
		$ur_long = trim($parts[2]);
		$ur_lat = trim($parts[3]);
		
		
		if($bl_long == -180) $bl_long = -179.9;
		if($bl_lat == -90) $bl_lat = -89.9;
		if($ur_long == 180) $ur_long = 179.9;
		if($ur_lat == 90) $ur_lat = 89.9;
		
		//echo "tile for -90: " . $this->foolat2tile(89, 1);exit();
		
		$x = $this->numberRangeArray($this->long2tile($bl_long, $zoom), $this->long2tile($ur_long, $zoom));
		$y = $this->numberRangeArray($this->foolat2tile($bl_lat, $zoom), $this->foolat2tile($ur_lat, $zoom));
		
		//$this->dumpVar($x);$this->dumpVar($y);exit();

		/*
		if($zoom = 0){
			$maxy = 0;
		}elseif($zoom = 1){
			$maxy = 1;
		}else{
			$maxy = ($zoom * $zoom) - 1; 
		}
		*/
		
		
		//echo $zoom;exit();
		
		$cartesianProduct = [];

		foreach($x as $valuex){
			foreach($y as $valuey){
				//if($valuey >= 0 && $valuey < $maxy || 1==1){
				if($valuey >= 0 ){
					array_push($cartesianProduct, $zoom."/".$valuex."/".$valuey);
				}
			}
		}

		return $cartesianProduct;

    }
    
    /* 20251016
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
    */

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

    public function countTileIds($bbox, $zoom) { //bbox in form of lower_left_long, lower_left_lat, upper_right_long, upper_right_lat
      
		$parts = explode(",",$bbox);
		$bl_long = trim($parts[0]);
		$bl_lat = trim($parts[1]);
		$ur_long = trim($parts[2]);
		$ur_lat = trim($parts[3]);

		$x = $this->numberRangeArray($this->long2tile($bl_long, $zoom), $this->long2tile($ur_long, $zoom));
		$y = $this->numberRangeArray($this->lat2tile($bl_lat, $zoom), $this->lat2tile($ur_lat, $zoom));

		$cartesianProduct = [];

		$count=0;
		foreach($x as $valuex){
			foreach($y as $valuey){
				$count++;
			}
		}

		return $count;

    }

	public function countOuterZooms($bbox, $currentZoom) {

		$outerZoomMax = $currentZoom - 1;
		
		$currentZoomTileArray = $this->getTileIds($bbox,$currentZoom);

		$tilesToCheck = ['0/0/0'];
		$x = 0;
		$y = 0;
		$z = 0;

		$count = 0;
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

				$count++;

			}
		}
		
		return $count;
	}

}
?>