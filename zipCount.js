class zipCount {
	
	constructor() {
	}

	long2tile(lon, zoom) {
		return Math.floor( (lon + 180) / 360 * Math.pow(2, zoom) );
	}

    lat2tile(lat, zoom) {
		return Math.floor((1 - Math.log(Math.tan(lat * Math.PI / 180) + 1 / Math.cos(lat * Math.PI / 180)) / Math.PI) / 2 * Math.pow(2,zoom));    
    }

	numberRangeArray(num1, num2) {
		var smallerNumber;
		var largerNumber;
		
		if (num1 < num2) {
			smallerNumber = num1;
			largerNumber = num2;
		} else {
			smallerNumber = num2;
			largerNumber = num1;
		}

		var range = new Array();

		for (let i = smallerNumber; i <= largerNumber; i++) {
			range.push(i);
		} 
		
		return range;
	}

	getTileIds(extent, zoom) {
		zoom = parseFloat(zoom);
		let parts = extent.split(",");
		let bl_long = parseFloat(parts[0].trim());
		let bl_lat = parseFloat(parts[1].trim());
		let ur_long = parseFloat(parts[2].trim());
		let ur_lat = parseFloat(parts[3].trim());

		let x = this.numberRangeArray(this.long2tile(bl_long, zoom), this.long2tile(ur_long, zoom));
		let y = this.numberRangeArray(this.lat2tile(bl_lat, zoom), this.lat2tile(ur_lat, zoom));
		
		var cartesianProduct = new Array();

		x.forEach(valuex => {
			y.forEach(valuey => {
				cartesianProduct.push(zoom + "/" + valuex + "/" + valuey);
			});
		});
		
		return cartesianProduct;
	}

	getOuterZooms(bbox, currentZoom) {

		let outerZoomMax = currentZoom - 1;
		
		let currentZoomTileArray = this.getTileIds(bbox,currentZoom);

		let tilesToCheck = ['0/0/0'];
		let x = 0;
		let y = 0;
		let z = 0;

		currentZoomTileArray.forEach(currentTile => {

			let parts = currentTile.split("/");
			let endX = parts[1];
			let endY = parts[2];

			x = 0;
			y = 0;
			z = 0;

			for(z = 0; z < outerZoomMax; z++) {

				let zDiff = currentZoom - z;
				let d = Math.pow(2, zDiff);
				let col = endX - d * x;
				let row = endY - d * y;
				x = col < (d / 2) ? 2 * x : 2 * x + 1;
				y = row < (d / 2) ? 2 * y : 2 * y + 1;
				
				let tileZ = z + 1;
				let tileId = tileZ + '/' + x + '/' + y;

				if(!tilesToCheck.includes(tileId)){
					tilesToCheck.push(tileId);
				}
			}
		});

		return tilesToCheck;
	}
	
	countZip(extent,zoom){
	
		let outCount = 0;
		
		let outertiles = this.getOuterZooms(extent, zoom);
		outCount += outertiles.length;

		let zoomleveltiles = this.getTileIds(extent, zoom);
		outCount += zoomleveltiles.length;

		return outCount;
	}

	getZipTiles(extent,zoom){
	
		let outTiles = new Array;
		
		let outertiles = this.getOuterZooms(extent, zoom);
		outertiles.forEach(tile => {
			if(!outTiles.includes(tile)){
				outTiles.push(tile);
			}
		});

		let zoomleveltiles = this.getTileIds(extent, zoom);
		zoomleveltiles.forEach(tile => {
			if(!outTiles.includes(tile)){
				outTiles.push(tile);
			}
		});

		return outTiles;
	}
}