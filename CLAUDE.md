# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

Source for https://fest.ink/ — "イカフェスレート", a Splatoon Splatfest tracking/rating site. Yii 2 PHP application backed by SQLite, with a server-rendered Bootstrap 5 frontend (plain Yii PHP views) and a JSON API documented in `API.md`.

Requires PHP 8.3+ (`ext-pdo_sqlite`, `ext-mbstring`, `ext-openssl`, `ext-zlib`, plus the always-bundled `ext-hash`/`ext-json`/`ext-pcre` — see `require` in `composer.json`, which is the canonical list), Node.js, and SQLite 3. ImageMagick (`convert`) and `pngcrush` are needed only when building the favicon.

## Common commands

The Makefile is the canonical build entry point — run targets through it rather than invoking the underlying tools directly.

- `make` — full setup: `composer install`, `npm ci`, compile JS/CSS/tz-data, generate `config/cookie-secret.php`, run migrations to create `db/fest.sqlite`, optionally rebuild the favicon.
- `make resource` — recompile only frontend assets (LESS → CSS via `lessc`+`postcss`, JS bundles via `uglifyjs`, plus `.gz` and `.br` precompressed copies under `resources/.compiled/`).
- `make favicon` — regenerate favicons; only works if `config/favicon.license.txt` exists (the artwork is non-free, see README).
- `make clean` / `make clean-resource` / `make clean-favicon` — staged cleanups.
- `make check-style` — PHP_CodeSniffer against PSR-12 over `actions assets commands controllers models` (note: `components` is intentionally excluded from the lint set).
- `make fix-style` — `phpcbf` autofix (PSR-2 ruleset).
- `./yii migrate/up --interactive=0` — apply DB migrations (the Makefile does this when building `db/fest.sqlite`).
- `./yii <controller>/<action>` — run console commands (see `commands/`, e.g. `./yii secret/cookie`, `./yii favicon/decrypt`).
- `./deploy.sh {vA.B.C|minor|patch}` — tag a release and run Deployer (`vendor/bin/dep deploy`) against the `fest.ink` host defined in `deploy.php`.

There is no test suite in this repo — no PHPUnit config, no `tests/` directory. Don't fabricate one.

## Architecture

**Yii 2 "basic" application layout, lightly customized.** Entry points are `web/index.php` (HTTP) and `yii` (CLI). Both gate `YII_DEBUG`/`YII_ENV` on the absence of a `REVISION` file — production deploys write that file, dev checkouts don't, so the same code runs in dev mode locally and prod mode on the server.

**Controllers are thin; logic lives in Action classes.** `controllers/FestController.php`, `SiteController.php`, `TimezoneController.php` mostly just register standalone actions from `actions/{fest,site,timezone}/`. When adding a route, the usual move is to add an Action class in `actions/<group>/` and wire it up in the controller's `actions()` method, not to add an inline action method.

**URL routing is strict and explicit.** `config/web.php` sets `enableStrictParsing => true` with hand-written rules (numeric IDs map to `fest/view`, `<id>.json` to `fest/view-json`, and so on). Adding a public URL means editing the `urlManager.rules` array — there is no convention-based fallback.

**Views are plain Yii PHP templates.** `views/layouts/` (`main.php`, `navbar.php`, `footer.php`) wraps page views under `views/{fest,site}/`. The convention in this repo is curly-brace block syntax (`<?php foreach (...) { ?> ... <?php } ?>`) rather than the alternative `endforeach`/`endif` form — match that style when editing.

**Models are Yii ActiveRecord against SQLite.** `models/Fest.php` is the central entity (a Splatfest event); `Team`, `Color`, `OfficialResult`, `OfficialData`, `Mvp`, `Timezone` hang off it. `Fest::toJsonArray()` is the canonical shape for the public JSON API and is what `IndexJsonAction`/`ViewJsonAction`/`EmulateOfficialJsonAction` serialize. The DB lives in `db/fest.sqlite` and is rebuilt from `migrations/` — each historical Splatfest has its own pair of `*_fest.php` / `*_data.php` / `*_result.php` migrations, so adding a new fest event means writing new migrations rather than editing seed scripts.

**Custom web components in `components/web/`** override Yii defaults: `Controller` reads a `timezone` cookie and applies it to the request; `Response` + `PrettyJsonResponseFormatter` produce the pretty-printed JSON; `AssetConverter` writes a `.gz` sibling for every published `.js`/`.css`, and `AssetManager` wires it up.

**Frontend bundling is concat-then-minify, not webpack.** `resources/fest.ink/fest.js/*.js` files are concatenated in lexical order (the numeric prefixes — `00-`, `01-`, `03-`, … `99-` — define load order) and piped through UglifyJS. There is no transpile step — the sources are written directly against the `.browserslistrc` baseline (which tracks Bootstrap 5's). LESS is compiled with `lessc` and post-processed by PostCSS. Outputs land in `resources/.compiled/` and are served from there with precompressed `.gz`/`.br` siblings. When adding new JS, pick a numeric prefix matching its phase rather than introducing a bundler.

**Timezone data is bundled from IANA tzdata.** `make` downloads `tzdata-latest.tar.gz` from `ftp.iana.org` and extracts it under `runtime/tzdata`; the JS frontend uses `timezone-js` against `resources/.compiled/tz-data/files`.

**Deployment uses Deployer** (`deploy.php`) with shared files for the SQLite DB, cookie secret, and favicon license — those persist across releases. The deploy hook reruns `make resource` and `make favicon-maybe` on the remote.

## Conventions worth knowing

- New PHP files start with `declare(strict_types=1);` where present, and use `namespace app\...` mirroring the directory.
- Lint scope (PSR-12) covers `actions assets commands controllers models`. Files under `components/` are not linted by the Makefile target — match the existing style there but don't expect the linter to enforce it.
- `config/cookie-secret.php`, `config/favicon.license.txt`, and `db/fest.sqlite` are gitignored / shared-across-deploys — don't commit them.
