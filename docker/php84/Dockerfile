# StraboTiles on PHP 8.4 (Debian 13 trixie; Debian security keeps php8.4 patched). Replaces the PHP 7.3 buster
# image. Only what the tile cache uses: GD (macrostrat crops), curl, pgsql, the PHP CLI (cmdlinezip.php runs in
# the background) and zip/du/ln. No GDAL, MapServer, CGI, Python or compilers.
# Build from the repo root: docker build -f docker/php84/Dockerfile -t tiles-php84 .
FROM debian:trixie

RUN apt-get update -y \
	&& apt-get upgrade -y \
	&& DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends \
	apache2 \
	libapache2-mod-php8.4 \
	php8.4-cli \
	php8.4-gd \
	php8.4-curl \
	php8.4-pgsql \
	zip \
	ca-certificates \
	&& rm -rf /var/lib/apt/lists/* \
	&& a2enmod rewrite headers remoteip \
	&& a2dismod -f autoindex status cgi cgid negotiation \
	&& a2disconf other-vhosts-access-log serve-cgi-bin

COPY docker/php84/php.ini /etc/php/8.4/apache2/conf.d/99-tiles.ini
COPY docker/php84/php.ini /etc/php/8.4/cli/conf.d/99-tiles.ini
COPY docker/php84/apache.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php84/security.conf /etc/apache2/conf-available/zz-tiles-security.conf
RUN a2enconf zz-tiles-security

CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]
