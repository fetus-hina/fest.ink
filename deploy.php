<?php

declare(strict_types=1);

namespace Deployer;

require 'recipe/yii.php';

set('repository', 'git@github.com:fetus-hina/fest.ink.git');
set('shared_files', [
    'config/cookie-secret.php',
    'config/favicon.license.txt',
    'db/fest.sqlite',
]);
set('shared_dirs', [
    'runtime/logs',
]);
set('writable_dirs', [
    'runtime',
    'runtime/logs',
    'web/assets',
]);
set('bin/make', fn () => run('which make'));
set('bin/npm', fn () => run('which npm'));

// Hosts
host('fest.ink')
    ->set('hostname', '192.168.0.27')
    ->set('remote_user', 'fest.ink')
    ->set('http_user', 'fest.ink')
    ->set('deploy_path', '~/app.dep')
    ->set('keep_releases', 2);

task('deploy:vendors', function () {
    within('{{release_or_current_path}}', function () {
        run('{{bin/composer}} {{composer_action}} {{composer_options}} 2>&1');
        run('{{bin/npm}} clean-install 2>&1');
    });
});

after('deploy:vendors', 'deploy:assets');

task('deploy:assets', function () {
    within('{{release_or_current_path}}', function () {
        run('{{bin/make}} vendor/smarty/smarty/libs/sysplugins/smarty_internal_templatecompilerbase.php 2>&1');
        run('{{bin/make}} resource 2>&1');
        run('{{bin/make}} favicon-maybe 2>&1');
    });
});

// Hooks

after('deploy:failed', 'deploy:unlock');
