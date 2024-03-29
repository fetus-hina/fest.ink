STYLE_TARGETS=actions assets commands components controllers models
JS_SRCS=$(shell ls -1 resources/fest.ink/fest.js/*.js)

FAVICON_TARGETS= \
	resources/.compiled/favicon/favicon.ico \
	resources/.compiled/favicon/76x76-precomposed.png \
	resources/.compiled/favicon/120x120-precomposed.png \
	resources/.compiled/favicon/152x152-precomposed.png \
	resources/.compiled/favicon/180x180-precomposed.png

RESOURCE_TARGETS_MAIN= \
	resources/.compiled/fest.ink/fest.css \
	resources/.compiled/fest.ink/fest.js \
	resources/.compiled/tz-data/tz-init.js

RESOURCE_TARGETS= \
	$(RESOURCE_TARGETS_MAIN) \
	$(RESOURCE_TARGETS_MAIN:.css=.css.br) \
	$(RESOURCE_TARGETS_MAIN:.css=.css.gz) \
	$(RESOURCE_TARGETS_MAIN:.js=.js.br) \
	$(RESOURCE_TARGETS_MAIN:.js=.js.gz) \
	resources/.compiled/tz-data/files

all: \
	composer.phar \
	vendor \
	vendor/smarty/smarty/libs/sysplugins/smarty_internal_templatecompilerbase.php \
	node_modules \
	config/cookie-secret.php \
	resource \
	favicon-maybe \
	db/fest.sqlite

favicon: $(FAVICON_TARGETS)

favicon-maybe:
	test -f config/favicon.license.txt && make favicon || true

resource: $(RESOURCE_TARGETS)

vendor: composer.phar composer.lock
	php composer.phar install --prefer-dist

composer.lock: composer.json composer.phar
	php composer.phar update -vvv
	touch -r composer.json composer.lock

node_modules: package-lock.json
	npm ci
	@touch $@

package-lock.json: package.json
	@rm -rf package-lock.json node_modules
	npm update
	@touch $@

check-style: vendor
	vendor/bin/phpcs --standard=PSR12 --encoding=UTF-8 $(STYLE_TARGETS)

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
ifeq (, $(shell which composer 2>/dev/null))
	curl -fsSL 'https://getcomposer.org/installer' | php -- --filename=$@ --stable
	@touch $@
else
	ln -s `which composer` $@
endif

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

resources/.compiled/fest.ink/fest.js: $(JS_SRCS) node_modules
	@mkdir -p resources/.compiled/fest.ink
	cat $(JS_SRCS) | \
		npx babel -s false -f jsfile | \
		npx uglifyjs -c -m -b beautify=false,ascii_only=true --comments -o $@

resources/.compiled/fest.ink/fest.css: resources/fest.ink/fest.less node_modules
	@mkdir -p resources/.compiled/fest.ink
	npx lessc $< | npx postcss --no-map -o $@

resources/.compiled/tz-data/tz-init.js: resources/tz-data/tz-init.js node_modules runtime/tzdata
	@mkdir -p resources/.compiled/tz-data
	npx babel -s false $< | npx uglifyjs -c -m -b beautify=false,ascii_only=true --comments -o $@

db/fest.sqlite: vendor runtime/tzdata FORCE
	./yii migrate/up --interactive=0
	sqlite3 db/fest.sqlite VACUUM

config/cookie-secret.php: vendor
	test -f config/cookie-secret.php || ./yii secret/cookie
	touch config/cookie-secret.php

runtime/tzdata: runtime/tzdata-latest.tar.gz
	mkdir runtime/tzdata || true
	tar -C runtime/tzdata -zxf runtime/tzdata-latest.tar.gz

runtime/tzdata-latest.tar.gz:
	wget -O runtime/tzdata-latest.tar.gz ftp://ftp.iana.org/tz/tzdata-latest.tar.gz

resources/.compiled/tz-data/files: runtime/tzdata
	rsync -a $</ $@/

vendor/smarty/smarty/libs/sysplugins/smarty_internal_templatecompilerbase.php: vendor FORCE
	head -n 815 vendor/smarty/smarty/libs/sysplugins/smarty_internal_templatecompilerbase.php | tail -n 10 | grep '\\1 \\2' > /dev/null && \
		patch -d vendor/smarty/smarty -p1 -Nst < data/patch/smarty-strip.patch || /bin/true

%.gz: %
	gzip -cq9 --rsyncable < $< > $@

BROTLI := $(shell if [ -e /usr/bin/brotli ]; then echo brotli; else echo bro; fi )
%.br: %
ifeq ($(BROTLI),bro)
	bro --quality 11 --force --input $< --output $@
else
	brotli -Zfo $@ $<
endif
	@chmod 644 $@
	@touch $@

.PHONY: all favicon resource check-style fix-style clean clean-resource clean-favicon FORCE
