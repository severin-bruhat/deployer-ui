<?php
require 'recipe/common.php';

server('prod', '178.170.71.9', 22)
    ->user('SaladeSFTP')
    ->password('AQ-HLwErRhZda2zsfmTzDrPE9pKbyr7')
    ->env('deploy_path', '/var/home/Salade/public_html')
    ->stage('production');

set('shared_files', ['inc/config.php', '.htaccess']);

set('repository', 'git@git.groupe361.com:php/bonduelle-salade-party.git');

task('deploy', [
    'deploy:prepare', //create releases and shared folders
    'deploy:release', // create specific release folder
    'deploy:update_code', //clone code
    'deploy:symlink', //create a symlink to the latest version
    'deploy:shared',
    'cleanup', //keeps only the 3 lastest releases
])->desc('Deploy your project');
after('deploy', 'success');

?>