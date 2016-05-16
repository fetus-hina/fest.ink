STYLE_TARGETS=actions assets commands components controllers models
JS_SRCS=$(shell ls -1 resources/fest.ink/fest.js/*.js)

FAVICON_TARGETS= \
	resources/.compiled/favicon/favicon.ico \
	resources/.compiled/favicon/76x76-precomposed.png \
	resources/.compiled/favicon/120x120-precomposed.png \
	resources/.compiled/favicon/152x152-precomposed.png \
	resources/.compiled/favicon/180x180-precomposed.png

RESOURCE_TARGETS= \
	resources/.compiled/fest.ink/fest.css.gz \
	resources/.compiled/fest.ink/fest.js.gz \
	resources/.compiled/gh-fork-ribbon/gh-fork-ribbon.js.gz \
	resources/.compiled/pixiv/pixiv_logo.png \
	resources/.compiled/tz-data/tz-init.js.gz

all: \
	composer.phar \
	composer-update \
	composer-plugin \
	vendor \
	vendor/smarty/smarty/libs/sysplugins/smarty_internal_templatecompilerbase.php \
	node_modules \
	config/google-analytics.php \
	config/google-adsense.php \
	config/twitter.php \
	config/cookie-secret.php \
	resource \
	favicon-maybe \
	db/fest.sqlite

favicon: $(FAVICON_TARGETS)

favicon-maybe:
	test -f config/favicon.license.txt && make favicon || true

resource: $(RESOURCE_TARGETS)

composer-update: composer.phar
	./composer.phar self-update
	touch -r composer.json composer.phar

composer-plugin: composer.phar composer-update
	grep '"fxp/composer-asset-plugin"' ~/.composer/composer.json >/dev/null || ./composer.phar global require 'fxp/composer-asset-plugin:^1.1'
	grep '"hirak/prestissimo"' ~/.composer/composer.json >/dev/null && ./composer.phar global remove 'hirak/prestissimo' || true

vendor: composer.phar composer.lock composer-plugin composer-update
	php composer.phar install --prefer-dist

node_modules:
	npm install

check-style: vendor
	vendor/bin/phpcs --standard=PSR2 --encoding=UTF-8 $(STYLE_TARGETS)
	vendor/bin/check-author.php --php-files $(STYLE_TARGETS)

fix-style: vendor
	vendor/bin/phpcbf --standard=PSR2 --encoding=UTF-8 $(STYLE_TARGETS)

clean: clean-resource
	rm -rf \
		composer.phar \
		node_modules \
		runtime/favicon \
		runtime/tzdata-latest.tar.gz \
		vendor

clean-resource:
	rm -rf \
		resources/.compiled/* \
		runtime/tzdata \
		web/assets/*

clean-favicon:
	rm -rf \
		resources/.compiled/favicon \
		runtime/favicon

composer.phar:
	curl -sS https://getcomposer.org/installer | php
	touch -r composer.json composer.phar

resources/.compiled/favicon/favicon.ico: runtime/favicon/face-320x320.png
	mkdir -p resources/.compiled/favicon || true
	convert runtime/favicon/face-320x320.png \
		\( -clone 0 -resize 16x16 -sharpen 0x1.0 \) \
		\( -clone 0 -resize 32x32 -sharpen 0x.4 \) \
		\( -clone 0 -resize 48x48 \) \
		\( -clone 0 -resize 64x64 \) \
		-delete 0 -alpha off -colors 256 resources/.compiled/favicon/favicon.ico

resources/.compiled/favicon/76x76-precomposed.png: runtime/favicon/bust-500x500.png
	mkdir -p resources/.compiled/favicon || true
	convert runtime/favicon/bust-500x500.png -resize 76x76 -sharpen 0x.4 runtime/favicon/bust-76x76.png
	pngcrush -rem allb -l 9 runtime/favicon/bust-76x76.png resources/.compiled/favicon/76x76-precomposed.png

resources/.compiled/favicon/120x120-precomposed.png: runtime/favicon/bust-500x500.png
	mkdir -p resources/.compiled/favicon || true
	convert runtime/favicon/bust-500x500.png -resize 120x120 runtime/favicon/bust-120x120.png
	pngcrush -rem allb -l 9 runtime/favicon/bust-120x120.png resources/.compiled/favicon/120x120-precomposed.png

resources/.compiled/favicon/152x152-precomposed.png: runtime/favicon/bust-500x500.png
	mkdir -p resources/.compiled/favicon || true
	convert runtime/favicon/bust-500x500.png -resize 152x152 runtime/favicon/bust-152x152.png
	pngcrush -rem allb -l 9 runtime/favicon/bust-152x152.png resources/.compiled/favicon/152x152-precomposed.png

resources/.compiled/favicon/180x180-precomposed.png: runtime/favicon/bust-500x500.png
	mkdir -p resources/.compiled/favicon || true
	convert runtime/favicon/bust-500x500.png -resize 180x180 runtime/favicon/bust-180x180.png
	pngcrush -rem allb -l 9 runtime/favicon/bust-180x180.png resources/.compiled/favicon/180x180-precomposed.png

runtime/favicon/face-320x320.png: data/favicon/ikagirl.png
	mkdir -p runtime/favicon || true
	convert data/favicon/ikagirl.png -crop 320x320+225+112 runtime/favicon/face-320x320.png

runtime/favicon/bust-500x500.png: data/favicon/ikagirl.png
	mkdir -p runtime/favicon || true
	convert data/favicon/ikagirl.png -crop 500x500+86+107 runtime/favicon/bust-500x500.png

data/favicon/ikagirl.png: vendor data/favicon/ikagirl.dat
	./yii favicon/decrypt

resources/.compiled/fest.ink/fest.js.gz: node_modules $(JS_SRCS)
	./node_modules/.bin/gulp fest-ink-js

resources/.compiled/fest.ink/fest.css.gz: node_modules resources/fest.ink/fest.less
	./node_modules/.bin/gulp fest-ink-css

resources/.compiled/gh-fork-ribbon/gh-fork-ribbon.js.gz: node_modules resources/gh-fork-ribbon/gh-fork-ribbon.js
	./node_modules/.bin/gulp gh-fork

resources/.compiled/tz-data/tz-init.js.gz: node_modules runtime/tzdata resources/tz-data/tz-init.js
	./node_modules/.bin/gulp tz-data

resources/.compiled/pixiv/pixiv_logo.png: runtime/pixiv_logo/pixiv_logo.png
	mkdir -p resources/.compiled/pixiv || true
	pngcrush -rem allb -l 9 runtime/pixiv_logo/pixiv_logo.png resources/.compiled/pixiv/pixiv_logo.png

runtime/pixiv_logo/pixiv_logo.png: runtime/pixiv_logo/pixiv_logo.svg
	convert -background none runtime/pixiv_logo/pixiv_logo.svg -resize 77x30 runtime/pixiv_logo/pixiv_logo.png

runtime/pixiv_logo/pixiv_logo.svg: runtime/pixiv_logo/pixiv_logo.zip
	unzip -j runtime/pixiv_logo/pixiv_logo.zip pixiv_logo/pixiv_logo.svg -d runtime/pixiv_logo
	touch runtime/pixiv_logo/pixiv_logo.svg

runtime/pixiv_logo/pixiv_logo.zip:
	mkdir -p runtime/pixiv_logo || true
	wget -O runtime/pixiv_logo/pixiv_logo.zip 'http://source.pixiv.net/www/images/pixiv_logo.zip'

db/fest.sqlite: vendor runtime/tzdata FORCE
	./yii migrate/up --interactive=0
	sqlite3 db/fest.sqlite VACUUM

config/cookie-secret.php: vendor
	test -f config/cookie-secret.php || ./yii secret/cookie
	touch config/cookie-secret.php

config/twitter.php:
	cp config/twitter.php.sample config/twitter.php

config/google-analytics.php:
	echo '<?php' > config/google-analytics.php
	echo 'return "";' >> config/google-analytics.php

config/google-adsense.php:
	echo '<?php'                >  config/google-adsense.php
	echo 'return ['             >> config/google-adsense.php
	echo "    'client' => '',"  >> config/google-adsense.php
	echo "    'slot'   => '',"  >> config/google-adsense.php
	echo '];'                   >> config/google-adsense.php

runtime/tzdata: runtime/tzdata-latest.tar.gz
	mkdir runtime/tzdata || true
	tar -C runtime/tzdata -zxf runtime/tzdata-latest.tar.gz

runtime/tzdata-latest.tar.gz:
	wget -O runtime/tzdata-latest.tar.gz ftp://ftp.iana.org/tz/tzdata-latest.tar.gz

vendor/smarty/smarty/libs/sysplugins/smarty_internal_templatecompilerbase.php: vendor FORCE
	head -n 815 vendor/smarty/smarty/libs/sysplugins/smarty_internal_templatecompilerbase.php | tail -n 10 | grep '\\1 \\2' > /dev/null && \
		patch -d vendor/smarty/smarty -p1 -Nst < data/patch/smarty-strip.patch || /bin/true

.PHONY: all favicon resource check-style fix-style clean clean-resource clean-favicon FORCE
