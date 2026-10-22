# StraboTiles on PHP 8.4

Replaces the PHP 7.3 (Debian buster) `tiles-php` image. The code on master runs on both, so `git pull` is safe
before the image swap (vendor is pinned to the PHP 7.3 platform in composer.json until 7.3 is gone).

What changed: PHP 8 constructors (StraboTileClass, ezSQL), Guzzle 6.3.3 -> 7.16 (all 6.x releases have security
advisories), a small image (Apache + PHP 8.4 with GD, curl, pgsql, CLI, zip; no GDAL, MapServer, CGI, Python),
PHP switched off in `cache/` and `ziptemp/`, code/config/tool paths refused, read-only container with all
capabilities dropped, ports on 127.0.0.1 only. Tested locally on 7.3 and 8.4 with identical results: every tile
layer through /v4 and /v5, zipcount, asyncstatus, a full asynczip job and its zip download.

## Deploy (prod host, Jason; docker needs sudo; docker-compose 1.25)

1. `cd ~/DC/StraboTiles/www && git pull --ff-only`
2. `sudo docker build -f docker/php84/Dockerfile -t tiles-php84 .`
3. `cd ~/DC/StraboTiles && cp docker-compose.yml docker-compose.yml.bak-php73 && cp www/docker/php84/docker-compose.prod.yml docker-compose.yml && sudo docker-compose config -q && sudo docker-compose up -d`
   (recreates tiles-php, and tiles-postgres for the port binding: a few seconds)
4. Check: `curl -s -o /dev/null -w '%{http_code}\n' https://tiles.strabospot.org/v5/osm/3/1/2.png` = 200, and a
   `/zipcount?extent=-98,38,-97,39&zoom=6`.

Rollback: `cp docker-compose.yml.bak-php73 docker-compose.yml && sudo docker-compose up -d` (the old tiles-php
image stays on the host; the code still runs on 7.3).
