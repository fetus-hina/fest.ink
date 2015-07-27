STYLE_TARGETS=actions commands components controllers models

all: depends-install db/fest.sqlite

depends-install: composer.phar
	php composer.phar install

check-style:
	vendor/bin/phpcs --standard=PSR2 --encoding=UTF-8 $(STYLE_TARGETS)

fix-style:
	vendor/bin/phpcbf --standard=PSR2 --encoding=UTF-8 $(STYLE_TARGETS)

clean:
	rm -rf composer.phar vendor

composer.phar:
	curl -sS https://getcomposer.org/installer | php

db/fest.sqlite:
	cat db/sqls/01/table.sql | sqlite3 db/fest.sqlite
	db/sqls/01/2ndfest.php | sqlite3 db/fest.sqlite
	db/sqls/02/3rdfest.php | sqlite3 db/fest.sqlite

.PHONY: all depends-install check-style fix-style clean
