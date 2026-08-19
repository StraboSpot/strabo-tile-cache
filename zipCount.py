import sys
import math
import json

class zipCount:

	def foo(self,bar):
		print(bar)
		
	def long2tile(self, lon, zoom):
		return int(math.floor( (lon + 180) / 360 * pow(2, zoom) ))
		
	def lat2tile(self, lat, zoom):
		return int(math.floor((1 - math.log(math.tan(lat * math.pi / 180) + 1 / math.cos(lat * math.pi / 180)) / math.pi) / 2 * pow(2,zoom)))
	
	def numberRangeArray(self, num1, num2):
		if (num1 < num2):
			smallerNumber = num1
			largerNumber = num2
		else:
			smallerNumber = num2
			largerNumber = num1
		
		return range(smallerNumber, largerNumber + 1)

	def getTileIds(self, extent, zoom):
		zoom = float(zoom);
		parts = extent.split(",")
		bl_long = float(parts[0].strip())
		bl_lat = float(parts[1].strip())
		ur_long = float(parts[2].strip())
		ur_lat = float(parts[3].strip())

		x = self.numberRangeArray(self.long2tile(bl_long, zoom), self.long2tile(ur_long, zoom))
		y = self.numberRangeArray(self.lat2tile(bl_lat, zoom), self.lat2tile(ur_lat, zoom))
		
		cartesianProduct = []
		
		for valuex in x:
			for valuey in y:
				cartesianProduct.append(str(int(zoom)) + "/" + str(valuex) + "/" + str(valuey))

		return cartesianProduct;

	def getOuterZooms(self, bbox, currentZoom):

		outerZoomMax = currentZoom - 1;
		
		currentZoomTileArray = self.getTileIds(bbox,currentZoom);

		tilesToCheck = ['0/0/0']

		for currentTile in currentZoomTileArray:

			parts = currentTile.split("/");
			endX = int(parts[1]);
			endY = int(parts[2]);

			x = 0;
			y = 0;
			z = 0;

			for z in range(0, outerZoomMax):
				zDiff = currentZoom - z
				d = math.pow(2, zDiff)

				col = endX - (d * x)
				row = endY - (d * y)

				if(col < (d / 2)):
					x = 2 * x
				else:
					x = (2 * x) + 1

				if(row < (d / 2)):
					y = 2 * y
				else:
					y = (2 * y) + 1

				tileZ = z + 1
				tileId = str(tileZ) + '/' + str(x) + '/' + str(y)

				if(tileId not in tilesToCheck):
					tilesToCheck.append(tileId)

		return tilesToCheck

	def countZip(self, extent, zoom):
	
		outCount = 0
		
		outertiles = self.getOuterZooms(extent, zoom)
		outCount += len(outertiles)

		zoomleveltiles = self.getTileIds(extent, zoom)
		outCount += len(zoomleveltiles)

		return outCount

zip = zipCount()

bbox = sys.argv[1]
zoom = int(sys.argv[2])

res = zip.countZip(bbox, zoom)
out = {"count":int(res)}
outJSON = json.dumps(out)

print outJSON
