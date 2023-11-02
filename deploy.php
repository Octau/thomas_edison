<?php

namespace Deployer;

require 'recipe/laravel.php';

// project name
set('application', 'thomas-edison');
//project repo
set('repository', 'git@gitlab.com:yosafatkesuma/thomas-edison-web.git');

set('writable_mode', 'chmod');

// Hosts
host('thomasedison')
    ->setLabels(['stage' => 'staging'])
    ->setHostname('209.97.172.97')
    ->setRemoteUser('root')
    ->setIdentityFile('~/.ssh/id_rsa')
    ->setDeployPath('~/thomasedison.com')
    ->set('stage', 'staging')
    ->set('branch', 'dev');

host('octachost')
    ->setLabels(['stage' => 'octa-prod'])
    ->setHostname('157.245.202.10')
    ->setRemoteUser('root')
    ->setIdentityFile('~/.ssh/id_rsa')
    ->setDeployPath('~/skripsiproject')
    ->set('stage', 'octa-prod')
    ->set('branch', 'master');


// Tasks

//task('yarn', function () {
//    run('cd {{release_path}} && yarn');
//});
//after('deploy:vendors', 'yarn');
//
//task('app:restart', function () {
////    run('sudo systemctl restart php8.2-fpm.service');
//    run('sudo supervisorctl restart {{alias}}_server');
//});
//after('deploy:symlink', 'app:restart');
//
//task('queue:restart', function () {
//    run('sudo supervisorctl restart {{alias}}_horizon');
//});
//after('deploy:symlink', 'queue:restart');
//
task('artisan:migrate', artisan('migrate --force', ['skipIfNoEnv']))->once();
//
//task('deploy:rollback', [
//    'rollback',
//    'app:restart',
//]);

task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'artisan:storage:link',
    'artisan:config:cache',
    'artisan:route:cache',
    'artisan:view:cache',
    'artisan:event:cache',
    'artisan:migrate',
    'deploy:publish',
]);


task('php:restart', function () {
//    run('sudo systemctl restart php8.2-fpm.service');
    run('sudo supervisorctl restart all');
});
after('deploy:symlink', 'php:restart');

//task('apidocs:generate', function () {
//    run('cd {{release_path}} && {{bin/php}} artisan scribe:generate');
//})->onStage('staging');
//after('deploy:symlink', 'apidocs:generate');

after('deploy:failed', 'deploy:unlock');
