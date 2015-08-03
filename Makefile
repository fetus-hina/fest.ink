STYLE_TARGETS=actions commands components controllers models

all: depends-install db/fest.sqlite web/res/tz

depends-install: composer.phar
	php composer.phar install

check-style:
	vendor/bin/phpcs --standard=PSR2 --encoding=UTF-8 $(STYLE_TARGETS)

fix-style:
	vendor/bin/phpcbf --standard=PSR2 --encoding=UTF-8 $(STYLE_TARGETS)

clean:
	rm -rf composer.phar vendor runtime/tzdata-latest.tar.gz web/res/tz

composer.phar:
	curl -sS https://getcomposer.org/installer | php

db/fest.sqlite: FORCE
	./yii migrate/up --interactive=0
	sqlite3 db/fest.sqlite VACUUM

web/res/tz: runtime/tzdata-latest.tar.gz
	mkdir web/res/tz
	tar -C web/res/tz -zxf runtime/tzdata-latest.tar.gz

runtime/tzdata-latest.tar.gz:
	wget -O runtime/tzdata-latest.tar.gz ftp://ftp.iana.org/tz/tzdata-latest.tar.gz

.PHONY: all depends-install check-style fix-style clean FORCE
