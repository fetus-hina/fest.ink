STYLE_TARGETS=actions assets commands components controllers models
JS_SRCS=$(shell ls -1 resources/fest.ink/fest.js/*.js)
RESOURCE_TARGETS=resources/.compiled/fest.ink/fest.css \
	resources/.compiled/fest.ink/fest.js \
	resources/.compiled/gh-fork-ribbon/gh-fork-ribbon.js \
	resources/.compiled/ikamodoki/ikamodoki.css \
	resources/.compiled/tz-data/tz-init.js

all: composer.phar vendor node_modules config/google-analytics.php config/twitter.php config/cookie-secret.php resource db/fest.sqlite

resource: $(RESOURCE_TARGETS)

vendor: composer.phar
	php composer.phar install

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
		runtime/ikamodoki1.zip \
		runtime/tzdata-latest.tar.gz \
		vendor

clean-resource:
	rm -rf \
		resources/.compiled/* \
		runtime/tzdata \
		web/assets/*

composer.phar:
	curl -sS https://getcomposer.org/installer | php

resources/.compiled/fest.ink/fest.js: node_modules $(JS_SRCS)
	./node_modules/.bin/gulp uglify

resources/.compiled/fest.ink/fest.css: node_modules resources/fest.ink/fest.less
	./node_modules/.bin/gulp less

resources/.compiled/gh-fork-ribbon/gh-fork-ribbon.js: node_modules resources/gh-fork-ribbon/gh-fork-ribbon.js
	./node_modules/.bin/gulp gh-fork

resources/.compiled/ikamodoki/ikamodoki.css: node_modules resources/.compiled/ikamodoki/font/ikamodoki1_0.woff resources/ikamodoki/ikamodoki.less
	./node_modules/.bin/gulp ikamodoki

resources/.compiled/tz-data/tz-init.js: node_modules runtime/tzdata resources/tz-data/tz-init.js
	./node_modules/.bin/gulp tz-data

resources/.compiled/ikamodoki/font/ikamodoki1_0.woff: resources/.compiled/ikamodoki/font/ikamodoki1_0.ttf
	webify resources/.compiled/ikamodoki/font/ikamodoki1_0.ttf

resources/.compiled/ikamodoki/font/ikamodoki1_0.ttf: runtime/ikamodoki1.zip
	mkdir -p resources/.compiled/ikamodoki/font || true
	unzip -j runtime/ikamodoki1.zip ikamodoki/ikamodoki1_0.ttf -d resources/.compiled/ikamodoki/font
	touch resources/.compiled/ikamodoki/font/ikamodoki1_0.ttf

runtime/ikamodoki1.zip:
	wget -O runtime/ikamodoki1.zip http://aramugi.com/wp-content/uploads/2015/07/ikamodoki1.zip

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

runtime/tzdata: runtime/tzdata-latest.tar.gz
	mkdir runtime/tzdata || true
	tar -C runtime/tzdata -zxf runtime/tzdata-latest.tar.gz

runtime/tzdata-latest.tar.gz:
	wget -O runtime/tzdata-latest.tar.gz ftp://ftp.iana.org/tz/tzdata-latest.tar.gz

.PHONY: all resource check-style fix-style clean clean-resource FORCE
