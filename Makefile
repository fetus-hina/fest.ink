STYLE_TARGETS=actions assets commands components controllers models

all: composer.phar vendor node_modules config/cookie-secret.php resource db/fest.sqlite

resource: clean-resource resources/.compiled

vendor: composer.phar
	php composer.phar install

node_modules:
	npm install

check-style: vendor
	vendor/bin/phpcs --standard=PSR2 --encoding=UTF-8 $(STYLE_TARGETS)

fix-style: vendor
	vendor/bin/phpcbf --standard=PSR2 --encoding=UTF-8 $(STYLE_TARGETS)

clean: clean-resource 
	rm -rf \
		composer.phar \
		node_modules \
		runtime/tzdata-latest.tar.gz \
		vendor

clean-resource:
	rm -rf \
		resources/.compiled \
		runtime/tzdata \
		web/assets/*

composer.phar:
	curl -sS https://getcomposer.org/installer | php

resources/.compiled: node_modules runtime/tzdata
	./node_modules/.bin/gulp

db/fest.sqlite: vendor runtime/tzdata FORCE
	./yii migrate/up --interactive=0
	sqlite3 db/fest.sqlite VACUUM

config/cookie-secret.php: vendor
	./yii secret/cookie

runtime/tzdata: runtime/tzdata-latest.tar.gz
	mkdir runtime/tzdata || true
	tar -C runtime/tzdata -zxf runtime/tzdata-latest.tar.gz

runtime/tzdata-latest.tar.gz:
	wget -O runtime/tzdata-latest.tar.gz ftp://ftp.iana.org/tz/tzdata-latest.tar.gz

.PHONY: all resource check-style fix-style clean clean-resource FORCE
